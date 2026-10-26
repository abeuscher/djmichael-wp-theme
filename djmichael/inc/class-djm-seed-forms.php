<?php
/**
 * Builds the Fluent Forms contact form from the compact description in
 * seed/content.php, so `wp djm seed` creates the form as well as the pages.
 *
 * Fluent Forms stores a form as a row in its own table with the fields as
 * JSON, plus one meta row per setting. The JSON shape here matches what the
 * plugin's own form builder and importer write.
 *
 * @package djmichael
 */

defined( 'ABSPATH' ) || exit;

class DJM_Seed_Forms {

	/**
	 * Create or update a form, matched by the seed key kept in its meta.
	 *
	 * @param string $key  Seed key, e.g. "contact".
	 * @param array  $form Form description from seed/content.php.
	 * @return int|WP_Error Form ID.
	 */
	public static function upsert( $key, $form ) {
		if ( ! function_exists( 'wpFluent' ) ) {
			return new WP_Error( 'djm_no_fluentform', __( 'Fluent Forms is not active.', 'djmichael' ) );
		}

		$fields = array(
			'fields'       => array(),
			'submitButton' => self::submit_button( $form['submit'] ?? __( 'Send', 'djmichael' ) ),
		);
		foreach ( array_values( $form['fields'] ) as $i => $field ) {
			$built = self::field( $field, $i );
			if ( $built ) {
				$fields['fields'][] = $built;
			}
		}

		$row = array(
			'title'       => $form['title'],
			'form_fields' => wp_json_encode( $fields ),
			'status'      => 'published',
			'has_payment' => 0,
			'type'        => 'form',
		);

		$existing = self::find( $key );
		if ( $existing ) {
			wpFluent()->table( 'fluentform_forms' )->where( 'id', $existing )->update( $row );
			$id = $existing;
		} else {
			$row['created_by'] = get_current_user_id() ? get_current_user_id() : 1;
			$id                = (int) wpFluent()->table( 'fluentform_forms' )->insertGetId( $row );
			self::set_meta( $id, '_djm_seed_key', $key );
		}

		self::set_meta( $id, 'formSettings', wp_json_encode( self::settings( $form ) ) );
		self::set_meta( $id, 'notifications', wp_json_encode( self::notification( $form ) ) );

		return $id;
	}

	/**
	 * The form ID previously created for a seed key, if any.
	 *
	 * @param string $key Seed key.
	 */
	private static function find( $key ) {
		$meta = wpFluent()->table( 'fluentform_form_meta' )
			->where( 'meta_key', '_djm_seed_key' )
			->where( 'value', $key )
			->first();
		if ( ! $meta ) {
			return 0;
		}
		// The form may have been deleted even though its meta row survived.
		$form = wpFluent()->table( 'fluentform_forms' )->where( 'id', $meta->form_id )->first();
		return $form ? (int) $meta->form_id : 0;
	}

	/**
	 * Write one meta row, replacing any previous value.
	 *
	 * @param int    $form_id Form ID.
	 * @param string $key     Meta key.
	 * @param string $value   Meta value.
	 */
	private static function set_meta( $form_id, $key, $value ) {
		wpFluent()->table( 'fluentform_form_meta' )
			->where( 'form_id', $form_id )
			->where( 'meta_key', $key )
			->delete();
		wpFluent()->table( 'fluentform_form_meta' )->insert(
			array(
				'form_id'  => $form_id,
				'meta_key' => $key,
				'value'    => $value,
			)
		);
	}

	/**
	 * One field, in Fluent Forms' JSON shape.
	 *
	 * @param array $field Field description.
	 * @param int   $index Position in the form.
	 */
	private static function field( $field, $index ) {
		$name     = $field['name'];
		$required = ! empty( $field['required'] );
		$label    = $field['label'];
		if ( ! empty( $field['optional'] ) ) {
			// The prototype marks optional fields in the label instead of
			// starring the required ones.
			$label .= ' <span class="field__optional">(' . __( 'optional', 'djmichael' ) . ')</span>';
		}

		$classes = 'field' . ( ! empty( $field['full'] ) ? ' field--full' : '' );
		$common  = array(
			'container_class'   => $classes,
			'label'             => $label,
			'label_placement'   => '',
			'admin_field_label' => $field['label'],
			'help_message'      => '',
			'validation_rules'  => array(
				'required' => array(
					'value'   => $required,
					'message' => __( 'This field is required', 'djmichael' ),
				),
			),
			'conditional_logics' => array(),
		);
		$uniq = 'el_djm_' . $name;

		switch ( $field['type'] ) {
			case 'text':
				return array(
					'index'          => $index,
					'element'        => 'input_text',
					'attributes'     => array(
						'type'         => 'text',
						'name'         => $name,
						'value'        => '',
						'id'           => '',
						'class'        => '',
						'placeholder'  => '',
						'autocomplete' => $field['autocomplete'] ?? '',
					),
					'settings'       => $common,
					'editor_options' => array(
						'title'      => __( 'Simple Text', 'fluentform' ),
						'icon_class' => 'ff-edit-text',
						'template'   => 'inputText',
					),
					'uniqElKey'      => $uniq,
				);

			case 'email':
				$common['validation_rules']['email'] = array(
					'value'   => true,
					'message' => __( 'This field must contain a valid email', 'djmichael' ),
				);
				return array(
					'index'          => $index,
					'element'        => 'input_email',
					'attributes'     => array(
						'type'         => 'email',
						'name'         => $name,
						'value'        => '',
						'id'           => '',
						'class'        => '',
						'placeholder'  => '',
						'autocomplete' => 'email',
					),
					'settings'       => $common,
					'editor_options' => array(
						'title'      => __( 'Email Address', 'fluentform' ),
						'icon_class' => 'ff-edit-email',
						'template'   => 'inputText',
					),
					'uniqElKey'      => $uniq,
				);

			case 'date':
				$settings                = $common;
				$settings['date_format'] = $field['format'] ?? 'Y-m-d';
				$settings['is_time_enabled'] = false;
				return array(
					'index'          => $index,
					'element'        => 'input_date',
					'attributes'     => array(
						'type'        => 'text',
						'name'        => $name,
						'value'       => '',
						'id'          => '',
						'class'       => '',
						'placeholder' => $field['placeholder'] ?? '',
					),
					'settings'       => $settings,
					'editor_options' => array(
						'title'      => __( 'Time & Date', 'fluentform' ),
						'icon_class' => 'ff-edit-date',
						'template'   => 'inputText',
					),
					'uniqElKey'      => $uniq,
				);

			case 'number':
				$settings                      = $common;
				$settings['number_step']       = $field['step'] ?? 1;
				$settings['prefix_label']      = '';
				$settings['suffix_label']      = '';
				$settings['numeric_formatter'] = '';
				return array(
					'index'          => $index,
					'element'        => 'input_number',
					'attributes'     => array(
						'type'        => 'number',
						'name'        => $name,
						'value'       => '',
						'id'          => '',
						'class'       => '',
						'placeholder' => '',
						'min'         => $field['min'] ?? '',
						'max'         => $field['max'] ?? '',
						'inputmode'   => 'numeric',
					),
					'settings'       => $settings,
					'editor_options' => array(
						'title'      => __( 'Numeric Field', 'fluentform' ),
						'icon_class' => 'ff-edit-numeric',
						'template'   => 'inputText',
					),
					'uniqElKey'      => $uniq,
				);

			case 'select':
				$options = array();
				foreach ( $field['options'] as $option ) {
					$options[] = array(
						'label'      => $option,
						'value'      => $option,
						'calc_value' => '',
					);
				}
				$settings                       = $common;
				$settings['placeholder']        = $field['placeholder'] ?? __( 'Choose one', 'djmichael' );
				$settings['advanced_options']   = $options;
				$settings['calc_value_status']  = false;
				$settings['enable_select_2']    = 'no';
				$settings['enable_image_input'] = false;
				$settings['randomize_options']  = 'no';
				return array(
					'index'          => $index,
					'element'        => 'select',
					'attributes'     => array(
						'name'  => $name,
						'value' => '',
						'id'    => '',
						'class' => '',
					),
					'settings'       => $settings,
					'editor_options' => array(
						'title'      => __( 'Dropdown', 'fluentform' ),
						'icon_class' => 'ff-edit-dropdown',
						'element'    => 'select',
						'template'   => 'select',
					),
					'uniqElKey'      => $uniq,
				);

			case 'textarea':
				return array(
					'index'          => $index,
					'element'        => 'textarea',
					'attributes'     => array(
						'name'        => $name,
						'value'       => '',
						'id'          => '',
						'class'       => '',
						'placeholder' => '',
						'rows'        => $field['rows'] ?? 5,
						'cols'        => 2,
					),
					'settings'       => $common,
					'editor_options' => array(
						'title'      => __( 'Text Area', 'fluentform' ),
						'icon_class' => 'ff-edit-textarea',
						'template'   => 'inputTextarea',
					),
					'uniqElKey'      => $uniq,
				);
		}

		return null;
	}

	/**
	 * The submit button, carrying the theme's own button classes.
	 *
	 * @param string $text Button label.
	 */
	private static function submit_button( $text ) {
		return array(
			'uniqElKey'      => 'el_djm_submit',
			'element'        => 'button',
			'attributes'     => array(
				'type'  => 'submit',
				// ff_btn_no_style opts out of the inline colour Fluent Forms
				// prints for its own button styling.
				'class' => 'btn btn--primary btn--large ff_btn_no_style',
			),
			'settings'       => array(
				'align'            => 'left',
				'button_style'     => 'default',
				'container_class'  => 'contact-form__actions',
				'help_message'     => '',
				'button_size'      => 'md',
				'button_ui'        => array(
					'type'    => 'default',
					'text'    => $text,
					'img_url' => '',
				),
			),
			'editor_options' => array(
				'title' => __( 'Submit Button', 'fluentform' ),
			),
		);
	}

	/**
	 * Form settings: what the visitor sees after sending.
	 *
	 * @param array $form Form description.
	 */
	private static function settings( $form ) {
		return array(
			'confirmation'              => array(
				'redirectTo'           => 'samePage',
				'messageToShow'        => $form['confirmation'] ?? __( 'Thank you.', 'djmichael' ),
				'customPage'           => null,
				'samePageFormBehavior' => 'hide_form',
				'customUrl'            => null,
			),
			'restrictions'              => array(
				'denyEmptySubmission' => array(
					'enabled' => true,
					'message' => __( 'Please fill in the form before sending.', 'djmichael' ),
				),
			),
			'layout'                    => array(
				'labelPlacement'        => 'top',
				'helpMessagePlacement'  => 'with_label',
				'errorMessagePlacement' => 'inline',
				'cssClassName'          => 'contact-form',
				// The prototype marks optional fields instead of required ones.
				'asteriskPlacement'     => 'asterisk-none',
			),
			'delete_entry_on_submission' => 'no',
		);
	}

	/**
	 * The email Michael gets for each submission.
	 *
	 * @param array $form Form description.
	 */
	private static function notification( $form ) {
		$note = $form['notification'] ?? array();
		return array(
			'name'         => __( 'Send to Michael', 'djmichael' ),
			'sendTo'       => array(
				'type'    => 'email',
				'email'   => $note['to'] ?? '{wp.admin_email}',
				'field'   => '',
				'routing' => array(),
			),
			'fromName'     => '',
			'fromEmail'    => '',
			'replyTo'      => $note['reply_to'] ?? '',
			'bcc'          => '',
			'subject'      => $note['subject'] ?? __( 'New enquiry', 'djmichael' ),
			'message'      => $note['message'] ?? '<p>{all_data}</p>',
			'conditionals' => array(),
			'enabled'      => true,
		);
	}
}

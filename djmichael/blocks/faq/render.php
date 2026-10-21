<?php
/**
 * Block: FAQ.
 *
 * Also prints the FAQPage structured data. It sits next to the questions
 * rather than in <head>, so the markup and the data can't drift apart;
 * JSON-LD is valid anywhere in the document.
 *
 * @package djmichael
 *
 * @var array $block      Block settings and attributes.
 * @var bool  $is_preview Rendering in the editor.
 */

defined( 'ABSPATH' ) || exit;

$djm_items = array_values(
	array_filter(
		(array) get_field( 'items' ),
		function ( $row ) {
			return is_array( $row ) && ! empty( $row['question'] ) && ! empty( $row['answer'] );
		}
	)
);

if ( ! $djm_items ) {
	if ( $is_preview ) {
		echo '<p style="padding:1rem">' . esc_html__( 'Add questions to this block.', 'djmichael' ) . '</p>';
	}
	return;
}

$djm_id_attr = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="section faq"<?php echo $djm_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> data-part="faq" aria-label="<?php esc_attr_e( 'Questions and answers', 'djmichael' ); ?>">
	<div class="container">
		<div class="faq__list">
			<?php foreach ( $djm_items as $djm_i => $djm_item ) : ?>
				<details class="faq__item"<?php echo 0 === $djm_i ? ' open' : ''; ?>>
					<summary class="faq__question"><?php echo esc_html( $djm_item['question'] ); ?></summary>
					<div class="faq__answer"><?php echo wp_kses_post( $djm_item['answer'] ); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
if ( $is_preview ) {
	return;
}

/**
 * Plain text for JSON-LD: no tags, and entities decoded, because JSON-LD
 * is data rather than HTML and "&#8217;" would be read literally.
 *
 * @param string $html Field value.
 */
$djm_plain = function ( $html ) {
	return trim( html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' ) );
};

$djm_entity = array();
foreach ( $djm_items as $djm_item ) {
	$djm_entity[] = array(
		'@type'          => 'Question',
		'name'           => $djm_plain( $djm_item['question'] ),
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $djm_plain( $djm_item['answer'] ),
		),
	);
}
echo '<script type="application/ld+json">' . wp_json_encode(
	array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $djm_entity,
	),
	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
) . "</script>\n";

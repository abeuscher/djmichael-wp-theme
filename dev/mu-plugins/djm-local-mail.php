<?php
/**
 * Local only: send all mail to Mailpit (http://localhost:8025) instead of the internet.
 *
 * @package djmichael
 */

add_action(
	'phpmailer_init',
	function ( $mailer ) {
		if ( 'local' !== wp_get_environment_type() ) {
			return;
		}
		$mailer->isSMTP();
		$mailer->Host     = 'mailpit';
		$mailer->Port     = 1025;
		$mailer->SMTPAuth = false;
	}
);

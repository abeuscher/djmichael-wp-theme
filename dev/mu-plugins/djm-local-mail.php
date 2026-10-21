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

/**
 * Local only: give mail a sender PHPMailer will accept.
 *
 * WordPress defaults the sender to "wordpress@" plus the site host, which is
 * "wordpress@localhost" here. PHPMailer rejects an address with no TLD, so
 * every message fails before it reaches Mailpit. Production runs on a real
 * domain, so this only matters locally.
 */
add_filter(
	'wp_mail_from',
	function ( $from ) {
		if ( 'local' !== wp_get_environment_type() ) {
			return $from;
		}
		return 'wordpress@localhost.test' === $from || 'wordpress@localhost' === $from
			? 'wordpress@djmichael.test'
			: $from;
	}
);

<?php
/**
 * ローカル環境専用：wp_mail を Mailpit に送る。
 */
add_action(
	'phpmailer_init',
	function ( $phpmailer ) {
		$phpmailer->isSMTP();
		$phpmailer->Host = '127.0.0.1';
		$phpmailer->Port = 1025;
	}
);

// localhost は送信元として無効なため、ローカルでは仮のアドレスを使う。
add_filter(
	'wp_mail_from',
	function () {
		return 'no-reply@aokikogyo.local';
	}
);

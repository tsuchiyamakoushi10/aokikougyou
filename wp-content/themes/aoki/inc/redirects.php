<?php
/**
 * 旧サイト（静的HTML）の URL から新 URL への 301 リダイレクト。
 * 検索エンジンの評価や外部リンク・ブックマークを引き継ぎ、404 を出さないようにします。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aoki_legacy_redirects() {
	return array(
		'index.html'            => '',
		'index.htm'             => '',
		'company/index.html'    => 'company',
		'business/index02.html' => 'business/automatic-door',
		'business/index01.html' => 'business/fittings',
		'business/index.html'   => 'business',
		'case/index.html'       => 'works',
		'case'                  => 'works',
		'recruit/index.html'    => 'recruit',
		'staff/index.html'      => 'staff',
		'contact/index.html'    => 'contact',
		'entry/index.html'      => 'entry',
	);
}

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $home && str_starts_with( $path, $home ) ) {
			$path = trim( substr( $path, strlen( $home ) ), '/' );
		}
		$map = aoki_legacy_redirects();
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( aoki_url( $map[ $path ] ), 301 );
			exit;
		}
		// 旧 CGI フォーム・画像ディレクトリ。
		if ( str_starts_with( $path, 'cgi-bin/contact' ) ) {
			wp_safe_redirect( aoki_url( 'contact' ), 301 );
			exit;
		}
		if ( str_starts_with( $path, 'cgi-bin/entry' ) ) {
			wp_safe_redirect( aoki_url( 'entry' ), 301 );
			exit;
		}
	},
	1
);

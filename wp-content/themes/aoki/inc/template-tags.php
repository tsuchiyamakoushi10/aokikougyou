<?php
/**
 * テンプレート用ヘルパー。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * サイト内パスの URL。
 */
function aoki_url( $path = '' ) {
	return home_url( $path ? '/' . trim( $path, '/' ) . '/' : '/' );
}

function aoki_asset( $rel ) {
	return AOKI_URI . '/assets/img/' . ltrim( $rel, '/' );
}

/**
 * テーマ同梱画像の <img>。width/height を付けてレイアウトのずれを防ぐ。
 */
function aoki_img( $rel, $alt = '', $attrs = array() ) {
	$path  = AOKI_DIR . '/assets/img/' . $rel;
	$attrs = array_merge(
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		),
		$attrs
	);
	if ( file_exists( $path ) && ( $size = wp_getimagesize( $path ) ) ) {
		$attrs['width']  = $size[0];
		$attrs['height'] = $size[1];
	}
	$html = '<img src="' . esc_url( aoki_asset( $rel ) ) . '" alt="' . esc_attr( $alt ) . '"';
	foreach ( $attrs as $k => $v ) {
		if ( false === $v ) {
			continue;
		}
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return $html . '>';
}

/**
 * 現在のページがナビ項目（またはその配下）か。
 */
function aoki_is_current( $path ) {
	$req  = trim( (string) wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( $home && str_starts_with( $req, $home ) ) {
		$req = trim( substr( $req, strlen( $home ) ), '/' );
	}
	return $req === $path || str_starts_with( $req, $path . '/' );
}

/**
 * インライン SVG アイコン（線画・24px グリッド）。
 */
function aoki_icon( $name, $class = 'icon' ) {
	$p = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'tel'      => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'entry'    => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
		'insta'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/>',
		'pin'      => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'home'     => '<path d="M3 11 12 4l9 7"/><path d="M5 10v10h14V10"/>',
		'door'     => '<rect x="3" y="3" width="18" height="18" rx="1"/><path d="M12 3v18M9 12h1M14 12h1"/><path d="M5 6h14" opacity=".5"/>',
		'control'  => '<rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M8 17h8"/>',
		'wiring'   => '<path d="M7 3v5M17 3v5M5 8h14v3a7 7 0 0 1-14 0zM12 18v3"/>',
		'wrench'   => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4z"/>',
		'frame'    => '<rect x="4" y="3" width="16" height="18"/><rect x="8" y="7" width="8" height="14"/>',
		'sash'     => '<rect x="3" y="4" width="18" height="16"/><path d="M12 4v16M3 12h18"/>',
		'install'  => '<path d="M4 20h16M7 20V8l5-4 5 4v12"/><path d="M10 20v-6h4v6"/>',
		'custom'   => '<path d="M3 21l3-1 12-12-2-2L4 18zM14 6l2 2M16 3l5 5"/>',
		'shield'   => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/><path d="m9 12 2 2 4-4"/>',
		'license'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.5"/><path d="M5.5 17a3.5 3.5 0 0 1 7 0M15 9h3M15 13h3"/>',
		'piggy'    => '<path d="M19 11c0-3.3-3.1-6-7-6S5 7.7 5 11c0 1.8.9 3.4 2.3 4.5V19h3v-2h3.4v2h3v-3.5A5.8 5.8 0 0 0 19 11z"/><path d="M19 10h2M9 4.5 8 3M12 9h3"/>',
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/><path d="m9 15 2 2 4-4"/>',
		'uniform'  => '<path d="M8 3 3 6l2 5 2-1v11h10V10l2 1 2-5-5-3a4 4 0 0 1-8 0z"/>',
		'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'car'      => '<path d="M5 16V11l2-5h10l2 5v5M3 16h18v3h-3v-1H6v1H3z"/><circle cx="7.5" cy="13.5" r="1"/><circle cx="16.5" cy="13.5" r="1"/>',
		'device'   => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
		'alert'    => '<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17h.01"/>',
	);
	if ( ! isset( $p[ $name ] ) ) {
		return '';
	}
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $p[ $name ] . '</svg>';
}

/**
 * ロゴ（マーク＋ロゴタイプ）。
 */
function aoki_logo( $variant = 'default' ) {
	static $mark = null, $word = null;
	if ( null === $mark ) {
		$mark = file_get_contents( AOKI_DIR . '/assets/img/logo/mark.svg' );
		$word = str_replace( '#393536', 'currentColor', file_get_contents( AOKI_DIR . '/assets/img/logo/wordmark.svg' ) );
	}
	$mark_svg = str_replace( '<svg ', '<svg class="logo__mark" aria-hidden="true" focusable="false" ', $mark );
	$word_svg = str_replace( '<svg ', '<svg class="logo__word" aria-hidden="true" focusable="false" ', $word );
	return '<span class="logo logo--' . esc_attr( $variant ) . '">' . $mark_svg . $word_svg . '<span class="screen-reader-text">青木工業株式会社</span></span>';
}

function aoki_mark_svg( $class = '' ) {
	$mark = file_get_contents( AOKI_DIR . '/assets/img/logo/mark.svg' );
	return str_replace( '<svg ', '<svg class="' . esc_attr( $class ) . '" aria-hidden="true" focusable="false" ', $mark );
}

/**
 * セクション見出し（英字ラベル＋日本語見出し）。
 */
function aoki_heading( $en, $ja, $tag = 'h2', $class = '' ) {
	printf(
		'<div class="heading %4$s"><p class="heading__en" aria-hidden="true">%1$s</p><%3$s class="heading__ja">%2$s</%3$s></div>',
		esc_html( $en ),
		esc_html( $ja ),
		tag_escape( $tag ),
		esc_attr( $class )
	);
}

/**
 * 下層ページのヘッダー（タイトル＋パンくず）。
 *
 * @param array $args en, title, lead, image, crumbs ( [ [label, url], ... ] ).
 */
function aoki_page_hero( $args ) {
	get_template_part( 'template-parts/page-hero', null, $args );
}

/**
 * パンくず（構造化データ付き）。
 */
function aoki_breadcrumbs( $crumbs ) {
	$items = array_merge( array( array( 'TOP', aoki_url() ) ), $crumbs );
	$ld    = array();
	echo '<nav class="breadcrumbs" aria-label="パンくずリスト"><ol>';
	foreach ( $items as $i => $c ) {
		$last = ( count( $items ) - 1 === $i );
		echo '<li>';
		if ( $last || empty( $c[1] ) ) {
			echo '<span aria-current="page">' . esc_html( $c[0] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a>';
		}
		echo '</li>';
		$ld[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $c[0],
			'item'     => $c[1] ? $c[1] : get_permalink(),
		);
	}
	echo '</ol></nav>';
	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $ld,
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	) . '</script>';
}

/**
 * 改行区切りテキストを段落/リストに。
 */
function aoki_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', explode( "\n", $text ) ), 'strlen' ) );
}

function aoki_tel_link( $tel ) {
	return 'tel:' . preg_replace( '/[^0-9]/', '', $tel );
}

/**
 * ページ下部の共通 CTA。
 *
 * @param string $mode 'both' | 'contact' | 'recruit'.
 */
function aoki_cta( $mode = 'both' ) {
	get_template_part( 'template-parts/cta', null, array( 'mode' => $mode ) );
}

/**
 * 「次に見るページ」導線。
 *
 * @param array $links [ [en, label, path, image], ... ].
 */
function aoki_next_links( $links ) {
	get_template_part( 'template-parts/next-links', null, array( 'links' => $links ) );
}

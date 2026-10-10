<?php
/**
 * メタディスクリプション・OGP・構造化データ。
 * SEO プラグインを導入した場合は、そちらに任せるため自動で出力を止めます。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aoki_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'SiteSEO' );
}

function aoki_meta_description() {
	$c     = aoki_company();
	$pages = array(
		'company'        => '青木工業株式会社の会社案内。企業理念「確かな技術で扉を開き、皆様に幸せな時間を」、代表挨拶、会社概要、アクセスをご紹介します。',
		'business'       => '青木工業の事業紹介。自動ドアの施工・保守・メンテナンスと、鋼製建具の取り付け・金物工事・カーテンウォール施工を行っています。',
		'automatic-door' => '商業施設や駅、コンビニなどの自動ドアや制御装置の設置、メンテナンス。緊急時は即日対応を心がけています。',
		'fittings'       => '自動ドア、窓、シャッターの土台や枠、金物、サッシなどの設計、製造、取り付け。回転扉など特殊な施工もお任せください。',
		'recruit'        => '青木工業の採用情報。未経験可・日給17,000円〜21,000円。資格取得支援や社用車支給など、安心して長く働ける環境です。',
		'staff'          => '青木工業で働くスタッフのインタビュー。入社のきっかけや仕事のやりがい、職場の雰囲気をご紹介します。',
		'contact'        => '青木工業株式会社へのお問い合わせ。自動ドア・建具の施工、保守・メンテナンスのご相談はお気軽にご連絡ください。TEL 048-623-5130',
		'entry'          => '青木工業株式会社の採用エントリーフォーム。自動ドア・建具スタッフを募集しています。',
	);
	if ( is_front_page() ) {
		return $c['description'];
	}
	if ( is_post_type_archive( 'works' ) || is_tax( 'works_cat' ) ) {
		return '青木工業の施工事例。商業施設やオフィスビルの自動ドア、回転扉・建具の取り付け実績をご紹介します。';
	}
	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $pages[ $slug ] ) ) {
			return $pages[ $slug ];
		}
		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			return wp_trim_words( $excerpt, 110, '…' );
		}
	}
	return $c['description'];
}

// canonical はテーマで出力するため、WordPress 標準の出力を止める（重複防止）。
add_action(
	'wp',
	function () {
		if ( ! aoki_seo_plugin_active() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

add_action(
	'wp_head',
	function () {
		if ( aoki_seo_plugin_active() || is_404() ) {
			return;
		}
		$c     = aoki_company();
		$desc  = aoki_meta_description();
		$title = wp_get_document_title();
		$url   = is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : ( is_post_type_archive( 'works' ) ? get_post_type_archive_link( 'works' ) : home_url( add_query_arg( array() ) ) ) );

		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_front_page() ? 'website' : 'article' ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $c['name'] ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( aoki_asset( 'ogp.jpg' ) ) . '">' . "\n";
		echo '<meta property="og:locale" content="ja_JP">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		if ( is_front_page() ) {
			$ld = array(
				'@context'  => 'https://schema.org',
				'@type'     => 'LocalBusiness',
				'name'      => $c['name'],
				'url'       => home_url( '/' ),
				'logo'      => aoki_asset( 'logo/logo-full.png' ),
				'image'     => aoki_asset( 'ogp.jpg' ),
				'telephone' => $c['tel'],
				'faxNumber' => $c['fax'],
				'address'   => array(
					'@type'           => 'PostalAddress',
					'postalCode'      => $c['zip'],
					'addressRegion'   => '埼玉県',
					'addressLocality' => 'さいたま市西区',
					'streetAddress'   => '大字佐知川302',
					'addressCountry'  => 'JP',
				),
				'sameAs'    => array( $c['instagram'] ),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		}
	},
	5
);

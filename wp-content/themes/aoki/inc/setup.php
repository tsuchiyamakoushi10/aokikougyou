<?php
/**
 * テーマの基本設定・アセット読み込み。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_image_size( 'aoki-card', 800, 534, true );
		add_image_size( 'aoki-wide', 1600, 1067, true );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'aoki-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Montserrat:wght@500;600;700&display=swap', array(), null );
		wp_enqueue_style( 'aoki-main', AOKI_URI . '/assets/css/main.css', array(), aoki_asset_ver( 'assets/css/main.css' ) );
		wp_enqueue_script( 'aoki-main', AOKI_URI . '/assets/js/main.js', array(), aoki_asset_ver( 'assets/js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );

		if ( is_front_page() ) {
			wp_enqueue_script( 'aoki-intro', AOKI_URI . '/assets/js/intro.js', array(), aoki_asset_ver( 'assets/js/intro.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		}
		if ( is_page( array( 'contact', 'entry' ) ) ) {
			wp_enqueue_script( 'aoki-form', AOKI_URI . '/assets/js/form.js', array(), aoki_asset_ver( 'assets/js/form.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		}

		// テーマでは使わないブロックエディタ用CSSを外す。
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
);

/**
 * ファイル更新時刻をバージョンにしてキャッシュを確実に更新する。
 */
function aoki_asset_ver( $rel ) {
	$path = AOKI_DIR . '/' . $rel;
	return file_exists( $path ) ? (string) filemtime( $path ) : AOKI_VERSION;
}

add_action(
	'wp_head',
	function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
		echo '<link rel="icon" href="' . esc_url( AOKI_URI . '/assets/img/logo/mark.svg' ) . '" type="image/svg+xml">' . "\n";
		echo '<meta name="theme-color" content="#0b5089">' . "\n";
		if ( is_front_page() ) {
			// 初回描画前に扉アニメーションの状態を決め、ちらつきを防ぐ。
			echo "<script>(function(d){var c=d.documentElement.classList;c.add('js');try{if(matchMedia('(prefers-reduced-motion: reduce)').matches||sessionStorage.getItem('aokiIntro')){c.add('intro-skip');}}catch(e){}})(document);</script>\n";
		} else {
			echo "<script>document.documentElement.classList.add('js');</script>\n";
		}
	},
	1
);

// Google アナリティクス（旧サイトと同じ測定ID）。ログイン中の管理者は計測しない。
add_action(
	'wp_head',
	function () {
		$id = aoki_company()['ga_id'];
		if ( ! $id || is_user_logged_in() ) {
			return;
		}
		printf(
			"<script async src=\"https://www.googletagmanager.com/gtag/js?id=%1\$s\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','%1\$s');</script>\n",
			esc_attr( $id )
		);
	},
	20
);

// 不要な head 出力を整理。
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// サイトは日本語のみ。管理画面の言語設定に関わらず lang="ja" を出力する。
add_filter(
	'language_attributes',
	function () {
		return 'lang="ja"';
	}
);

add_filter(
	'document_title_separator',
	function () {
		return '|';
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_page() ) {
			$classes[] = 'page-' . get_post_field( 'post_name', get_queried_object_id() );
		}
		return $classes;
	}
);

/**
 * 本番以外（ローカル・ステージング）は検索エンジンに載せない。
 */
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( 'production' !== wp_get_environment_type() ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		$robots['max-image-preview'] = 'large';
		return $robots;
	}
);

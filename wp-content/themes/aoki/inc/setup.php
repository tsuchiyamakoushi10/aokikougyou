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
 * Google Fonts の URL。
 */
function aoki_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Montserrat:wght@500;600;700&display=swap';
}

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
		// フォントは画面の表示を止めないように後から読み込む（読み込むまでは端末の標準フォントで表示）。
		echo '<link rel="stylesheet" id="aoki-fonts" href="' . esc_url( aoki_fonts_url() ) . '" media="print">' . "\n";
		echo "<script>(function(l){function on(){l.media='all'}if(l.sheet){on()}else{l.addEventListener('load',on)}})(document.getElementById('aoki-fonts'));</script>\n";
		echo '<noscript><link rel="stylesheet" href="' . esc_url( aoki_fonts_url() ) . '"></noscript>' . "\n";
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
// 計測用の大きなスクリプトは、ページの表示が終わってから読み込む（表示速度のため）。
// それまでの計測内容は dataLayer にためておき、読み込み後にまとめて送られる。
add_action(
	'wp_head',
	function () {
		$id = aoki_company()['ga_id'];
		if ( ! $id || is_user_logged_in() ) {
			return;
		}
		printf(
			"<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','%1\$s');addEventListener('load',function(){setTimeout(function(){var s=document.createElement('script');s.async=true;s.src='https://www.googletagmanager.com/gtag/js?id=%1\$s';document.head.appendChild(s)},0)});</script>\n",
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

// body のクラスは、ページを見分ける1つだけにする（WordPress 標準のクラスは CSS で使っていない）。
add_filter(
	'body_class',
	function () {
		if ( is_front_page() ) {
			return array( 'page-home' );
		}
		if ( is_page() ) {
			return array( 'page-' . get_post_field( 'post_name', get_queried_object_id() ) );
		}
		if ( is_post_type_archive( 'works' ) || is_tax( 'works_cat' ) ) {
			return array( 'page-works' );
		}
		if ( is_singular( 'works' ) ) {
			return array( 'page-work' );
		}
		if ( is_404() ) {
			return array( 'page-404' );
		}
		return array();
	}
);

// WordPress が自動で出す、このサイトでは不要な出力を止める。
add_filter( 'wp_speculation_rules_configuration', '__return_null' ); // 先読みルール（wp-admin などのパスが書かれる）
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );         // 画像の sizes="auto" と、そのための style
remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );

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

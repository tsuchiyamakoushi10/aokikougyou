<?php
/**
 * 初期コンテンツの自動作成。
 * テーマを有効化すると、固定ページ・施工事例・パーマリンク設定を一度だけ作成します。
 * 既に同じスラッグのページがある場合は作成しません（上書きはしません）。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'aoki_seed_content' );

function aoki_seed_pages() {
	return array(
		array( 'slug' => 'home', 'title' => 'トップページ' ),
		array( 'slug' => 'company', 'title' => '会社案内' ),
		array( 'slug' => 'business', 'title' => '事業紹介' ),
		array( 'slug' => 'automatic-door', 'title' => '自動ドア事業', 'parent' => 'business' ),
		array( 'slug' => 'fittings', 'title' => '建具事業', 'parent' => 'business' ),
		array( 'slug' => 'recruit', 'title' => '採用情報' ),
		array( 'slug' => 'staff', 'title' => 'スタッフ紹介' ),
		array( 'slug' => 'contact', 'title' => 'お問い合わせ' ),
		array( 'slug' => 'entry', 'title' => '採用エントリー' ),
	);
}

function aoki_seed_content() {
	if ( get_option( 'aoki_seeded' ) ) {
		return;
	}

	// 固定ページ（本文はテーマのテンプレートで表示）。
	$ids = array();
	foreach ( aoki_seed_pages() as $i => $p ) {
		$path     = isset( $p['parent'] ) ? $p['parent'] . '/' . $p['slug'] : $p['slug'];
		$existing = get_page_by_path( $path );
		if ( $existing ) {
			$ids[ $p['slug'] ] = $existing->ID;
			continue;
		}
		$ids[ $p['slug'] ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'post_parent'  => isset( $p['parent'] ) ? $ids[ $p['parent'] ] : 0,
				'menu_order'   => $i,
				'post_content' => '',
			)
		);
	}

	// WordPress 初期状態のサンプル投稿・ページは不要なので削除。
	foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
		$sample = get_page_by_path( $slug, OBJECT, $type );
		if ( $sample ) {
			wp_delete_post( $sample->ID, true );
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	// 施工事例。
	foreach ( array(
		'automatic-door' => '自動ドア事業',
		'fittings'       => '建具事業',
	) as $slug => $name ) {
		if ( ! term_exists( $slug, 'works_cat' ) ) {
			wp_insert_term( $name, 'works_cat', array( 'slug' => $slug ) );
		}
	}

	$has_works = get_posts(
		array(
			'post_type'      => 'works',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! $has_works ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		foreach ( aoki_seed_works() as $i => $w ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'works',
					'post_status'  => 'publish',
					'post_title'   => $w['title'],
					'post_content' => $w['summary'],
					'menu_order'   => $i,
				)
			);
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}
			update_post_meta( $post_id, '_aoki_place', $w['place'] );
			update_post_meta( $post_id, '_aoki_period', $w['period'] );
			wp_set_object_terms( $post_id, $w['cat'], 'works_cat' );

			if ( $w['image'] ) {
				$src = AOKI_DIR . '/assets/img/works/' . $w['image'] . '.webp';
				if ( file_exists( $src ) ) {
					$tmp = wp_tempnam( $w['image'] . '.webp' );
					copy( $src, $tmp );
					$att = media_handle_sideload(
						array(
							'name'     => $w['image'] . '.webp',
							'tmp_name' => $tmp,
						),
						$post_id,
						$w['title']
					);
					if ( ! is_wp_error( $att ) ) {
						set_post_thumbnail( $post_id, $att );
					} else {
						wp_delete_file( $tmp );
					}
				}
			}
		}
	}

	update_option( 'aoki_seeded', AOKI_VERSION );
	flush_rewrite_rules();
}

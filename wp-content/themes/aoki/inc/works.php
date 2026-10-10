<?php
/**
 * 施工事例（カスタム投稿タイプ works）。
 * 管理画面「施工事例」から追加・編集できます。
 *   - タイトル      : 事例名
 *   - 本文          : 概要説明
 *   - アイキャッチ   : 写真
 *   - 施工情報      : 場所・工事期間
 *   - 事業区分      : 自動ドア事業 / 建具事業
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		// 事業区分を先に登録し、works/category/... が個別事例の URL より優先されるようにする。
		register_taxonomy(
			'works_cat',
			'works',
			array(
				'labels'            => array(
					'name'          => '事業区分',
					'singular_name' => '事業区分',
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'works/category', 'with_front' => false ),
			)
		);
		register_post_type(
			'works',
			array(
				'labels'        => array(
					'name'          => '施工事例',
					'singular_name' => '施工事例',
					'add_new'       => '新規追加',
					'add_new_item'  => '施工事例を追加',
					'edit_item'     => '施工事例を編集',
					'all_items'     => '施工事例一覧',
				),
				'public'        => true,
				'has_archive'   => 'works',
				'rewrite'       => array( 'slug' => 'works', 'with_front' => false ),
				'menu_icon'     => 'dashicons-building',
				'menu_position' => 5,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'show_in_rest'  => true,
			)
		);
	}
);

// 並び順は「順序」→ 公開日の新しい順。
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_post_type_archive( 'works' ) || $q->is_tax( 'works_cat' ) ) {
			$q->set( 'posts_per_page', 24 );
			$q->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				)
			);
		}
	}
);

// 事例は一覧ページで完結させるため、個別ページは一覧の該当位置へ。
add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'works' ) ) {
			wp_safe_redirect( get_post_type_archive_link( 'works' ) . '#works-' . get_queried_object_id(), 301 );
			exit;
		}
	}
);

/* ---------- 施工情報メタボックス ---------- */

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'aoki_works_meta', '施工情報', 'aoki_works_meta_box', 'works', 'side', 'high' );
	}
);

function aoki_works_meta_box( $post ) {
	wp_nonce_field( 'aoki_works_meta', 'aoki_works_meta_nonce' );
	$place  = get_post_meta( $post->ID, '_aoki_place', true );
	$period = get_post_meta( $post->ID, '_aoki_period', true );
	?>
	<p><label for="aoki_place"><strong>場所</strong></label><br>
	<input type="text" id="aoki_place" name="aoki_place" value="<?php echo esc_attr( $place ); ?>" class="widefat" placeholder="例：埼玉県"></p>
	<p><label for="aoki_period"><strong>工事期間</strong></label><br>
	<input type="text" id="aoki_period" name="aoki_period" value="<?php echo esc_attr( $period ); ?>" class="widefat" placeholder="例：半年"></p>
	<p class="description">本文欄に「概要説明」を入力してください。</p>
	<?php
}

add_action(
	'save_post_works',
	function ( $post_id ) {
		if ( ! isset( $_POST['aoki_works_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['aoki_works_meta_nonce'] ), 'aoki_works_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array( 'place', 'period' ) as $key ) {
			if ( isset( $_POST[ 'aoki_' . $key ] ) ) {
				update_post_meta( $post_id, '_aoki_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'aoki_' . $key ] ) ) );
			}
		}
	}
);

/**
 * 事例カード 1 件分のデータ。
 */
function aoki_work_data( $post = null ) {
	$post  = get_post( $post );
	$terms = get_the_terms( $post, 'works_cat' );
	return array(
		'id'      => $post->ID,
		'title'   => get_the_title( $post ),
		'place'   => get_post_meta( $post->ID, '_aoki_place', true ),
		'period'  => get_post_meta( $post->ID, '_aoki_period', true ),
		'summary' => wp_strip_all_tags( $post->post_content ),
		'cat'     => ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null,
		'thumb'   => get_post_thumbnail_id( $post ),
	);
}

/**
 * 事例を取得（TOP・事業ページ用）。
 */
function aoki_get_works( $count = 3, $cat = '' ) {
	$args = array(
		'post_type'      => 'works',
		'posts_per_page' => $count,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'  => true,
	);
	if ( $cat ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'works_cat',
				'field'    => 'slug',
				'terms'    => $cat,
			),
		);
	}
	return get_posts( $args );
}

/**
 * 事業区分の一覧 URL（区分が未作成なら施工事例一覧）。
 */
function aoki_works_cat_url( $slug ) {
	$link = get_term_link( $slug, 'works_cat' );
	return is_wp_error( $link ) ? get_post_type_archive_link( 'works' ) : $link;
}

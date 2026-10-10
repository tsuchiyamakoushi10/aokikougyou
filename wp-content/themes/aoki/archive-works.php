<?php
/**
 * 施工事例一覧（事業区分での絞り込みも兼用）。
 */

get_header();
$term   = is_tax( 'works_cat' ) ? get_queried_object() : null;
$crumbs = array( array( '施工事例', $term ? get_post_type_archive_link( 'works' ) : '' ) );
if ( $term ) {
	$crumbs[] = array( $term->name, '' );
}
aoki_page_hero(
	array(
		'en'     => 'Works',
		'title'  => $term ? $term->name . 'の施工事例' : '施工事例',
		'lead'   => '商業施設やオフィスビル、店舗など。これまでに手がけた施工の一部をご紹介します。',
		'image'  => 'works/c-tora.webp',
		'crumbs' => $crumbs,
	)
);
$terms = get_terms(
	array(
		'taxonomy'   => 'works_cat',
		'hide_empty' => true,
		'orderby'    => 'term_id',
	)
);
?>

<section class="section section--first">
	<div class="container">
		<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
			<nav class="filter" aria-label="事業区分で絞り込み">
				<ul>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>"<?php echo $term ? '' : ' aria-current="page"'; ?>>すべて</a></li>
					<?php foreach ( $terms as $t ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"<?php echo ( $term && $term->term_id === $t->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $t->name ); ?><span class="filter__count"><?php echo (int) $t->count; ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="works-grid works-grid--archive">
				<?php
				while ( have_posts() ) :
					the_post();
					echo '<div class="reveal">';
					get_template_part( 'template-parts/work-card', null, array( 'post' => get_post(), 'heading' => 'h2', 'detail' => true ) );
					echo '</div>';
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => '前へ',
					'next_text'          => '次へ',
					'screen_reader_text' => 'ページ送り',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state">
				<p>現在、掲載している施工事例はありません。</p>
				<a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>">すべての施工事例を見る</a>
			</div>
		<?php endif; ?>

		<?php if ( $term ) : ?>
			<p class="center-btn"><a class="btn btn--outline" href="<?php echo esc_url( aoki_url( 'business/' . $term->slug ) ); ?>"><?php echo esc_html( $term->name ); ?>について詳しく見る<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a></p>
		<?php endif; ?>
	</div>
</section>

<?php
aoki_next_links(
	array(
		array( 'Automatic Door', '自動ドア事業', 'business/automatic-door', 'photo/work-autodoor-1.webp' ),
		array( 'Steel Fittings', '建具事業', 'business/fittings', 'photo/work-door-2.webp' ),
		array( 'Company', '会社案内', 'company', 'photo/office-sign.webp' ),
	)
);
aoki_cta( 'contact' );
get_footer();

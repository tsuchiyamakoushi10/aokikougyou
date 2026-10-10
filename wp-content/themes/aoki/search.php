<?php
/**
 * 検索結果。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Search',
		'title'  => '「' . get_search_query() . '」の検索結果',
		'crumbs' => array( array( '検索結果', '' ) ),
	)
);
?>
<section class="section section--first">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<?php get_search_form(); ?>
			<ul class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$link = 'works' === get_post_type() ? get_post_type_archive_link( 'works' ) . '#works-' . get_the_ID() : get_permalink();
					?>
					<li><a href="<?php echo esc_url( $link ); ?>"><span class="post-list__type"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span><span><?php the_title(); ?></span></a></li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination( array( 'prev_text' => '前へ', 'next_text' => '次へ' ) ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/not-found' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

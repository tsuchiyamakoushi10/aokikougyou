<?php
/**
 * フォールバック（投稿一覧など）。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Archive',
		'title'  => is_search() ? '検索結果' : wp_strip_all_tags( get_the_archive_title() ? get_the_archive_title() : 'お知らせ' ),
		'crumbs' => array( array( is_search() ? '検索結果' : '一覧', '' ) ),
	)
);
?>
<section class="section section--first">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<ul class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li><a href="<?php the_permalink(); ?>"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time><span><?php the_title(); ?></span></a></li>
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

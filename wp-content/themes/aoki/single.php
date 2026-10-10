<?php
/**
 * 投稿（お知らせ等）の詳細。
 */

get_header();
while ( have_posts() ) :
	the_post();
	aoki_page_hero(
		array(
			'en'     => 'News',
			'title'  => get_the_title(),
			'crumbs' => array( array( get_the_title(), '' ) ),
		)
	);
	?>
	<article class="section section--first">
		<div class="container container--narrow prose prose--entry">
			<p class="post-date"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y年n月j日' ) ); ?></time></p>
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;
aoki_cta();
get_footer();

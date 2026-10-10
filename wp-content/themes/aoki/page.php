<?php
/**
 * 汎用固定ページ（管理画面で追加したページ用）。
 */

get_header();
while ( have_posts() ) :
	the_post();
	$crumbs = array();
	foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
		$crumbs[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
	}
	$crumbs[] = array( get_the_title(), '' );
	aoki_page_hero(
		array(
			'en'     => strtoupper( get_post_field( 'post_name' ) ),
			'title'  => get_the_title(),
			'crumbs' => $crumbs,
		)
	);
	?>
	<section class="section section--first">
		<div class="container container--narrow prose prose--entry">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;
aoki_cta();
get_footer();

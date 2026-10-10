<?php
/**
 * 404 ページが見つかりません。
 */

status_header( 404 );
nocache_headers();
get_header();
?>
<section class="error-page">
	<div class="container error-page__inner">
		<div class="error-visual" aria-hidden="true">
			<?php echo aoki_mark_svg( 'error-visual__mark' ); // phpcs:ignore ?>
			<p class="error-visual__code">404</p>
		</div>
		<div class="error-page__body">
			<p class="error-page__code">404 Not Found</p>
			<h1 class="error-page__title">お探しのページは<br class="sp-only">見つかりませんでした</h1>
			<p>ページが移動または削除されたか、URLが間違っている可能性があります。<br class="pc-only">サイトリニューアルに伴い、ページのURLが変更になっている場合があります。</p>
			<?php get_template_part( 'template-parts/not-found', null, array( 'search' => false ) ); ?>
		</div>
	</div>
</section>
<?php
get_footer();

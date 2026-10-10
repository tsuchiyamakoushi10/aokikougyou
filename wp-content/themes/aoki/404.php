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
		<div class="error-door" aria-hidden="true">
			<div class="error-door__frame">
				<div class="error-door__leaf error-door__leaf--l"><span>4</span></div>
				<div class="error-door__leaf error-door__leaf--r"><span>4</span></div>
				<div class="error-door__gap"><span>0</span></div>
			</div>
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

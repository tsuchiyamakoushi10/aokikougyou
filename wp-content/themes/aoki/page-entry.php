<?php
/**
 * 採用エントリー。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Entry',
		'title'  => '採用エントリー',
		'crumbs' => array( array( '採用情報', aoki_url( 'recruit' ) ), array( '採用エントリー', '' ) ),
	)
);
?>

<section class="section section--first">
	<div class="container container--narrow">
		<?php if ( ! isset( $_GET['sent'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<p class="form-intro">「まずは話を聞きたい」「職場見学をしたい」だけでも大丈夫です。<br class="pc-only">履歴書は面接の日にお持ちください。募集内容は<a href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>#jobs">募集要項</a>をご確認ください。</p>
		<?php endif; ?>
		<?php aoki_render_form( 'entry' ); ?>
	</div>
</section>

<?php
get_footer();

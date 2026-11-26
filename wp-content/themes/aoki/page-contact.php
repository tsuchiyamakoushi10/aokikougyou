<?php
/**
 * お問い合わせ。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Contact',
		'title'  => 'お問い合わせ',
		'crumbs' => array( array( 'お問い合わせ', '' ) ),
	)
);
?>

<section class="section section--first">
	<div class="container container--narrow">
		<?php if ( ! isset( $_GET['sent'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<p class="form-intro">自動ドアの不具合・点検、建具・サッシ工事のご相談はこちらから。</p>
			<p class="form-intro form-intro--note">営業のご連絡はお断りしています。</p>
		<?php endif; ?>
		<?php aoki_render_form( 'contact' ); ?>
	</div>
</section>

<?php
get_footer();

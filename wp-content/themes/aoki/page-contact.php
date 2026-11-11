<?php
/**
 * お問い合わせ。
 */

get_header();
$c = aoki_company();
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
			<div class="contact-tel">
				<div class="contact-tel__body">
					<p class="contact-tel__title">お電話でのお問い合わせ</p>
					<a class="contact-tel__num" href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo aoki_icon( 'tel' ); // phpcs:ignore ?><?php echo esc_html( $c['tel'] ); ?></a>
					<p class="contact-tel__note">FAX：<?php echo esc_html( $c['fax'] ); ?>　／　24時間サポート体制を整えています。</p>
				</div>
			</div>
			<p class="form-intro">自動ドアの不具合・点検、建具・サッシ工事のご相談はこちらから。<br class="pc-only">お急ぎの場合はお電話（<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo esc_html( $c['tel'] ); ?></a>）でどうぞ。</p>
			<p class="form-intro form-intro--note">営業のご連絡はお断りしています。</p>
		<?php endif; ?>
		<?php aoki_render_form( 'contact' ); ?>
	</div>
</section>

<?php
get_footer();

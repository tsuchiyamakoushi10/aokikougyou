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
			<p class="form-intro">フォームからのお問い合わせは、下記に必要事項をご入力のうえ送信してください。<br class="pc-only">内容を確認のうえ、担当者よりご連絡いたします。</p>
		<?php endif; ?>
		<?php aoki_render_form( 'contact' ); ?>
	</div>
</section>

<?php
get_footer();

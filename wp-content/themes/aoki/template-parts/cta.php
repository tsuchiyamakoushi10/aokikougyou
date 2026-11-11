<?php
/**
 * ページ下部の CTA（お問い合わせ／採用）。
 *
 * @var array $args mode.
 */

$c    = aoki_company();
$mode = $args['mode'] ?? 'both';
?>
<section class="cta" aria-label="お問い合わせ・採用エントリー">
	<div class="cta__inner container">
		<?php if ( 'recruit' !== $mode ) : ?>
			<div class="cta__panel cta__panel--contact reveal">
				<p class="cta__en" aria-hidden="true">Contact</p>
				<h2 class="cta__title">自動ドア・建具のことなら、<br class="sp-only">お気軽にご相談ください</h2>
				<p class="cta__text">施工・保守・メンテナンス、緊急時の修理まで。24時間サポート体制でお応えします。</p>
				<div class="cta__actions">
					<a class="cta__tel" href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>">
						<?php echo aoki_icon( 'tel' ); // phpcs:ignore ?>
						<span><span class="cta__tel-num"><?php echo esc_html( $c['tel'] ); ?></span><span class="cta__tel-note">FAX <?php echo esc_html( $c['fax'] ); ?></span></span>
					</a>
					<a class="btn btn--white" href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>"><?php echo aoki_icon( 'mail' ); // phpcs:ignore ?>お問い合わせフォーム<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
				</div>
			</div>
		<?php endif; ?>
		<?php if ( 'contact' !== $mode ) : ?>
			<div class="cta__panel cta__panel--recruit reveal">
				<p class="cta__en" aria-hidden="true">Recruit</p>
				<h2 class="cta__title">未経験から、一人で<br>現場を任される職人に。</h2>
				<p class="cta__text">未経験でも2ヶ月の研修を経て独立可能。資格取得も会社がサポートします。</p>
				<div class="cta__actions">
					<a class="btn btn--white" href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>">詳しくはこちら<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="container">
		<a class="insta-band reveal" href="<?php echo esc_url( $c['instagram'] ); ?>" target="_blank" rel="noopener">
			<span class="insta-band__icon"><?php echo aoki_icon( 'insta' ); // phpcs:ignore ?></span>
			<span class="insta-band__body">
				<span class="insta-band__en">Instagram</span>
				<span class="insta-band__title">現場の様子や日々の仕事を発信中</span>
				<span class="insta-band__id">@<?php echo esc_html( basename( untrailingslashit( $c['instagram'] ) ) ); ?></span>
			</span>
			<span class="insta-band__btn">フォローする<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></span>
		</a>
	</div>
</section>

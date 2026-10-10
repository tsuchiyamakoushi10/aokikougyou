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
				<h2 class="cta__title">あなたのペースで成長を<br>安心の環境で一緒に</h2>
				<p class="cta__text">未経験でも2ヶ月の研修を経て独立可能。資格取得も会社がサポートします。</p>
				<div class="cta__actions">
					<a class="btn btn--white" href="<?php echo esc_url( aoki_url( 'entry' ) ); ?>"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?>採用エントリー<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
					<a class="btn btn--ghost-white" href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>">募集要項を見る</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

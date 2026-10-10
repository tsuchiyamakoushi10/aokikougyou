<?php
/**
 * 共通フッター。
 */

$c = aoki_company();
?>
</main>

<footer class="site-footer">
	<div class="site-footer__inner container">
		<div class="site-footer__brand">
			<a class="site-footer__logo" href="<?php echo esc_url( aoki_url() ); ?>" aria-label="青木工業株式会社 トップページ"><?php echo aoki_logo( 'light' ); // phpcs:ignore ?></a>
			<p class="site-footer__philosophy"><?php echo esc_html( $c['philosophy'] ); ?></p>
			<address class="site-footer__address">
				<span class="site-footer__office">本社</span>
				〒<?php echo esc_html( $c['zip'] ); ?> <?php echo esc_html( $c['address'] ); ?><br>
				TEL <a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo esc_html( $c['tel'] ); ?></a>　FAX <?php echo esc_html( $c['fax'] ); ?>
			</address>
			<address class="site-footer__address">
				<span class="site-footer__office"><?php echo esc_html( $c['branch']['name'] ); ?></span>
				〒<?php echo esc_html( $c['branch']['zip'] ); ?> <?php echo esc_html( $c['branch']['address'] ); ?><br>
				TEL <a href="<?php echo esc_attr( aoki_tel_link( $c['branch']['tel'] ) ); ?>"><?php echo esc_html( $c['branch']['tel'] ); ?></a>　FAX <?php echo esc_html( $c['branch']['fax'] ); ?>
			</address>
		</div>

		<nav class="site-footer__nav" aria-label="フッターメニュー">
			<ul>
				<li><a href="<?php echo esc_url( aoki_url() ); ?>">トップ</a></li>
				<li><a href="<?php echo esc_url( aoki_url( 'company' ) ); ?>">会社案内</a>
					<ul>
						<li><a href="<?php echo esc_url( aoki_url( 'company' ) ); ?>#philosophy">企業理念</a></li>
						<li><a href="<?php echo esc_url( aoki_url( 'company' ) ); ?>#message">代表挨拶</a></li>
						<li><a href="<?php echo esc_url( aoki_url( 'company' ) ); ?>#profile">会社概要・アクセス</a></li>
					</ul>
				</li>
			</ul>
			<ul>
				<li><a href="<?php echo esc_url( aoki_url( 'business' ) ); ?>">事業紹介</a>
					<ul>
						<li><a href="<?php echo esc_url( aoki_url( 'business/automatic-door' ) ); ?>">自動ドア事業</a></li>
						<li><a href="<?php echo esc_url( aoki_url( 'business/fittings' ) ); ?>">建具事業</a></li>
					</ul>
				</li>
				<li><a href="<?php echo esc_url( aoki_url( 'works' ) ); ?>">施工事例</a></li>
			</ul>
			<ul>
				<li><a href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>">採用情報</a>
					<ul>
						<li><a href="<?php echo esc_url( aoki_url( 'staff' ) ); ?>">スタッフ紹介</a></li>
						<li><a href="<?php echo esc_url( aoki_url( 'entry' ) ); ?>">採用エントリー</a></li>
					</ul>
				</li>
				<li><a href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>">お問い合わせ</a></li>
			</ul>
		</nav>
	</div>

	<div class="site-footer__bottom container">
		<a class="site-footer__sns" href="<?php echo esc_url( $c['instagram'] ); ?>" target="_blank" rel="noopener"><?php echo aoki_icon( 'insta' ); // phpcs:ignore ?><span>Instagram</span></a>
		<p class="site-footer__copy"><small>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $c['name_en'] ); ?></small></p>
	</div>
</footer>

<nav class="mobile-bar" aria-label="お問い合わせ">
	<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo aoki_icon( 'tel' ); // phpcs:ignore ?><span>電話</span></a>
	<a href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>"><?php echo aoki_icon( 'mail' ); // phpcs:ignore ?><span>お問い合わせ</span></a>
	<a href="<?php echo esc_url( aoki_url( 'entry' ) ); ?>" class="mobile-bar__entry"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?><span>採用エントリー</span></a>
</nav>

<?php wp_footer(); ?>
</body>
</html>

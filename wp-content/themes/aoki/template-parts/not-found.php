<?php
/**
 * 該当なし（404・検索結果なし）の共通導線。
 *
 * @var array $args search (bool) 検索フォームを表示するか.
 */

$c = aoki_company();
?>
<div class="not-found">
	<?php if ( ! isset( $args['search'] ) || $args['search'] ) : ?>
		<p>該当するページが見つかりませんでした。別のキーワードでお試しください。</p>
	<?php endif; ?>
	<?php get_search_form(); ?>
	<p class="not-found__label">よく見られているページ</p>
	<ul class="not-found__links">
		<li><a href="<?php echo esc_url( aoki_url() ); ?>"><?php echo aoki_icon( 'home' ); // phpcs:ignore ?>トップページ</a></li>
		<li><a href="<?php echo esc_url( aoki_url( 'business/automatic-door' ) ); ?>"><?php echo aoki_icon( 'door' ); // phpcs:ignore ?>自動ドア事業</a></li>
		<li><a href="<?php echo esc_url( aoki_url( 'business/fittings' ) ); ?>"><?php echo aoki_icon( 'frame' ); // phpcs:ignore ?>建具事業</a></li>
		<li><a href="<?php echo esc_url( aoki_url( 'works' ) ); ?>"><?php echo aoki_icon( 'install' ); // phpcs:ignore ?>施工事例</a></li>
		<li><a href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?>採用情報</a></li>
		<li><a href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>"><?php echo aoki_icon( 'mail' ); // phpcs:ignore ?>お問い合わせ</a></li>
	</ul>
	<p class="not-found__tel">お急ぎの方はお電話ください　<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo aoki_icon( 'tel' ); // phpcs:ignore ?><?php echo esc_html( $c['tel'] ); ?></a></p>
</div>

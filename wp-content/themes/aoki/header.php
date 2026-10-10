<?php
/**
 * 共通ヘッダー。
 */

$c = aoki_company();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">本文へスキップ</a>

<header class="site-header<?php echo is_front_page() ? ' site-header--overlay' : ''; ?>" id="site-header">
	<div class="site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( aoki_url() ); ?>" aria-label="青木工業株式会社 トップページ">
			<?php echo aoki_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>

		<nav class="gnav" aria-label="メインメニュー">
			<ul class="gnav__list">
				<?php foreach ( aoki_nav() as $item ) : ?>
					<?php
					if ( 'staff' === $item['path'] ) {
						continue; // 採用情報の下に表示。
					}
					$has_children = ! empty( $item['children'] );
					$current      = aoki_is_current( $item['path'] ) || ( 'recruit' === $item['path'] && ( aoki_is_current( 'staff' ) || aoki_is_current( 'entry' ) ) );
					?>
					<li class="gnav__item<?php echo $has_children ? ' has-children' : ''; ?><?php echo $current ? ' is-current' : ''; ?>">
						<a class="gnav__link" href="<?php echo esc_url( aoki_url( $item['path'] ) ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>>
							<span class="gnav__ja"><?php echo esc_html( $item['label'] ); ?></span>
						</a>
						<?php if ( $has_children ) : ?>
							<ul class="gnav__sub">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo esc_url( aoki_url( $child['path'] ) ); ?>"><?php echo esc_html( $child['label'] ); ?><?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="site-header__actions">
			<a class="header-tel" href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>">
				<?php echo aoki_icon( 'tel' ); // phpcs:ignore ?>
				<span class="header-tel__body"><span class="header-tel__num"><?php echo esc_html( $c['tel'] ); ?></span><span class="header-tel__note">24時間サポート体制</span></span>
			</a>
			<a class="btn btn--primary btn--sm header-contact" href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>"><?php echo aoki_icon( 'mail' ); // phpcs:ignore ?>お問い合わせ</a>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="drawer" aria-label="メニューを開く">
				<span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span>
				<span class="menu-toggle__label">MENU</span>
			</button>
		</div>
	</div>
</header>

<div class="drawer" id="drawer" hidden>
	<div class="drawer__inner">
		<nav aria-label="メニュー">
			<ul class="drawer__nav">
				<li><a href="<?php echo esc_url( aoki_url() ); ?>"><span class="drawer__en">Top</span>トップ</a></li>
				<?php foreach ( aoki_nav() as $item ) : ?>
					<li>
						<a href="<?php echo esc_url( aoki_url( $item['path'] ) ); ?>"><span class="drawer__en"><?php echo esc_html( $item['en'] ); ?></span><?php echo esc_html( $item['label'] ); ?></a>
						<?php if ( ! empty( $item['children'] ) && 'business' === $item['path'] ) : ?>
							<ul class="drawer__sub">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo esc_url( aoki_url( $child['path'] ) ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<div class="drawer__cta">
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( aoki_url( 'contact' ) ); ?>"><?php echo aoki_icon( 'mail' ); // phpcs:ignore ?>お問い合わせ</a>
			<a class="btn btn--outline btn--block" href="<?php echo esc_url( aoki_url( 'entry' ) ); ?>"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?>採用エントリー</a>
			<a class="drawer__tel" href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo aoki_icon( 'tel' ); // phpcs:ignore ?><?php echo esc_html( $c['tel'] ); ?></a>
		</div>
	</div>
</div>

<main id="main" class="site-main">

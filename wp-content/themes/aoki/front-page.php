<?php
/**
 * トップページ。
 */

get_header();
$c        = aoki_company();
$business = aoki_business();
$staff    = array_slice( aoki_staff(), 0, 3 );
$works    = aoki_get_works( 3 );
?>

<section class="hero" id="hero" aria-label="メインビジュアル">
	<div class="hero__scene" aria-hidden="true">
		<picture class="hero__photo">
			<source media="(max-width: 767px)" srcset="<?php echo esc_url( aoki_asset( 'photo/hero-team-sp.webp' ) ); ?>">
			<img src="<?php echo esc_url( aoki_asset( 'photo/hero-team.webp' ) ); ?>" alt="" width="1920" height="1440" fetchpriority="high">
		</picture>
		<div class="hero__light"></div>
		<div class="hero__shade"></div>
	</div>

	<div class="hero__copy container">
		<p class="hero__label">自動ドア・建具の青木工業</p>
		<h1 class="hero__title">
			<span class="hero__line"><span>確かな技術で</span><span>扉を開き、</span></span>
			<span class="hero__line hero__line--accent"><span>皆様に</span><span>幸せな時間を</span></span>
		</h1>
		<p class="hero__sub">自動ドアの施工・保守・メンテナンス<span aria-hidden="true"> ／ </span><br class="sp-only">鋼製建具・金物工事・カーテンウォール施工</p>
	</div>

	<a class="hero__scroll" href="#about"><span>Scroll</span></a>

	<div class="intro" id="intro" aria-hidden="true">
		<div class="intro__door intro__door--l"></div>
		<div class="intro__door intro__door--r"></div>
		<div class="intro__seam"></div>
	</div>
	<button class="intro__skip" type="button" id="intro-skip">SKIP</button>
</section>

<section class="about section" id="about">
	<div class="container about__grid">
		<div class="about__body">
			<?php aoki_heading( 'About us', '青木工業とは' ); ?>
			<p class="about__catch reveal">確かな技術で扉を開き、<br>皆様に幸せな時間を</p>
			<p class="reveal">私たちの使命は、扉や窓の空間づくりを通して、お客様に安心と満足を提供することです。確かな技術と実績を基に、高品質なサービスを提供し続けます。私たちは、正確で迅速かつ丁寧な施工を心がけ、24時間サポート体制を整えています。これにより、お客様に常に安心して頂けることを日々心がけています。</p>
			<p class="reveal">そして、従業員が安心して長く働ける職場環境を作ることが私たちのもう一つの使命です。アットホームな企業風土を重視し、家族経営から発展した企業文化を大切にしています。</p>
			<ul class="about__badges reveal">
				<li><?php echo aoki_icon( 'clock' ); // phpcs:ignore ?>24時間サポート体制</li>
				<li><?php echo aoki_icon( 'license' ); // phpcs:ignore ?>自動ドア施工 1級・2級</li>
				<li><?php echo aoki_icon( 'frame' ); // phpcs:ignore ?>カーテンウォール 1級</li>
			</ul>
			<a class="btn btn--outline reveal" href="<?php echo esc_url( aoki_url( 'company' ) ); ?>">会社案内を見る<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
		</div>
		<div class="about__media">
			<figure class="about__photo about__photo--main door-reveal"><?php echo aoki_img( 'photo/work-door-1.webp', '自動ドアを施工するスタッフ' ); // phpcs:ignore ?></figure>
			<figure class="about__photo about__photo--sub door-reveal"><?php echo aoki_img( 'photo/meeting-2.webp', '打ち合わせをするスタッフ' ); // phpcs:ignore ?></figure>
		</div>
	</div>
</section>

<section class="biz section section--soft" id="business">
	<div class="container">
		<div class="section__head">
			<?php aoki_heading( 'Business', '事業紹介' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( aoki_url( 'business' ) ); ?>">事業紹介トップへ<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="biz__list">
			<?php $n = 0; foreach ( $business as $slug => $b ) : $n++; ?>
				<a class="biz-card reveal" href="<?php echo esc_url( aoki_url( 'business/' . $slug ) ); ?>">
					<figure class="biz-card__media door-reveal"><?php echo aoki_img( $b['image'], '' ); // phpcs:ignore ?></figure>
					<div class="biz-card__body">
						<p class="biz-card__num" aria-hidden="true">0<?php echo (int) $n; ?></p>
						<p class="biz-card__en" aria-hidden="true"><?php echo esc_html( $b['en'] ); ?></p>
						<h3 class="biz-card__title"><?php echo esc_html( $b['title'] ); ?></h3>
						<p class="biz-card__lead"><?php echo esc_html( $b['lead'] ); ?></p>
						<span class="biz-card__more">詳しく見る<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( $works ) : ?>
<section class="top-works section" id="works">
	<div class="container">
		<div class="section__head">
			<?php aoki_heading( 'Works', '施工事例' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>">施工事例一覧へ<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="works-grid">
			<?php foreach ( $works as $w ) : ?>
				<div class="reveal"><?php get_template_part( 'template-parts/work-card', null, array( 'post' => $w, 'detail' => true ) ); ?></div>
			<?php endforeach; ?>
		</div>
		<p class="center-btn"><a class="btn btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'works' ) ); ?>">すべての施工事例を見る<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a></p>
	</div>
</section>
<?php endif; ?>

<section class="top-recruit" id="recruit">
	<div class="top-recruit__media" aria-hidden="true"><?php echo aoki_img( 'photo/meeting-1.webp', '' ); // phpcs:ignore ?></div>
	<div class="top-recruit__inner container">
		<?php aoki_heading( 'Recruit', '採用情報', 'h2', 'heading--light' ); ?>
		<p class="top-recruit__catch reveal">あなたのペースで成長を<br>安心の環境で一緒に</p>
		<ol class="top-recruit__values">
			<?php foreach ( aoki_recruit_values() as $i => $v ) : ?>
				<li class="reveal" style="--i:<?php echo (int) $i; ?>"><span class="num"><?php echo (int) $i + 1; ?></span><?php echo esc_html( $v['title'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<div class="top-recruit__actions reveal">
			<a class="btn btn--white" href="<?php echo esc_url( aoki_url( 'recruit' ) ); ?>">採用情報を見る<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
			<a class="btn btn--ghost-white" href="<?php echo esc_url( aoki_url( 'entry' ) ); ?>"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?>採用エントリー</a>
		</div>
	</div>
</section>

<section class="top-staff section" id="staff">
	<div class="container">
		<div class="section__head">
			<?php aoki_heading( 'Staff', 'スタッフ紹介' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( aoki_url( 'staff' ) ); ?>">スタッフ紹介一覧へ<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<ul class="staff-cards">
			<?php foreach ( $staff as $s ) : ?>
				<li class="reveal">
					<a class="staff-card" href="<?php echo esc_url( aoki_url( 'staff' ) . '#staff-' . $s['id'] ); ?>">
						<figure class="staff-card__media"><?php echo aoki_img( $s['image'], $s['name'] . 'の写真' ); // phpcs:ignore ?></figure>
						<p class="staff-card__career"><?php echo esc_html( $s['career'] ); ?></p>
						<p class="staff-card__catch"><?php echo esc_html( $s['catch'] ); ?></p>
						<p class="staff-card__name"><?php echo esc_html( $s['name'] ); ?><span class="staff-card__more"><?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></span></p>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php aoki_cta(); ?>

<?php
get_footer();

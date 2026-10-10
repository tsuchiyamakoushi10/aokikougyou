<?php
/**
 * 事業詳細ページ（自動ドア事業／建具事業 共通）。
 *
 * @var array $args slug.
 */

$slug  = $args['slug'];
$all   = aoki_business();
$b     = $all[ $slug ];
$other = 'automatic-door' === $slug ? 'fittings' : 'automatic-door';
$works = aoki_get_works( 3, $slug );

aoki_page_hero(
	array(
		'en'     => $b['en'],
		'title'  => $b['title'],
		'lead'   => $b['lead'],
		'image'  => $b['image'],
		'crumbs' => array( array( '事業紹介', aoki_url( 'business' ) ), array( $b['title'], '' ) ),
	)
);
?>

<section class="section biz-intro">
	<div class="container biz-intro__grid">
		<div class="biz-intro__body">
			<p class="biz-intro__motto reveal"><span>モットー</span><?php echo esc_html( $b['motto'] ); ?></p>
			<p class="biz-intro__text reveal"><?php echo esc_html( $b['intro'] ); ?></p>
		</div>
		<figure class="biz-intro__media door-reveal"><?php echo aoki_img( $b['image2'], '' ); // phpcs:ignore ?></figure>
	</div>
</section>

<section class="section section--soft">
	<div class="container">
		<?php aoki_heading( 'Strength', '私たちの強み', 'h2', 'heading--center' ); ?>
		<div class="features">
			<?php foreach ( $b['features'] as $i => $f ) : ?>
				<article class="feature reveal">
					<p class="feature__num" aria-hidden="true">0<?php echo (int) $i + 1; ?></p>
					<h3 class="feature__title"><?php echo esc_html( $f['title'] ); ?></h3>
					<p><?php echo esc_html( $f['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php aoki_heading( 'Service', '事業内容', 'h2', 'heading--center' ); ?>
		<ul class="services services--<?php echo count( $b['services'] ); ?>">
			<?php foreach ( $b['services'] as $s ) : ?>
				<li class="service reveal">
					<span class="service__icon"><?php echo aoki_icon( $s['icon'] ); // phpcs:ignore ?></span>
					<h3 class="service__title"><?php echo esc_html( $s['title'] ); ?></h3>
					<p><?php echo esc_html( $s['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php if ( $works ) : ?>
<section class="section section--soft">
	<div class="container">
		<div class="section__head">
			<?php aoki_heading( 'Works', $b['title'] . 'の施工事例' ); ?>
			<a class="link-arrow" href="<?php echo esc_url( aoki_works_cat_url( $slug ) ); ?>">一覧を見る<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="works-grid">
			<?php foreach ( $works as $w ) : ?>
				<div class="reveal"><?php get_template_part( 'template-parts/work-card', null, array( 'post' => $w, 'detail' => true ) ); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
aoki_next_links(
	array(
		array( $all[ $other ]['en'], $all[ $other ]['title'], 'business/' . $other, $all[ $other ]['image'] ),
		array( 'Works', '施工事例一覧', 'works', 'works/c-kasumi.webp' ),
		array( 'Company', '会社案内', 'company', 'photo/office-sign.webp' ),
	)
);
aoki_cta( 'contact' );

<?php
/**
 * 事業紹介（トップ）。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Business',
		'title'  => '事業紹介',
		'lead'   => '扉や窓の空間づくりを通して、お客様に安心と満足を。',
		'image'  => 'photo/work-autodoor-3.webp',
		'crumbs' => array( array( '事業紹介', '' ) ),
	)
);
?>

<section class="section">
	<div class="container">
		<div class="biz-overview">
			<?php $n = 0; foreach ( aoki_business() as $slug => $b ) : $n++; ?>
				<article class="biz-overview__item">
					<figure class="biz-overview__media door-reveal"><?php echo aoki_img( $b['image'], '' ); // phpcs:ignore ?></figure>
					<div class="biz-overview__body reveal">
						<p class="biz-card__num" aria-hidden="true">0<?php echo (int) $n; ?></p>
						<p class="biz-card__en" aria-hidden="true"><?php echo esc_html( $b['en'] ); ?></p>
						<h2 class="biz-overview__title"><?php echo esc_html( $b['title'] ); ?></h2>
						<p class="biz-overview__lead"><?php echo esc_html( $b['lead'] ); ?></p>
						<p><?php echo esc_html( $b['intro'] ); ?></p>
						<ul class="tag-list">
							<?php foreach ( $b['services'] as $s ) : ?>
								<li><?php echo esc_html( $s['title'] ); ?></li>
							<?php endforeach; ?>
						</ul>
						<div class="btn-row">
							<a class="btn btn--primary" href="<?php echo esc_url( aoki_url( 'business/' . $slug ) ); ?>"><?php echo esc_html( $b['title'] ); ?>を詳しく見る<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a>
							<a class="link-arrow" href="<?php echo esc_url( aoki_works_cat_url( $slug ) ); ?>">施工事例<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
aoki_next_links(
	array(
		array( 'Works', '施工事例', 'works', 'works/c-laketown.webp' ),
		array( 'Company', '会社案内', 'company', 'photo/office-sign.webp' ),
		array( 'Staff', 'スタッフ紹介', 'staff', 'photo/meeting-3.webp' ),
	)
);
aoki_cta( 'contact' );
get_footer();

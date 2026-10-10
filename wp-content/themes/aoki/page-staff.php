<?php
/**
 * スタッフ紹介。
 */

get_header();
$staff = aoki_staff();
aoki_page_hero(
	array(
		'en'     => 'Staff',
		'title'  => 'スタッフ紹介',
		'lead'   => '青木工業で働く仲間の声をご紹介します。',
		'image'  => 'photo/meeting-2.webp',
		'crumbs' => array( array( '採用情報', aoki_url( 'recruit' ) ), array( 'スタッフ紹介', '' ) ),
	)
);
?>

<section class="section section--first">
	<div class="container">
		<ul class="staff-index">
			<?php foreach ( $staff as $s ) : ?>
				<li class="reveal">
					<a href="#staff-<?php echo esc_attr( $s['id'] ); ?>">
						<span class="staff-index__media"><?php echo aoki_img( $s['image'], '' ); // phpcs:ignore ?></span>
						<span class="staff-index__name"><?php echo esc_html( $s['name'] ); ?></span>
						<span class="staff-index__career"><?php echo esc_html( $s['career'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<?php foreach ( $staff as $i => $s ) : ?>
	<article class="interview<?php echo $i % 2 ? ' interview--reverse section--soft' : ''; ?>" id="staff-<?php echo esc_attr( $s['id'] ); ?>">
		<div class="container interview__grid">
			<div class="interview__profile">
				<figure class="interview__photo img-reveal"><?php echo aoki_img( $s['image'], $s['name'] . 'の写真' ); // phpcs:ignore ?></figure>
				<p class="interview__no" aria-hidden="true">Interview <span>0<?php echo (int) $i + 1; ?></span></p>
			</div>
			<div class="interview__body">
				<p class="interview__career"><?php echo esc_html( $s['career'] ); ?></p>
				<h2 class="interview__catch"><?php echo esc_html( $s['catch'] ); ?></h2>
				<p class="interview__name"><?php echo esc_html( $s['name'] ); ?></p>
				<dl class="qa">
					<?php foreach ( $s['qa'] as $q => $a ) : ?>
						<div class="qa__item reveal">
							<dt><?php echo esc_html( $q ); ?></dt>
							<dd>「<?php echo esc_html( $a ); ?>」</dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
	</article>
<?php endforeach; ?>

<?php
aoki_next_links(
	array(
		array( 'Recruit', '採用情報・募集要項', 'recruit', 'photo/team.webp' ),
		array( 'Business', '事業紹介', 'business', 'photo/work-autodoor-2.webp' ),
		array( 'Company', '会社案内', 'company', 'photo/office-sign.webp' ),
	)
);
aoki_cta( 'recruit' );
get_footer();

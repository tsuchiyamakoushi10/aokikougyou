<?php
/**
 * 下層ページのヘッダー。
 *
 * @var array $args en, title, lead, image, crumbs.
 */

$args = wp_parse_args(
	$args,
	array(
		'en'     => '',
		'title'  => get_the_title(),
		'lead'   => '',
		'image'  => '',
		'crumbs' => array(),
	)
);
?>
<section class="page-hero<?php echo $args['image'] ? ' page-hero--image' : ''; ?>">
	<?php if ( $args['image'] ) : ?>
		<div class="page-hero__media" aria-hidden="true">
			<?php echo aoki_img( $args['image'], '', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore ?>
		</div>
	<?php endif; ?>
	<div class="page-hero__door" aria-hidden="true"><span></span><span></span></div>
	<div class="page-hero__inner container">
		<p class="page-hero__en" aria-hidden="true"><?php echo esc_html( $args['en'] ); ?></p>
		<h1 class="page-hero__title"><?php echo esc_html( $args['title'] ); ?></h1>
		<?php if ( $args['lead'] ) : ?>
			<p class="page-hero__lead"><?php echo esc_html( $args['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
<div class="container">
	<?php aoki_breadcrumbs( $args['crumbs'] ); ?>
</div>

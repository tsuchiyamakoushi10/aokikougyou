<?php
/**
 * 施工事例カード。
 *
 * @var array $args post, heading (h2|h3), detail (bool).
 */

$w       = aoki_work_data( $args['post'] );
$heading = $args['heading'] ?? 'h3';
?>
<article class="work-card" id="works-<?php echo esc_attr( $w['id'] ); ?>" data-cat="<?php echo esc_attr( $w['cat'] ? $w['cat']->slug : '' ); ?>">
	<div class="work-card__media">
		<?php if ( $w['thumb'] ) : ?>
			<?php echo wp_get_attachment_image( $w['thumb'], 'aoki-card', false, array( 'loading' => 'lazy', 'alt' => $w['title'] ) ); ?>
		<?php else : ?>
			<div class="work-card__noimage" aria-hidden="true"><?php echo aoki_mark_svg( 'work-card__mark' ); // phpcs:ignore ?><span>No Image</span></div>
		<?php endif; ?>
		<?php if ( $w['cat'] ) : ?>
			<span class="work-card__cat work-card__cat--<?php echo esc_attr( $w['cat']->slug ); ?>"><?php echo esc_html( $w['cat']->name ); ?></span>
		<?php endif; ?>
	</div>
	<div class="work-card__body">
		<<?php echo tag_escape( $heading ); ?> class="work-card__title"><?php echo esc_html( $w['title'] ); ?></<?php echo tag_escape( $heading ); ?>>
		<dl class="work-card__meta">
			<?php if ( $w['place'] ) : ?>
				<div><dt><?php echo aoki_icon( 'pin' ); // phpcs:ignore ?>場所</dt><dd><?php echo esc_html( $w['place'] ); ?></dd></div>
			<?php endif; ?>
			<?php if ( $w['period'] ) : ?>
				<div><dt><?php echo aoki_icon( 'clock' ); // phpcs:ignore ?>工事期間</dt><dd><?php echo esc_html( $w['period'] ); ?></dd></div>
			<?php endif; ?>
		</dl>
		<?php if ( ! empty( $args['detail'] ) && $w['summary'] ) : ?>
			<p class="work-card__summary"><?php echo esc_html( $w['summary'] ); ?></p>
		<?php endif; ?>
	</div>
</article>

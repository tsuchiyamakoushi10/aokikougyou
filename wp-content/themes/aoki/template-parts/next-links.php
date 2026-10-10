<?php
/**
 * 「あわせて見る」導線。
 *
 * @var array $args links: [ [en, label, path, image], ... ].
 */
?>
<nav class="next-links section section--tight" aria-label="関連ページ">
	<div class="container">
		<p class="next-links__label">あわせてご覧ください</p>
		<ul class="next-links__list">
			<?php foreach ( $args['links'] as $l ) : ?>
				<li class="reveal">
					<a class="next-link" href="<?php echo esc_url( aoki_url( $l[2] ) ); ?>">
						<span class="next-link__media"><?php echo aoki_img( $l[3] ); // phpcs:ignore ?></span>
						<span class="next-link__body">
							<span class="next-link__en" aria-hidden="true"><?php echo esc_html( $l[0] ); ?></span>
							<span class="next-link__title"><?php echo esc_html( $l[1] ); ?></span>
						</span>
						<span class="next-link__arrow"><?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>

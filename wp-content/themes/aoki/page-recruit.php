<?php
/**
 * 採用情報。
 */

get_header();
aoki_page_hero(
	array(
		'en'     => 'Recruit',
		'title'  => '採用情報',
		'lead'   => 'あなたのペースで成長を　安心の環境で一緒に',
		'image'  => 'photo/team.webp',
		'crumbs' => array( array( '採用情報', '' ) ),
	)
);
$jobs = aoki_jobs();
?>

<nav class="page-toc container" aria-label="ページ内リンク">
	<ul>
		<li><a href="#values">大切にしていること</a></li>
		<li><a href="#points">おすすめポイント</a></li>
		<li><a href="#benefits">福利厚生</a></li>
		<li><a href="#jobs">募集要項</a></li>
	</ul>
</nav>

<section class="section" id="values">
	<div class="container">
		<?php aoki_heading( 'Our Values', '私たちが採用で大切にしていること', 'h2', 'heading--center' ); ?>
		<ol class="values">
			<?php foreach ( aoki_recruit_values() as $i => $v ) : ?>
				<li class="value reveal">
					<span class="value__num" aria-hidden="true"><?php echo (int) $i + 1; ?></span>
					<h3 class="value__title"><?php echo esc_html( $v['title'] ); ?></h3>
					<p><?php echo esc_html( $v['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="recruit-message">
	<div class="recruit-message__media" aria-hidden="true"><?php echo aoki_img( 'photo/meeting-3.webp', '' ); // phpcs:ignore ?></div>
	<div class="recruit-message__inner container">
		<p class="recruit-message__text reveal">当社で<span>「あなたのペースで成長を」</span><br>私たちと<span>「安心の環境で一緒に」</span></p>
	</div>
</section>

<section class="section section--soft" id="points">
	<div class="container">
		<?php aoki_heading( 'Points', '青木工業のおすすめポイント', 'h2', 'heading--center' ); ?>
		<div class="points">
			<?php foreach ( aoki_recruit_points() as $i => $p ) : ?>
				<article class="point reveal">
					<p class="point__num" aria-hidden="true">Point<span>0<?php echo (int) $i + 1; ?></span></p>
					<h3 class="point__title"><?php echo esc_html( $p['title'] ); ?></h3>
					<p><?php echo esc_html( $p['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="benefits">
	<div class="container">
		<?php aoki_heading( 'Benefits', '福利厚生', 'h2', 'heading--center' ); ?>
		<ul class="benefits">
			<?php foreach ( aoki_benefits() as $b ) : ?>
				<li class="benefit reveal">
					<span class="benefit__icon"><?php echo aoki_icon( $b['icon'] ); // phpcs:ignore ?></span>
					<h3 class="benefit__title"><?php echo esc_html( $b['title'] ); ?></h3>
					<p><?php echo esc_html( $b['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--soft" id="jobs">
	<div class="container container--narrow">
		<?php aoki_heading( 'Requirements', '募集要項', 'h2', 'heading--center' ); ?>

		<div class="tabs" data-tabs>
			<div class="tabs__list" role="tablist" aria-label="募集職種">
				<?php $first = true; foreach ( $jobs as $key => $job ) : ?>
					<button class="tabs__tab" role="tab" type="button" id="tab-<?php echo esc_attr( $key ); ?>" aria-controls="panel-<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>"<?php echo $first ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $job['title'] ); ?></button>
				<?php $first = false; endforeach; ?>
			</div>

			<?php $first = true; foreach ( $jobs as $key => $job ) : ?>
				<div class="tabs__panel" role="tabpanel" id="panel-<?php echo esc_attr( $key ); ?>" aria-labelledby="tab-<?php echo esc_attr( $key ); ?>"<?php echo $first ? '' : ' hidden'; ?>>
					<h3 class="screen-reader-text js-hidden-heading"><?php echo esc_html( $job['title'] ); ?></h3>
					<dl class="data-table data-table--job">
						<?php foreach ( $job['rows'] as $label => $value ) : ?>
							<div>
								<dt><?php echo esc_html( $label ); ?></dt>
								<dd>
									<?php if ( '業務内容' === $label ) : ?>
										<dl class="job-duties">
											<?php foreach ( $value as $t => $d ) : ?>
												<dt><?php echo esc_html( $t ); ?></dt><dd><?php echo esc_html( $d ); ?></dd>
											<?php endforeach; ?>
										</dl>
									<?php elseif ( '給与' === $label ) : ?>
										<p class="job-salary"><?php echo esc_html( $value['list'][0] ); ?></p>
										<ul class="job-list">
											<?php foreach ( array_slice( $value['list'], 1 ) as $li ) : ?>
												<li><?php echo esc_html( $li ); ?></li>
											<?php endforeach; ?>
										</ul>
										<?php $lines = aoki_lines( $value['text'] ); ?>
										<p><strong><?php echo esc_html( $lines[0] ); ?></strong><br><span class="job-note"><?php echo esc_html( $lines[1] ); ?></span></p>
									<?php elseif ( in_array( $label, array( '休暇', '福利厚生' ), true ) ) : ?>
										<ul class="job-list job-list--cols">
											<?php foreach ( aoki_lines( $value ) as $li ) : ?>
												<li><?php echo esc_html( $li ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php else : ?>
										<?php echo nl2br( esc_html( $value ) ); // phpcs:ignore ?>
									<?php endif; ?>
								</dd>
							</div>
						<?php endforeach; ?>
					</dl>
					<p class="center-btn"><a class="btn btn--primary btn--lg" href="<?php echo esc_url( add_query_arg( 'job', $key, aoki_url( 'entry' ) ) ); ?>"><?php echo aoki_icon( 'entry' ); // phpcs:ignore ?><?php echo esc_html( $job['title'] ); ?>にエントリーする<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></a></p>
				</div>
			<?php $first = false; endforeach; ?>
		</div>
	</div>
</section>

<?php
aoki_next_links(
	array(
		array( 'Staff', 'スタッフ紹介', 'staff', 'photo/team.webp' ),
		array( 'Business', '事業紹介', 'business', 'photo/work-autodoor-1.webp' ),
		array( 'Company', '会社案内', 'company', 'photo/office-sign.webp' ),
	)
);
aoki_cta( 'recruit' );
get_footer();

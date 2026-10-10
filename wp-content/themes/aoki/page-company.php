<?php
/**
 * 会社案内。
 */

get_header();
$c = aoki_company();
aoki_page_hero(
	array(
		'en'     => 'Company',
		'title'  => '会社案内',
		'image'  => 'photo/office-sign.webp',
		'crumbs' => array( array( '会社案内', '' ) ),
	)
);
?>

<nav class="page-toc container" aria-label="ページ内リンク">
	<ul>
		<li><a href="#philosophy">企業理念</a></li>
		<li><a href="#message">代表挨拶</a></li>
		<li><a href="#profile">会社概要</a></li>
		<li><a href="#access">アクセス</a></li>
	</ul>
</nav>

<section class="section philosophy" id="philosophy">
	<div class="container">
		<?php aoki_heading( 'Philosophy', '企業理念', 'h2', 'heading--center' ); ?>
		<p class="philosophy__catch reveal"><span>確かな技術で扉を開き、</span><span>皆様に幸せな時間を</span></p>
		<p class="philosophy__lead reveal">私たちの使命は、扉や窓の空間づくりを通して、お客様に安心と満足を提供することです。<br class="pc-only">確かな技術と実績を基に、高品質なサービスを提供し続けます。</p>

		<h3 class="philosophy__sub reveal">青木工業の2つの幸せ</h3>
		<div class="happiness">
			<div class="happiness__item reveal">
				<p class="happiness__num" aria-hidden="true">01</p>
				<h4 class="happiness__title">お客様に安心を</h4>
				<p>私たちは、正確で迅速かつ丁寧な施工を心がけ、24時間サポート体制を整えています。これにより、お客様に常に安心して頂けることを日々心がけています。</p>
			</div>
			<div class="happiness__item reveal">
				<p class="happiness__num" aria-hidden="true">02</p>
				<h4 class="happiness__title">従業員とその家族に安心を</h4>
				<p>従業員が安心して長く働ける職場環境を作ることが私たちのもう一つの使命です。アットホームな企業風土を重視し、家族経営から発展した企業文化を大切にしています。</p>
			</div>
		</div>
	</div>
</section>

<section class="section section--soft message" id="message">
	<div class="container message__grid">
		<figure class="message__photo img-reveal">
			<?php echo aoki_img( 'staff/president.webp', '代表取締役会長 中本 祥二' ); // phpcs:ignore ?>
		</figure>
		<div class="message__body">
			<?php aoki_heading( 'Message', '代表挨拶' ); ?>
			<div class="prose">
				<p>皆様、こんにちは。弊社のホームページをご覧いただき、誠にありがとうございます。</p>
				<p>私は、元々運送業で仕事をしていましたが、結婚を機に義理の父が社長を務める会社で建具職人として働く機会をいただきました。当初は従業員3名ほどの小さな会社で、仕事の教え方も厳しく、とても大変でした。しかし、様々な建具を取り付けていくうちに、一つひとつの扉が完成していくことに次第に面白さを感じるようになりました。</p>
				<p>家族経営を中心に、知り合いや友人の紹介を通じて少しずつ会社も成長していきました。約20年前には、取引先の紹介がきっかけで自動ドア整備の事業も開始し、今では多くのお客様に信頼していただける企業へと成長することができました。</p>
				<p>私たちの使命は、確かな技術と実績を基に、お客様に安心と満足を提供することです。正確で迅速な施工、24時間のサポート体制を整え、お客様に常に安心を提供しています。また、従業員とその家族が安心して長く働ける職場環境の整備にも力を入れています。</p>
				<p>今後は、新規事業も含めて新たなチャレンジをしていきたいと考えています。これまで培ってきた技術と信頼を基に、さらに多くのお客様に貢献できる企業を目指してまいります。皆様のご支援とご愛顧を賜りますよう、どうぞよろしくお願い申し上げます。</p>
			</div>
			<p class="message__sign"><span>代表取締役会長</span>中本 祥二</p>
		</div>
	</div>
</section>

<section class="section profile" id="profile">
	<div class="container container--narrow">
		<?php aoki_heading( 'Profile', '会社概要', 'h2', 'heading--center' ); ?>
		<dl class="data-table reveal">
			<div><dt>会社名</dt><dd><?php echo esc_html( $c['name'] ); ?></dd></div>
			<div><dt>本社</dt><dd>〒<?php echo esc_html( $c['zip'] ); ?> 埼玉県さいたま市西区佐知川302<br>TEL：<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo esc_html( $c['tel'] ); ?></a>　FAX：<?php echo esc_html( $c['fax'] ); ?></dd></div>
			<div><dt><?php echo esc_html( $c['branch']['name'] ); ?></dt><dd>〒<?php echo esc_html( $c['branch']['zip'] ); ?> <?php echo esc_html( $c['branch']['address'] ); ?><br>TEL：<a href="<?php echo esc_attr( aoki_tel_link( $c['branch']['tel'] ) ); ?>"><?php echo esc_html( $c['branch']['tel'] ); ?></a>　FAX：<?php echo esc_html( $c['branch']['fax'] ); ?></dd></div>
			<div><dt>事業内容</dt><dd>
				<ul class="data-table__list">
					<li><a href="<?php echo esc_url( aoki_url( 'business/automatic-door' ) ); ?>">自動ドア事業</a>　自動ドアの施工・保守・メンテナンス</li>
					<li><a href="<?php echo esc_url( aoki_url( 'business/fittings' ) ); ?>">建具事業</a>　鋼製建具の取り付け、金物工事、カーテンウォール施工</li>
				</ul>
			</dd></div>
			<div><dt>許可認可</dt><dd><?php echo esc_html( $c['license'] ); ?></dd></div>
			<div><dt>資格</dt><dd>カーテンウォール1級<br>自動ドア施工1級・2級</dd></div>
		</dl>
	</div>
</section>

<section class="section section--soft access" id="access">
	<div class="container">
		<?php aoki_heading( 'Access', 'アクセス', 'h2', 'heading--center' ); ?>
		<div class="access__grid">
			<div class="access__map reveal">
				<iframe src="<?php echo esc_url( $c['map_embed'] ); ?>" title="青木工業株式会社 本社の地図" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</div>
			<div class="access__info reveal">
				<h3 class="access__name">本社</h3>
				<p>〒<?php echo esc_html( $c['zip'] ); ?><br><?php echo esc_html( $c['address'] ); ?></p>
				<p>TEL：<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo esc_html( $c['tel'] ); ?></a><br>FAX：<?php echo esc_html( $c['fax'] ); ?></p>
				<a class="link-arrow" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode( $c['address'] ); ?>" target="_blank" rel="noopener">Googleマップで開く<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
				<h3 class="access__name"><?php echo esc_html( $c['branch']['name'] ); ?></h3>
				<p>〒<?php echo esc_html( $c['branch']['zip'] ); ?><br><?php echo esc_html( $c['branch']['address'] ); ?></p>
				<p>TEL：<a href="<?php echo esc_attr( aoki_tel_link( $c['branch']['tel'] ) ); ?>"><?php echo esc_html( $c['branch']['tel'] ); ?></a><br>FAX：<?php echo esc_html( $c['branch']['fax'] ); ?></p>
				<a class="link-arrow" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode( $c['branch']['address'] ); ?>" target="_blank" rel="noopener">Googleマップで開く<?php echo aoki_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>
		</div>
	</div>
</section>

<?php
aoki_next_links(
	array(
		array( 'Business', '事業紹介', 'business', 'photo/work-autodoor-2.webp' ),
		array( 'Works', '施工事例', 'works', 'works/c-tora.webp' ),
		array( 'Recruit', '採用情報', 'recruit', 'photo/team.webp' ),
	)
);
aoki_cta();
get_footer();

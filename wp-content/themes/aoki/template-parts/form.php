<?php
/**
 * フォーム本体（入力・エラー・送信完了）。
 *
 * @var array $args form_id.
 */

$form_id = $args['form_id'];
$form    = aoki_forms()[ $form_id ];
$state   = aoki_form_state();
$errors  = $state['errors'];
$values  = $state['values'];
$sent    = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification
$c       = aoki_company();

// 募集要項の「エントリーする」ボタンから来た場合は職種を選択済みにする。
if ( 'entry' === $form_id && empty( $values ) && isset( $_GET['job'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$jobs = aoki_jobs();
	$job  = sanitize_key( $_GET['job'] ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( isset( $jobs[ $job ] ) ) {
		$values['job'] = $jobs[ $job ]['title'];
	}
}

$val = function ( $key, $sub = null ) use ( $values ) {
	if ( ! isset( $values[ $key ] ) ) {
		return '';
	}
	return null === $sub ? $values[ $key ] : ( $values[ $key ][ $sub ] ?? '' );
};
?>
<div class="form-wrap" id="form">
<?php if ( $sent ) : ?>
	<div class="form-result form-result--success" role="status">
		<div class="form-result__icon"><?php echo aoki_mark_svg( 'form-result__mark' ); // phpcs:ignore ?></div>
		<h2 class="form-result__title">送信が完了しました</h2>
		<p><?php echo 'entry' === $form_id ? '採用エントリーをいただき' : 'お問い合わせいただき'; ?>、誠にありがとうございます。<br>ご入力いただいたメールアドレスに確認メールをお送りしました。<br>内容を確認のうえ、担当者より折り返しご連絡いたします。</p>
		<p class="form-result__note">確認メールが届かない場合は、迷惑メールフォルダをご確認いただくか、お電話（<a href="<?php echo esc_attr( aoki_tel_link( $c['tel'] ) ); ?>"><?php echo esc_html( $c['tel'] ); ?></a>）にてお問い合わせください。</p>
		<div class="form-result__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( aoki_url() ); ?>">トップページへ戻る</a>
			<?php if ( 'entry' === $form_id ) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( aoki_url( 'staff' ) ); ?>">スタッフ紹介を見る</a>
			<?php else : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( aoki_url( 'works' ) ); ?>">施工事例を見る</a>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	<?php if ( $errors ) : ?>
		<div class="form-alert" role="alert" tabindex="-1" id="form-alert">
			<?php echo aoki_icon( 'alert' ); // phpcs:ignore ?>
			<div>
				<p class="form-alert__title"><?php echo isset( $errors['_form'] ) ? esc_html( $errors['_form'] ) : '入力内容に誤りがあります。以下の項目をご確認ください。'; ?></p>
				<?php if ( count( array_diff_key( $errors, array( '_form' => 1 ) ) ) ) : ?>
					<ul>
						<?php foreach ( $errors as $k => $msg ) : ?>
							<?php if ( '_form' !== $k ) : ?>
								<li><a href="#f-<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $msg ); ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<form class="form" action="<?php echo esc_url( aoki_url( $form_id ) ); ?>" method="post" novalidate data-form="<?php echo esc_attr( $form_id ); ?>">
		<input type="hidden" name="action" value="aoki_form">
		<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
		<input type="hidden" name="_ts" value="<?php echo esc_attr( time() ); ?>">
		<?php wp_nonce_field( 'aoki_form_' . $form_id, '_aoki_nonce', false ); ?>
		<div class="form__hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

		<p class="form__legend"><span class="req">必須</span>は入力必須項目です。</p>

		<?php foreach ( $form['fields'] as $key => $f ) : ?>
			<?php
			$id    = 'f-' . $key;
			$err   = $errors[ $key ] ?? '';
			$req   = ! empty( $f['required'] );
			$group = in_array( $f['type'], array( 'name', 'kana', 'address', 'radio' ), true );
			$desc  = array();
			if ( ! empty( $f['note'] ) ) {
				$desc[] = $id . '-note';
			}
			if ( $err ) {
				$desc[] = $id . '-err';
			}
			$aria = ( $desc ? ' aria-describedby="' . esc_attr( implode( ' ', $desc ) ) . '"' : '' ) . ( $err ? ' aria-invalid="true"' : '' ) . ( $req ? ' required aria-required="true"' : '' );
			?>
			<div class="form__row<?php echo $err ? ' has-error' : ''; ?>" data-field="<?php echo esc_attr( $key ); ?>">
				<<?php echo $group ? 'p' : 'label'; ?> class="form__label"<?php echo $group ? ' id="' . esc_attr( $id ) . '-label"' : ' for="' . esc_attr( $id ) . '"'; ?>>
					<?php echo esc_html( $f['label'] ); ?>
					<?php echo $req ? '<span class="req">必須</span>' : '<span class="opt">任意</span>'; ?>
				</<?php echo $group ? 'p' : 'label'; ?>>
				<div class="form__control"<?php echo $group ? ' role="group" aria-labelledby="' . esc_attr( $id ) . '-label"' : ''; ?>>
					<?php
					switch ( $f['type'] ) :
						case 'name':
						case 'kana':
							$is_kana = 'kana' === $f['type'];
							?>
							<div class="form__split">
								<label><span class="form__sublabel"><?php echo $is_kana ? 'セイ' : '姓'; ?></span>
									<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>[sei]" value="<?php echo esc_attr( $val( $key, 'sei' ) ); ?>" autocomplete="<?php echo $is_kana ? 'off' : 'family-name'; ?>" placeholder="<?php echo $is_kana ? 'アオキ' : '青木'; ?>"<?php echo $aria; // phpcs:ignore ?><?php echo $is_kana ? ' data-kana' : ''; ?>></label>
								<label><span class="form__sublabel"><?php echo $is_kana ? 'メイ' : '名'; ?></span>
									<input type="text" name="<?php echo esc_attr( $key ); ?>[mei]" value="<?php echo esc_attr( $val( $key, 'mei' ) ); ?>" autocomplete="<?php echo $is_kana ? 'off' : 'given-name'; ?>" placeholder="<?php echo $is_kana ? 'タロウ' : '太郎'; ?>"<?php echo $aria; // phpcs:ignore ?><?php echo $is_kana ? ' data-kana' : ''; ?>></label>
							</div>
							<?php
							break;
						case 'address':
							?>
							<div class="form__address">
								<label><span class="form__sublabel">都道府県</span>
									<select name="<?php echo esc_attr( $key ); ?>[pref]" id="<?php echo esc_attr( $id ); ?>" autocomplete="address-level1" data-pref>
										<option value="">選択してください</option>
										<?php foreach ( aoki_prefectures() as $pref ) : ?>
											<option<?php selected( $val( $key, 'pref' ), $pref ); ?>><?php echo esc_html( $pref ); ?></option>
										<?php endforeach; ?>
									</select></label>
								<label><span class="form__sublabel">市区町村</span>
									<input type="text" name="<?php echo esc_attr( $key ); ?>[city]" value="<?php echo esc_attr( $val( $key, 'city' ) ); ?>" autocomplete="address-level2" data-city></label>
								<label class="form__address-wide"><span class="form__sublabel">丁目番地・建物名</span>
									<input type="text" name="<?php echo esc_attr( $key ); ?>[street]" value="<?php echo esc_attr( $val( $key, 'street' ) ); ?>" autocomplete="address-line1" data-street></label>
							</div>
							<?php
							break;
						case 'radio':
							?>
							<div class="form__radios">
								<?php foreach ( $f['options'] as $i => $opt ) : ?>
									<label class="radio"><input type="radio" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $opt ); ?>"<?php echo 0 === $i ? ' id="' . esc_attr( $id ) . '"' : ''; ?><?php checked( $val( $key ), $opt ); ?>><span><?php echo esc_html( $opt ); ?></span></label>
								<?php endforeach; ?>
							</div>
							<?php
							break;
						case 'textarea':
							?>
							<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="8" maxlength="3000" placeholder="<?php echo esc_attr( $f['placeholder'] ?? '' ); ?>"<?php echo $aria; // phpcs:ignore ?>><?php echo esc_textarea( $val( $key ) ); ?></textarea>
							<?php
							break;
						default:
							$type = array(
								'email' => 'email',
								'tel'   => 'tel',
								'zip'   => 'text',
							)[ $f['type'] ] ?? 'text';
							$ac   = $f['autocomplete'] ?? array(
								'email' => ! empty( $f['confirm'] ) ? 'off' : 'email',
								'tel'   => 'tel',
								'zip'   => 'postal-code',
							)[ $f['type'] ] ?? 'off';
							$ph   = array(
								'email' => 'example@aokikogyo.co.jp',
								'tel'   => '048-623-5130',
								'zip'   => '331-0064',
							)[ $f['type'] ] ?? '';
							?>
							<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val( $key ) ); ?>" autocomplete="<?php echo esc_attr( $ac ); ?>"<?php echo $ph ? ' placeholder="' . esc_attr( $ph ) . '"' : ''; ?><?php echo 'zip' === $f['type'] ? ' inputmode="numeric" data-zip class="input--short"' : ''; ?><?php echo 'tel' === $f['type'] ? ' inputmode="tel"' : ''; ?><?php echo ! empty( $f['confirm'] ) ? ' data-confirm="' . esc_attr( $f['confirm'] ) . '"' : ''; ?><?php echo $aria; // phpcs:ignore ?>>
							<?php
					endswitch;
					?>
					<?php if ( ! empty( $f['note'] ) ) : ?>
						<p class="form__note" id="<?php echo esc_attr( $id ); ?>-note"><?php echo esc_html( $f['note'] ); ?></p>
					<?php endif; ?>
					<p class="form__error" id="<?php echo esc_attr( $id ); ?>-err"<?php echo $err ? '' : ' hidden'; ?>><?php echo esc_html( $err ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="form__submit">
			<button type="submit" class="btn btn--primary btn--lg">入力内容を送信する<?php echo aoki_icon( 'arrow', 'icon icon--end' ); // phpcs:ignore ?></button>
			<p class="form__privacy">ご入力いただいた個人情報は、お問い合わせへの回答・ご連絡のためにのみ使用し、適切に管理いたします。</p>
		</div>
	</form>
<?php endif; ?>
</div>

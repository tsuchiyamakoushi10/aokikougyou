<?php
/**
 * お問い合わせ／採用エントリーフォーム。
 * 旧サイトの CGI（mailformpro）を置き換えるテーマ内蔵フォームです。
 * 送信先は「外観 > カスタマイズ > フォーム送信先」で設定します（未設定時は管理者メール）。
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aoki_prefectures() {
	return array( '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県' );
}

/**
 * フォーム定義。type: name / kana / text / email / tel / zip / select / textarea / address.
 */
function aoki_forms() {
	return array(
		'contact' => array(
			'title'   => 'お問い合わせ',
			'subject' => '【青木工業】お問い合わせがありました',
			'fields'  => array(
				'name'          => array( 'label' => 'お名前', 'type' => 'name', 'required' => true ),
				'kana'          => array( 'label' => 'フリガナ', 'type' => 'kana' ),
				'company'       => array( 'label' => '会社名', 'type' => 'text', 'autocomplete' => 'organization' ),
				'email'         => array( 'label' => 'メールアドレス', 'type' => 'email', 'required' => true ),
				'email_confirm' => array( 'label' => 'メールアドレス（確認用）', 'type' => 'email', 'required' => true, 'confirm' => 'email', 'note' => '確認のためもう一度ご入力ください。' ),
				'tel'           => array( 'label' => '電話番号', 'type' => 'tel', 'required' => true ),
				'message'       => array( 'label' => 'メッセージ', 'type' => 'textarea', 'required' => true ),
			),
		),
		'entry'   => array(
			'title'   => '採用エントリー',
			'subject' => '【青木工業】採用エントリーがありました',
			'fields'  => array(
				'job'           => array( 'label' => '希望職種', 'type' => 'radio', 'options' => array( '自動ドア（常勤）', '建具（常勤）', '未定・相談したい' ) ),
				'name'          => array( 'label' => 'お名前', 'type' => 'name', 'required' => true ),
				'kana'          => array( 'label' => 'フリガナ', 'type' => 'kana' ),
				'email'         => array( 'label' => 'メールアドレス', 'type' => 'email', 'required' => true ),
				'email_confirm' => array( 'label' => 'メールアドレス（確認用）', 'type' => 'email', 'required' => true, 'confirm' => 'email', 'note' => '確認のためもう一度ご入力ください。' ),
				'zip'           => array( 'label' => '郵便番号', 'type' => 'zip', 'note' => '入力すると住所が自動で入ります。' ),
				'address'       => array( 'label' => 'ご住所', 'type' => 'address' ),
				'tel'           => array( 'label' => '電話番号', 'type' => 'tel', 'required' => true ),
				'message'       => array( 'label' => 'メッセージ', 'type' => 'textarea', 'required' => true, 'placeholder' => 'ご経歴や希望の勤務開始時期、ご質問などをご記入ください。' ),
			),
		),
	);
}

/* ---------- 送信先設定（カスタマイザー） ---------- */

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section( 'aoki_forms', array( 'title' => 'フォーム送信先', 'priority' => 160 ) );
		foreach ( array(
			'contact' => 'お問い合わせの送信先メールアドレス',
			'entry'   => '採用エントリーの送信先メールアドレス',
		) as $key => $label ) {
			$wp_customize->add_setting( 'aoki_form_to_' . $key, array( 'sanitize_callback' => 'sanitize_email' ) );
			$wp_customize->add_control(
				'aoki_form_to_' . $key,
				array(
					'section'     => 'aoki_forms',
					'label'       => $label,
					'type'        => 'email',
					'description' => '未入力の場合は「設定 > 一般」の管理者メールアドレスに送信されます。',
				)
			);
		}
	}
);

/* ---------- 送信処理 ---------- */

// フォームはページ自身に POST し、ここで処理する（admin-post.php より軽量）。
add_action(
	'template_redirect',
	function () {
		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['action'] ) && 'aoki_form' === $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			aoki_handle_form();
		}
	},
	0
);

function aoki_handle_form() {
	$form_id = isset( $_POST['form_id'] ) ? sanitize_key( $_POST['form_id'] ) : '';
	$forms   = aoki_forms();
	if ( ! isset( $forms[ $form_id ] ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	$form    = $forms[ $form_id ];
	$back    = aoki_url( $form_id );
	$values  = aoki_form_collect( $form['fields'] );
	$errors  = array();

	if ( ! isset( $_POST['_aoki_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_aoki_nonce'] ), 'aoki_form_' . $form_id ) ) {
		$errors['_form'] = 'ページの有効期限が切れました。お手数ですが、もう一度送信してください。';
	}

	// スパム対策：ハニーポット・送信までの時間。
	$hp      = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	$started = isset( $_POST['_ts'] ) ? (int) $_POST['_ts'] : 0;
	if ( '' !== $hp || ( $started && time() - $started < 3 ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '1', $back ) );
		exit;
	}

	// 同一 IP からの連続送信を制限。
	$ip_key = 'aoki_form_rl_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= 5 ) {
		$errors['_form'] = '短時間に多数の送信がありました。時間をおいてから再度お試しいただくか、お電話にてお問い合わせください。';
	}

	if ( empty( $errors ) ) {
		$errors = aoki_form_validate( $form['fields'], $values );
	}

	if ( $errors ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'aoki_form_state_' . $token, array( 'errors' => $errors, 'values' => $values ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'form', $token, $back ) . '#form' );
		exit;
	}

	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$to = get_theme_mod( 'aoki_form_to_' . $form_id );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$body   = aoki_form_mail_body( $form['fields'], $values );
	$reply  = $values['email'];
	$name   = $values['name']['sei'] . ' ' . $values['name']['mei'];
	$header = array( 'Reply-To: ' . aoki_mail_encode_name( $name ) . ' <' . $reply . '>' );

	$admin_body = $form['title'] . "フォームから送信がありました。\n\n" . $body . "\n--\n送信日時：" . wp_date( 'Y年n月j日 H:i' ) . "\n送信元ページ：" . $back . "\n";
	$sent       = wp_mail( $to, $form['subject'], $admin_body, $header );

	if ( ! $sent ) {
		$token = wp_generate_password( 12, false );
		set_transient(
			'aoki_form_state_' . $token,
			array(
				'errors' => array( '_form' => '送信処理でエラーが発生しました。お手数ですが、時間をおいて再度お試しいただくか、お電話（' . aoki_company()['tel'] . '）にてご連絡ください。' ),
				'values' => $values,
			),
			10 * MINUTE_IN_SECONDS
		);
		wp_safe_redirect( add_query_arg( 'form', $token, $back ) . '#form' );
		exit;
	}

	// 自動返信。
	$c         = aoki_company();
	$auto_body = $name . " 様\n\nこの度は青木工業株式会社へ" . ( 'entry' === $form_id ? '採用エントリーをいただき' : 'お問い合わせいただき' ) . "、誠にありがとうございます。\n以下の内容で受け付けいたしました。担当者より折り返しご連絡いたしますので、今しばらくお待ちください。\n\n" . $body . "\n――――――――――――――――――\n" . $c['name'] . "\n〒" . $c['zip'] . ' ' . $c['address'] . "\nTEL：" . $c['tel'] . "\n" . home_url( '/' ) . "\n――――――――――――――――――\n※本メールは送信専用です。\n";
	wp_mail( $reply, '【青木工業株式会社】' . $form['title'] . 'を受け付けました', $auto_body );

	wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#form' );
	exit;
}

function aoki_mail_encode_name( $name ) {
	return '=?UTF-8?B?' . base64_encode( $name ) . '?=';
}

function aoki_form_collect( $fields ) {
	$v = array();
	foreach ( $fields as $key => $f ) {
		switch ( $f['type'] ) {
			case 'name':
			case 'kana':
				$a       = (array) ( $_POST[ $key ] ?? array() ); // phpcs:ignore WordPress.Security.NonceVerification
				$v[ $key ] = array(
					'sei' => sanitize_text_field( wp_unslash( $a['sei'] ?? '' ) ),
					'mei' => sanitize_text_field( wp_unslash( $a['mei'] ?? '' ) ),
				);
				break;
			case 'address':
				$a       = (array) ( $_POST[ $key ] ?? array() ); // phpcs:ignore WordPress.Security.NonceVerification
				$v[ $key ] = array(
					'pref'   => sanitize_text_field( wp_unslash( $a['pref'] ?? '' ) ),
					'city'   => sanitize_text_field( wp_unslash( $a['city'] ?? '' ) ),
					'street' => sanitize_text_field( wp_unslash( $a['street'] ?? '' ) ),
				);
				break;
			case 'textarea':
				$v[ $key ] = sanitize_textarea_field( wp_unslash( $_POST[ $key ] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification
				break;
			case 'email':
				// 形式チェックは validate で行う（sanitize_email だと不正値が空になり「未入力」扱いになるため）。
				$v[ $key ] = trim( sanitize_text_field( wp_unslash( $_POST[ $key ] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
				break;
			default:
				$v[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
	}
	return $v;
}

function aoki_form_validate( $fields, $v ) {
	$e = array();
	foreach ( $fields as $key => $f ) {
		$val   = $v[ $key ];
		$empty = is_array( $val ) ? ( 'name' === $f['type'] ? ( '' === $val['sei'] || '' === $val['mei'] ) : '' === implode( '', $val ) ) : '' === $val;
		if ( ! empty( $f['required'] ) && $empty ) {
			$e[ $key ] = $f['label'] . 'を入力してください。';
			continue;
		}
		if ( $empty ) {
			continue;
		}
		switch ( $f['type'] ) {
			case 'email':
				if ( ! is_email( $val ) ) {
					$e[ $key ] = 'メールアドレスの形式が正しくありません。';
				} elseif ( ! empty( $f['confirm'] ) && $val !== $v[ $f['confirm'] ] ) {
					$e[ $key ] = 'メールアドレスが一致しません。';
				}
				break;
			case 'tel':
				if ( strlen( preg_replace( '/[^0-9]/', '', mb_convert_kana( $val, 'n' ) ) ) < 9 ) {
					$e[ $key ] = '電話番号は9桁以上の数字で入力してください。';
				}
				break;
			case 'kana':
				if ( ! preg_match( '/^[ァ-ヶー　 ]*$/u', $val['sei'] . $val['mei'] ) ) {
					$e[ $key ] = 'フリガナは全角カタカナで入力してください。';
				}
				break;
			case 'zip':
				if ( ! preg_match( '/^\d{3}-?\d{4}$/', mb_convert_kana( $val, 'n' ) ) ) {
					$e[ $key ] = '郵便番号は7桁の数字で入力してください。';
				}
				break;
			case 'radio':
				if ( ! in_array( $val, $f['options'], true ) ) {
					$e[ $key ] = $f['label'] . 'を選択してください。';
				}
				break;
			case 'address':
				if ( '' !== $val['pref'] && ! in_array( $val['pref'], aoki_prefectures(), true ) ) {
					$e[ $key ] = '都道府県を選択してください。';
				}
				break;
			case 'textarea':
				if ( mb_strlen( $val ) > 3000 ) {
					$e[ $key ] = $f['label'] . 'は3000文字以内で入力してください。';
				}
				break;
		}
	}
	return $e;
}

function aoki_form_mail_body( $fields, $v ) {
	$out = '';
	foreach ( $fields as $key => $f ) {
		if ( ! empty( $f['confirm'] ) ) {
			continue;
		}
		$val = $v[ $key ];
		switch ( $f['type'] ) {
			case 'name':
			case 'kana':
				$val = trim( $val['sei'] . ' ' . $val['mei'] );
				break;
			case 'address':
				$val = $val['pref'] . $val['city'] . $val['street'];
				break;
		}
		$out .= '■' . $f['label'] . "\n" . ( '' === $val ? '（未入力）' : $val ) . "\n\n";
	}
	return $out;
}

/**
 * 直前の送信で発生したエラーと入力値。
 */
function aoki_form_state() {
	static $state = null;
	if ( null !== $state ) {
		return $state;
	}
	$state = array( 'errors' => array(), 'values' => array() );
	$token = isset( $_GET['form'] ) ? sanitize_key( $_GET['form'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $token ) {
		$saved = get_transient( 'aoki_form_state_' . $token );
		if ( is_array( $saved ) ) {
			$state = $saved;
		}
	}
	return $state;
}

/**
 * フォームの出力。
 */
function aoki_render_form( $form_id ) {
	get_template_part( 'template-parts/form', null, array( 'form_id' => $form_id ) );
}

// 入力エラー・送信完了の画面は検索結果に出さない。
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( isset( $_GET['form'] ) || isset( $_GET['sent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$robots['noindex'] = true;
		}
		return $robots;
	}
);

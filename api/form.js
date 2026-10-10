/**
 * お問い合わせ／採用エントリーフォームの送信 API（Vercel Serverless Function）。
 * WordPress 版 inc/forms.php と同じ項目・同じ入力チェックです。
 *
 * 必要な環境変数（Vercel > Settings > Environment Variables）
 *   SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS  … 送信に使うメールサーバー
 *   MAIL_FROM        … 送信元アドレス（例: no-reply@aokikogyo.co.jp）
 *   MAIL_TO_CONTACT  … お問い合わせの受信先
 *   MAIL_TO_ENTRY    … 採用エントリーの受信先（未設定時は MAIL_TO_CONTACT）
 */
const nodemailer = require('nodemailer');

const COMPANY = {
  name: '青木工業株式会社',
  zip: '331-0064',
  address: '埼玉県さいたま市西区大字佐知川302',
  tel: '048-623-5130',
  url: 'https://www.aokikogyo.co.jp/',
};

const PREFS = '北海道,青森県,岩手県,宮城県,秋田県,山形県,福島県,茨城県,栃木県,群馬県,埼玉県,千葉県,東京都,神奈川県,新潟県,富山県,石川県,福井県,山梨県,長野県,岐阜県,静岡県,愛知県,三重県,滋賀県,京都府,大阪府,兵庫県,奈良県,和歌山県,鳥取県,島根県,岡山県,広島県,山口県,徳島県,香川県,愛媛県,高知県,福岡県,佐賀県,長崎県,熊本県,大分県,宮崎県,鹿児島県,沖縄県'.split(',');

const FORMS = {
  contact: {
    title: 'お問い合わせ',
    subject: '【青木工業】お問い合わせがありました',
    fields: {
      name: { label: 'お名前', type: 'name', required: true },
      kana: { label: 'フリガナ', type: 'kana' },
      company: { label: '会社名', type: 'text' },
      email: { label: 'メールアドレス', type: 'email', required: true },
      email_confirm: { label: 'メールアドレス（確認用）', type: 'email', required: true, confirm: 'email' },
      tel: { label: '電話番号', type: 'tel', required: true },
      message: { label: 'メッセージ', type: 'textarea', required: true },
    },
  },
  entry: {
    title: '採用エントリー',
    subject: '【青木工業】採用エントリーがありました',
    fields: {
      job: { label: '希望職種', type: 'radio', options: ['自動ドア（常勤）', '建具（常勤）', '未定・相談したい'] },
      name: { label: 'お名前', type: 'name', required: true },
      kana: { label: 'フリガナ', type: 'kana' },
      email: { label: 'メールアドレス', type: 'email', required: true },
      email_confirm: { label: 'メールアドレス（確認用）', type: 'email', required: true, confirm: 'email' },
      zip: { label: '郵便番号', type: 'zip' },
      address: { label: 'ご住所', type: 'address' },
      tel: { label: '電話番号', type: 'tel', required: true },
      message: { label: 'メッセージ', type: 'textarea', required: true },
    },
  },
};

const toHalf = (s) => s.replace(/[０-９]/g, (c) => String.fromCharCode(c.charCodeAt(0) - 0xfee0)).replace(/[－ー―‐]/g, '-');
const str = (v, max = 200) => (typeof v === 'string' ? v : '').replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, '').trim().slice(0, max);

function collect(fields, body) {
  const v = {};
  for (const [key, f] of Object.entries(fields)) {
    if (f.type === 'name' || f.type === 'kana') v[key] = { sei: str(body[`${key}[sei]`]), mei: str(body[`${key}[mei]`]) };
    else if (f.type === 'address') v[key] = { pref: str(body[`${key}[pref]`]), city: str(body[`${key}[city]`]), street: str(body[`${key}[street]`]) };
    else v[key] = str(body[key], f.type === 'textarea' ? 3000 : 200);
  }
  return v;
}

function validate(fields, v) {
  const e = {};
  for (const [key, f] of Object.entries(fields)) {
    const val = v[key];
    const empty = typeof val === 'object' ? (f.type === 'name' ? !val.sei || !val.mei : !Object.values(val).join('')) : !val;
    if (f.required && empty) { e[key] = `${f.label}を入力してください。`; continue; }
    if (empty) continue;
    if (f.type === 'email') {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) e[key] = 'メールアドレスの形式が正しくありません。';
      else if (f.confirm && val !== v[f.confirm]) e[key] = 'メールアドレスが一致しません。';
    } else if (f.type === 'tel' && toHalf(val).replace(/[^0-9]/g, '').length < 9) e[key] = '電話番号は9桁以上の数字で入力してください。';
    else if (f.type === 'kana' && !/^[ァ-ヶー\s　]*$/.test(val.sei + val.mei)) e[key] = 'フリガナは全角カタカナで入力してください。';
    else if (f.type === 'zip' && !/^\d{3}-?\d{4}$/.test(toHalf(val))) e[key] = '郵便番号は7桁の数字で入力してください。';
    else if (f.type === 'radio' && !f.options.includes(val)) e[key] = `${f.label}を選択してください。`;
    else if (f.type === 'address' && val.pref && !PREFS.includes(val.pref)) e[key] = '都道府県を選択してください。';
  }
  return e;
}

function body(fields, v) {
  let out = '';
  for (const [key, f] of Object.entries(fields)) {
    if (f.confirm) continue;
    let val = v[key];
    if (f.type === 'name' || f.type === 'kana') val = `${val.sei} ${val.mei}`.trim();
    if (f.type === 'address') val = val.pref + val.city + val.street;
    out += `■${f.label}\n${val || '（未入力）'}\n\n`;
  }
  return out;
}

const encodeName = (n) => `=?UTF-8?B?${Buffer.from(n).toString('base64')}?=`;

module.exports = async (req, res) => {
  const wantsJson = (req.headers.accept || '').includes('application/json');
  const reply = (status, data, redirect) => {
    if (wantsJson || !redirect) return res.status(status).json(data);
    res.setHeader('Location', redirect);
    return res.status(303).end();
  };

  if (req.method !== 'POST') return res.status(405).setHeader('Allow', 'POST').json({ ok: false, message: 'Method Not Allowed' });

  const b = req.body && typeof req.body === 'object' ? req.body : {};
  const formId = str(b.form_id);
  const form = FORMS[formId];
  if (!form) return reply(400, { ok: false, message: '不正な送信です。' }, '/');
  const back = `/${formId}/`;

  // スパム対策：ハニーポット・送信までの時間（3秒未満は機械的な送信とみなす）
  const started = parseInt(b._ts, 10) || 0;
  if (str(b.website) || (started && Date.now() / 1000 - started < 3)) {
    return reply(200, { ok: true, redirect: `${back}thanks/` }, `${back}thanks/`);
  }

  const values = collect(form.fields, b);
  const errors = validate(form.fields, values);
  if (Object.keys(errors).length) {
    return reply(422, { ok: false, message: '入力内容に誤りがあります。以下の項目をご確認ください。', errors }, `${back}#form`);
  }

  const env = process.env;
  const to = formId === 'entry' ? env.MAIL_TO_ENTRY || env.MAIL_TO_CONTACT : env.MAIL_TO_CONTACT;
  const failMsg = `送信処理でエラーが発生しました。お手数ですが、時間をおいて再度お試しいただくか、お電話（${COMPANY.tel}）にてご連絡ください。`;
  if (!env.SMTP_HOST || !env.MAIL_FROM || !to) {
    console.error('Mail settings are missing (SMTP_HOST / MAIL_FROM / MAIL_TO_*)');
    return reply(500, { ok: false, message: failMsg }, `${back}#form`);
  }

  const transporter = nodemailer.createTransport({
    host: env.SMTP_HOST,
    port: Number(env.SMTP_PORT || 587),
    secure: Number(env.SMTP_PORT) === 465,
    auth: env.SMTP_USER ? { user: env.SMTP_USER, pass: env.SMTP_PASS } : undefined,
  });

  const name = `${values.name.sei} ${values.name.mei}`;
  const text = body(form.fields, values);
  const now = new Date().toLocaleString('ja-JP', { timeZone: 'Asia/Tokyo' });

  try {
    await transporter.sendMail({
      from: env.MAIL_FROM,
      to,
      replyTo: `${encodeName(name)} <${values.email}>`,
      subject: form.subject,
      text: `${form.title}フォームから送信がありました。\n\n${text}--\n送信日時：${now}\n`,
    });
  } catch (err) {
    console.error('Admin mail failed:', err.message);
    return reply(500, { ok: false, message: failMsg }, `${back}#form`);
  }

  try {
    await transporter.sendMail({
      from: `${encodeName(COMPANY.name)} <${env.MAIL_FROM}>`,
      to: values.email,
      subject: `【${COMPANY.name}】${form.title}を受け付けました`,
      text: `${name} 様\n\nこの度は${COMPANY.name}へ${formId === 'entry' ? '採用エントリーをいただき' : 'お問い合わせいただき'}、誠にありがとうございます。\n以下の内容で受け付けいたしました。担当者より折り返しご連絡いたしますので、今しばらくお待ちください。\n\n${text}――――――――――――――――――\n${COMPANY.name}\n〒${COMPANY.zip} ${COMPANY.address}\nTEL：${COMPANY.tel}\n${COMPANY.url}\n――――――――――――――――――\n※本メールは送信専用です。\n`,
    });
  } catch (err) {
    // 会社宛ては届いているので、自動返信の失敗はログのみ
    console.error('Auto-reply failed:', err.message);
  }

  return reply(200, { ok: true, redirect: `${back}thanks/` }, `${back}thanks/`);
};

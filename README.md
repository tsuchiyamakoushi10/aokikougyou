# 青木工業株式会社 コーポレートサイト（WordPress テーマ）

旧サイト http://www.aokikogyo.co.jp/ （静的 HTML）を、掲載内容はそのままに WordPress でリニューアルしたものです。

## 構成

```
wp-content/
├─ themes/aoki/          オリジナルテーマ本体
│  ├─ front-page.php     TOP（扉が開くオープニング）
│  ├─ page-*.php         会社案内・事業紹介・採用情報・スタッフ紹介・お問い合わせ・採用エントリー
│  ├─ archive-works.php  施工事例一覧（事業区分での絞り込みあり）
│  ├─ 404.php / search.php
│  ├─ inc/data.php       会社情報と各ページの文章（文言の修正はこのファイル）
│  ├─ inc/forms.php      お問い合わせ・エントリーフォーム（CGI の代わり）
│  ├─ inc/redirects.php  旧 URL → 新 URL の 301 リダイレクト
│  └─ assets/            CSS・JS・画像（WebP に最適化済み）
├─ php-error.php         システムエラー時のページ（500）
├─ db-error.php          DB 接続エラー時のページ（503）
└─ maintenance.php       更新作業中のページ（503）
```

## 本番サーバーへの設置

1. WordPress をインストールし、「設定 > 一般」でサイトの言語を日本語にする
2. `wp-content/themes/aoki` を本番の `wp-content/themes/` にアップロード
3. `wp-content/php-error.php`・`db-error.php`・`maintenance.php` を本番の `wp-content/` 直下にアップロード
4. 「外観 > テーマ」で **Aoki Industry** を有効化
   → 固定ページ・施工事例 13 件・パーマリンク設定（`/%postname%/`）が自動で作成されます
5. 「外観 > カスタマイズ > フォーム送信先」で、お問い合わせ・エントリーの受信アドレスを設定
6. フォームのテスト送信をして、メールが届くことを確認
   （届かない場合は WP Mail SMTP などの SMTP プラグインで送信元を設定してください）

## URL の変更（旧 URL は自動で転送）

| 旧 URL | 新 URL |
|---|---|
| /index.html | / |
| /company/index.html | /company/ |
| /business/index02.html | /business/automatic-door/ |
| /business/index01.html | /business/fittings/ |
| — | /business/（新設：事業紹介トップ） |
| /case/index.html | /works/ |
| /recruit/index.html | /recruit/ |
| /staff/index.html | /staff/ |
| /contact/index.html | /contact/ |
| /entry/index.html | /entry/ |

## 更新のしかた

- **施工事例**：管理画面「施工事例 > 新規追加」。タイトル＝事例名、本文＝概要説明、アイキャッチ＝写真、右側の「施工情報」に場所と工事期間を入力し、事業区分（自動ドア事業／建具事業）を選びます。TOP・事業ページにも自動で表示されます。
- **その他の文章**（会社概要・募集要項・スタッフインタビューなど）：`inc/data.php` を編集します。

## ローカルでの確認

```bash
docker compose up -d
./bin/setup.sh            # 初回のみ
```

- サイト：http://localhost:8080/ （管理画面 admin / admin）
- 送信メールの確認：http://localhost:8025/ （Mailpit）

## Vercel での公開（静的サイト＋フォーム API）

Vercel では WordPress（PHP / データベース）が動かないため、ローカルの WordPress を静的 HTML に書き出した `dist/` と、
フォーム送信用のサーバー関数 `api/form.js` を公開しています。

### フォームの送信先

フォームの見た目・入力チェック・ロボット対策はサイト側で行い、送信内容は次のどちらか（または両方）に届けます。

**1. Google フォーム（標準）**

1. Google フォームを作る（お問い合わせ用・採用エントリー用の2つ）。
   - 質問名はサイトの項目名と同じにする
     - お問い合わせ：お問い合わせの種類／お名前／フリガナ／会社名／メールアドレス／電話番号／メッセージ
     - 採用エントリー：応募の種類／応募職種／お名前／フリガナ／電話番号／メールアドレス／年齢／連絡してよい時間帯／今のお仕事・経験／聞きたいこと
   - 質問の種類は「記述式」（メッセージなど長いものは「段落」）。必須にはしない（チェックはサイト側で行う）
   - 設定：ログイン不要・メールアドレスを収集しない
   - 回答タブ →「新しい回答についてのメール通知を受け取る」をオンにすると、送信のたびにメールが届く
2. 公開リンク（`https://docs.google.com/forms/d/e/…/viewform`）で対応表を作る。
   ```bash
   python3 bin/google-form-map.py contact <お問い合わせ用のURL>
   python3 bin/google-form-map.py entry   <採用エントリー用のURL>
   ```
   `api/google-forms.json` ができるので、コミットして公開する。

※ Google フォームだけの場合、お客様への自動返信メールは送られません。

**2. メール（任意）**：Vercel の環境変数（Settings > Environment Variables）に設定すると、会社宛てメールと自動返信も送ります。

| 変数 | 内容 | 例 |
|---|---|---|
| `SMTP_HOST` | 送信に使うメールサーバー | `smtp.example.com` |
| `SMTP_PORT` | ポート（465 なら SSL） | `587` |
| `SMTP_USER` / `SMTP_PASS` | メールサーバーのユーザー・パスワード | |
| `MAIL_FROM` | 送信元アドレス | `no-reply@aokikogyo-sk.com` |
| `MAIL_TO_CONTACT` | お問い合わせの受信先 | |
| `MAIL_TO_ENTRY` | 採用エントリーの受信先（省略時は上と同じ） | |

どちらも未設定のままだと、送信時に「送信処理でエラーが発生しました（お電話ください）」と表示されます。

**ロボット対策（任意）**：Cloudflare Turnstile を使う場合は、Vercel に `TURNSTILE_SECRET_KEY` を設定し、
`TURNSTILE_SITE_KEY=<サイトキー> python3 bin/export-static.py` で書き出す。

### 内容を更新するとき

```bash
docker compose up -d
python3 bin/export-static.py   # dist/ を更新（本番ドメインは SITE_URL で変更可）
git add -A && git commit -m "サイト更新" && git push
```

施工事例の追加などは、ローカルの WordPress 管理画面（http://localhost:8080/wp-admin）で行ってから書き出してください。

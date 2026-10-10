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

## Vercel プレビュー（静的版）

Vercel では PHP / データベースが動かないため、ローカルの WordPress を静的 HTML に書き出した `dist/` を公開しています（デザイン確認用。フォームは送信されません）。

```bash
docker compose up -d
python3 bin/export-static.py   # dist/ を更新
git add dist && git commit -m "Update preview" && git push
```

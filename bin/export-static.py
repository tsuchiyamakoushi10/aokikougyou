#!/usr/bin/env python3
"""ローカル WordPress（http://localhost:8080）を静的 HTML に書き出す（Vercel 公開用）。
使い方: docker compose up -d && python3 bin/export-static.py  → dist/ に出力
  SITE_URL=https://example.com python3 bin/export-static.py  で本番ドメインを変更
"""
import os, re, shutil, subprocess, urllib.request, urllib.error
from datetime import date

BASE = 'http://localhost:8080'
SITE = os.environ.get('SITE_URL', 'https://www.aokikogyo.co.jp').rstrip('/')
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DIST = os.path.join(ROOT, 'dist')

PAGES = ['/', '/company/', '/business/', '/business/automatic-door/', '/business/fittings/',
         '/works/', '/works/category/automatic-door/', '/works/category/fittings/',
         '/recruit/', '/staff/', '/contact/', '/entry/']

def fetch(path):
    try:
        with urllib.request.urlopen(BASE + path) as r:
            html = r.read().decode('utf-8')
    except urllib.error.HTTPError as e:
        html = e.read().decode('utf-8')
    # WordPress が正常に表示できていないページは公開しない
    if len(html) < 5000 or '</html>' not in html:
        raise SystemExit('ERROR: %s の内容が取得できませんでした（%d バイト）。WordPress の表示を確認してください。\n'
                         'ヒント：git でブランチを切り替えた後は docker compose up -d --force-recreate wordpress' % (path, len(html)))
    return html

def clean(html):
    html = re.sub(r'<link[^>]+(wp-json|xmlrpc|EditURI|shortlink|oembed)[^>]*>\n?', '', html)
    # 検索エンジン向け：canonical・OGP・構造化データは本番ドメインの絶対 URL に
    html = re.sub(r'(rel="canonical" href=")' + re.escape(BASE), r'\1' + SITE, html)
    html = re.sub(r'(property="og:[a-z]+" content=")' + re.escape(BASE), r'\1' + SITE, html)
    html = re.sub(r'<script type="application/ld\+json">.*?</script>', lambda m: m.group(0).replace(BASE, SITE), html, flags=re.S)
    html = html.replace(BASE, '')
    # ローカル環境の noindex を外す（本番は検索対象）
    html = re.sub(r"<meta name='robots' content='[^']*' />", "<meta name='robots' content='max-image-preview:large' />", html)
    html = re.sub(r'(\.(css|js))\?ver=[^"\']+', r'\1', html)
    # フォームは Vercel の API（/api/form）へ送信
    html = re.sub(r'<form class="form" action="[^"]*"', '<form class="form" action="/api/form" data-endpoint="/api/form"', html)
    html = re.sub(r'<input type="hidden" id="_aoki_nonce"[^>]*>', '', html)
    return html

shutil.rmtree(DIST, ignore_errors=True)
for path in PAGES:
    out = os.path.join(DIST, path.strip('/'), 'index.html')
    os.makedirs(os.path.dirname(out), exist_ok=True)
    open(out, 'w').write(clean(fetch(path)))
    print('page', path)
open(os.path.join(DIST, '404.html'), 'w').write(clean(fetch('/this-page-does-not-exist/')))
print('page 404.html')

# 送信完了ページ（WordPress の ?sent=1 表示を書き出し）
for form in ('contact', 'entry'):
    out = os.path.join(DIST, form, 'thanks', 'index.html')
    os.makedirs(os.path.dirname(out), exist_ok=True)
    html = clean(fetch('/%s/?sent=1' % form))
    html = html.replace("<meta name='robots' content='max-image-preview:large' />", "<meta name='robots' content='noindex' />")
    html = re.sub(r'<link rel="canonical"[^>]*>', '', html)
    open(out, 'w').write(html)
    print('page /%s/thanks/' % form)

# sitemap.xml / robots.txt
today = date.today().isoformat()
urls = ''.join('  <url><loc>%s%s</loc><lastmod>%s</lastmod></url>\n' % (SITE, p, today) for p in PAGES)
open(os.path.join(DIST, 'sitemap.xml'), 'w').write('<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' + urls + '</urlset>\n')
open(os.path.join(DIST, 'robots.txt'), 'w').write('User-agent: *\nDisallow: /api/\nDisallow: /contact/thanks/\nDisallow: /entry/thanks/\n\nSitemap: %s/sitemap.xml\n' % SITE)
shutil.copy(os.path.join(ROOT, 'wp-content/themes/aoki/assets/img/logo/mark.svg'), os.path.join(DIST, 'favicon.svg'))

# テーマのアセットとアップロード画像
shutil.copytree(os.path.join(ROOT, 'wp-content/themes/aoki/assets'), os.path.join(DIST, 'wp-content/themes/aoki/assets'))
up = os.path.join(DIST, 'wp-content/uploads')
os.makedirs(up, exist_ok=True)
cid = subprocess.check_output(['docker', 'compose', 'ps', '-q', 'wordpress'], cwd=ROOT, text=True).strip()
subprocess.check_call(['docker', 'cp', cid + ':/var/www/html/wp-content/uploads/.', up])

# 参照漏れチェック
missing = set()
for d, _, files in os.walk(DIST):
    for f in files:
        if f.endswith('.html'):
            for u in re.findall(r'(?:src|href|srcset)="(/wp-content/[^"\s#?]+)', open(os.path.join(d, f)).read()):
                if not os.path.exists(os.path.join(DIST, u.lstrip('/'))):
                    missing.add(u)
print('missing assets:', sorted(missing) or 'none')

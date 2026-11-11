#!/usr/bin/env python3
"""ローカル WordPress（http://localhost:8080）を静的 HTML に書き出す（Vercel 公開用）。
使い方: docker compose up -d && python3 bin/export-static.py  → dist/ に出力
  SITE_URL=https://example.com python3 bin/export-static.py  で本番ドメインを変更
  TURNSTILE_SITE_KEY=... を付けると、フォームに Cloudflare Turnstile（ロボット判定）を入れる
  （Vercel 側にも TURNSTILE_SECRET_KEY を設定すること）

書き出しのたびに、次の2つも自動で更新する。
  bin/lastmod.json … ページの中身が変わった日（sitemap.xml の lastmod に使う）
  vercel.json      … Content-Security-Policy（インラインのスクリプトのハッシュ）
"""
import base64, hashlib, json, os, re, shutil, subprocess, urllib.request, urllib.error
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
    # フォームは Vercel の API（/api/form）へ送信
    html = re.sub(r'<form class="form" action="[^"]*"', '<form class="form" action="/api/form" data-endpoint="/api/form"', html)
    html = re.sub(r'<input type="hidden" id="_aoki_nonce"[^>]*>', '', html)
    return html

TURNSTILE_SITE_KEY = os.environ.get('TURNSTILE_SITE_KEY', '').strip()

def turnstile(html):
    if not TURNSTILE_SITE_KEY:
        return html
    widget = ('<div class="form__turnstile"><div class="cf-turnstile" data-sitekey="%s" data-language="ja"></div></div>\n'
              '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>' % TURNSTILE_SITE_KEY)
    return html.replace('<!--aoki:turnstile-->', widget)

shutil.rmtree(DIST, ignore_errors=True)
for path in PAGES:
    out = os.path.join(DIST, path.strip('/'), 'index.html')
    os.makedirs(os.path.dirname(out), exist_ok=True)
    open(out, 'w').write(turnstile(clean(fetch(path))))
    print('page', path)
# 404 ページは検索結果に出さない
html404 = clean(fetch('/this-page-does-not-exist/')).replace("<meta name='robots' content='max-image-preview:large' />", "<meta name='robots' content='noindex' />")
open(os.path.join(DIST, '404.html'), 'w').write(html404)
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
# lastmod は「ページの中身が変わった日」。中身（ファイルの版番号を除く）のハッシュを bin/lastmod.json に記録し、
# 変わったページだけ日付を更新する（Google は lastmod が正確なときだけ使う）。
today = date.today().isoformat()
LASTMOD = os.path.join(ROOT, 'bin', 'lastmod.json')
try:
    lastmod = json.load(open(LASTMOD))
except (OSError, ValueError):
    lastmod = {}
for p in PAGES:
    body = open(os.path.join(DIST, p.strip('/'), 'index.html')).read()
    body = re.sub(r'\?(?:ver|v)=\d+', '', body)
    digest = hashlib.sha256(body.encode()).hexdigest()
    if lastmod.get(p, {}).get('hash') != digest:
        lastmod[p] = {'hash': digest, 'date': today}
json.dump(lastmod, open(LASTMOD, 'w'), ensure_ascii=False, indent=1, sort_keys=True)
urls = ''.join('  <url><loc>%s%s</loc><lastmod>%s</lastmod></url>\n' % (SITE, p, lastmod[p]['date']) for p in PAGES)
open(os.path.join(DIST, 'sitemap.xml'), 'w').write('<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' + urls + '</urlset>\n')
# 送信完了ページは noindex で検索結果から外す（robots.txt で止めると noindex が読まれないため止めない）
open(os.path.join(DIST, 'robots.txt'), 'w').write('User-agent: *\nDisallow: /api/\n\nSitemap: %s/sitemap.xml\n' % SITE)
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

# Content-Security-Policy：インラインのスクリプトはハッシュで個別に許可する（'unsafe-inline' を使わない）
hashes = set()
for d, _, files in os.walk(DIST):
    for f in files:
        if f.endswith('.html'):
            for attrs, body in re.findall(r'<script([^>]*)>(.*?)</script>', open(os.path.join(d, f)).read(), flags=re.S):
                if 'src=' in attrs or 'application/ld+json' in attrs:
                    continue
                hashes.add("'sha256-%s'" % base64.b64encode(hashlib.sha256(body.encode()).digest()).decode())
csp = '; '.join([
    "default-src 'self'",
    "script-src 'self' %s https://www.googletagmanager.com https://zipcloud.ibsnet.co.jp https://challenges.cloudflare.com" % ' '.join(sorted(hashes)),
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "font-src 'self' https://fonts.gstatic.com",
    "img-src 'self' data: https://*.google-analytics.com https://*.googletagmanager.com https://*.g.doubleclick.net https://www.google.com",
    "connect-src 'self' https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com https://*.g.doubleclick.net https://www.google.com",
    "frame-src https://www.google.com https://challenges.cloudflare.com",
    "object-src 'none'",
    "base-uri 'none'",
    "form-action 'self'",
    "frame-ancestors 'none'",
    "upgrade-insecure-requests",
    "report-uri /api/csp-report",
    "report-to csp",
])
VJ = os.path.join(ROOT, 'vercel.json')
conf = json.load(open(VJ))
for rule in conf['headers']:
    for h in rule['headers']:
        if h['key'] in ('Content-Security-Policy', 'Content-Security-Policy-Report-Only'):
            h['value'] = csp
json.dump(conf, open(VJ, 'w'), ensure_ascii=False, indent=2)
open(VJ, 'a').write('\n')
print('csp: %d inline script hashes' % len(hashes))

#!/usr/bin/env python3
"""ローカル WordPress（http://localhost:8080）を静的 HTML に書き出す（Vercel プレビュー用）。
使い方: docker compose up -d && python3 bin/export-static.py  → dist/ に出力
"""
import os, re, shutil, subprocess, urllib.request, urllib.error

BASE = 'http://localhost:8080'
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DIST = os.path.join(ROOT, 'dist')

PAGES = ['/', '/company/', '/business/', '/business/automatic-door/', '/business/fittings/',
         '/works/', '/works/category/automatic-door/', '/works/category/fittings/',
         '/recruit/', '/staff/', '/contact/', '/entry/']

def fetch(path):
    try:
        with urllib.request.urlopen(BASE + path) as r:
            return r.read().decode('utf-8')
    except urllib.error.HTTPError as e:
        return e.read().decode('utf-8')

def clean(html):
    html = re.sub(r'<link[^>]+(wp-json|xmlrpc|EditURI|shortlink|oembed)[^>]*>\n?', '', html)
    html = html.replace(BASE, '')
    html = re.sub(r'(\.(css|js))\?ver=[^"\']+', r'\1', html)
    html = html.replace('<form class="form"', '<form class="form" data-preview')
    return html

shutil.rmtree(DIST, ignore_errors=True)
for path in PAGES:
    out = os.path.join(DIST, path.strip('/'), 'index.html')
    os.makedirs(os.path.dirname(out), exist_ok=True)
    open(out, 'w').write(clean(fetch(path)))
    print('page', path)
open(os.path.join(DIST, '404.html'), 'w').write(clean(fetch('/this-page-does-not-exist/')))
print('page 404.html')

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

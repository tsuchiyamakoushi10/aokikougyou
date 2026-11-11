#!/usr/bin/env python3
"""テーマ画像の縮小版（スマホ・タブレット用）を作る。
使い方: python3 bin/make-image-sizes.py
  assets/img/{photo,staff,works}/*.webp から、横幅 480 / 1024 px の「名前-480w.webp」「名前-1024w.webp」を作る。
  元の画像より小さい幅だけ作る。テンプレートの aoki_img() が自動で srcset に使う。
  写真を差し替えたら、もう一度実行すること。
"""
import glob, os, re
from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, 'wp-content/themes/aoki/assets/img')
WIDTHS = (480, 1024)

for src in sorted(glob.glob(os.path.join(IMG, '*', '*.webp'))):
    if re.search(r'-\d+w\.webp$', src):
        continue
    im = Image.open(src)
    for w in WIDTHS:
        if im.width <= w:
            continue
        out = src[:-5] + '-%dw.webp' % w
        if os.path.exists(out) and os.path.getmtime(out) >= os.path.getmtime(src):
            continue
        h = round(im.height * w / im.width)
        im.resize((w, h), Image.LANCZOS).save(out, 'WEBP', quality=78, method=6)
        print('made', os.path.relpath(out, IMG), os.path.getsize(out) // 1024, 'KB')

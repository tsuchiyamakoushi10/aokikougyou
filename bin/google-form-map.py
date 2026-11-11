#!/usr/bin/env python3
"""Google フォームの質問と、サイトのフォームの項目を対応づける（api/google-forms.json を作る）。

使い方:
  python3 bin/google-form-map.py contact https://docs.google.com/forms/d/e/XXXX/viewform
  python3 bin/google-form-map.py entry   https://docs.google.com/forms/d/e/YYYY/viewform

Google フォーム側の質問名は、サイトのフォームの項目名と同じにする（例：「お名前」「電話番号」）。
質問の種類は「記述式」または「段落」にする（ラジオボタンにする場合は、選択肢をサイトと完全に同じ文言に）。
フォームは「ログイン不要」「メールアドレスを収集しない」に設定する。
"""
import json, os, re, sys, urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, 'api', 'google-forms.json')

# サイト側の項目（api/form.js の FORMS と同じ。確認用メールアドレスは送らない）
FIELDS = {
    'contact': {'type': 'お問い合わせの種類', 'name': 'お名前', 'kana': 'フリガナ', 'company': '会社名',
                'email': 'メールアドレス', 'tel': '電話番号', 'message': 'メッセージ'},
    'entry': {'type': '応募の種類', 'job': '応募職種', 'name': 'お名前', 'kana': 'フリガナ', 'tel': '電話番号',
              'email': 'メールアドレス', 'age': '年齢', 'time': '連絡してよい時間帯', 'career': '今のお仕事・経験',
              'question': '聞きたいこと'},
}

def norm(s):
    return re.sub(r'[\s　（）()・*＊必須]', '', s or '')

def questions(html):
    m = re.search(r'FB_PUBLIC_LOAD_DATA_\s*=\s*(\[.*?\]);\s*</script>', html, re.S)
    if not m:
        raise SystemExit('Google フォームの質問を読み取れませんでした。公開中（回答を受付中）・ログイン不要か確認してください。')
    data = json.loads(m.group(1))
    out = []
    for item in data[1][1] or []:
        try:
            out.append((item[1], 'entry.%d' % item[4][0][0]))
        except (IndexError, TypeError):
            pass  # 見出し・説明文など、回答欄のない項目
    return out

def main():
    if len(sys.argv) != 3 or sys.argv[1] not in FIELDS:
        raise SystemExit(__doc__)
    form_id, url = sys.argv[1], sys.argv[2]
    m = re.search(r'/forms/d/e/([\w-]+)/', url)
    if not m:
        raise SystemExit('URL は「https://docs.google.com/forms/d/e/…/viewform」の形で指定してください（共有→リンク で取得）。')
    action = 'https://docs.google.com/forms/d/e/%s/formResponse' % m.group(1)
    html = urllib.request.urlopen(urllib.request.Request(url.split('?')[0], headers={'User-Agent': 'Mozilla/5.0'}), timeout=20).read().decode('utf-8')
    qs = questions(html)
    entries, missing = {}, []
    for key, label in FIELDS[form_id].items():
        hit = [e for t, e in qs if norm(t) == norm(label)]
        if hit:
            entries[key] = hit[0]
        else:
            missing.append(label)
    conf = json.load(open(OUT)) if os.path.exists(OUT) else {}
    conf[form_id] = {'action': action, 'entries': entries}
    json.dump(conf, open(OUT, 'w'), ensure_ascii=False, indent=2)
    open(OUT, 'a').write('\n')
    print('対応づけた項目:', ', '.join('%s=%s' % (FIELDS[form_id][k], v) for k, v in entries.items()))
    if missing:
        print('Google フォームに見つからない項目（送られません）:', ', '.join(missing))
    extra = [t for t, _ in qs if norm(t) not in {norm(l) for l in FIELDS[form_id].values()}]
    if extra:
        print('サイトのフォームにない Google フォームの質問（空欄で送られます）:', ', '.join(extra))

if __name__ == '__main__':
    main()

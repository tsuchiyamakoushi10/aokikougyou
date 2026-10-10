/* フォーム：入力チェック・郵便番号から住所入力 */
(function () {
	'use strict';

	var form = document.querySelector('form[data-form]');
	var alertBox = document.getElementById('form-alert');
	if (alertBox) {
		alertBox.focus();
	}
	if (!form) return;

	function rowOf(el) { return el.closest('.form__row'); }

	function setError(row, msg) {
		var err = row.querySelector('.form__error');
		var inputs = row.querySelectorAll('input:not([type="radio"]), select, textarea');
		row.classList.toggle('has-error', !!msg);
		err.textContent = msg || '';
		err.hidden = !msg;
		inputs.forEach(function (i) {
			if (msg) {
				i.setAttribute('aria-invalid', 'true');
				var ids = (i.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
				if (ids.indexOf(err.id) === -1) ids.push(err.id);
				i.setAttribute('aria-describedby', ids.join(' '));
			} else {
				i.removeAttribute('aria-invalid');
			}
		});
	}

	function label(row) {
		var l = row.querySelector('.form__label');
		return l ? l.childNodes[0].textContent.trim() : '';
	}

	function toHalf(s) {
		return s.replace(/[０-９]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 0xFEE0); }).replace(/[－ー―‐]/g, '-');
	}

	function validateRow(row) {
		var inputs = Array.prototype.slice.call(row.querySelectorAll('input:not([type="radio"]):not([type="hidden"]), select, textarea'));
		if (!inputs.length) return true;
		var required = inputs.some(function (i) { return i.required; });
		var values = inputs.map(function (i) { return i.value.trim(); });
		var msg = '';
		if (required && values.some(function (v) { return v === ''; })) {
			msg = label(row) + 'を入力してください。';
		} else if (values.join('') !== '') {
			var el = inputs[0];
			if (el.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim())) {
				msg = 'メールアドレスの形式が正しくありません。';
			} else if (el.dataset.confirm) {
				var target = form.querySelector('[name="' + el.dataset.confirm + '"]');
				if (target && target.value.trim() !== el.value.trim()) msg = 'メールアドレスが一致しません。';
			} else if (el.type === 'tel' && toHalf(el.value).replace(/[^0-9]/g, '').length < 9) {
				msg = '電話番号は9桁以上の数字で入力してください。';
			} else if (el.hasAttribute('data-zip') && !/^\d{3}-?\d{4}$/.test(toHalf(el.value.trim()))) {
				msg = '郵便番号は7桁の数字で入力してください。';
			} else if (el.hasAttribute('data-kana') && !/^[ァ-ヶー\s　]*$/.test(values.join(''))) {
				msg = 'フリガナは全角カタカナで入力してください。';
			}
		}
		setError(row, msg);
		return !msg;
	}

	form.querySelectorAll('input, select, textarea').forEach(function (el) {
		el.addEventListener('blur', function () {
			var row = rowOf(el);
			if (row && (row.classList.contains('has-error') || el.value.trim() !== '')) validateRow(row);
		});
		el.addEventListener('input', function () {
			var row = rowOf(el);
			if (row && row.classList.contains('has-error')) validateRow(row);
		});
	});

	form.addEventListener('submit', function (e) {
		var firstBad = null;
		form.querySelectorAll('.form__row').forEach(function (row) {
			if (!validateRow(row) && !firstBad) firstBad = row;
		});
		if (firstBad) {
			e.preventDefault();
			var f = firstBad.querySelector('input, select, textarea');
			firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
			if (f) setTimeout(function () { f.focus({ preventScroll: true }); }, 350);
			return;
		}
		// 静的サイト版（Vercel）：API に送信して結果を表示
		if (form.dataset.endpoint) {
			e.preventDefault();
			sendToApi();
			return;
		}
		var btn = form.querySelector('[type="submit"]');
		if (btn) { btn.disabled = true; btn.firstChild.textContent = '送信中…'; }
	});

	function showAlert(message, errors) {
		var box = document.getElementById('form-alert');
		if (!box) {
			box = document.createElement('div');
			box.className = 'form-alert';
			box.id = 'form-alert';
			box.setAttribute('role', 'alert');
			box.tabIndex = -1;
			form.parentNode.insertBefore(box, form);
		}
		box.innerHTML = '<div><p class="form-alert__title"></p></div>';
		box.querySelector('.form-alert__title').textContent = message;
		if (errors) {
			var ul = document.createElement('ul');
			Object.keys(errors).forEach(function (k) {
				var row = form.querySelector('[data-field="' + k + '"]');
				if (row) setError(row, errors[k]);
				var li = document.createElement('li');
				var a = document.createElement('a');
				a.href = '#f-' + k;
				a.textContent = errors[k];
				li.appendChild(a);
				ul.appendChild(li);
			});
			box.firstChild.appendChild(ul);
		}
		box.scrollIntoView({ behavior: 'smooth', block: 'center' });
		box.focus({ preventScroll: true });
	}

	function sendToApi() {
		var btn = form.querySelector('[type="submit"]');
		var label = btn ? btn.firstChild.textContent : '';
		if (btn) { btn.disabled = true; btn.firstChild.textContent = '送信中…'; }
		var data = {};
		new FormData(form).forEach(function (v, k) { data[k] = v; });
		fetch(form.dataset.endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
			body: JSON.stringify(data)
		}).then(function (r) {
			return r.json().catch(function () { return { ok: false }; });
		}).then(function (res) {
			if (res.ok) {
				location.href = res.redirect || ('/' + form.dataset.form + '/thanks/');
				return;
			}
			showAlert(res.message || '送信処理でエラーが発生しました。お手数ですが、お電話にてご連絡ください。', res.errors);
			if (btn) { btn.disabled = false; btn.firstChild.textContent = label; }
		}).catch(function () {
			showAlert('通信エラーが発生しました。電波の良い場所で再度お試しいただくか、お電話にてご連絡ください。');
			if (btn) { btn.disabled = false; btn.firstChild.textContent = label; }
		});
	}

	// 送信までの時間チェック用（ページを開いた時刻）
	var ts = form.querySelector('[name="_ts"]');
	if (ts) ts.value = Math.floor(Date.now() / 1000);

	/* 郵便番号 → 住所（zipcloud API / JSONP） */
	var zip = form.querySelector('[data-zip]');
	if (zip) {
		var pref = form.querySelector('[data-pref]');
		var city = form.querySelector('[data-city]');
		var street = form.querySelector('[data-street]');
		var lastZip = '';
		zip.addEventListener('input', function () {
			var z = toHalf(zip.value).replace(/[^0-9]/g, '');
			if (z.length !== 7 || z === lastZip) return;
			lastZip = z;
			var cb = 'aokiZip' + Date.now();
			var s = document.createElement('script');
			window[cb] = function (res) {
				delete window[cb];
				s.remove();
				if (!res || !res.results || !res.results[0]) return;
				var r = res.results[0];
				if (pref) pref.value = r.address1;
				if (city) city.value = r.address2 + r.address3;
				if (street && !street.value) street.focus();
			};
			s.src = 'https://zipcloud.ibsnet.co.jp/api/search?zipcode=' + z + '&callback=' + cb;
			s.onerror = function () { delete window[cb]; s.remove(); };
			document.body.appendChild(s);
		});
	}
})();

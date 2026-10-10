/* TOP：扉の隙間から光が漏れ、扉が開いて幸せな時間が見えるオープニング */
(function () {
	'use strict';

	var doc = document.documentElement;
	var intro = document.getElementById('intro');
	var skip = document.getElementById('intro-skip');

	// 2回目以降・動きを減らす設定：扉は出さず、写真と文字だけ表示
	if (!intro || doc.classList.contains('intro-skip')) {
		requestAnimationFrame(function () {
			doc.classList.add('intro-opened', 'intro-done');
		});
		return;
	}

	var timers = [];
	var finished = false;
	function at(ms, fn) { timers.push(setTimeout(fn, ms)); }

	function done() {
		if (finished) return;
		finished = true;
		timers.forEach(clearTimeout);
		doc.classList.add('intro-ready', 'intro-crack', 'intro-opened', 'intro-done');
		try { sessionStorage.setItem('aokiIntro', '1'); } catch (e) {}
	}

	function start() {
		at(150, function () { doc.classList.add('intro-ready'); });   // 合わせ目に光の線
		at(1500, function () { doc.classList.add('intro-crack'); });  // 扉がわずかに開き、光が強まる
		at(2500, function () { doc.classList.add('intro-opened'); }); // 扉が開き、光の向こうに写真
		at(4900, done);
	}

	// 写真の読み込みを待って開始（最大 1.2 秒）
	var img = document.querySelector('.hero__photo img');
	var started = false;
	function go() { if (!started) { started = true; start(); } }
	if (img && !img.complete) {
		img.addEventListener('load', go, { once: true });
		img.addEventListener('error', go, { once: true });
		setTimeout(go, 1200);
	} else {
		requestAnimationFrame(go);
	}

	// スキップ：ボタン・スクロール・キー操作
	function skipNow() {
		if (finished) return;
		if (!doc.classList.contains('intro-opened')) {
			timers.forEach(clearTimeout);
			doc.classList.add('intro-ready', 'intro-crack', 'intro-opened');
			setTimeout(done, 1200);
		} else {
			done();
		}
	}
	if (skip) skip.addEventListener('click', skipNow);
	window.addEventListener('wheel', skipNow, { passive: true, once: true });
	window.addEventListener('touchmove', skipNow, { passive: true, once: true });
	window.addEventListener('keydown', function (e) {
		if (['ArrowDown', 'PageDown', ' ', 'Enter', 'Escape', 'Tab'].indexOf(e.key) !== -1) skipNow();
	}, { once: true });
})();

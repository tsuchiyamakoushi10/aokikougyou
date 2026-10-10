/* Aoki Industry — 共通スクリプト */
(function () {
	'use strict';

	var doc = document.documentElement;
	var header = document.getElementById('site-header');
	var mobileBar = document.querySelector('.mobile-bar');

	/* ヘッダー：スクロールで背景を付ける／モバイル下部バーを表示 */
	var ticking = false;
	function onScroll() {
		var y = window.scrollY;
		if (header) header.classList.toggle('is-scrolled', y > 40);
		if (mobileBar) mobileBar.classList.toggle('is-visible', y > window.innerHeight * 0.6);
		ticking = false;
	}
	window.addEventListener('scroll', function () {
		if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
	}, { passive: true });
	onScroll();

	/* ドロワーメニュー */
	var toggle = document.querySelector('.menu-toggle');
	var drawer = document.getElementById('drawer');
	if (toggle && drawer) {
		var setOpen = function (open) {
			toggle.setAttribute('aria-expanded', String(open));
			toggle.setAttribute('aria-label', open ? 'メニューを閉じる' : 'メニューを開く');
			doc.classList.toggle('is-drawer-open', open);
			if (open) {
				drawer.hidden = false;
				requestAnimationFrame(function () { drawer.classList.add('is-open'); });
				var first = drawer.querySelector('a');
				if (first) setTimeout(function () { first.focus({ preventScroll: true }); }, 300);
			} else {
				drawer.classList.remove('is-open');
				setTimeout(function () { if (!drawer.classList.contains('is-open')) drawer.hidden = true; }, 350);
			}
		};
		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});
		drawer.addEventListener('click', function (e) {
			if (e.target.closest('a')) setOpen(false);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
				toggle.focus();
			}
		});
		window.matchMedia('(min-width: 1100px)').addEventListener('change', function (m) {
			if (m.matches) setOpen(false);
		});
	}

	/* スクロールで要素を表示 */
	var targets = document.querySelectorAll('.reveal, .img-reveal');
	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-in');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
		targets.forEach(function (el) { io.observe(el); });
	} else {
		targets.forEach(function (el) { el.classList.add('is-in'); });
	}

	/* タブ（募集要項） */
	document.querySelectorAll('[data-tabs]').forEach(function (root) {
		var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
		function select(tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.setAttribute('aria-selected', String(on));
				t.tabIndex = on ? 0 : -1;
				document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
			});
			if (focus) tab.focus();
		}
		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function () { select(tab); });
			tab.addEventListener('keydown', function (e) {
				var next = null;
				if (e.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
				if (e.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
				if (next) { e.preventDefault(); select(next, true); }
			});
		});
		// URL の #jobs-fittings などで直接開く
		var m = location.hash.match(/^#jobs-(.+)$/);
		if (m) {
			var t = document.getElementById('tab-' + m[1]);
			if (t) select(t);
		}
	});
})();

/**
 * File: messenger.js
 * Path: plugins/messenger/js/messenger.js
 *
 * Messenger client. Runs on every page for signed-in users (header counter, persistent popups, sounds,
 * "write" button on profiles) and builds the full Telegram-style interface on the messenger page.
 * The server is the only source of truth: this code sends actions and draws what it receives.
 * All user text goes through textContent / DOM nodes, never innerHTML.
 */
(function () {
	'use strict';

	var B = window.MSGR_BOOT;
	if (!B || window.__msgrLoaded) { return; }
	window.__msgrLoaded = true;

	var L = B.lang || {};
	var isApp = B.page === 'app';
	var baseTitle = document.title;

	/* ====================================================== helpers */

	function t(key, vars) {
		var s = L[key];
		if (s === undefined) { s = key; }
		s = String(s);
		if (vars && vars.n !== undefined && s.indexOf('|') >= 0) {
			// plural forms "one|few|many" (Russian) or "one|other" (English)
			var f = s.split('|'), n = Math.abs(parseInt(vars.n, 10) || 0), i;
			if (f.length >= 3) { i = (n % 10 === 1 && n % 100 !== 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : 2); }
			else { i = n === 1 ? 0 : 1; }
			s = f[i];
		}
		return s.replace(/\{(\w+)\}/g, function (m, k) { return (vars && vars[k] !== undefined) ? vars[k] : m; });
	}

	function el(tag, cls, text) {
		var e = document.createElement(tag);
		if (cls) { e.className = cls; }
		if (text !== undefined && text !== null) { e.textContent = text; }
		return e;
	}

	function clear(n) { while (n.firstChild) { n.removeChild(n.firstChild); } }
	function url(kind, id) { return String((B.urls || {})[kind] || '').replace('__ID__', id); }
	function lsGet(k) { try { return window.localStorage.getItem(k); } catch (e) { return null; } }
	function lsSet(k, v) { try { window.localStorage.setItem(k, v); } catch (e) { /* ignore */ } }
	function ssGet(k) { try { return window.sessionStorage.getItem(k); } catch (e) { return null; } }
	function ssSet(k, v) { try { window.sessionStorage.setItem(k, v); } catch (e) { /* ignore */ } }

	function api(action, data) {
		var f = new FormData();
		f.append('action', action);
		f.append('x', B.xk);
		Object.keys(data || {}).forEach(function (k) { f.append(k, data[k]); });
		return fetch(B.ajax, { method: 'POST', body: f, credentials: 'same-origin' })
			.then(function (r) { return r.text(); })
			.then(function (txt) {
				try { return JSON.parse(txt); } catch (e) { return { success: false, message: t('err_unknown') }; }
			})
			.catch(function () { return { success: false, message: t('err_network'), network: true }; });
	}

	// inline icons (static strings, no user data)
	var ICONS = {
		plane: '<svg viewBox="0 0 24 24"><path d="M2.5 20.5 22 12 2.5 3.5l.01 6.6L15 12 2.51 13.9z"/></svg>',
		back: '<svg viewBox="0 0 24 24"><path d="M20 11H7.8l5.6-5.6L12 4l-8 8 8 8 1.4-1.4L7.8 13H20z"/></svg>',
		adduser: '<svg viewBox="0 0 24 24"><path d="M15 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-9 0V9H4v3H1v2h3v3h2v-3h3v-2zm9 2c-2.7 0-8 1.3-8 4v2h16v-2c0-2.7-5.3-4-8-4z"/></svg>',
		bell: '<svg viewBox="0 0 24 24"><path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6v-5c0-3.1-1.6-5.6-4.5-6.3V4a1.5 1.5 0 0 0-3 0v.7C7.6 5.4 6 7.9 6 11v5l-2 2v1h16v-1z"/></svg>',
		bellOff: '<svg viewBox="0 0 24 24"><path d="M20 18.7V19H5.3l-1.5-1.5L4 17.3l2-2V11c0-.6.1-1.2.3-1.7L2.1 5.1 3.5 3.7l17 17-1.4 1.4zM12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6.8-8.9-8.9C9.6 5.5 10.3 5 11.5 4.7V4a1.5 1.5 0 0 1 3 0v.7C17.4 5.4 18 7.9 18 11z"/></svg>',
		smile: '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-3.5 7a1.3 1.3 0 1 1 0 2.6 1.3 1.3 0 0 1 0-2.6zm7 0a1.3 1.3 0 1 1 0 2.6 1.3 1.3 0 0 1 0-2.6zM12 18c-2.3 0-4.2-1.4-5-3.4h10c-.8 2-2.7 3.4-5 3.4z"/></svg>',
		info: '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-6h2zm0-8h-2V7h2z"/></svg>',
		sound: '<svg viewBox="0 0 24 24"><path d="M3 10v4h4l5 5V5L7 10zm13.5 2A4.5 4.5 0 0 0 14 8v8a4.5 4.5 0 0 0 2.5-4z"/></svg>',
		soundOff: '<svg viewBox="0 0 24 24"><path d="M3 10v4h4l5 5V5L7 10zm13.6 2 2.7-2.7-1.4-1.4-2.7 2.7-2.7-2.7-1.4 1.4 2.7 2.7-2.7 2.7 1.4 1.4 2.7-2.7 2.7 2.7 1.4-1.4z"/></svg>'
	};
	function icon(name) {
		var s = document.createElement('span');
		s.style.display = 'inline-flex';
		s.innerHTML = ICONS[name] || '';
		return s;
	}
	function iconBtn(name, title, onClick, cls) {
		var b = el('button', 'tg-icon-btn' + (cls ? ' ' + cls : ''));
		b.type = 'button';
		b.title = title;
		b.setAttribute('aria-label', title);
		b.appendChild(icon(name));
		if (onClick) { b.addEventListener('click', onClick); }
		return b;
	}
	function textBtn(label, cls, onClick) {
		var b = el('button', 'tg-btn' + (cls ? ' ' + cls : ''), label);
		b.type = 'button';
		if (onClick) { b.addEventListener('click', onClick); }
		return b;
	}

	function avatar(seed, name, size, online) {
		var a = el('span', 'tg-ava tg-c' + (Math.abs(parseInt(seed, 10) || 0) % 7) + (size ? ' is-' + size : ''));
		var ch = String(name || '?').replace(/^[^\p{L}\p{N}]+/u, '').charAt(0) || '?';
		a.textContent = ch;
		if (online) { a.appendChild(el('i', 'dot')); }
		return a;
	}

	function pad(n) { return (n < 10 ? '0' : '') + n; }
	function hhmm(ts) { var d = new Date(ts * 1000); return pad(d.getHours()) + ':' + pad(d.getMinutes()); }
	function dayKey(ts) { var d = new Date(ts * 1000); return d.getFullYear() + '-' + d.getMonth() + '-' + d.getDate(); }
	function startOfDay(d) { return new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime(); }
	function dayLabel(ts) {
		var d = new Date(ts * 1000), today = startOfDay(new Date()), diff = Math.round((today - startOfDay(d)) / 86400000);
		if (diff === 0) { return t('today'); }
		if (diff === 1) { return t('yesterday'); }
		var o = { day: 'numeric', month: 'long' };
		if (d.getFullYear() !== new Date().getFullYear()) { o.year = 'numeric'; }
		return d.toLocaleDateString(L.locale || undefined, o);
	}
	function listTime(ts) {
		if (!ts) { return ''; }
		var d = new Date(ts * 1000), diff = Math.round((startOfDay(new Date()) - startOfDay(d)) / 86400000);
		if (diff === 0) { return hhmm(ts); }
		if (diff === 1) { return t('yesterday'); }
		if (diff < 7) { return d.toLocaleDateString(L.locale || undefined, { weekday: 'short' }); }
		return pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + String(d.getFullYear()).slice(2);
	}
	function seenText(ts) {
		if (!ts) { return t('seen_long'); }
		var diff = Math.round((startOfDay(new Date()) - startOfDay(new Date(ts * 1000))) / 86400000);
		if (diff === 0) { return t('seen_today', { time: hhmm(ts) }); }
		if (diff === 1) { return t('seen_yesterday', { time: hhmm(ts) }); }
		if (diff < 30) { return t('seen_recent'); }
		return t('seen_long');
	}

	// text -> DOM with clickable http(s) links; nothing is ever parsed as HTML
	function linkify(text, parent) {
		var re = /(https?:\/\/[^\s<>"']+)/g, last = 0, m;
		while ((m = re.exec(text)) !== null) {
			var link = m[1], tail = '';
			var cut = link.match(/[.,;:!?)\]}]+$/);
			if (cut) { tail = cut[0]; link = link.slice(0, -tail.length); }
			if (m.index > last) { parent.appendChild(document.createTextNode(text.slice(last, m.index))); }
			var a = el('a', '', link);
			a.href = link;
			a.target = '_blank';
			a.rel = 'noopener noreferrer nofollow ugc';
			parent.appendChild(a);
			if (tail) { parent.appendChild(document.createTextNode(tail)); }
			last = m.index + m[1].length;
		}
		if (last < text.length) { parent.appendChild(document.createTextNode(text.slice(last))); }
	}

	var SMILEYS = [[':)', '🙂'], [':-)', '🙂'], [':(', '🙁'], [':-(', '🙁'], [':D', '😄'],
		[';)', '😉'], [':P', '😛'], [':p', '😛'], ['<3', '❤️'], [':O', '😮'], [':*', '😘'], ['XD', '😆']];
	function smileys(text) {
		return text.split(/(\s+)/).map(function (w) {
			for (var i = 0; i < SMILEYS.length; i++) { if (w === SMILEYS[i][0]) { return SMILEYS[i][1]; } }
			return w;
		}).join('');
	}

	/* ====================================================== sounds (Web Audio, no files) */

	var Snd = (function () {
		var ctx = null, on = (lsGet('msgr_sound') !== '0') && B.sounds !== false;

		function ac() {
			if (!ctx) {
				var C = window.AudioContext || window.webkitAudioContext;
				if (!C) { return null; }
				try { ctx = new C(); } catch (e) { return null; }
			}
			if (ctx.state === 'suspended' && ctx.resume) { ctx.resume(); }
			return ctx;
		}

		function tone(freq, start, dur, vol, type, glideTo) {
			var c = ac();
			if (!c) { return; }
			var o = c.createOscillator(), g = c.createGain(), t0 = c.currentTime + start;
			o.type = type || 'sine';
			o.frequency.setValueAtTime(freq, t0);
			if (glideTo) { o.frequency.exponentialRampToValueAtTime(glideTo, t0 + dur); }
			g.gain.setValueAtTime(0.0001, t0);
			g.gain.exponentialRampToValueAtTime(vol, t0 + 0.012);
			g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
			o.connect(g);
			g.connect(c.destination);
			o.start(t0);
			o.stop(t0 + dur + 0.03);
		}

		// a bell-like note: a fundamental plus two quiet harmonics, soft attack, long tail
		function bell(freq, start, dur, vol) {
			tone(freq, start, dur, vol, 'sine');
			tone(freq * 2, start, dur * 0.6, vol * 0.28, 'sine');
			tone(freq * 3.01, start, dur * 0.35, vol * 0.1, 'sine');
		}

		function swoosh(start, vol) {
			var c = ac();
			if (!c) { return; }
			var len = Math.floor(c.sampleRate * 0.09), buf = c.createBuffer(1, len, c.sampleRate), d = buf.getChannelData(0);
			for (var i = 0; i < len; i++) { d[i] = (Math.random() * 2 - 1) * Math.sin(Math.PI * i / len); }
			var s = c.createBufferSource(), f = c.createBiquadFilter(), g = c.createGain(), t0 = c.currentTime + start;
			s.buffer = buf;
			f.type = 'bandpass';
			f.frequency.setValueAtTime(1200, t0);
			f.frequency.exponentialRampToValueAtTime(5200, t0 + 0.09);
			f.Q.value = 1.2;
			g.gain.value = vol;
			s.connect(f);
			f.connect(g);
			g.connect(c.destination);
			s.start(t0);
		}

		var sounds = {
			// incoming message: two soft bell notes, like a notification "ding-ding"
			incoming: function () { bell(1568, 0, 0.5, 0.16); bell(2093, 0.13, 0.7, 0.14); },
			// message arrives in the chat you are looking at: one quiet pop
			inchat: function () { bell(1760, 0, 0.28, 0.1); },
			// sent: the short "whoosh + pop" of the send button
			sent: function () { swoosh(0, 0.07); tone(620, 0.04, 0.09, 0.1, 'sine', 940); },
			// invitation: rising three-note chime
			invite: function () { bell(1175, 0, 0.4, 0.15); bell(1480, 0.12, 0.4, 0.15); bell(1976, 0.24, 0.8, 0.15); },
			error: function () { tone(180, 0, 0.16, 0.1, 'triangle', 120); },
			click: function () { tone(1400, 0, 0.03, 0.05, 'sine'); }
		};

		document.addEventListener('pointerdown', function () { if (on) { ac(); } }, { passive: true });

		return {
			play: function (name) { if (on && sounds[name]) { try { sounds[name](); } catch (e) { /* ignore */ } } },
			isOn: function () { return on; },
			toggle: function () {
				on = !on;
				lsSet('msgr_sound', on ? '1' : '0');
				if (on) { sounds.click(); }
				return on;
			}
		};
	})();

	/* ====================================================== counters: header link, title, launcher */

	var counters = { unread: 0, invites: 0 };
	var fab = null;

	function totalBadge() { return counters.unread + counters.invites; }

	function ensureLauncher() {
		if (fab || document.querySelector('.msgr-hdr') || isApp) { return; }
		fab = el('a', 'msgr-fab');
		fab.href = url('home', '');
		fab.title = t('hdr_link');
		fab.appendChild(el('span', 'msgr-fab-ico'));
		var b = el('span', 'msgr-badge is-zero');
		b.setAttribute('data-msgr-badge', '1');
		fab.appendChild(b);
		document.body.appendChild(fab);
	}

	function setCounters(unread, invites) {
		var changed = unread !== counters.unread || invites !== counters.invites;
		var grew = (unread + invites) > totalBadge();
		counters.unread = unread;
		counters.invites = invites;
		var n = totalBadge();
		ensureLauncher();
		Array.prototype.forEach.call(document.querySelectorAll('[data-msgr-badge]'), function (b) {
			b.textContent = n > 99 ? '99+' : String(n);
			b.classList.toggle('is-zero', n === 0);
			if (changed && grew) {
				b.classList.remove('is-bump');
				void b.offsetWidth;
				b.classList.add('is-bump');
			}
		});
		document.title = (n > 0 ? '(' + (n > 99 ? '99+' : n) + ') ' : '') + baseTitle;
	}

	/* ====================================================== persistent popups */

	var toastBox = null;
	var toasts = {};        // notif id -> {node, data}
	var seenTs = parseInt(ssGet('msgr_seen') || '0', 10) || 0;
	var firstSync = true;
	var hadSeen = ssGet('msgr_seen') !== null;
	var onOpenChat = null;  // set by the app: open a chat without a page load
	var viewing = function () { return 0; };   // app: id of the chat on screen (and the tab is visible)

	function box() {
		if (!toastBox) {
			toastBox = el('div', 'msgr-toasts');
			toastBox.setAttribute('aria-live', 'polite');
			document.body.appendChild(toastBox);
		}
		return toastBox;
	}

	function goChat(cid) {
		if (onOpenChat) { onOpenChat(cid); } else { window.location.href = url('chat', cid); }
	}

	function dismiss(id, remote) {
		var o = toasts[id];
		if (o) {
			o.node.classList.add('is-out');
			setTimeout(function () { if (o.node.parentNode) { o.node.parentNode.removeChild(o.node); } }, 200);
			delete toasts[id];
			renderMore();
		}
		if (remote !== false) { api('closenotif', { id: id }); }
	}

	function renderMore() {
		var old = toastBox && toastBox.querySelector('.msgr-more');
		if (old) { old.remove(); }
	}

	function buildToast(n) {
		var node = el('div', 'msgr-toast' + (n.kind === 'invite' ? ' is-invite' : ''));
		node.setAttribute('role', 'status');
		node.appendChild(avatar(n.is_group ? n.chat_id : n.from_id, n.kind === 'invite' ? n.title : n.from, 'sm'));
		var body = el('div', 'tb');
		var head = el('div', 'tt');
		head.appendChild(el('span', '', n.kind === 'invite' ? t('invited_title') : (n.is_group ? n.title : n.from)));
		var cnt = el('span', 'msgr-badge', '');
		cnt.hidden = true;
		head.appendChild(cnt);
		body.appendChild(head);
		var msg = el('div', 'tm');
		body.appendChild(msg);
		node.appendChild(body);
		var x = el('button', 'tx', '×');
		x.type = 'button';
		x.title = t('close');
		x.setAttribute('aria-label', t('close'));
		x.addEventListener('click', function (e) { e.stopPropagation(); dismiss(n.id); });
		node.appendChild(x);
		if (n.kind === 'invite') {
			var acts = el('div', 'ta');
			acts.appendChild(textBtn(t('btn_join'), '', function (e) {
				e.stopPropagation();
				api('join', { chat: n.chat_id }).then(function (r) {
					if (!r.success) { toast(r.message); Snd.play('error'); return; }
					dismiss(n.id, false);
					goChat(n.chat_id);
				});
			}));
			acts.appendChild(textBtn(t('btn_decline'), 'is-ghost', function (e) {
				e.stopPropagation();
				api('decline', { chat: n.chat_id }).then(function () { dismiss(n.id, false); syncNow(); });
			}));
			body.appendChild(acts);
		}
		node.addEventListener('click', function () {
			if (n.kind === 'invite') { return; }
			dismiss(n.id);
			goChat(n.chat_id);
		});
		return { node: node, cnt: cnt, msg: msg, head: head };
	}

	function fillToast(o, n) {
		o.data = n;
		clear(o.msg);
		if (n.kind === 'invite') {
			o.msg.appendChild(document.createTextNode(t('invited_text', { by: n.from, title: n.title })));
		} else {
			if (n.is_group) { o.msg.appendChild(el('b', '', n.from + ': ')); }
			o.msg.appendChild(document.createTextNode(n.preview));
		}
		o.cnt.hidden = !(n.kind === 'message' && n.cnt > 1);
		o.cnt.textContent = String(n.cnt);
	}

	function renderToasts(list) {
		if (!B.popups) { return; }
		var wanted = {};
		var shown = 0;
		list.forEach(function (n) {
			if (n.kind === 'message' && n.chat_id === viewing()) {
				// the user is looking at this chat right now: no popup, mark it read
				api('closenotif', { id: n.id });
				return;
			}
			wanted[n.id] = true;
			shown++;
			var o = toasts[n.id];
			if (!o) {
				o = buildToast(n);
				toasts[n.id] = o;
				box().appendChild(o.node);
			} else if (o.data.ts !== n.ts) {
				o.node.classList.remove('is-pulse');
				void o.node.offsetWidth;
				o.node.classList.add('is-pulse');
			}
			fillToast(o, n);
		});
		Object.keys(toasts).forEach(function (id) {
			if (!wanted[id]) {
				var o = toasts[id];
				if (o.node.parentNode) { o.node.parentNode.removeChild(o.node); }
				delete toasts[id];
			}
		});
		// show at most three, the rest is summarised
		var nodes = toastBox ? Array.prototype.filter.call(toastBox.children, function (c) { return c.classList.contains('msgr-toast'); }) : [];
		nodes.forEach(function (n, i) { n.style.display = (i < nodes.length - 3) ? 'none' : ''; });
		renderMore();
		if (nodes.length > 3) {
			var more = el('div', 'msgr-more', t('more_popups', { n: nodes.length - 3 }));
			toastBox.appendChild(more);
		}
	}

	// small status toast (errors, "sent")
	function toast(text, opts) {
		opts = opts || {};
		var node = el('div', 'msgr-toast');
		node.style.paddingLeft = '1rem';
		var b = el('div', 'tb');
		b.appendChild(el('div', 'tm', text));
		if (opts.action) {
			var a = el('div', 'ta');
			a.appendChild(textBtn(opts.action.label, 'is-ghost', function (e) { e.stopPropagation(); opts.action.fn(); node.remove(); }));
			b.appendChild(a);
		}
		node.appendChild(b);
		var x = el('button', 'tx', '×');
		x.type = 'button';
		x.addEventListener('click', function (e) { e.stopPropagation(); node.remove(); });
		node.appendChild(x);
		box().appendChild(node);
		setTimeout(function () { if (node.parentNode) { node.remove(); } }, opts.ms || 4500);
	}

	/* ====================================================== background sync */

	var syncTimer = null, syncBusy = false;
	var appHook = null;   // the app subscribes to be told about fresh sync data

	function applySync(s) {
		if (!s || !s.success) { return; }
		var maxTs = seenTs, fresh = null;
		(s.notifs || []).forEach(function (n) {
			if (n.ts > seenTs && !(n.kind === 'message' && n.chat_id === viewing())) {
				if (!fresh || n.kind === 'invite') { fresh = n; }
			}
			if (n.ts > maxTs) { maxTs = n.ts; }
		});
		if ((!firstSync || hadSeen) && fresh) { Snd.play(fresh.kind === 'invite' ? 'invite' : 'incoming'); }
		firstSync = false;
		if (maxTs > seenTs) { seenTs = maxTs; ssSet('msgr_seen', String(seenTs)); }
		setCounters(s.unread || 0, s.invites || 0);
		renderToasts(s.notifs || []);
		if (appHook) { appHook(s); }
	}

	function syncNow() {
		if (syncBusy) { return; }
		syncBusy = true;
		api('sync', {}).then(function (s) { syncBusy = false; applySync(s); }, function () { syncBusy = false; });
	}

	function syncLoop() {
		clearTimeout(syncTimer);
		var every = (document.hidden ? Math.max(B.poll * 2, 10) : (isApp ? Math.min(B.poll, 4) : B.poll)) * 1000;
		syncTimer = setTimeout(function () { syncNow(); syncLoop(); }, every);
	}

	/* ====================================================== modal */

	function modal(title) {
		var back = el('div', 'msgr-modal-back');
		var m = el('div', 'msgr-modal');
		m.setAttribute('role', 'dialog');
		m.setAttribute('aria-modal', 'true');
		if (title) { m.appendChild(el('h3', '', title)); }
		back.appendChild(m);
		function close() { document.removeEventListener('keydown', onKey); if (back.parentNode) { back.remove(); } }
		function onKey(e) { if (e.key === 'Escape') { close(); } }
		back.addEventListener('mousedown', function (e) { if (e.target === back) { close(); } });
		document.addEventListener('keydown', onKey);
		document.body.appendChild(back);
		return { box: m, close: close };
	}

	/** "Write to user" form (profile button). The dialog is created with the first message. */
	function composeModal(uid, name, onSent) {
		var md = modal(t('write_to', { name: name }));
		var ta = el('textarea');
		ta.maxLength = B.maxLen;
		ta.placeholder = t('placeholder');
		md.box.appendChild(ta);
		var cnt = el('div', 'tg-count', '');
		var foot = el('div', 'foot');
		foot.appendChild(cnt);
		var cancel = textBtn(t('cancel'), 'is-ghost', md.close);
		var send = textBtn(t('send'), '', function () {
			var text = smileys(ta.value.trim());
			if (!text) { Snd.play('error'); ta.focus(); return; }
			send.disabled = true;
			api('start', { user: uid, text: text }).then(function (r) {
				if (!r.success) { send.disabled = false; Snd.play('error'); toast(r.message); return; }
				Snd.play('sent');
				md.close();
				if (onSent) { onSent(r); return; }
				toast(t('sent_ok'), { action: { label: t('open_chat'), fn: function () { window.location.href = url('chat', r.chat_id); } }, ms: 7000 });
			});
		});
		foot.appendChild(cancel);
		foot.appendChild(send);
		md.box.appendChild(foot);
		ta.addEventListener('input', function () { cnt.textContent = ta.value.length > B.maxLen - 150 ? (ta.value.length + '/' + B.maxLen) : ''; });
		ta.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); send.click(); }
		});
		setTimeout(function () { ta.focus(); }, 30);
	}

	/** Profile page: "Write" opens the form instead of leaving the page. */
	function initProfile() {
		var p = B.profile;
		if (!p || isApp) { return; }
		var btn = document.querySelector('[data-msgr-write]');
		if (!btn) {
			btn = el('a', 'msgr-write-btn');
			btn.href = url('new', p.id);
			btn.setAttribute('data-msgr-write', p.id);
			btn.setAttribute('data-name', p.name);
			btn.appendChild(el('span', 'msgr-write-ico'));
			btn.appendChild(document.createTextNode(t('btn_write')));
			var h = document.querySelector('h1');
			if (h && h.parentNode) { h.parentNode.insertBefore(btn, h.nextSibling); } else {
				btn.classList.add('msgr-write-float');
				document.body.appendChild(btn);
			}
		}
		btn.addEventListener('click', function (e) {
			if (e.metaKey || e.ctrlKey || e.shiftKey) { return; }
			e.preventDefault();
			composeModal(parseInt(btn.getAttribute('data-msgr-write'), 10) || p.id, btn.getAttribute('data-name') || p.name);
		});
	}

	/* ====================================================== the messenger page */

	var NAME_COLORS = ['#e17076', '#faa774', '#a695e7', '#7bc862', '#6ec9cb', '#65aadd', '#ee7aae'];
	var EMOJI = ['😀', '😄', '😆', '😅', '😂', '😊', '😍', '😘',
		'😉', '😎', '🤔', '😮', '😢', '😭', '😡', '😴',
		'👍', '👎', '👋', '🙏', '👏', '💪', '❤️', '🔥',
		'🎉', '✨', '🙈', '🤝', '✅', '❌', '💡', '☕'];

	var A = {
		chats: [], invites: [], cid: 0, to: null, view: null, msgs: [], lastId: 0, peerRead: 0, more: false,
		divider: 0, typing: [], drafts: {}, pollTimer: null, listTimer: null, q: '', people: [], tmp: 0, sig: '', typingSent: 0
	};
	var D = {};   // dom references

	function initApp() {
		var root = document.getElementById('msgr-app');
		if (!root) { return; }
		D.root = root;
		clear(root);

		D.side = el('div', 'tg-side');
		var head = el('div', 'tg-side-head');
		D.soundBtn = iconBtn(Snd.isOn() ? 'sound' : 'soundOff', t('sound'), function () {
			var on = Snd.toggle();
			clear(D.soundBtn);
			D.soundBtn.appendChild(icon(on ? 'sound' : 'soundOff'));
			D.soundBtn.classList.toggle('is-on', on);
		}, Snd.isOn() ? 'is-on' : '');
		D.search = el('input', 'tg-input');
		D.search.type = 'search';
		D.search.placeholder = t('search');
		D.search.autocomplete = 'off';
		D.search.addEventListener('input', onSearch);
		head.appendChild(D.search);
		head.appendChild(D.soundBtn);
		D.side.appendChild(head);
		D.list = el('div', 'tg-list');
		D.side.appendChild(D.list);

		D.main = el('div', 'tg-main');
		root.appendChild(D.side);
		root.appendChild(D.main);

		onOpenChat = function (cid) { openChat(cid, true); };
		viewing = function () { return (!document.hidden && A.cid) ? A.cid : 0; };
		appHook = function (s) {
			var sig = s.unread + ':' + s.invites + ':' + (s.notifs || []).map(function (n) { return n.id + '.' + n.ts; }).join(',');
			if (sig !== A.sig) { A.sig = sig; loadChats(); }
		};

		window.addEventListener('popstate', function () { routeFromUrl(); });
		document.addEventListener('visibilitychange', function () {
			if (!document.hidden) { loadChats(); if (A.cid) { pollChat(true); } syncNow(); }
		});

		showEmpty();
		loadChats().then(function () {
			if (B.open) { openChat(B.open, false); }
			else if (B.to) { openNew({ id: B.to, name: B.toName || '' }, false); }
		});
		listLoop();
	}

	function listLoop() {
		clearTimeout(A.listTimer);
		A.listTimer = setTimeout(function () { if (!document.hidden) { loadChats(); } listLoop(); }, 6000);
	}

	function routeFromUrl() {
		var q = new URLSearchParams(window.location.search), id = parseInt(q.get('id'), 10) || 0, u = parseInt(q.get('u'), 10) || 0;
		if (q.get('m') === 'chat' && id) { openChat(id, false); }
		else if (q.get('m') === 'new' && u) { api('find', { user: u }).then(function (r) { if (r.success && r.chat_id) { openChat(r.chat_id, false); } else { openNew({ id: u, name: '' }, false); } }); }
		else { closeChat(false); }
	}

	/* ---------- chat list ---------- */

	function loadChats() {
		return api('chats', {}).then(function (r) {
			if (!r.success) { return; }
			A.chats = r.chats || [];
			A.invites = r.invites || [];
			var unread = 0;
			A.chats.forEach(function (c) { unread += c.unread; });
			setCounters(unread, A.invites.length);
			renderSide();
		});
	}

	function onSearch() {
		A.q = D.search.value.trim();
		clearTimeout(A.searchTimer);
		if (A.q.length < 2) { A.people = []; renderSide(); return; }
		renderSide();
		A.searchTimer = setTimeout(function () {
			var q = A.q;
			api('usersearch', { chat: 0, q: q }).then(function (r) {
				if (q !== A.q) { return; }
				A.people = r.success ? r.users : [];
				renderSide();
			});
		}, 250);
	}

	function lastPreview(c) {
		var s = el('div', 'tg-last');
		if (!c.last) { s.appendChild(el('em', '', t('no_messages'))); return s; }
		if (c.last.sys) { s.appendChild(el('em', '', c.last.text)); return s; }
		if (c.last.mine) { s.appendChild(el('span', 'me', t('you') + ': ')); }
		else if (c.last.from) { s.appendChild(document.createTextNode(c.last.from + ': ')); }
		s.appendChild(document.createTextNode(c.last.text));
		return s;
	}

	function renderSide() {
		var st = D.list.scrollTop;
		clear(D.list);
		var q = A.q.toLowerCase();

		if (!q && A.invites.length) {
			D.list.appendChild(el('div', 'tg-section', t('invitations')));
			A.invites.forEach(function (iv) {
				var card = el('div', 'tg-invite');
				card.appendChild(el('div', 't', t('invited_text', { by: iv.by, title: iv.title })));
				var a = el('div', 'a');
				a.appendChild(textBtn(t('btn_join'), '', function () {
					api('join', { chat: iv.chat_id }).then(function (r) {
						if (!r.success) { toast(r.message); Snd.play('error'); return; }
						loadChats().then(function () { openChat(iv.chat_id, true); });
						syncNow();
					});
				}));
				a.appendChild(textBtn(t('btn_decline'), 'is-ghost', function () {
					api('decline', { chat: iv.chat_id }).then(function () { loadChats(); syncNow(); });
				}));
				card.appendChild(a);
				D.list.appendChild(card);
			});
		}

		var shown = A.chats.filter(function (c) { return !q || c.title.toLowerCase().indexOf(q) >= 0; });
		shown.forEach(function (c) {
			var it = el('div', 'tg-item' + (c.id === A.cid ? ' is-active' : ''));
			it.setAttribute('role', 'button');
			it.tabIndex = 0;
			it.appendChild(avatar(c.seed, c.title, null, c.online));
			var body = el('div', 'tg-ibody');
			var r1 = el('div', 'tg-row');
			r1.appendChild(el('div', 'tg-name', c.title));
			r1.appendChild(el('div', 'tg-time', listTime(c.ts)));
			var r2 = el('div', 'tg-row');
			r2.appendChild(lastPreview(c));
			if (c.unread > 0 && c.id !== A.cid) { r2.appendChild(el('span', 'tg-unread' + (c.muted ? ' is-muted' : ''), c.unread > 99 ? '99+' : String(c.unread))); }
			body.appendChild(r1);
			body.appendChild(r2);
			it.appendChild(body);
			it.addEventListener('click', function () { openChat(c.id, true); });
			it.addEventListener('keydown', function (e) { if (e.key === 'Enter') { openChat(c.id, true); } });
			D.list.appendChild(it);
		});

		if (q && A.people.length) {
			D.list.appendChild(el('div', 'tg-section', t('people')));
			A.people.forEach(function (u) {
				var it = el('div', 'tg-item');
				it.tabIndex = 0;
				it.appendChild(avatar(u.id, u.name));
				var body = el('div', 'tg-ibody');
				body.appendChild(el('div', 'tg-name', u.name));
				body.appendChild(el('div', 'tg-last', t('write_first')));
				it.appendChild(body);
				var go = function () {
					api('find', { user: u.id }).then(function (r) {
						D.search.value = '';
						A.q = '';
						A.people = [];
						if (r.success && r.chat_id) { openChat(r.chat_id, true); } else { openNew(u, true); }
					});
				};
				it.addEventListener('click', go);
				it.addEventListener('keydown', function (e) { if (e.key === 'Enter') { go(); } });
				D.list.appendChild(it);
			});
		}

		if (!shown.length && !A.invites.length && !(q && A.people.length)) {
			D.list.appendChild(el('div', 'tg-hint', q ? t('nothing_found') : t('no_chats')));
		}
		D.list.scrollTop = st;
	}

	/* ---------- main pane ---------- */

	function showEmpty() {
		stopPoll();
		A.cid = 0;
		A.to = null;
		clear(D.main);
		D.root.classList.remove('is-chat-open');
		D.main.appendChild(el('div', 'tg-empty', t('select_chat')));
	}

	function stopPoll() { clearTimeout(A.pollTimer); A.pollTimer = null; }

	function saveDraft() {
		if (D.ta) {
			var key = A.cid ? 'c' + A.cid : (A.to ? 'u' + A.to.id : '');
			if (key) { A.drafts[key] = D.ta.value; }
		}
	}

	function closeChat(push) {
		saveDraft();
		showEmpty();
		if (push !== false && window.history && history.pushState) { history.pushState({}, '', url('home', '')); }
		renderSide();
	}

	function openChat(cid, push) {
		cid = parseInt(cid, 10);
		if (!cid) { return; }
		if (cid === A.cid && D.scroll) { D.root.classList.add('is-chat-open'); return; }
		saveDraft();
		stopPoll();
		A.cid = cid;
		A.to = null;
		A.msgs = [];
		A.lastId = 0;
		A.peerRead = 0;
		A.more = false;
		A.divider = 0;
		A.view = null;
		A.typing = [];
		D.root.classList.add('is-chat-open');
		buildMain();
		if (push !== false && window.history && history.pushState) { history.pushState({}, '', url('chat', cid)); }
		renderSide();
		api('history', { chat: cid }).then(function (r) {
			if (A.cid !== cid) { return; }
			if (!r.success) {
				toast(r.message || t('err_unknown'));
				closeChat(true);
				return;
			}
			A.view = r.chat;
			A.msgs = r.items;
			A.more = r.more;
			A.peerRead = r.peer_read;
			var readId = r.read_id || 0;
			for (var i = 0; i < A.msgs.length; i++) {
				if (!A.msgs[i].sys && !A.msgs[i].mine && A.msgs[i].id > readId) { A.divider = A.msgs[i].id; break; }
			}
			A.lastId = A.msgs.length ? A.msgs[A.msgs.length - 1].id : 0;
			renderHead();
			renderMessages(A.divider ? 'divider' : 'bottom');
			restoreDraft();
			if (!document.hidden) { api('read', { chat: cid }).then(function () { loadChats(); syncNow(); }); }
			pollChat(false);
			D.ta.focus();
		});
	}

	function openNew(user, push) {
		saveDraft();
		stopPoll();
		A.cid = 0;
		A.to = { id: user.id, name: user.name };
		A.msgs = [];
		A.view = null;
		D.root.classList.add('is-chat-open');
		buildMain();
		if (push !== false && window.history && history.pushState) { history.pushState({}, '', url('new', user.id)); }
		renderHead();
		renderMessages('bottom');
		restoreDraft();
		renderSide();
		D.ta.focus();
	}

	function restoreDraft() {
		var key = A.cid ? 'c' + A.cid : (A.to ? 'u' + A.to.id : '');
		D.ta.value = (key && A.drafts[key]) || '';
		autoGrow();
		updateCount();
	}

	function buildMain() {
		clear(D.main);
		D.head = el('div', 'tg-head');
		D.scroll = el('div', 'tg-scroll');
		D.typing = el('div', 'tg-typing');
		D.compose = el('div', 'tg-compose');

		D.emojiBtn = iconBtn('smile', t('emoji'), function () { toggleEmoji(); });
		D.ta = el('textarea');
		D.ta.rows = 1;
		D.ta.maxLength = B.maxLen;
		D.ta.placeholder = t('placeholder');
		D.ta.addEventListener('input', function () { autoGrow(); updateCount(); sendTyping(); });
		D.ta.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); sendMessage(); }
		});
		D.count = el('div', 'tg-count', '');
		D.send = el('button', 'tg-send');
		D.send.type = 'button';
		D.send.title = t('send');
		D.send.setAttribute('aria-label', t('send'));
		D.send.appendChild(icon('plane'));
		D.send.addEventListener('click', sendMessage);
		D.compose.appendChild(D.emojiBtn);
		D.compose.appendChild(D.ta);
		D.compose.appendChild(D.count);
		D.compose.appendChild(D.send);

		D.main.appendChild(D.head);
		D.main.appendChild(D.scroll);
		D.main.appendChild(D.typing);
		D.main.appendChild(D.compose);
		D.emoji = null;
	}

	function toggleEmoji() {
		if (D.emoji) { D.emoji.remove(); D.emoji = null; return; }
		D.emoji = el('div', 'tg-emoji');
		EMOJI.forEach(function (e) {
			var b = el('button', '', e);
			b.type = 'button';
			b.addEventListener('click', function () {
				var s = D.ta.selectionStart || D.ta.value.length, en = D.ta.selectionEnd || s;
				if (D.ta.value.length + e.length > B.maxLen) { return; }
				D.ta.value = D.ta.value.slice(0, s) + e + D.ta.value.slice(en);
				D.ta.selectionStart = D.ta.selectionEnd = s + e.length;
				D.ta.focus();
				autoGrow();
				updateCount();
			});
			D.emoji.appendChild(b);
		});
		D.main.appendChild(D.emoji);
	}

	function autoGrow() {
		D.ta.style.height = 'auto';
		D.ta.style.height = Math.min(D.ta.scrollHeight, 144) + 'px';
	}

	function updateCount() {
		var n = D.ta.value.length;
		D.count.textContent = n > B.maxLen - 120 ? (n + '/' + B.maxLen) : '';
		D.count.classList.toggle('is-over', n >= B.maxLen);
	}

	function sendTyping() {
		var now = Date.now();
		if (!A.cid || now - A.typingSent < 3000 || !D.ta.value.trim()) { return; }
		A.typingSent = now;
		api('typing', { chat: A.cid });
	}

	/* ---------- header ---------- */

	function memberLine(v) {
		var total = v.members.length, online = v.members.filter(function (m) { return m.online; }).length;
		var s = t('members_n', { n: total });
		if (online > 0) { s += ', ' + t('online_n', { n: online }); }
		return s;
	}

	function renderHead() {
		clear(D.head);
		var back = iconBtn('back', t('back'), function () { closeChat(true); }, 'back');
		D.head.appendChild(back);
		var who = el('div', 'who');
		var title, sub = '', subCls = 's', seed;
		if (A.to) {
			title = A.to.name;
			seed = A.to.id;
			sub = t('new_dialog');
		} else if (A.view) {
			title = A.view.title;
			seed = A.view.seed;
			if (A.typing.length) {
				sub = (A.view.type === 'group' ? A.typing.join(', ') + ' ' : '') + t('typing');
				subCls += ' is-typing tg-dots';
			} else if (A.view.type === 'dm' && A.view.peer) {
				sub = A.view.peer.online ? t('online') : seenText(A.view.peer.seen);
				if (A.view.peer.online) { subCls += ' is-online'; }
			} else { sub = memberLine(A.view); }
		} else { title = ''; seed = A.cid; }
		D.head.appendChild(avatar(seed, title, 'sm', A.view && A.view.peer && A.view.peer.online));
		who.appendChild(el('div', 't', title));
		D.sub = el('div', subCls, sub);
		who.appendChild(D.sub);
		who.addEventListener('click', function () { if (A.view) { infoModal(); } });
		D.head.appendChild(who);
		if (A.view) {
			D.head.appendChild(iconBtn('adduser', t('invite'), inviteModal));
			D.head.appendChild(iconBtn(A.view.muted ? 'bellOff' : 'bell', A.view.muted ? t('unmute') : t('mute'), toggleMute, A.view.muted ? '' : 'is-on'));
			D.head.appendChild(iconBtn('info', t('info'), infoModal));
		}
	}

	function toggleMute() {
		if (!A.view) { return; }
		var on = !A.view.muted;
		api('mute', { chat: A.cid, on: on ? 1 : 0 }).then(function (r) {
			if (!r.success) { toast(r.message); return; }
			A.view.muted = on;
			renderHead();
			loadChats();
			toast(on ? t('muted_on') : t('muted_off'), { ms: 2500 });
		});
	}

	/* ---------- messages ---------- */

	var TICK1 = 'M1 6.5 4.5 10 11 2.5';
	var TICK2 = 'M7 8.6 8.5 10 15 2.5';

	function ticks(m) {
		var s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		s.setAttribute('class', 'tg-ticks');
		s.setAttribute('viewBox', '0 0 17 12');
		function path(d) { var p = document.createElementNS('http://www.w3.org/2000/svg', 'path'); p.setAttribute('d', d); s.appendChild(p); }
		path(TICK1);
		if (m.id > 0 && m.id <= A.peerRead) { path(TICK2); }
		return s;
	}

	function sameGroup(a, b) {
		return !!(a && b && !a.sys && !b.sys && a.uid === b.uid && a.pending === b.pending && Math.abs(b.ts - a.ts) < 300 && dayKey(a.ts) === dayKey(b.ts));
	}

	function renderMessages(mode) {
		var sc = D.scroll;
		var prevH = sc.scrollHeight, prevTop = sc.scrollTop;
		var atBottom = prevH - prevTop - sc.clientHeight < 70;
		clear(sc);
		if (A.more) {
			sc.appendChild(textBtn(t('load_earlier'), 'is-ghost tg-more', loadEarlier));
		}
		if (!A.msgs.length) {
			sc.appendChild(el('div', 'tg-sys', A.to ? t('write_first_hint', { name: A.to.name }) : t('no_messages')));
		}
		var lastDay = '', dividerEl = null;
		A.msgs.forEach(function (m, i) {
			var prev = A.msgs[i - 1], next = A.msgs[i + 1];
			var dk = dayKey(m.ts);
			if (dk !== lastDay) { sc.appendChild(el('div', 'tg-day', dayLabel(m.ts))); lastDay = dk; }
			if (A.divider && m.id === A.divider) { dividerEl = el('div', 'tg-newdiv', t('unread_messages')); sc.appendChild(dividerEl); }
			if (m.sys) { sc.appendChild(el('div', 'tg-sys', m.text)); return; }
			var first = !sameGroup(prev, m), last = !sameGroup(m, next);
			var group = A.view && A.view.type === 'group';
			var row = el('div', 'tg-msg ' + (m.mine ? 'is-out' : 'is-in') + (first ? ' is-first' : '') + (last ? ' is-last' : '')
				+ (m.pending ? ' is-sending' : '') + (m.failed ? ' is-failed' : ''));
			if (!m.mine && group) {
				if (last) { row.appendChild(avatar(m.uid, m.name, 'xs')); } else { row.appendChild(el('span', 'spacer')); }
			}
			var bub = el('div', 'tg-bubble');
			if (!m.mine && group && first) {
				var f = el('div', 'tg-from', m.name);
				f.style.color = NAME_COLORS[m.uid % NAME_COLORS.length];
				bub.appendChild(f);
			}
			var tx = el('div', 'tg-text');
			linkify(m.text, tx);
			bub.appendChild(tx);
			var meta = el('span', 'tg-meta');
			meta.appendChild(el('span', '', m.failed ? '!' : (m.pending ? '…' : hhmm(m.ts))));
			if (m.mine && !m.pending && !m.failed) { meta.appendChild(ticks(m)); }
			bub.appendChild(meta);
			row.appendChild(bub);
			sc.appendChild(row);
		});
		if (mode === 'prepend') { sc.scrollTop = sc.scrollHeight - prevH + prevTop; }
		else if (mode === 'divider' && dividerEl) { sc.scrollTop = Math.max(0, dividerEl.offsetTop - 60); }
		else if (mode === 'bottom' || (mode === 'keep' && atBottom)) { sc.scrollTop = sc.scrollHeight; }
		else { sc.scrollTop = prevTop; }
	}

	function loadEarlier() {
		var first = A.msgs.length ? A.msgs[0].id : 0;
		if (!first || first < 0) { return; }
		var cid = A.cid;
		api('history', { chat: cid, before: first }).then(function (r) {
			if (A.cid !== cid || !r.success) { return; }
			A.msgs = r.items.concat(A.msgs);
			A.more = r.more;
			renderMessages('prepend');
		});
	}

	function mergeItems(items) {
		var added = [], known = {};
		A.msgs.forEach(function (m) { known[m.id] = true; });
		items.forEach(function (m) {
			if (known[m.id]) { return; }
			A.msgs.push(m);
			added.push(m);
			if (m.id > A.lastId) { A.lastId = m.id; }
		});
		return added;
	}

	/* ---------- polling the open chat ---------- */

	function pollChat(now) {
		clearTimeout(A.pollTimer);
		var cid = A.cid;
		if (!cid || A.to) { return; }
		var run = function () {
			api('poll', { chat: cid, after: A.lastId, read: document.hidden ? 0 : 1 }).then(function (r) {
				if (A.cid !== cid) { return; }
				if (!r.success) {
					if (r.gone) { toast(r.message || t('err_access')); closeChat(true); loadChats(); return; }
					A.pollTimer = setTimeout(function () { pollChat(true); }, 4000);
					return;
				}
				var added = mergeItems(r.items || []);
				var ticksChanged = r.peer_read !== A.peerRead;
				A.peerRead = r.peer_read;
				var typingChanged = (r.typing || []).join('|') !== A.typing.join('|');
				A.typing = r.typing || [];
				if (r.chat && r.chat.title) { A.view = r.chat; }
				if (added.length || ticksChanged) { renderMessages('keep'); }
				if (added.length || typingChanged) { renderHead(); }
				var incoming = added.filter(function (m) { return !m.mine && !m.sys; });
				if (incoming.length) {
					if (!document.hidden) { Snd.play('inchat'); loadChats(); syncNow(); }
				}
				A.pollTimer = setTimeout(function () { pollChat(true); }, document.hidden ? 6000 : 1600);
			});
		};
		if (now) { run(); } else { A.pollTimer = setTimeout(run, 1200); }
	}

	/* ---------- sending ---------- */

	function sendMessage() {
		var raw = D.ta.value;
		var text = smileys(raw.replace(/\r\n?/g, '\n').trim());
		if (!text) { D.ta.focus(); return; }
		if (text.length > B.maxLen) { Snd.play('error'); toast(t('err_long')); return; }
		if (D.emoji) { D.emoji.remove(); D.emoji = null; }
		var pend = { id: -(++A.tmp), uid: B.me, name: '', mine: true, sys: false, text: text, ts: Math.floor(Date.now() / 1000), pending: true };
		A.msgs.push(pend);
		D.ta.value = '';
		autoGrow();
		updateCount();
		saveDraft();
		renderMessages('bottom');
		Snd.play('sent');
		var cid = A.cid, to = A.to;
		var req = cid ? api('send', { chat: cid, text: text }) : api('start', { user: to.id, text: text });
		req.then(function (r) {
			A.msgs = A.msgs.filter(function (m) { return m !== pend; });
			if (!r.success) {
				Snd.play('error');
				toast(r.message || t('err_unknown'));
				if (!D.ta.value) { D.ta.value = text; autoGrow(); updateCount(); }
				if (A.cid === cid) { renderMessages('keep'); }
				return;
			}
			if (to && !cid) {
				// the dialog exists now: switch to it
				delete A.drafts['u' + to.id];
				A.to = null;
				A.cid = 0;
				if (window.history && history.replaceState) { history.replaceState({}, '', url('chat', r.chat_id)); }
				loadChats().then(function () { openChat(r.chat_id, false); });
				return;
			}
			if (A.cid !== cid) { return; }
			mergeItems([r.message]);
			renderMessages('bottom');
			loadChats();
		});
	}

	/* ---------- invite a person, chat info ---------- */

	function personRow(u, sub, click) {
		var li = el('li', click ? 'is-click' : '');
		li.appendChild(avatar(u.id, u.name, 'xs', u.online));
		var n = el('div', 'n');
		var a = el('a', '', u.name);
		a.href = url('user', u.id);
		a.addEventListener('click', function (e) { if (click) { e.preventDefault(); } });
		n.appendChild(click ? document.createTextNode(u.name) : a);
		if (sub) { n.appendChild(el('div', 's', sub)); }
		li.appendChild(n);
		if (click) { li.addEventListener('click', click); }
		return li;
	}

	function inviteModal() {
		if (!A.view) { return; }
		var md = modal(t('invite_title'));
		md.box.appendChild(el('div', 'tg-sub', ''));
		var hint = el('div', 'tg-hint', A.view.type === 'dm' ? t('invite_hint_dm') : t('invite_hint'));
		hint.style.padding = '0 0 .6rem';
		hint.style.textAlign = 'left';
		md.box.appendChild(hint);
		var inp = el('input', 'tg-input');
		inp.type = 'search';
		inp.placeholder = t('invite_search');
		inp.autocomplete = 'off';
		md.box.appendChild(inp);
		var ul = el('ul', 'tg-people');
		ul.style.marginTop = '.6rem';
		md.box.appendChild(ul);
		var timer = null, seq = 0;
		function showHint(txt) { clear(ul); if (txt) { var li = el('li'); li.appendChild(el('div', 's', txt)); ul.appendChild(li); } }
		inp.addEventListener('input', function () {
			clearTimeout(timer);
			var q = inp.value.trim();
			if (q.length < 2) { showHint(''); return; }
			var my = ++seq;
			timer = setTimeout(function () {
				api('usersearch', { chat: A.cid, q: q }).then(function (r) {
					if (my !== seq) { return; }
					clear(ul);
					if (!r.success) { showHint(r.message); return; }
					if (!r.users.length) { showHint(t('nothing_found')); return; }
					r.users.forEach(function (u) {
						ul.appendChild(personRow(u, '', function () {
							api('invite', { chat: A.cid, user: u.id }).then(function (res) {
								if (!res.success) { Snd.play('error'); toast(res.message); return; }
								Snd.play('sent');
								A.view = res.chat;
								renderHead();
								md.close();
								toast(t('invited_ok', { name: u.name }), { ms: 3500 });
								pollChat(true);
								loadChats();
							});
						}));
					});
				});
			}, 250);
		});
		var foot = el('div', 'foot');
		foot.appendChild(textBtn(t('close'), 'is-ghost', md.close));
		md.box.appendChild(foot);
		setTimeout(function () { inp.focus(); }, 30);
	}

	function infoModal() {
		var v = A.view;
		if (!v) { return; }
		var md = modal(v.title);
		var cid = A.cid;

		if (v.type === 'group') {
			var row = el('div', 'row');
			var inp = el('input', 'tg-input');
			inp.value = v.title;
			inp.maxLength = 100;
			inp.placeholder = t('group_name');
			row.appendChild(inp);
			row.appendChild(textBtn(t('save'), '', function () {
				api('rename', { chat: cid, title: inp.value }).then(function (r) {
					if (!r.success) { toast(r.message); return; }
					A.view = r.chat;
					renderHead();
					loadChats();
					pollChat(true);
					md.close();
				});
			}));
			md.box.appendChild(row);
		}

		md.box.appendChild(el('div', 'lbl', t('members_title', { n: v.members.length })));
		var ul = el('ul', 'tg-people');
		v.members.forEach(function (m) {
			var sub = m.online ? t('online') : seenText(m.seen);
			var li = personRow(m, m.me ? t('you_member') : sub, null);
			ul.appendChild(li);
		});
		md.box.appendChild(ul);
		if (v.invited && v.invited.length) {
			md.box.appendChild(el('div', 'lbl', t('invited_pending')));
			var ul2 = el('ul', 'tg-people');
			v.invited.forEach(function (m) { ul2.appendChild(personRow(m, t('waiting'), null)); });
			md.box.appendChild(ul2);
		}

		var foot = el('div', 'foot');
		foot.style.flexWrap = 'wrap';
		foot.style.justifyContent = 'flex-start';
		foot.appendChild(textBtn(t('invite'), 'is-ghost', function () { md.close(); inviteModal(); }));
		foot.appendChild(textBtn(v.muted ? t('unmute') : t('mute'), 'is-ghost', function () { md.close(); toggleMute(); }));
		if (v.type === 'group') {
			var armed = false;
			var leave = textBtn(t('leave'), 'is-danger', function () {
				if (!armed) { armed = true; leave.textContent = t('leave_confirm'); setTimeout(function () { armed = false; leave.textContent = t('leave'); }, 3000); return; }
				api('leave', { chat: cid }).then(function (r) {
					if (!r.success) { toast(r.message); return; }
					md.close();
					closeChat(true);
					loadChats();
					syncNow();
				});
			});
			foot.appendChild(leave);
		}
		foot.appendChild(textBtn(t('close'), '', md.close));
		md.box.appendChild(foot);
	}

	/* ====================================================== start */

	function init() {
		setCounters((B.sync && B.sync.unread) || 0, (B.sync && B.sync.invites) || 0);
		applySync(B.sync);
		initProfile();
		if (isApp) { initApp(); }
		syncLoop();
		document.addEventListener('visibilitychange', function () { if (!document.hidden) { syncNow(); } syncLoop(); });
	}

	init();
})();

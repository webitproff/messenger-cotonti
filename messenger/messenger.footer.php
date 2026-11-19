<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=footer.tags
Order=50
[END_COT_EXT]
==================== */

/**
 * File: messenger.footer.php
 * Path: plugins/messenger/messenger.footer.php
 * Loads the browser code on every full page: popups, counters, sounds, the "write" button on profiles.
 * The code is appended to {FOOTER_RC}, so the theme footer must output it (standard Cotonti themes do).
 */
defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] > 0 && !defined('COT_ADMIN') && cot_auth('plug', 'messenger', 'R')) {
	require_once cot_incfile('messenger', 'plug');
	msgr_lang_load();

	$msgrExtra = Cot::$sys['msgr_app'] ?? ['page' => 'widget'];
	if (!empty(Cot::$sys['msgr_profile'])) {
		$msgrExtra['profile'] = Cot::$sys['msgr_profile'];
	}
	$msgrJson = json_encode(
		msgr_boot((int) Cot::$usr['id'], $msgrExtra),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	);
	Cot::$out['footer_rc'] = (isset(Cot::$out['footer_rc']) ? Cot::$out['footer_rc'] : '')
		. '<script>window.MSGR_BOOT=' . $msgrJson . ';</script>' . "\n"
		. '<script src="' . Cot::$cfg['plugins_dir'] . '/messenger/js/messenger.js?v=1.0.0"></script>' . "\n";
	unset($msgrExtra, $msgrJson);
}

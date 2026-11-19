<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.details.tags
Order=50
[END_COT_EXT]
==================== */

/**
 * File: messenger.users.php
 * Path: plugins/messenger/messenger.users.php
 * "Write a message" button on a user's profile.
 * Tags for users.details.tpl: {USERS_DETAILS_MESSENGER} (the button) and {USERS_DETAILS_MESSENGER_URL}.
 * Without the tag the script places the button next to the page title.
 */
defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] > 0 && isset($urr, $t) && is_object($t) && (int) $urr['user_id'] !== (int) Cot::$usr['id'] && cot_auth('plug', 'messenger', 'W')) {
	require_once cot_incfile('messenger', 'plug');
	msgr_lang_load();
	if (msgr_user_ok((int) $urr['user_id'])) {
		$msgrUrl = msgr_url('new', ['u' => (int) $urr['user_id']]);
		Cot::$sys['msgr_profile'] = ['id' => (int) $urr['user_id'], 'name' => (string) $urr['user_name']];
		$t->assign([
			'USERS_DETAILS_MESSENGER'     => '<a class="msgr-write-btn" href="' . htmlspecialchars($msgrUrl) . '" data-msgr-write="' . (int) $urr['user_id']
				. '" data-name="' . htmlspecialchars((string) $urr['user_name']) . '"><span class="msgr-write-ico" aria-hidden="true"></span>'
				. htmlspecialchars(Cot::$L['msgr_btn_write']) . '</a>',
			'USERS_DETAILS_MESSENGER_URL' => $msgrUrl,
		]);
		unset($msgrUrl);
	}
}

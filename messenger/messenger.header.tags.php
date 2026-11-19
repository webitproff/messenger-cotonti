<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Order=50
[END_COT_EXT]
==================== */

/**
 * File: messenger.header.tags.php
 * Path: plugins/messenger/messenger.header.tags.php
 * Header link with the unread counter.
 * Put {HEADER_MESSENGER} into header.tpl of your theme to place it. Without the tag the script adds a round launcher button.
 */
defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] > 0 && !defined('COT_ADMIN') && isset($t) && is_object($t) && cot_auth('plug', 'messenger', 'R')) {
	require_once cot_incfile('messenger', 'plug');
	msgr_lang_load();
	$t->assign([
		'HEADER_MESSENGER'     => msgr_header_link((int) Cot::$usr['id']),
		'HEADER_MESSENGER_URL' => msgr_url('home'),
	]);
}

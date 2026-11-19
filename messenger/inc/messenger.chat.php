<?php
/**
 * File: messenger.chat.php
 * Path: plugins/messenger/inc/messenger.chat.php
 * Mode "chat": a particular chat (?e=messenger&m=chat&id=N). Non-members are sent to the list.
 */
defined('COT_CODE') or die('Wrong URL.');

$msgrCid = (int) cot_import('id', 'G', 'INT');
if (Cot::$usr['id'] > 0 && $msgrCid > 0) {
	$msgrMem = msgr_member($msgrCid, (int) Cot::$usr['id']);
	if (!$msgrMem) {
		cot_message(Cot::$L['msgr_err_access'], 'error');
		cot_redirect(msgr_url('home', [], true));
	}
	if ($msgrMem['state'] === 'invited') {
		$msgrCid = 0;   // the list shows the invitation with the "join" button
	}
}

$msgrApp = ['page' => 'app', 'open' => $msgrCid, 'to' => 0];
require cot_incfile('messenger', 'plug', 'app');

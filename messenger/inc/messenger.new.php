<?php
/**
 * File: messenger.new.php
 * Path: plugins/messenger/inc/messenger.new.php
 * Mode "new": write to a user (?e=messenger&m=new&u=USERID). An existing dialog is opened instead.
 */
defined('COT_CODE') or die('Wrong URL.');

$msgrTo = (int) cot_import('u', 'G', 'INT');
if (Cot::$usr['id'] > 0 && $msgrTo > 0) {
	$msgrMe = (int) Cot::$usr['id'];
	if ($msgrTo === $msgrMe || !msgr_user_ok($msgrTo)) {
		cot_message(Cot::$L['msgr_err_no_user'], 'error');
		cot_redirect(msgr_url('home', [], true));
	}
	$msgrExisting = msgr_find_dm($msgrMe, $msgrTo);
	if ($msgrExisting > 0) {
		cot_redirect(msgr_url('chat', ['id' => $msgrExisting], true));
	}
	$msgrToName = msgr_names([$msgrTo])[$msgrTo]['name'];
} else {
	$msgrTo     = 0;
	$msgrToName = '';
}

$msgrApp = ['page' => 'app', 'open' => 0, 'to' => $msgrTo, 'toName' => $msgrToName];
require cot_incfile('messenger', 'plug', 'app');

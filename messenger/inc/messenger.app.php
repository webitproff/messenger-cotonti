<?php
/**
 * File: messenger.app.php
 * Path: plugins/messenger/inc/messenger.app.php
 * Shared renderer for the modes home / chat / new: outputs the app shell, the browser code builds the UI.
 * Expects $msgrApp (array of page specific JS config) from the mode file.
 */
defined('COT_CODE') or die('Wrong URL.');

Cot::$out['subtitle'] = Cot::$L['msgr_title'];
Cot::$sys['msgr_app'] = $msgrApp;

$msgrTpl = new XTemplate(cot_tplfile(['messenger', 'app'], 'plug'));
$msgrTpl->assign([
	'MSGR_LOGGED'    => (Cot::$usr['id'] > 0),
	'MSGR_LOGIN_URL' => cot_url('users', 'm=auth'),
]);
cot_display_messages($msgrTpl);
$msgrTpl->parse('MAIN');

// Cotonti >= 0.9.26 plugin.php reads $pluginBody and keeps its own $t; older cores read $plugin_body
$msgrHtml = $msgrTpl->text('MAIN');
$pluginBody  = (isset($pluginBody) ? $pluginBody : '') . $msgrHtml;
$plugin_body = (isset($plugin_body) ? $plugin_body : '') . $msgrHtml;

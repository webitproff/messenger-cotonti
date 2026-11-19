<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
Order=10
[END_COT_EXT]
==================== */

/**
 * File: messenger.php
 * Path: plugins/messenger/messenger.php
 * Standalone entry + mode router
 *
 * Modes (files in plugins/messenger/inc/, template in plugins/messenger/tpl/):
 *   home (default) - ?e=messenger                 chat list + empty dialog pane
 *   chat           - ?e=messenger&m=chat&id=N     open chat N
 *   new            - ?e=messenger&m=new&u=USERID  write to a user (the dialog is created with the first message)
 */

defined('COT_CODE') or die('Wrong URL.');

Cot::$env['location'] = 'messenger';

require_once cot_incfile('messenger', 'plug');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'messenger');
cot_block($usr['auth_read']);

msgr_lang_load();

$m = cot_import('m', 'G', 'ALP');
if (empty($m) || !in_array($m, ['home', 'chat', 'new'], true)) {
	$m = 'home';
}

require_once cot_incfile('messenger', 'plug', $m);

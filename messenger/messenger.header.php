<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.first
Order=50
[END_COT_EXT]
==================== */

/**
 * File: messenger.header.php
 * Path: plugins/messenger/messenger.header.php
 * Adds the stylesheet to every full page for signed-in users with access.
 */
defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] > 0 && !empty(Cot::$sys['displayHeader']) && !defined('COT_ADMIN') && cot_auth('plug', 'messenger', 'R')) {
	Cot::$out['head'] = (isset(Cot::$out['head']) ? Cot::$out['head'] : '')
		. '<link rel="stylesheet" href="' . Cot::$cfg['plugins_dir'] . '/messenger/css/messenger.css?v=1.0.0" />' . "\n";
}

<?php
/**
 * File: messenger.home.php
 * Path: plugins/messenger/inc/messenger.home.php
 * Mode "home": chat list, no dialog selected.
 */
defined('COT_CODE') or die('Wrong URL.');

$msgrApp = ['page' => 'app', 'open' => 0, 'to' => 0];
require cot_incfile('messenger', 'plug', 'app');

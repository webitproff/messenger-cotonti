<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * File: messenger.admin.php
 * Path: plugins/messenger/messenger.admin.php
 * Admin tools (Admin -> Other -> Messenger): totals, chats list with filter, delete a chat,
 * clean up old messages / empty chats / closed popups.
 */
defined('COT_CODE') or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'messenger', 'RWA');
cot_block($usr['isadmin']);

require_once cot_incfile('messenger', 'plug');
msgr_lang_load();

$db = Cot::$db;
$L  = Cot::$L;

$type = (string) cot_import('type', 'G', 'ALP');
if (!in_array($type, ['dm', 'group'], true)) {
	$type = '';
}
$a  = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');

$base = 'm=other&p=messenger';

if ($a === 'delete' && $id > 0) {
	cot_check_xg();
	msgr_delete_chat($id);
	cot_message($L['msgr_admin_deleted'], 'ok');
	cot_redirect(cot_url('admin', $base . ($type ? '&type=' . $type : ''), '', true));
}

if ($a === 'old') {
	cot_check_xg();
	$days = (int) cot_import('days', 'G', 'INT');
	$days = in_array($days, [30, 90, 180, 365], true) ? $days : 365;
	$n = $db->delete(msgr_t('messages'), 'created_ts < ?', [msgr_now() - $days * 86400]);
	cot_message($L['msgr_admin_old_done'] . ': ' . (int) $n, 'ok');
	cot_redirect(cot_url('admin', $base, '', true));
}

if ($a === 'empty') {
	cot_check_xg();
	$ids = $db->query('SELECT chat_id FROM ' . msgr_t('chats') . ' c WHERE NOT EXISTS (SELECT 1 FROM ' . msgr_t('messages') . ' m WHERE m.chat_id = c.chat_id)')->fetchAll();
	foreach ($ids as $r) {
		msgr_delete_chat((int) $r['chat_id']);
	}
	cot_message($L['msgr_admin_empty_done'] . ': ' . count($ids), 'ok');
	cot_redirect(cot_url('admin', $base, '', true));
}

if ($a === 'notifs') {
	cot_check_xg();
	$n = $db->delete(msgr_t('notifs'), 'closed = 1 OR updated_ts < ?', [msgr_now() - 30 * 86400]);
	cot_message($L['msgr_admin_notifs_done'] . ': ' . (int) $n, 'ok');
	cot_redirect(cot_url('admin', $base, '', true));
}

Cot::$out['subtitle'] = $L['msgr_admin_title'];

$where = $type ? ('WHERE c.chat_type = ' . $db->quote($type)) : '';
$total = (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('chats') . " c $where")->fetchColumn();

$perPage = 30;
list($pn, $d, $d_url) = cot_import_pagenav('d', $perPage);

$rows = $db->query(
	'SELECT c.*, (SELECT COUNT(*) FROM ' . msgr_t('messages') . ' m WHERE m.chat_id = c.chat_id) AS msgs,
	        (SELECT COUNT(*) FROM ' . msgr_t('members') . " mb WHERE mb.chat_id = c.chat_id AND mb.state = 'active') AS mems
	 FROM " . msgr_t('chats') . " c $where ORDER BY c.last_ts DESC LIMIT " . (int) $d . ', ' . (int) $perPage
)->fetchAll();

$pagenav = cot_pagenav('admin', $base . ($type ? '&type=' . $type : ''), $d, $total, $perPage);

$stat = [
	'chats'  => (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('chats'))->fetchColumn(),
	'groups' => (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('chats') . " WHERE chat_type = 'group'")->fetchColumn(),
	'msgs'   => (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('messages') . " WHERE msg_type = 'text'")->fetchColumn(),
	'day'    => (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('messages') . " WHERE msg_type = 'text' AND created_ts > ?", [msgr_now() - 86400])->fetchColumn(),
	'invites' => (int) $db->query('SELECT COUNT(*) FROM ' . msgr_t('members') . " WHERE state = 'invited'")->fetchColumn(),
];

$top = $db->query(
	'SELECT u.user_id, u.user_name, COUNT(*) AS n FROM ' . msgr_t('messages') . ' m INNER JOIN ' . msgr_users_t() . " u ON u.user_id = m.user_id
	 WHERE m.msg_type = 'text' GROUP BY u.user_id, u.user_name ORDER BY n DESC LIMIT 10"
)->fetchAll();

$mt = new XTemplate(cot_tplfile(['messenger', 'admin'], 'plug'));
$xk = Cot::$sys['xk'];
$mt->assign([
	'ADMIN_TITLE'         => $L['msgr_admin_title'],
	'FILTER_ALL_URL'      => cot_url('admin', $base),
	'FILTER_DM_URL'       => cot_url('admin', $base . '&type=dm'),
	'FILTER_GROUP_URL'    => cot_url('admin', $base . '&type=group'),
	'FILTER_ALL_CLASS'    => $type === '' ? 'active' : '',
	'FILTER_DM_CLASS'     => $type === 'dm' ? 'active' : '',
	'FILTER_GROUP_CLASS'  => $type === 'group' ? 'active' : '',
	'STAT_CHATS'          => $stat['chats'],
	'STAT_GROUPS'         => $stat['groups'],
	'STAT_MSGS'           => $stat['msgs'],
	'STAT_DAY'            => $stat['day'],
	'STAT_INVITES'        => $stat['invites'],
	'OLD_90_URL'          => cot_url('admin', $base . '&a=old&days=90&x=' . $xk),
	'OLD_365_URL'         => cot_url('admin', $base . '&a=old&days=365&x=' . $xk),
	'EMPTY_URL'           => cot_url('admin', $base . '&a=empty&x=' . $xk),
	'NOTIFS_URL'          => cot_url('admin', $base . '&a=notifs&x=' . $xk),
	'PAGENAV_PAGES'       => $pagenav['main'] ?? '',
	'PAGENAV_PREV'        => $pagenav['prev'] ?? '',
	'PAGENAV_NEXT'        => $pagenav['next'] ?? '',
]);

$ownerIds = array_map(static fn($r) => (int) $r['owner_id'], $rows);
$names    = msgr_names($ownerIds);
foreach ($rows as $r) {
	$mt->assign([
		'CHAT_ID'      => (int) $r['chat_id'],
		'CHAT_TYPE'    => $r['chat_type'] === 'group' ? $L['msgr_admin_t_group'] : $L['msgr_admin_t_dm'],
		'CHAT_TITLE'   => $r['title'] !== '' ? htmlspecialchars($r['title']) : '—',
		'CHAT_OWNER'   => htmlspecialchars($names[(int) $r['owner_id']]['name']),
		'CHAT_MEMBERS' => (int) $r['mems'],
		'CHAT_MSGS'    => (int) $r['msgs'],
		'CHAT_LAST'    => $r['last_ts'] > 0 ? cot_date('datetime_short', (int) $r['last_ts']) : '—',
		'CHAT_DELETE_URL' => cot_url('admin', $base . '&a=delete&id=' . (int) $r['chat_id'] . '&type=' . $type . '&x=' . $xk),
	]);
	$mt->parse('MAIN.CHAT_ROW');
}
if (!$rows) {
	$mt->parse('MAIN.EMPTY');
}
foreach ($top as $r) {
	$mt->assign(['TOP_NAME' => htmlspecialchars((string) $r['user_name']), 'TOP_MSGS' => (int) $r['n'], 'TOP_UID' => (int) $r['user_id'], 'TOP_URL' => cot_url('users', ['m' => 'details', 'id' => (int) $r['user_id']])]);
	$mt->parse('MAIN.TOP_ROW');
}

cot_display_messages($mt);
$mt->parse('MAIN');
$html = $mt->text('MAIN');
$plugin_body = (isset($plugin_body) ? $plugin_body : '') . $html;

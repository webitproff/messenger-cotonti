<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL.');

/* File: messenger.ajax.php */
/* Path: plugins/messenger/messenger.ajax.php */

require_once cot_incfile('messenger', 'plug');
msgr_lang_load();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (Cot::$usr['id'] == 0) {
    echo json_encode(['success' => false, 'message' => Cot::$L['msgr_err_auth']]);
    exit;
}
if (!cot_auth('plug', 'messenger', 'R')) {
    echo json_encode(['success' => false, 'message' => Cot::$L['msgr_err_access']]);
    exit;
}

$x = cot_import('x', 'P', 'ALP');
if ($x === null || ($x !== Cot::$sys['xk'] && (!isset(Cot::$sys['xk_prev']) || $x !== Cot::$sys['xk_prev']))) {
    echo json_encode(['success' => false, 'message' => 'CSRF token mismatch', 'csrf' => true]);
    exit;
}

$action = (string) cot_import('action', 'P', 'ALP');
$uid    = (int) Cot::$usr['id'];
$cid    = (int) cot_import('chat', 'P', 'INT');
$write  = cot_auth('plug', 'messenger', 'W');

try {
    switch ($action) {
        case 'sync':
            $data = msgr_sync($uid);
            break;
        case 'chats':
            $data = msgr_list_chats($uid);
            break;
        case 'history':
            $data = msgr_history($uid, $cid, (int) cot_import('before', 'P', 'INT'));
            break;
        case 'poll':
            $data = msgr_poll($uid, $cid, (int) cot_import('after', 'P', 'INT'), (int) cot_import('read', 'P', 'INT') === 1);
            break;
        case 'read':
            msgr_mark_read($uid, $cid);
            $data = ['success' => true];
            break;
        case 'typing':
            $data = msgr_typing($uid, $cid);
            break;
        case 'closenotif':
            $data = msgr_close_notif($uid, (int) cot_import('id', 'P', 'INT'));
            break;
        case 'usersearch':
            $data = msgr_user_search($uid, $cid, isset($_POST['q']) ? (string) $_POST['q'] : '');
            break;
        case 'find':
            $found = msgr_find_dm($uid, (int) cot_import('user', 'P', 'INT'));
            $data  = ['success' => true, 'chat_id' => $found];
            break;
        case 'send':
        case 'start':
        case 'invite':
        case 'join':
        case 'decline':
        case 'leave':
        case 'mute':
        case 'rename':
            if (!$write) {
                $data = msgr_fail('err_access');
                break;
            }
            $text = isset($_POST['text']) ? (string) $_POST['text'] : '';
            if ($action === 'send') {
                $data = msgr_send($uid, $cid, $text);
            } elseif ($action === 'start') {
                $data = msgr_start($uid, (int) cot_import('user', 'P', 'INT'), $text);
            } elseif ($action === 'invite') {
                $data = msgr_invite($uid, $cid, (int) cot_import('user', 'P', 'INT'));
            } elseif ($action === 'join') {
                $data = msgr_join($uid, $cid);
            } elseif ($action === 'decline') {
                $data = msgr_decline($uid, $cid);
            } elseif ($action === 'leave') {
                $data = msgr_leave($uid, $cid);
            } elseif ($action === 'mute') {
                $data = msgr_mute($uid, $cid, (int) cot_import('on', 'P', 'INT') === 1);
            } else {
                $data = msgr_rename($uid, $cid, isset($_POST['title']) ? (string) $_POST['title'] : '');
            }
            break;
        default:
            $data = msgr_fail('err_unknown');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => Cot::$L['msgr_err_unknown']]);
}

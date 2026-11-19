<?php
/**
 * File: messenger.functions.php
 * Path: plugins/messenger/inc/messenger.functions.php
 *
 * Messenger core: chats, members, messages, unread counters, invitations, popup notifications.
 * Everything the browser needs goes through the functions at the bottom (msgr_sync, msgr_boot...).
 * Timestamps are unix seconds (UTC); the browser formats them in the visitor's time zone.
 */
defined('COT_CODE') or die('Wrong URL.');

/* ---------------------------------------------------------------- helpers */

function msgr_cfg(string $key, $default)
{
    $v = Cot::$cfg['plugin']['messenger'][$key] ?? null;
    return ($v === null || $v === '') ? $default : $v;
}

function msgr_lang_load(): void
{
    if (!isset(Cot::$L['msgr_title'])) {
        $L = [];
        require cot_langfile('messenger', 'plug');
        Cot::$L = array_merge(Cot::$L, $L);
    }
}

function msgr_msg(string $key): string
{
    return Cot::$L['msgr_' . $key] ?? $key;
}

function msgr_fail(string $key, array $extra = []): array
{
    return array_merge(['success' => false, 'message' => msgr_msg($key)], $extra);
}

function msgr_now(): int
{
    return time();
}

function msgr_t(string $name): string
{
    return Cot::$db_x . 'msgr_' . $name;
}

function msgr_users_t(): string
{
    return '`' . Cot::$db_x . 'users`';
}

function msgr_max_len(): int
{
    return max(1, min(1024, (int) msgr_cfg('max_len', 1024)));
}

function msgr_transaction(callable $fn)
{
    $db = Cot::$db;
    $db->beginTransaction();
    try {
        $res = $fn();
        $db->commit();
        return $res;
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}

/** @return array<int,array{name:string,lastlog:int}> id => info (unknown ids get a placeholder) */
function msgr_names(array $ids): array
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn($i) => $i > 0)));
    $out = [];
    if ($ids) {
        $in = implode(',', $ids);
        foreach (Cot::$db->query('SELECT user_id, user_name, user_lastlog FROM ' . msgr_users_t() . " WHERE user_id IN ($in)")->fetchAll() as $r) {
            $out[(int) $r['user_id']] = ['name' => (string) $r['user_name'], 'lastlog' => (int) $r['user_lastlog']];
        }
    }
    foreach ($ids as $i) {
        if (!isset($out[$i])) {
            $out[$i] = ['name' => '#' . $i, 'lastlog' => 0];
        }
    }
    return $out;
}

function msgr_user_ok(int $id): bool
{
    if ($id <= 0) {
        return false;
    }
    $grp = Cot::$db->query('SELECT user_maingrp FROM ' . msgr_users_t() . ' WHERE user_id = ?', [$id])->fetchColumn();
    if ($grp === false) {
        return false;
    }
    $bad = [defined('COT_GROUP_INACTIVE') ? COT_GROUP_INACTIVE : 2, defined('COT_GROUP_BANNED') ? COT_GROUP_BANNED : 3];
    return !in_array((int) $grp, $bad, true);
}

function msgr_clean_text(string $s): string
{
    $s = str_replace(["\r\n", "\r"], "\n", $s);
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
    $s = preg_replace('/[ \t]+/u', ' ', $s) ?? '';
    $s = preg_replace('/ ?\n ?/u', "\n", $s) ?? '';
    $s = preg_replace('/\n{3,}/', "\n\n", $s) ?? '';
    return trim($s);
}

function msgr_trim(string $s, int $n): string
{
    $s = trim(preg_replace('/\s+/u', ' ', $s) ?? '');
    return mb_strlen($s) > $n ? rtrim(mb_substr($s, 0, $n - 1)) . '…' : $s;
}

function msgr_url(string $mode = 'home', array $params = [], bool $raw = false): string
{
    $p = ['e' => 'messenger'];
    if ($mode !== 'home') {
        $p['m'] = $mode;
    }
    return cot_url('plug', array_merge($p, $params), '', $raw);
}

/* ---------------------------------------------------------------- chats and members */

function msgr_chat(int $cid): ?array
{
    $r = Cot::$db->query('SELECT * FROM ' . msgr_t('chats') . ' WHERE chat_id = ?', [$cid])->fetch();
    return $r ?: null;
}

function msgr_member(int $cid, int $uid): ?array
{
    $r = Cot::$db->query('SELECT * FROM ' . msgr_t('members') . ' WHERE chat_id = ? AND user_id = ?', [$cid, $uid])->fetch();
    return $r ?: null;
}

/** Active members: [user_id => row] */
function msgr_members(int $cid, string $state = 'active'): array
{
    $out = [];
    foreach (Cot::$db->query('SELECT * FROM ' . msgr_t('members') . ' WHERE chat_id = ? AND state = ? ORDER BY joined_ts, user_id', [$cid, $state])->fetchAll() as $r) {
        $out[(int) $r['user_id']] = $r;
    }
    return $out;
}

function msgr_dm_key(int $a, int $b): string
{
    return min($a, $b) . '_' . max($a, $b);
}

function msgr_find_dm(int $a, int $b): int
{
    $id = Cot::$db->query('SELECT chat_id FROM ' . msgr_t('chats') . ' WHERE dm_key = ?', [msgr_dm_key($a, $b)])->fetchColumn();
    return $id === false ? 0 : (int) $id;
}

function msgr_create_dm(int $a, int $b): int
{
    $db  = Cot::$db;
    $now = msgr_now();
    try {
        $db->insert(msgr_t('chats'), [
            'chat_type' => 'dm', 'title' => '', 'owner_id' => $a, 'dm_key' => msgr_dm_key($a, $b),
            'last_msg_id' => 0, 'last_ts' => $now, 'created_ts' => $now,
        ]);
    } catch (Throwable $e) {
        $existing = msgr_find_dm($a, $b);   // somebody created it a moment ago (unique dm_key)
        if ($existing > 0) {
            return $existing;
        }
        throw $e;
    }
    $cid = (int) $db->lastInsertId();
    $db->insert(msgr_t('members'), ['chat_id' => $cid, 'user_id' => $a, 'role' => 'owner', 'state' => 'active', 'joined_ts' => $now]);
    $db->insert(msgr_t('members'), ['chat_id' => $cid, 'user_id' => $b, 'role' => 'member', 'state' => 'active', 'joined_ts' => $now]);
    return $cid;
}

function msgr_delete_chat(int $cid): void
{
    $db = Cot::$db;
    foreach (['notifs', 'messages', 'members'] as $t) {
        $db->delete(msgr_t($t), 'chat_id = ?', [$cid]);
    }
    $db->delete(msgr_t('chats'), 'chat_id = ?', [$cid]);
}

/** Display title for a viewer. */
function msgr_title(array $chat, array $members, array $names, int $uid): string
{
    if ($chat['chat_type'] === 'dm') {
        foreach ($members as $id => $m) {
            if ($id !== $uid) {
                return $names[$id]['name'] ?? ('#' . $id);
            }
        }
        return msgr_msg('title');
    }
    if ($chat['title'] !== '') {
        return $chat['title'];
    }
    $list = [];
    foreach ($members as $id => $m) {
        $list[] = $names[$id]['name'] ?? ('#' . $id);
        if (count($list) >= 3) {
            break;
        }
    }
    return implode(', ', $list) ?: msgr_msg('group');
}

/* ---------------------------------------------------------------- messages */

function msgr_sys_text(string $body, array $names): string
{
    $d = json_decode($body, true);
    if (!is_array($d) || empty($d['k'])) {
        return $body;
    }
    $n = static fn($k) => $names[(int) ($d[$k] ?? 0)]['name'] ?? '?';
    switch ($d['k']) {
        case 'invite':
            return strtr(msgr_msg('sys_invite'), ['{a}' => $n('a'), '{b}' => $n('b')]);
        case 'join':
            return strtr(msgr_msg('sys_join'), ['{a}' => $n('a')]);
        case 'left':
            return strtr(msgr_msg('sys_left'), ['{a}' => $n('a')]);
        case 'declined':
            return strtr(msgr_msg('sys_declined'), ['{a}' => $n('a')]);
        case 'rename':
            return strtr(msgr_msg('sys_rename'), ['{a}' => $n('a'), '{t}' => (string) ($d['t'] ?? '')]);
        case 'group':
            return strtr(msgr_msg('sys_group'), ['{a}' => $n('a')]);
    }
    return '';
}

function msgr_item(array $r, array $names, int $uid): array
{
    $sys = $r['msg_type'] === 'system';
    return [
        'id'   => (int) $r['msg_id'],
        'uid'  => (int) $r['user_id'],
        'name' => $sys ? '' : ($names[(int) $r['user_id']]['name'] ?? ('#' . $r['user_id'])),
        'mine' => !$sys && (int) $r['user_id'] === $uid,
        'sys'  => $sys,
        'text' => $sys ? msgr_sys_text((string) $r['body'], $names) : (string) $r['body'],
        'ts'   => (int) $r['created_ts'],
    ];
}

/** Inserts a message and moves the chat's "last message" pointer. Must run inside a transaction. */
function msgr_insert_message(int $cid, int $uid, string $type, string $body): array
{
    $db  = Cot::$db;
    $now = msgr_now();
    $db->insert(msgr_t('messages'), ['chat_id' => $cid, 'user_id' => $uid, 'msg_type' => $type, 'body' => $body, 'created_ts' => $now]);
    $mid = (int) $db->lastInsertId();
    $db->update(msgr_t('chats'), ['last_msg_id' => $mid, 'last_ts' => $now], 'chat_id = ?', [$cid]);
    if ($uid > 0) {
        // the sender has obviously read everything up to his own message
        $db->update(msgr_t('members'), ['last_read_id' => $mid, 'typing_ts' => 0], 'chat_id = ? AND user_id = ?', [$cid, $uid]);
        msgr_clear_notifs($uid, $cid, 'message');
    }
    return ['msg_id' => $mid, 'chat_id' => $cid, 'user_id' => $uid, 'msg_type' => $type, 'body' => $body, 'created_ts' => $now];
}

function msgr_system_message(int $cid, array $payload): array
{
    return msgr_insert_message($cid, 0, 'system', json_encode($payload, JSON_UNESCAPED_UNICODE));
}

/** One popup row per (user, chat, kind). A new message reopens a closed one with a fresh counter. */
function msgr_upsert_notif(int $uid, int $cid, string $kind, int $from, string $preview): void
{
    $db  = Cot::$db;
    $t   = msgr_t('notifs');
    $now = msgr_now();
    $row = $db->query("SELECT notif_id, cnt, closed FROM {$t} WHERE user_id = ? AND chat_id = ? AND kind = ?", [$uid, $cid, $kind])->fetch();
    if ($row) {
        $cnt = ((int) $row['closed'] === 1 || $kind === 'invite') ? 1 : ((int) $row['cnt'] + 1);
        $db->update($t, ['from_id' => $from, 'preview' => $preview, 'cnt' => $cnt, 'closed' => 0, 'updated_ts' => $now], 'notif_id = ?', [(int) $row['notif_id']]);
    } else {
        $db->insert($t, ['user_id' => $uid, 'chat_id' => $cid, 'kind' => $kind, 'from_id' => $from, 'preview' => $preview,
            'cnt' => 1, 'closed' => 0, 'created_ts' => $now, 'updated_ts' => $now]);
    }
}

function msgr_clear_notifs(int $uid, int $cid, ?string $kind = null): void
{
    $sql = 'chat_id = ? AND user_id = ?' . ($kind ? ' AND kind = ?' : '');
    $par = $kind ? [$cid, $uid, $kind] : [$cid, $uid];
    Cot::$db->delete(msgr_t('notifs'), $sql, $par);
}

function msgr_flood_wait(int $uid): int
{
    $gap = (int) msgr_cfg('flood_seconds', 1);
    if ($gap <= 0) {
        return 0;
    }
    $last = (int) Cot::$db->query('SELECT MAX(created_ts) FROM ' . msgr_t('messages') . " WHERE user_id = ? AND msg_type = 'text'", [$uid])->fetchColumn();
    $wait = $gap - (msgr_now() - $last);
    return $last > 0 && $wait > 0 ? $wait : 0;
}

/** Validates the text; returns [cleanText, errorArray|null]. */
function msgr_check_text(string $text): array
{
    $text = msgr_clean_text($text);
    if ($text === '') {
        return ['', msgr_fail('err_empty')];
    }
    if (mb_strlen($text) > msgr_max_len()) {
        return ['', msgr_fail('err_long')];
    }
    return [$text, null];
}

function msgr_send(int $uid, int $cid, string $text): array
{
    [$text, $err] = msgr_check_text($text);
    if ($err) {
        return $err;
    }
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access');
    }
    $wait = msgr_flood_wait($uid);
    if ($wait > 0) {
        return msgr_fail('err_flood', ['wait' => $wait]);
    }
    return msgr_transaction(function () use ($uid, $cid, $text) {
        $row = msgr_insert_message($cid, $uid, 'text', $text);
        msgr_fan_out($cid, $uid, $text);
        $names = msgr_names([$uid]);
        return ['success' => true, 'chat_id' => $cid, 'message' => msgr_item($row, $names, $uid)];
    });
}

/** Popup + counters for every other active member (muted chats get the counter but no popup). */
function msgr_fan_out(int $cid, int $from, string $text): void
{
    $preview = msgr_trim($text, 140);
    foreach (msgr_members($cid) as $id => $m) {
        if ($id === $from || (int) $m['muted'] === 1) {
            continue;
        }
        msgr_upsert_notif($id, $cid, 'message', $from, $preview);
    }
}

/** First message to a user: creates the dialog (once) and sends. */
function msgr_start(int $uid, int $to, string $text): array
{
    if ($to === $uid) {
        return msgr_fail('err_self');
    }
    if (!msgr_user_ok($to)) {
        return msgr_fail('err_no_user');
    }
    [$text, $err] = msgr_check_text($text);
    if ($err) {
        return $err;
    }
    $wait = msgr_flood_wait($uid);
    if ($wait > 0) {
        return msgr_fail('err_flood', ['wait' => $wait]);
    }
    return msgr_transaction(function () use ($uid, $to, $text) {
        $cid = msgr_find_dm($uid, $to);
        if ($cid === 0) {
            $cid = msgr_create_dm($uid, $to);
        }
        $row = msgr_insert_message($cid, $uid, 'text', $text);
        msgr_fan_out($cid, $uid, $text);
        $names = msgr_names([$uid]);
        return ['success' => true, 'chat_id' => $cid, 'message' => msgr_item($row, $names, $uid)];
    });
}

/* ---------------------------------------------------------------- reading */

function msgr_unread_total(int $uid): int
{
    return (int) Cot::$db->query(
        'SELECT COUNT(*) FROM ' . msgr_t('messages') . ' m INNER JOIN ' . msgr_t('members') . " mb
           ON mb.chat_id = m.chat_id AND mb.user_id = ? AND mb.state = 'active'
         WHERE m.msg_id > mb.last_read_id AND m.user_id <> ? AND m.msg_type = 'text'",
        [$uid, $uid]
    )->fetchColumn();
}

function msgr_invites_total(int $uid): int
{
    return (int) Cot::$db->query('SELECT COUNT(*) FROM ' . msgr_t('members') . " WHERE user_id = ? AND state = 'invited'", [$uid])->fetchColumn();
}

/** Marks everything read, closes the message popup of this chat. Returns the previous marker. */
function msgr_mark_read(int $uid, int $cid): int
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return 0;
    }
    $prev = (int) $mem['last_read_id'];
    $last = (int) Cot::$db->query('SELECT last_msg_id FROM ' . msgr_t('chats') . ' WHERE chat_id = ?', [$cid])->fetchColumn();
    if ($last > $prev) {
        Cot::$db->update(msgr_t('members'), ['last_read_id' => $last], 'chat_id = ? AND user_id = ?', [$cid, $uid]);
    }
    msgr_clear_notifs($uid, $cid, 'message');
    return $prev;
}

/** The highest read marker among the *other* members: drives the double tick. */
function msgr_peer_read(int $cid, int $uid): int
{
    return (int) Cot::$db->query('SELECT MAX(last_read_id) FROM ' . msgr_t('members') . " WHERE chat_id = ? AND user_id <> ? AND state = 'active'", [$cid, $uid])->fetchColumn();
}

function msgr_history(int $uid, int $cid, int $before = 0): array
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access');
    }
    $limit = 40;
    $t     = msgr_t('messages');
    if ($before > 0) {
        $rows = Cot::$db->query("SELECT * FROM {$t} WHERE chat_id = ? AND msg_id < ? ORDER BY msg_id DESC LIMIT " . ($limit + 1), [$cid, $before])->fetchAll();
    } else {
        $rows = Cot::$db->query("SELECT * FROM {$t} WHERE chat_id = ? ORDER BY msg_id DESC LIMIT " . ($limit + 1), [$cid])->fetchAll();
    }
    $more = count($rows) > $limit;
    $rows = array_reverse(array_slice($rows, 0, $limit));
    $ids  = [];
    foreach ($rows as $r) {
        $ids[] = (int) $r['user_id'];
        if ($r['msg_type'] === 'system') {
            foreach ((array) json_decode((string) $r['body'], true) as $v) {
                if (is_int($v)) {
                    $ids[] = $v;
                }
            }
        }
    }
    $names = msgr_names($ids);
    $items = [];
    foreach ($rows as $r) {
        $items[] = msgr_item($r, $names, $uid);
    }
    $out = ['success' => true, 'items' => $items, 'more' => $more, 'peer_read' => msgr_peer_read($cid, $uid)];
    if ($before === 0) {
        $out['read_id'] = (int) $mem['last_read_id'];
        $out['chat']    = msgr_chat_view($uid, $cid);
    }
    return $out;
}

function msgr_poll(int $uid, int $cid, int $after, bool $markRead): array
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access', ['gone' => true]);
    }
    $rows = Cot::$db->query('SELECT * FROM ' . msgr_t('messages') . ' WHERE chat_id = ? AND msg_id > ? ORDER BY msg_id ASC LIMIT 100', [$cid, $after])->fetchAll();
    $ids  = [];
    foreach ($rows as $r) {
        $ids[] = (int) $r['user_id'];
        if ($r['msg_type'] === 'system') {
            foreach ((array) json_decode((string) $r['body'], true) as $v) {
                if (is_int($v)) {
                    $ids[] = $v;
                }
            }
        }
    }
    $names = msgr_names($ids);
    $items = [];
    foreach ($rows as $r) {
        $items[] = msgr_item($r, $names, $uid);
    }
    if ($markRead) {
        msgr_mark_read($uid, $cid);
    }
    $typing = [];
    $limit  = msgr_now() - 5;
    foreach (Cot::$db->query('SELECT user_id FROM ' . msgr_t('members') . " WHERE chat_id = ? AND user_id <> ? AND state = 'active' AND typing_ts >= ?", [$cid, $uid, $limit])->fetchAll() as $r) {
        $typing[] = (int) $r['user_id'];
    }
    $tn = $typing ? array_map(static fn($i) => msgr_names([$i])[$i]['name'], $typing) : [];
    return [
        'success'   => true,
        'items'     => $items,
        'peer_read' => msgr_peer_read($cid, $uid),
        'typing'    => $tn,
        'chat'      => msgr_chat_view($uid, $cid),
    ];
}

function msgr_typing(int $uid, int $cid): array
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access');
    }
    Cot::$db->update(msgr_t('members'), ['typing_ts' => msgr_now()], 'chat_id = ? AND user_id = ?', [$cid, $uid]);
    return ['success' => true];
}

/* ---------------------------------------------------------------- chat list / views */

/** Light description of one chat for the viewer (title, peer, members). */
function msgr_chat_view(int $uid, int $cid): array
{
    $chat = msgr_chat($cid);
    if (!$chat) {
        return [];
    }
    $members = msgr_members($cid);
    $invited = msgr_members($cid, 'invited');
    $names   = msgr_names(array_merge(array_keys($members), array_keys($invited)));
    $mine    = $members[$uid] ?? null;
    $view = [
        'id'      => (int) $chat['chat_id'],
        'type'    => $chat['chat_type'],
        'title'   => msgr_title($chat, $members, $names, $uid),
        'muted'   => $mine ? (int) $mine['muted'] === 1 : false,
        'owner'   => (int) $chat['owner_id'] === $uid,
        'seed'    => (int) $chat['chat_id'],
        'members' => [],
        'invited' => [],
    ];
    foreach ($members as $id => $m) {
        $view['members'][] = ['id' => $id, 'name' => $names[$id]['name'], 'online' => msgr_online($names[$id]['lastlog']), 'seen' => $names[$id]['lastlog'], 'me' => $id === $uid];
    }
    foreach ($invited as $id => $m) {
        $view['invited'][] = ['id' => $id, 'name' => $names[$id]['name']];
    }
    if ($chat['chat_type'] === 'dm') {
        foreach ($members as $id => $m) {
            if ($id !== $uid) {
                $view['seed']   = $id;
                $view['peer']   = ['id' => $id, 'name' => $names[$id]['name'], 'online' => msgr_online($names[$id]['lastlog']), 'seen' => $names[$id]['lastlog']];
                $view['peer_url'] = cot_url('users', ['m' => 'details', 'id' => $id, 'u' => $names[$id]['name']], '', true);
            }
        }
    }
    return $view;
}

function msgr_online(int $lastlog): bool
{
    return $lastlog > 0 && msgr_now() - $lastlog < 300;
}

/** Sidebar: active chats (newest first) + pending invitations. */
function msgr_list_chats(int $uid): array
{
    $db = Cot::$db;
    $rows = $db->query(
        'SELECT c.*, mb.muted, mb.last_read_id FROM ' . msgr_t('members') . ' mb INNER JOIN ' . msgr_t('chats') . " c ON c.chat_id = mb.chat_id
         WHERE mb.user_id = ? AND mb.state = 'active' ORDER BY c.last_ts DESC, c.chat_id DESC LIMIT 200",
        [$uid]
    )->fetchAll();
    $unread = [];
    foreach ($db->query(
        'SELECT mb.chat_id, COUNT(m.msg_id) AS n FROM ' . msgr_t('members') . ' mb LEFT JOIN ' . msgr_t('messages') . " m
           ON m.chat_id = mb.chat_id AND m.msg_id > mb.last_read_id AND m.user_id <> mb.user_id AND m.msg_type = 'text'
         WHERE mb.user_id = ? AND mb.state = 'active' GROUP BY mb.chat_id",
        [$uid]
    )->fetchAll() as $r) {
        $unread[(int) $r['chat_id']] = (int) $r['n'];
    }
    $cids = array_map(static fn($r) => (int) $r['chat_id'], $rows);
    $allMembers = [];
    $ids = [];
    if ($cids) {
        foreach ($db->query('SELECT chat_id, user_id FROM ' . msgr_t('members') . ' WHERE state = \'active\' AND chat_id IN (' . implode(',', $cids) . ') ORDER BY joined_ts, user_id')->fetchAll() as $r) {
            $allMembers[(int) $r['chat_id']][(int) $r['user_id']] = $r;
            $ids[] = (int) $r['user_id'];
        }
    }
    $lastRows = [];
    foreach ($rows as $r) {
        if ((int) $r['last_msg_id'] > 0) {
            $lastRows[(int) $r['chat_id']] = (int) $r['last_msg_id'];
        }
    }
    $lastMsgs = [];
    if ($lastRows) {
        foreach ($db->query('SELECT * FROM ' . msgr_t('messages') . ' WHERE msg_id IN (' . implode(',', $lastRows) . ')')->fetchAll() as $m) {
            $lastMsgs[(int) $m['chat_id']] = $m;
            $ids[] = (int) $m['user_id'];
            if ($m['msg_type'] === 'system') {
                foreach ((array) json_decode((string) $m['body'], true) as $v) {
                    if (is_int($v)) {
                        $ids[] = $v;
                    }
                }
            }
        }
    }
    $names = msgr_names($ids);
    $chats = [];
    foreach ($rows as $r) {
        $cid  = (int) $r['chat_id'];
        $mem  = $allMembers[$cid] ?? [];
        $item = [
            'id'     => $cid,
            'type'   => $r['chat_type'],
            'title'  => msgr_title($r, $mem, $names, $uid),
            'seed'   => $cid,
            'unread' => $unread[$cid] ?? 0,
            'muted'  => (int) $r['muted'] === 1,
            'ts'     => (int) $r['last_ts'],
            'last'   => null,
            'online' => false,
            'count'  => count($mem),
        ];
        if ($r['chat_type'] === 'dm') {
            foreach ($mem as $id => $_) {
                if ($id !== $uid) {
                    $item['seed']   = $id;
                    $item['online'] = msgr_online($names[$id]['lastlog'] ?? 0);
                }
            }
        }
        if (isset($lastMsgs[$cid])) {
            $lm  = msgr_item($lastMsgs[$cid], $names, $uid);
            $txt = msgr_trim($lm['text'], 90);
            $item['last'] = ['text' => $txt, 'mine' => $lm['mine'], 'sys' => $lm['sys'], 'from' => $r['chat_type'] === 'group' && !$lm['sys'] && !$lm['mine'] ? $lm['name'] : '', 'ts' => $lm['ts']];
        }
        $chats[] = $item;
    }
    return ['success' => true, 'chats' => $chats, 'invites' => msgr_invite_list($uid)];
}

function msgr_invite_list(int $uid): array
{
    $rows = Cot::$db->query(
        'SELECT mb.chat_id, mb.invited_by, c.title, c.chat_type FROM ' . msgr_t('members') . ' mb INNER JOIN ' . msgr_t('chats') . " c ON c.chat_id = mb.chat_id
         WHERE mb.user_id = ? AND mb.state = 'invited' ORDER BY mb.chat_id DESC LIMIT 20",
        [$uid]
    )->fetchAll();
    $names = msgr_names(array_map(static fn($r) => (int) $r['invited_by'], $rows));
    $out   = [];
    foreach ($rows as $r) {
        $cid     = (int) $r['chat_id'];
        $members = msgr_members($cid);
        $mn      = msgr_names(array_keys($members));
        $chat    = ['chat_type' => 'group', 'title' => (string) $r['title']];
        $out[] = ['chat_id' => $cid, 'title' => msgr_title($chat, $members, $mn, 0), 'by' => $names[(int) $r['invited_by']]['name'], 'by_id' => (int) $r['invited_by']];
    }
    return $out;
}

/* ---------------------------------------------------------------- invitations and membership */

function msgr_invite(int $uid, int $cid, int $target): array
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access');
    }
    if ($target === $uid || !msgr_user_ok($target)) {
        return msgr_fail('err_no_user');
    }
    return msgr_transaction(function () use ($uid, $cid, $target) {
        $chat = msgr_chat($cid);
        if (!$chat) {
            return msgr_fail('err_no_chat');
        }
        $existing = msgr_member($cid, $target);
        if ($existing) {
            return msgr_fail($existing['state'] === 'active' ? 'err_already' : 'err_already_invited');
        }
        $members = msgr_members($cid);
        $pending = msgr_members($cid, 'invited');
        if (count($members) + count($pending) >= (int) msgr_cfg('max_members', 20)) {
            return msgr_fail('err_full');
        }
        $db  = Cot::$db;
        $now = msgr_now();
        if ($chat['chat_type'] === 'dm') {
            // a dialog between two becomes a group chat; a new dialog of the same pair may be started later
            $db->update(msgr_t('chats'), ['chat_type' => 'group', 'dm_key' => null], 'chat_id = ?', [$cid]);
            msgr_system_message($cid, ['k' => 'group', 'a' => $uid]);
        }
        $db->insert(msgr_t('members'), ['chat_id' => $cid, 'user_id' => $target, 'role' => 'member', 'state' => 'invited', 'invited_by' => $uid, 'joined_ts' => $now]);
        msgr_system_message($cid, ['k' => 'invite', 'a' => $uid, 'b' => $target]);
        msgr_upsert_notif($target, $cid, 'invite', $uid, '');
        return ['success' => true, 'chat' => msgr_chat_view($uid, $cid)];
    });
}

function msgr_join(int $uid, int $cid): array
{
    return msgr_transaction(function () use ($uid, $cid) {
        $mem = msgr_member($cid, $uid);
        if (!$mem || $mem['state'] !== 'invited') {
            return msgr_fail('err_no_invite');
        }
        $last = (int) Cot::$db->query('SELECT last_msg_id FROM ' . msgr_t('chats') . ' WHERE chat_id = ?', [$cid])->fetchColumn();
        Cot::$db->update(msgr_t('members'), ['state' => 'active', 'joined_ts' => msgr_now(), 'last_read_id' => $last], 'chat_id = ? AND user_id = ?', [$cid, $uid]);
        msgr_clear_notifs($uid, $cid, 'invite');
        $row = msgr_system_message($cid, ['k' => 'join', 'a' => $uid]);
        // let the others know a person joined (no popup counter for system lines)
        return ['success' => true, 'chat_id' => $cid, 'last' => (int) $row['msg_id']];
    });
}

function msgr_decline(int $uid, int $cid): array
{
    return msgr_transaction(function () use ($uid, $cid) {
        $mem = msgr_member($cid, $uid);
        if (!$mem || $mem['state'] !== 'invited') {
            return msgr_fail('err_no_invite');
        }
        Cot::$db->delete(msgr_t('members'), 'chat_id = ? AND user_id = ?', [$cid, $uid]);
        msgr_clear_notifs($uid, $cid);
        msgr_system_message($cid, ['k' => 'declined', 'a' => $uid]);
        return ['success' => true];
    });
}

function msgr_leave(int $uid, int $cid): array
{
    return msgr_transaction(function () use ($uid, $cid) {
        $mem  = msgr_member($cid, $uid);
        $chat = msgr_chat($cid);
        if (!$mem || !$chat || $mem['state'] !== 'active') {
            return msgr_fail('err_access');
        }
        if ($chat['chat_type'] === 'dm') {
            return msgr_fail('err_leave_dm');
        }
        Cot::$db->delete(msgr_t('members'), 'chat_id = ? AND user_id = ?', [$cid, $uid]);
        msgr_clear_notifs($uid, $cid);
        $rest = msgr_members($cid);
        if (!$rest) {
            msgr_delete_chat($cid);
            return ['success' => true, 'deleted' => true];
        }
        if ((int) $chat['owner_id'] === $uid) {
            Cot::$db->update(msgr_t('chats'), ['owner_id' => (int) array_key_first($rest)], 'chat_id = ?', [$cid]);
        }
        msgr_system_message($cid, ['k' => 'left', 'a' => $uid]);
        return ['success' => true];
    });
}

function msgr_mute(int $uid, int $cid, bool $on): array
{
    $mem = msgr_member($cid, $uid);
    if (!$mem || $mem['state'] !== 'active') {
        return msgr_fail('err_access');
    }
    Cot::$db->update(msgr_t('members'), ['muted' => $on ? 1 : 0], 'chat_id = ? AND user_id = ?', [$cid, $uid]);
    if ($on) {
        msgr_clear_notifs($uid, $cid, 'message');
    }
    return ['success' => true, 'muted' => $on];
}

function msgr_rename(int $uid, int $cid, string $title): array
{
    $chat = msgr_chat($cid);
    $mem  = msgr_member($cid, $uid);
    if (!$chat || !$mem || $mem['state'] !== 'active' || $chat['chat_type'] !== 'group') {
        return msgr_fail('err_access');
    }
    $title = msgr_trim(msgr_clean_text($title), 100);
    return msgr_transaction(function () use ($uid, $cid, $title) {
        Cot::$db->update(msgr_t('chats'), ['title' => $title], 'chat_id = ?', [$cid]);
        msgr_system_message($cid, ['k' => 'rename', 'a' => $uid, 't' => $title]);
        return ['success' => true, 'chat' => msgr_chat_view($uid, $cid)];
    });
}

/** Nickname search for the "invite" form: not yet in the chat, not banned/inactive. */
function msgr_user_search(int $uid, int $cid, string $q): array
{
    $q = trim(preg_replace('/\s+/u', ' ', $q) ?? '');
    if (mb_strlen($q) < 2) {
        return ['success' => true, 'users' => []];
    }
    if ($cid > 0) {
        $mem = msgr_member($cid, $uid);
        if (!$mem || $mem['state'] !== 'active') {
            return msgr_fail('err_access');
        }
    }
    $like = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q) . '%';
    $pre  = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q) . '%';
    $bad  = (defined('COT_GROUP_INACTIVE') ? COT_GROUP_INACTIVE : 2) . ',' . (defined('COT_GROUP_BANNED') ? COT_GROUP_BANNED : 3);
    $sql  = 'SELECT user_id, user_name FROM ' . msgr_users_t() . " WHERE user_name LIKE ? ESCAPE '!' AND user_id <> ? AND user_maingrp NOT IN ($bad)";
    $par  = [$like, $uid];
    if ($cid > 0) {
        $sql .= ' AND user_id NOT IN (SELECT user_id FROM ' . msgr_t('members') . ' WHERE chat_id = ?)';
        $par[] = $cid;
    }
    $sql .= " ORDER BY (CASE WHEN user_name LIKE ? ESCAPE '!' THEN 0 ELSE 1 END), user_name LIMIT 8";
    $par[] = $pre;
    $users = [];
    foreach (Cot::$db->query($sql, $par)->fetchAll() as $r) {
        $users[] = ['id' => (int) $r['user_id'], 'name' => (string) $r['user_name']];
    }
    return ['success' => true, 'users' => $users];
}

/* ---------------------------------------------------------------- notifications and boot data */

function msgr_close_notif(int $uid, int $notifId): array
{
    Cot::$db->update(msgr_t('notifs'), ['closed' => 1], 'notif_id = ? AND user_id = ?', [$notifId, $uid]);
    return ['success' => true];
}

/** Everything the background widget needs: counters and the popups that are still open. */
function msgr_sync(int $uid): array
{
    $rows = Cot::$db->query(
        'SELECT * FROM ' . msgr_t('notifs') . ' WHERE user_id = ? AND closed = 0 ORDER BY updated_ts DESC, notif_id DESC LIMIT 8',
        [$uid]
    )->fetchAll();
    $ids = [];
    foreach ($rows as $r) {
        $ids[] = (int) $r['from_id'];
    }
    $names  = msgr_names($ids);
    $notifs = [];
    foreach ($rows as $r) {
        $cid  = (int) $r['chat_id'];
        $chat = msgr_chat($cid);
        if (!$chat) {
            continue;
        }
        $mem  = msgr_members($cid);
        $mn   = msgr_names(array_keys($mem));
        $uidV = $r['kind'] === 'invite' ? 0 : $uid;
        $notifs[] = [
            'id'      => (int) $r['notif_id'],
            'kind'    => $r['kind'],
            'chat_id' => $cid,
            'from_id' => (int) $r['from_id'],
            'from'    => $names[(int) $r['from_id']]['name'],
            'title'   => msgr_title($chat, $mem, $mn, $uidV),
            'is_group' => $chat['chat_type'] === 'group',
            'preview' => (string) $r['preview'],
            'cnt'     => (int) $r['cnt'],
            'ts'      => (int) $r['updated_ts'],
        ];
    }
    return [
        'success' => true,
        'unread'  => msgr_unread_total($uid),
        'invites' => msgr_invites_total($uid),
        'notifs'  => $notifs,
        'now'     => msgr_now(),
    ];
}

/** Language strings for JS (msgr_* without the prefix, minus admin_*). */
function msgr_js_lang(): array
{
    msgr_lang_load();
    $lang = [];
    foreach (Cot::$L as $key => $value) {
        $key = (string) $key;
        if (strpos($key, 'msgr_') === 0 && strpos($key, 'msgr_admin_') !== 0) {
            $lang[substr($key, 5)] = $value;
        }
    }
    return $lang;
}

/** Config for the JS client on every page. $extra: page specific keys. */
function msgr_boot(int $uid, array $extra = []): array
{
    return array_merge([
        'ajax'      => cot_url('plug', ['r' => 'messenger'], '', true),
        'xk'        => Cot::$sys['xk'],
        'me'        => $uid,
        'maxLen'    => msgr_max_len(),
        'flood'     => (int) msgr_cfg('flood_seconds', 1),
        'poll'      => max(2, (int) msgr_cfg('poll_seconds', 5)),
        'sounds'    => (int) msgr_cfg('sounds', 1) === 1,
        'popups'    => (int) msgr_cfg('popups', 1) === 1,
        'urls'      => [
            'home' => msgr_url('home', [], true),
            'chat' => msgr_url('chat', ['id' => '__ID__'], true),
            'new'  => msgr_url('new', ['u' => '__ID__'], true),
            'user' => cot_url('users', ['m' => 'details', 'id' => '__ID__'], '', true),
        ],
        'sync'      => msgr_sync($uid),
        'lang'      => msgr_js_lang(),
    ], $extra);
}

/** Header link markup (also used for the {HEADER_MESSENGER} tag). */
function msgr_header_link(int $uid): string
{
    $n = msgr_unread_total($uid) + msgr_invites_total($uid);
    return '<a class="msgr-hdr" href="' . htmlspecialchars(msgr_url('home')) . '" data-msgr-link="1">'
        . '<span class="msgr-hdr-ico" aria-hidden="true"></span><span class="msgr-hdr-text">' . htmlspecialchars(msgr_msg('hdr_link')) . '</span>'
        . '<span class="msgr-badge' . ($n > 0 ? '' : ' is-zero') . '" data-msgr-badge="1">' . ($n > 99 ? '99+' : $n) . '</span></a>';
}

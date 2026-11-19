<?php
/**
 * File: messenger.en.lang.php
 * Path: plugins/messenger/lang/messenger.en.lang.php
 * Messenger plugin - English language file.
 * Plural forms in JS strings are separated by "|": one|other.
 */
defined('COT_CODE') or die('Wrong URL.');

// Plugin info and config labels
$L['info_name'] = 'Messenger';
$L['info_desc'] = 'Telegram-style messenger: dialogs, group chats by invitation, header counter, popup notifications';
$L['cfg_max_len'] = 'Max message length (characters)';
$L['cfg_flood_seconds'] = 'Minimum interval between messages of one user (seconds)';
$L['cfg_max_members'] = 'Max members in a group chat';
$L['cfg_poll_seconds'] = 'Background check interval on ordinary pages (seconds)';
$L['cfg_sounds'] = 'Telegram-like sounds (users can switch them off)';
$L['cfg_popups'] = 'Popup notifications in the bottom right corner';

// General
$L['msgr_title'] = 'Messenger';
$L['msgr_group'] = 'Group chat';
$L['msgr_hdr_link'] = 'Messages';
$L['msgr_loading'] = 'Loading…';
$L['msgr_need_js'] = 'The messenger needs JavaScript.';
$L['msgr_login_required'] = 'Please sign in to use the messenger.';
$L['msgr_btn_login'] = 'Sign in';
$L['msgr_btn_write'] = 'Send a message';
$L['msgr_locale'] = 'en-US';

// Errors
$L['msgr_err_auth'] = 'Please sign in.';
$L['msgr_err_access'] = 'You have no access to this chat.';
$L['msgr_err_unknown'] = 'Something went wrong. Please try again.';
$L['msgr_err_network'] = 'No connection to the server.';
$L['msgr_err_empty'] = 'Empty message.';
$L['msgr_err_long'] = 'The message is too long.';
$L['msgr_err_flood'] = 'Not so fast! Wait a second.';
$L['msgr_err_self'] = 'You cannot write to yourself.';
$L['msgr_err_no_user'] = 'User not found or unavailable.';
$L['msgr_err_no_chat'] = 'Chat not found.';
$L['msgr_err_no_invite'] = 'The invitation is no longer valid.';
$L['msgr_err_already'] = 'This user is already in the chat.';
$L['msgr_err_already_invited'] = 'This user has already been invited.';
$L['msgr_err_full'] = 'The chat already has the maximum number of members.';
$L['msgr_err_leave_dm'] = 'You cannot leave a personal dialog.';

// Service lines in chats
$L['msgr_sys_invite'] = '{a} invited {b}';
$L['msgr_sys_join'] = '{a} joined the chat';
$L['msgr_sys_left'] = '{a} left the chat';
$L['msgr_sys_declined'] = '{a} declined the invitation';
$L['msgr_sys_rename'] = '{a} renamed the chat to "{t}"';
$L['msgr_sys_group'] = '{a} turned the dialog into a group chat';

// Interface (JS)
$L['msgr_search'] = 'Search';
$L['msgr_sound'] = 'Sounds';
$L['msgr_back'] = 'Back';
$L['msgr_emoji'] = 'Emoji';
$L['msgr_placeholder'] = 'Write a message…';
$L['msgr_send'] = 'Send';
$L['msgr_cancel'] = 'Cancel';
$L['msgr_close'] = 'Close';
$L['msgr_save'] = 'Save';
$L['msgr_info'] = 'Chat info';
$L['msgr_invite'] = 'Invite';
$L['msgr_mute'] = 'Mute notifications';
$L['msgr_unmute'] = 'Unmute notifications';
$L['msgr_muted_on'] = 'Notifications of this chat are off';
$L['msgr_muted_off'] = 'Notifications of this chat are on';
$L['msgr_leave'] = 'Leave chat';
$L['msgr_leave_confirm'] = 'Really leave?';
$L['msgr_you'] = 'You';
$L['msgr_you_member'] = 'you';
$L['msgr_today'] = 'Today';
$L['msgr_yesterday'] = 'Yesterday';
$L['msgr_online'] = 'online';
$L['msgr_typing'] = 'typing';
$L['msgr_waiting'] = 'waiting to join';
$L['msgr_seen_today'] = 'last seen today at {time}';
$L['msgr_seen_yesterday'] = 'last seen yesterday at {time}';
$L['msgr_seen_recent'] = 'last seen recently';
$L['msgr_seen_long'] = 'last seen a long time ago';
$L['msgr_members_n'] = '{n} member|{n} members';
$L['msgr_online_n'] = '{n} online';
$L['msgr_members_title'] = 'Members: {n}';
$L['msgr_invited_pending'] = 'Invited, not joined yet';
$L['msgr_group_name'] = 'Chat name';
$L['msgr_select_chat'] = 'Select a chat to start messaging';
$L['msgr_no_chats'] = 'No chats yet. Find a person with search or press "Send a message" on a profile.';
$L['msgr_no_messages'] = 'No messages here yet';
$L['msgr_nothing_found'] = 'Nothing found';
$L['msgr_people'] = 'People';
$L['msgr_write_first'] = 'Write the first message';
$L['msgr_write_first_hint'] = 'Write the first message to {name} - a personal chat appears after sending';
$L['msgr_new_dialog'] = 'new dialog';
$L['msgr_write_to'] = 'Write to {name}';
$L['msgr_sent_ok'] = 'Message sent';
$L['msgr_open_chat'] = 'Open chat';
$L['msgr_load_earlier'] = 'Show earlier messages';
$L['msgr_unread_messages'] = 'Unread messages';
$L['msgr_invitations'] = 'Invitations';
$L['msgr_invited_title'] = 'You were invited to a chat';
$L['msgr_invited_text'] = '{by} invites you to the chat "{title}"';
$L['msgr_invited_ok'] = '{name} was invited';
$L['msgr_invite_title'] = 'Invite to chat';
$L['msgr_invite_hint'] = 'Start typing a nickname or login. The person gets a notification and can join.';
$L['msgr_invite_hint_dm'] = 'Start typing a nickname or login. The dialog becomes a group chat and the invited person sees the history after joining.';
$L['msgr_invite_search'] = 'Nickname or login (2+ characters)';
$L['msgr_btn_join'] = 'Join';
$L['msgr_btn_decline'] = 'Decline';
$L['msgr_more_popups'] = 'and {n} more';

// Admin
$L['msgr_admin_title'] = 'Messenger - management';
$L['msgr_admin_f_all'] = 'All';
$L['msgr_admin_t_dm'] = 'Personal';
$L['msgr_admin_t_group'] = 'Group';
$L['msgr_admin_s_chats'] = 'Chats';
$L['msgr_admin_s_groups'] = 'Groups';
$L['msgr_admin_s_msgs'] = 'Messages';
$L['msgr_admin_s_day'] = 'Last 24 h';
$L['msgr_admin_s_invites'] = 'Pending invites';
$L['msgr_admin_old_90'] = 'Удалить сообщения старше 90 дней';
$L['msgr_admin_old_365'] = 'Удалить сообщения старше года';
$L['msgr_admin_empty'] = 'Delete empty chats';
$L['msgr_admin_notifs'] = 'Clean closed notifications';
$L['msgr_admin_confirm'] = 'Run the cleanup?';
$L['msgr_admin_old_done'] = 'Messages deleted';
$L['msgr_admin_empty_done'] = 'Empty chats deleted';
$L['msgr_admin_notifs_done'] = 'Notifications deleted';
$L['msgr_admin_col_type'] = 'Type';
$L['msgr_admin_col_title'] = 'Title';
$L['msgr_admin_col_owner'] = 'Creator';
$L['msgr_admin_col_members'] = 'Members';
$L['msgr_admin_col_msgs'] = 'Messages';
$L['msgr_admin_col_last'] = 'Last';
$L['msgr_admin_delete'] = 'Delete';
$L['msgr_admin_delete_confirm'] = 'Delete the chat with all messages?';
$L['msgr_admin_deleted'] = 'Chat deleted.';
$L['msgr_admin_no_chats'] = 'No chats.';
$L['msgr_admin_top'] = 'Most active';

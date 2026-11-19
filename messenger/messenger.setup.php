<?php
/* ====================
[BEGIN_COT_EXT]
Code=messenger
Name=Messenger
Description=Telegram-style messenger: dialogs, group chats by invitation, unread counter in the header, persistent popups
Version=1.0.0
Date=2026-11-17
Author=webitproff
Copyright=webitproff
Notes=BSD
Auth_guests=
Lock_guests=RW12345A
Auth_members=RW
Lock_members=12345
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
max_len=01:select:256,512,1024:1024:Max message length (characters)
flood_seconds=02:select:0,1,2,3,5:1:Minimum interval between messages of one user (seconds)
max_members=03:select:5,10,20,50,100:20:Max members in a group chat
poll_seconds=04:select:3,5,8,15:5:Background check interval on ordinary pages (seconds)
sounds=05:radio:0,1:1:Telegram-like sounds (users can still switch them off)
popups=06:radio:0,1:1:Popup notifications in the bottom right corner
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL.');

/* File: messenger.setup.php */
/* Path: plugins/messenger/messenger.setup.php */

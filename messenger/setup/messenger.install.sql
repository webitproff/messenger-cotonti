-- File: messenger.install.sql
-- Path: plugins/messenger/setup/messenger.install.sql

-- Chats: personal (dm) or group. dm_key = "minId_maxId" keeps exactly one dialog per pair.
CREATE TABLE IF NOT EXISTS `cot_msgr_chats` (
  `chat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `chat_type` ENUM('dm','group') NOT NULL DEFAULT 'dm',
  `title` VARCHAR(100) NOT NULL DEFAULT '',
  `owner_id` INT(11) NOT NULL DEFAULT 0,
  `dm_key` VARCHAR(24) DEFAULT NULL,
  `last_msg_id` INT(11) NOT NULL DEFAULT 0,
  `last_ts` INT(11) NOT NULL DEFAULT 0,
  `created_ts` INT(11) NOT NULL,
  PRIMARY KEY (`chat_id`),
  UNIQUE KEY `dm_key` (`dm_key`),
  KEY `last_ts` (`last_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Members. state: active (in chat) | invited (waiting for "join"). last_read_id drives unread counters and read ticks.
CREATE TABLE IF NOT EXISTS `cot_msgr_members` (
  `chat_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `role` ENUM('owner','member') NOT NULL DEFAULT 'member',
  `state` ENUM('active','invited') NOT NULL DEFAULT 'active',
  `invited_by` INT(11) NOT NULL DEFAULT 0,
  `muted` TINYINT(1) NOT NULL DEFAULT 0,
  `last_read_id` INT(11) NOT NULL DEFAULT 0,
  `typing_ts` INT(11) NOT NULL DEFAULT 0,
  `joined_ts` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`chat_id`,`user_id`),
  KEY `user_state` (`user_id`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages. user_id = 0 and msg_type = 'system' for service lines ("X joined").
CREATE TABLE IF NOT EXISTS `cot_msgr_messages` (
  `msg_id` INT(11) NOT NULL AUTO_INCREMENT,
  `chat_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL DEFAULT 0,
  `msg_type` ENUM('text','system') NOT NULL DEFAULT 'text',
  `body` TEXT NOT NULL,
  `created_ts` INT(11) NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `chat_msg` (`chat_id`,`msg_id`),
  KEY `user_ts` (`user_id`,`created_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Popup notifications. They stay until the user closes them (closed = 1), also after page reloads.
CREATE TABLE IF NOT EXISTS `cot_msgr_notifs` (
  `notif_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `chat_id` INT(11) NOT NULL,
  `kind` ENUM('message','invite') NOT NULL DEFAULT 'message',
  `from_id` INT(11) NOT NULL DEFAULT 0,
  `preview` VARCHAR(255) NOT NULL DEFAULT '',
  `cnt` INT(11) NOT NULL DEFAULT 1,
  `closed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_ts` INT(11) NOT NULL,
  `updated_ts` INT(11) NOT NULL,
  PRIMARY KEY (`notif_id`),
  UNIQUE KEY `user_chat_kind` (`user_id`,`chat_id`,`kind`),
  KEY `user_open` (`user_id`,`closed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

<?php
/**
 * File: messenger.ru.lang.php
 * Path: plugins/messenger/lang/messenger.ru.lang.php
 * Messenger plugin - Russian language file.
 * Plural forms in JS strings are separated by "|": один|несколько|много.
 */
defined('COT_CODE') or die('Wrong URL.');

// Plugin info and config labels
$L['info_name'] = 'Мессенджер';
$L['info_desc'] = 'Мессенджер в стиле Telegram: диалоги, групповые чаты по приглашению, счётчик в шапке, всплывающие уведомления';
$L['cfg_max_len'] = 'Максимальная длина сообщения (символов)';
$L['cfg_flood_seconds'] = 'Минимальный интервал между сообщениями пользователя (сек.)';
$L['cfg_max_members'] = 'Максимум участников в групповом чате';
$L['cfg_poll_seconds'] = 'Как часто проверять новые сообщения на обычных страницах (сек.)';
$L['cfg_sounds'] = 'Звуки как в Telegram (пользователь может выключить у себя)';
$L['cfg_popups'] = 'Всплывающие уведомления в правом нижнем углу';

// General
$L['msgr_title'] = 'Мессенджер';
$L['msgr_group'] = 'Групповой чат';
$L['msgr_hdr_link'] = 'Сообщения';
$L['msgr_loading'] = 'Загрузка…';
$L['msgr_need_js'] = 'Для работы мессенджера нужен включённый JavaScript.';
$L['msgr_login_required'] = 'Чтобы пользоваться мессенджером, войдите на сайт.';
$L['msgr_btn_login'] = 'Войти';
$L['msgr_btn_write'] = 'Написать сообщение';
$L['msgr_locale'] = 'ru-RU';

// Errors
$L['msgr_err_auth'] = 'Нужно войти на сайт.';
$L['msgr_err_access'] = 'У вас нет доступа к этому чату.';
$L['msgr_err_unknown'] = 'Что-то пошло не так. Попробуйте ещё раз.';
$L['msgr_err_network'] = 'Нет связи с сервером.';
$L['msgr_err_empty'] = 'Пустое сообщение.';
$L['msgr_err_long'] = 'Сообщение слишком длинное.';
$L['msgr_err_flood'] = 'Не так быстро! Подождите секунду.';
$L['msgr_err_self'] = 'Нельзя писать самому себе.';
$L['msgr_err_no_user'] = 'Пользователь не найден или недоступен.';
$L['msgr_err_no_chat'] = 'Чат не найден.';
$L['msgr_err_no_invite'] = 'Приглашение уже недействительно.';
$L['msgr_err_already'] = 'Этот пользователь уже в чате.';
$L['msgr_err_already_invited'] = 'Этому пользователю уже отправлено приглашение.';
$L['msgr_err_full'] = 'В чате уже максимальное число участников.';
$L['msgr_err_leave_dm'] = 'Из личного диалога выйти нельзя.';

// Service lines in chats
$L['msgr_sys_invite'] = '{a} пригласил(а) {b}';
$L['msgr_sys_join'] = '{a} присоединился(-ась) к чату';
$L['msgr_sys_left'] = '{a} покинул(а) чат';
$L['msgr_sys_declined'] = '{a} отклонил(а) приглашение';
$L['msgr_sys_rename'] = '{a} переименовал(а) чат: «{t}»';
$L['msgr_sys_group'] = '{a} превратил(а) диалог в групповой чат';

// Interface (JS)
$L['msgr_search'] = 'Поиск';
$L['msgr_sound'] = 'Звуки';
$L['msgr_back'] = 'Назад';
$L['msgr_emoji'] = 'Смайлики';
$L['msgr_placeholder'] = 'Написать сообщение…';
$L['msgr_send'] = 'Отправить';
$L['msgr_cancel'] = 'Отмена';
$L['msgr_close'] = 'Закрыть';
$L['msgr_save'] = 'Сохранить';
$L['msgr_info'] = 'Информация о чате';
$L['msgr_invite'] = 'Пригласить';
$L['msgr_mute'] = 'Выключить уведомления';
$L['msgr_unmute'] = 'Включить уведомления';
$L['msgr_muted_on'] = 'Уведомления этого чата выключены';
$L['msgr_muted_off'] = 'Уведомления этого чата включены';
$L['msgr_leave'] = 'Выйти из чата';
$L['msgr_leave_confirm'] = 'Точно выйти?';
$L['msgr_you'] = 'Вы';
$L['msgr_you_member'] = 'это вы';
$L['msgr_today'] = 'Сегодня';
$L['msgr_yesterday'] = 'Вчера';
$L['msgr_online'] = 'в сети';
$L['msgr_typing'] = 'печатает';
$L['msgr_waiting'] = 'ждёт ответа';
$L['msgr_seen_today'] = 'был(а) сегодня в {time}';
$L['msgr_seen_yesterday'] = 'был(а) вчера в {time}';
$L['msgr_seen_recent'] = 'был(а) недавно';
$L['msgr_seen_long'] = 'был(а) давно';
$L['msgr_members_n'] = '{n} участник|{n} участника|{n} участников';
$L['msgr_online_n'] = '{n} в сети';
$L['msgr_members_title'] = 'Участники: {n}';
$L['msgr_invited_pending'] = 'Приглашены, но ещё не присоединились';
$L['msgr_group_name'] = 'Название чата';
$L['msgr_select_chat'] = 'Выберите, кому хотели бы написать';
$L['msgr_no_chats'] = 'Чатов пока нет. Найдите человека в поиске или нажмите «Написать сообщение» в его профиле.';
$L['msgr_no_messages'] = 'Здесь пока нет сообщений';
$L['msgr_nothing_found'] = 'Ничего не найдено';
$L['msgr_people'] = 'Пользователи';
$L['msgr_write_first'] = 'Написать первое сообщение';
$L['msgr_write_first_hint'] = 'Напишите первое сообщение для {name} - после отправки появится личный чат';
$L['msgr_new_dialog'] = 'новый диалог';
$L['msgr_write_to'] = 'Написать: {name}';
$L['msgr_sent_ok'] = 'Сообщение отправлено';
$L['msgr_open_chat'] = 'Открыть чат';
$L['msgr_load_earlier'] = 'Показать более ранние';
$L['msgr_unread_messages'] = 'Непрочитанные сообщения';
$L['msgr_invitations'] = 'Приглашения';
$L['msgr_invited_title'] = 'Вас пригласили в чат';
$L['msgr_invited_text'] = '{by} приглашает вас в чат «{title}»';
$L['msgr_invited_ok'] = '{name} приглашён(а) в чат';
$L['msgr_invite_title'] = 'Пригласить в чат';
$L['msgr_invite_hint'] = 'Начните вводить ник или логин. Человек получит уведомление и сможет присоединиться.';
$L['msgr_invite_hint_dm'] = 'Начните вводить ник или логин. Диалог станет групповым чатом, а приглашённый увидит всю переписку после присоединения.';
$L['msgr_invite_search'] = 'Ник или логин (от 2 символов)';
$L['msgr_btn_join'] = 'Присоединиться';
$L['msgr_btn_decline'] = 'Отклонить';
$L['msgr_more_popups'] = 'и ещё {n}';

// Admin
$L['msgr_admin_title'] = 'Мессенджер - управление';
$L['msgr_admin_f_all'] = 'Все';
$L['msgr_admin_t_dm'] = 'Личные';
$L['msgr_admin_t_group'] = 'Групповые';
$L['msgr_admin_s_chats'] = 'Чатов';
$L['msgr_admin_s_groups'] = 'Групповых';
$L['msgr_admin_s_msgs'] = 'Сообщений';
$L['msgr_admin_s_day'] = 'За сутки';
$L['msgr_admin_s_invites'] = 'Ждут приглашений';
$L['msgr_admin_old_90'] = 'Удалить сообщения старше 90 дней';
$L['msgr_admin_old_365'] = 'Удалить сообщения старше года';
$L['msgr_admin_empty'] = 'Удалить пустые чаты';
$L['msgr_admin_notifs'] = 'Очистить закрытые уведомления';
$L['msgr_admin_confirm'] = 'Выполнить очистку?';
$L['msgr_admin_old_done'] = 'Удалено сообщений';
$L['msgr_admin_empty_done'] = 'Удалено пустых чатов';
$L['msgr_admin_notifs_done'] = 'Удалено уведомлений';
$L['msgr_admin_col_type'] = 'Тип';
$L['msgr_admin_col_title'] = 'Название';
$L['msgr_admin_col_owner'] = 'Создатель';
$L['msgr_admin_col_members'] = 'Участников';
$L['msgr_admin_col_msgs'] = 'Сообщений';
$L['msgr_admin_col_last'] = 'Последнее';
$L['msgr_admin_delete'] = 'Удалить';
$L['msgr_admin_delete_confirm'] = 'Удалить чат вместе с перепиской?';
$L['msgr_admin_deleted'] = 'Чат удалён.';
$L['msgr_admin_no_chats'] = 'Чатов нет.';
$L['msgr_admin_top'] = 'Самые активные';

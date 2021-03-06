<?php

if (false === load_constants($bb_cfg['apc_prefix'])) {
	set_constants($bb_cfg['apc_prefix'], [
		/* Параметры куков */
		'COOKIE_DATA'  => $bb_cfg['cookie_prefix'] . 'data',
		'COOKIE_FORUM' => $bb_cfg['cookie_prefix'] . 'f',
		'COOKIE_LOAD'  => $bb_cfg['cookie_prefix'] . 'isl',
		'COOKIE_MARK'  => $bb_cfg['cookie_prefix'] . 'mark_read',
		'COOKIE_TEST'  => $bb_cfg['cookie_prefix'] . 'test',
		'COOKIE_TOPIC' => $bb_cfg['cookie_prefix'] . 't',

		'COOKIE_SESSION'    => 0,
		'COOKIE_MAX_TRACKS' => 90,

		'DELETED' => -1,

		/* Уровни пользователей */
		'USER'         => 0,
		'ADMIN'        => 1,
		'MOD'          => 2,
		'GROUP_MEMBER' => 20,

		/* Константы, относящиеся к пользователю */
		'USER_ACTIVATION_NONE'  => 0,
		'USER_ACTIVATION_SELF'  => 1,
		'USER_ACTIVATION_ADMIN' => 2,

		'USER_AVATAR_NONE'    => 0,
		'USER_AVATAR_UPLOAD'  => 1,
		'USER_AVATAR_REMOTE'  => 2,
		'USER_AVATAR_GALLERY' => 3,

		/* Настройки групп */
		'GROUP_OPEN'   => 0,
		'GROUP_CLOSED' => 1,
		'GROUP_HIDDEN' => 2,

		/* Состояния форумов */
		'FORUM_UNLOCKED' => 0,
		'FORUM_LOCKED'   => 1,

		/* Состояния тем */
		'TOPIC_UNLOCKED' => 0,
		'TOPIC_LOCKED'   => 1,
		'TOPIC_MOVED'    => 2,

		'TOPIC_WATCH_NOTIFIED'    => 1,
		'TOPIC_WATCH_UN_NOTIFIED' => 0,

		/* Типы тем */
		'POST_NORMAL'          => 0,
		'POST_STICKY'          => 1,
		'POST_ANNOUNCE'        => 2,
		'POST_GLOBAL_ANNOUNCE' => 3,

		/* Типы поиска */
		'SEARCH_TYPE_POST'    => 0,
		'SEARCH_TYPE_TRACKER' => 1,

		/* Коды ошибок */
		'GENERAL_MESSAGE'  => 200,
		'GENERAL_ERROR'    => 202,
		'CRITICAL_MESSAGE' => 203,
		'CRITICAL_ERROR'   => 204,

		'E_AJAX_GENERAL_ERROR' => 1000,
		'E_AJAX_NEED_LOGIN'    => 1001,

		/* Личные сообщения */
		'PRIVMSGS_READ_MAIL'      => 0,
		'PRIVMSGS_NEW_MAIL'       => 1,
		'PRIVMSGS_SENT_MAIL'      => 2,
		'PRIVMSGS_SAVED_IN_MAIL'  => 3,
		'PRIVMSGS_SAVED_OUT_MAIL' => 4,
		'PRIVMSGS_UNREAD_MAIL'    => 5,

		/* Параметры URL */
		'POST_CAT_URL'    => 'c',
		'POST_FORUM_URL'  => 'f',
		'POST_GROUPS_URL' => 'g',
		'POST_POST_URL'   => 'p',
		'POST_TOPIC_URL'  => 't',
		'POST_USERS_URL'  => 'u',

		/* Режимы скачивания */
		'INLINE_LINK'   => 1,
		'PHYSICAL_LINK' => 2,

		/* Категории */
		'NONE_CAT'   => 0,
		'IMAGE_CAT'  => 1,
		'STREAM_CAT' => 2,
		'SWF_CAT'    => 3,

		/* Прочее */
		'MEGABYTE'              => 1024,
		'ADMIN_MAX_ATTACHMENTS' => 50,
		'THUMB_DIR'             => 'thumbs',
		'MODE_THUMBNAIL'        => 1,

		/* Права группы на все форумы */
		'GPERM_ALL' => 0,

		/* Квоты */
		'QUOTA_UPLOAD_LIMIT' => 1,
		'QUOTA_PM_LIMIT'     => 2,

		/* Торренты */
		'TOR_STATUS_NORMAL' => 0,
		'TOR_STATUS_FROZEN' => 1,
		'TORRENT_EXT'       => 'torrent',

		'TOPIC_DL_TYPE_NORMAL' => 0,
		'TOPIC_DL_TYPE_DL'     => 1,

		'SHOW_PEERS_COUNT' => 1,
		'SHOW_PEERS_NAMES' => 2,
		'SHOW_PEERS_FULL'  => 3,

		'SEARCH_ID_LENGTH' => 12,
		'SID_LENGTH'       => 20,
		'LOGIN_KEY_LENGTH' => 12,

		'CAT_URL'      => '/?c=',
		'DOWNLOAD_URL' => '/download.php?id=',
		'FORUM_URL'    => '/viewforum.php?f=',
		'GROUP_URL'    => '/groupcp.php?g=',
		'LOGIN_URL'    => '/login.php?redirect=',
		'MODCP_URL'    => '/modcp.php?f=',
		'PM_URL'       => '/privmsg.php?mode=post&amp;u=',
		'POST_URL'     => '/viewtopic.php?p=',
		'PROFILE_URL'  => '/profile.php?mode=viewprofile&amp;u=',
		'TOPIC_URL'    => '/viewtopic.php?t=',

		'HTML_SELECT_MAX_LENGTH' => 60,
		'HTML_WBR_LENGTH'        => 12,

		'HTML_CHECKED'  => ' checked ',
		'HTML_DISABLED' => ' disabled ',
		'HTML_READONLY' => ' readonly ',
		'HTML_SELECTED' => ' selected ',

		'HTML_SF_SPACER' => '&nbsp;|-&nbsp;',

		/* GPC */
		'KEY_NAME' => 0,
		'DEF_VAL'  => 1,
		'GPC_TYPE' => 2,

		'GET'     => 1,
		'POST'    => 2,
		'COOKIE'  => 3,
		'REQUEST' => 4,
		'CHBOX'   => 5,
		'SELECT'  => 6,

		/* Права доступа */
		'AUTH_LIST_ALL' => 0,

		/* Права доступа к форумам */
		'AUTH_REG'   => 1,
		'AUTH_ACL'   => 2,
		'AUTH_ADMIN' => 5,

		/* Значения полей прав доступа */
		'AUTH_ALL'        => 0,
		'AUTH_VIEW'       => 1,
		'AUTH_READ'       => 2,
		'AUTH_MOD'        => 3,
		'AUTH_POST'       => 4,
		'AUTH_REPLY'      => 5,
		'AUTH_EDIT'       => 6,
		'AUTH_DELETE'     => 7,
		'AUTH_STICKY'     => 8,
		'AUTH_ANNOUNCE'   => 9,
		'AUTH_VOTE'       => 10,
		'AUTH_POLLCREATE' => 11,
		'AUTH_ATTACH'     => 12,
		'AUTH_DOWNLOAD'   => 13,

		/* Когда определяем права пользователя, принимать во внимание: */
		'UG_PERM_BOTH'       => 1, /* и права пользователя и права группы */
		'UG_PERM_USER_ONLY'  => 2, /* только права пользователя */
		'UG_PERM_GROUP_ONLY' => 3, /* только права группы */
	]);
}

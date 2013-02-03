<?php

// Parse and show the overall page header

if (!defined('SITE_DIR'))
{
	exit;
}

if (defined('PAGE_HEADER_SENT')) return;

global $page_cfg, $db, $bb_cache, $userdata, $user, $ads, $bb_cfg, $template, $lang, $images, $static_path;

$logged_in = (int) !empty($userdata['session_logged_in']);
$is_admin  = ($logged_in && IS_ADMIN);
$is_mod    = ($logged_in && IS_MOD);

// Generate logged in/logged out status
if ($logged_in)
{
	$u_login_logout = "/login.php?logout=1";
}
else
{
	$u_login_logout = "/login.php";
}

// Online userlist
if (defined('SHOW_ONLINE') && SHOW_ONLINE)
{
	$online_full = !empty($_REQUEST['online_full']);
	$online_list = ($online_full) ? 'online' : 'online_short';

	${$online_list} = array(
		'stat'     => '',
		'userlist' => '',
		'cnt'      => '',
	);

/*	if (defined('IS_GUEST') && !(IS_GUEST || IS_USER))
	{*/
		$template->assign_var('SHOW_ONLINE_LIST');

		if (!${$online_list} = $bb_cache->get($online_list))
		{
			require(INC_DIR .'online_userlist.php');
		}
/*	}*/

	$template->assign_vars(array(
		'TOTAL_USERS_ONLINE'  => ${$online_list}['stat'],
		'LOGGED_IN_USER_LIST' => ${$online_list}['userlist'],
		'USERS_ONLINE_COUNTS' => ${$online_list}['cnt'],
		'RECORD_USERS'        => sprintf($lang['Record_online_users'], $bb_cfg['record_online_users'], bb_date($bb_cfg['record_online_date'])),
		'U_STATS'				=> 'stats.php',
		'U_VIEWONLINE'        => "viewonline.php",
	));
}

// Info about new private messages
$icon_pm = $images['pm_no_new_msg'];
$pm_info = $lang['No_new_pm'];
$have_new_pm = $have_unread_pm = 0;

if ($logged_in && empty($gen_simple_header) && !defined('IN_ADMIN'))
{
	if ($userdata['user_new_privmsg'])
	{
		$have_new_pm = $userdata['user_new_privmsg'];
		$icon_pm = $images['pm_new_msg'];
		$pm_info = declension($userdata['user_new_privmsg'], $lang['New_pms_declension'], $lang['New_pms_format']);

		if ($userdata['user_last_privmsg'] > $userdata['user_lastvisit'] && defined('IN_PM'))
		{
			$userdata['user_last_privmsg'] = $userdata['user_lastvisit'];

			db_update_userdata($userdata, array(
				'user_last_privmsg' => $userdata['user_lastvisit'],
			));

			$have_new_pm = ($userdata['user_new_privmsg'] > 1);
		}

		$pm_info = $lang['HAVE_NEW_PM'];
	}
	if (!$have_new_pm && $userdata['user_unread_privmsg'])
	{
		// synch unread pm count
		if (defined('IN_PM'))
		{
			$row = $db->fetch_row("
				SELECT COUNT(*) AS pm_count
				FROM ". PRIVMSGS_TABLE ."
				WHERE privmsgs_to_userid = ". $userdata['user_id'] ."
					AND privmsgs_type = ". PRIVMSGS_UNREAD_MAIL ."
				GROUP BY privmsgs_to_userid
			");

			$real_unread_pm_count = (int) $row['pm_count'];

			if ($userdata['user_unread_privmsg'] != $real_unread_pm_count)
			{
				$userdata['user_unread_privmsg'] = $real_unread_pm_count;

				db_update_userdata($userdata, array(
					'user_unread_privmsg' => $real_unread_pm_count,
				));
			}
		}

		$pm_info = declension($userdata['user_unread_privmsg'], $lang['Unread_pms_declension'], $lang['Unread_pms_format']);
		$pm_info = $lang['HAVE_NEW_PM'];
		$have_unread_pm = true;
	}
}
$template->assign_vars(array(
	'HAVE_NEW_PM'    => $have_new_pm,
	'HAVE_UNREAD_PM' => $have_unread_pm,
));

$autocomplete = false;

if(
	$_SERVER['REMOTE_ADDR'] == '192.168.1.1' || // vacuum
	$_SERVER['REMOTE_ADDR'] == '10.171.87.121' || // mindblower
	$_SERVER['REMOTE_ADDR'] == '10.171.66.250' || // bonik
	$_SERVER['REMOTE_ADDR'] == '10.231.213.188' || // whitejocker
	$_SERVER['REMOTE_ADDR'] == '10.171.204.99' || // limit
	$_SERVER['REMOTE_ADDR'] == '10.171.102.179' || // woolf
	$_SERVER['REMOTE_ADDR'] == '10.221.22.190'
)
{
	$autocomplete = true;
}

// The following assigns all _common_ variables that may be used at any point in a template
$template->assign_vars(array(
	'AUTOCOMPLETE'       => $autocomplete,
	'CHAT_ALLOWED'       => isset($userdata['user_chat']) && $userdata['user_id'] > 0 ? $userdata['user_chat'] : 0,
	'STATIC_PATH'        => $static_path,
	'SIMPLE_HEADER'      => !empty($gen_simple_header),
	'IN_ADMIN'           => defined('IN_ADMIN'),
	'QUIRKS_MODE'        => !empty($page_cfg['quirks_mode']),
	'SHOW_ADS'           => (!$logged_in || isset($bb_cfg['show_ads_users'][$user->id]) || (!($is_admin || $is_mod) && $user->show_ads)),

	'INCLUDE_BBCODE_JS'  => !empty($page_cfg['include_bbcode_js']),
	'USER_OPTIONS_JS'    => ($logged_in) ? json_encode($user->opt_js) : '{}',

	'SITENAME'           => $bb_cfg['sitename'],
	'U_INDEX'            => "/index.php",
	'T_INDEX'            => sprintf($lang['Forum_Index'], $bb_cfg['sitename']),

	'LAST_VISIT_DATE'    => ($logged_in) ? sprintf($lang['You_last_visit'], create_date($bb_cfg['last_visit_date_format'], $userdata['user_lastvisit'])) : '',
	'CURRENT_TIME'       => sprintf($lang['Current_time'], create_date($bb_cfg['last_visit_date_format'], TIMENOW)),
	'S_TIMEZONE'         => sprintf($lang['All_times'], $lang[''.str_replace(',', '.', floatval($bb_cfg['board_timezone'])).'']),

	'PM_INFO'            => $pm_info,
	'PRIVMSG_IMG'        => $icon_pm,

	'LOGGED_IN'          => $logged_in,
	'SESSION_USER_ID'		=> $userdata['user_id'],
	'THIS_USERNAME'      => $userdata['username'],
	'USERNAME_ESCAPED'   => htmlspecialchars($userdata['username'], ENT_QUOTES),
	'SHOW_LOGIN_LINK'    => !defined('IN_LOGIN'),
	'AUTOLOGIN_DISABLED' => !$bb_cfg['allow_autologin'],
	'S_LOGIN_ACTION'     => "/login.php",

	'U_CUR_DOWNLOADS'    => PROFILE_URL . $userdata['user_id'],
	'U_FAQ'              => $bb_cfg['faq_url'],
	'U_FORUM'            => "viewforum.php",
	'U_GROUP_CP'         => "groupcp.php",
	'U_LOGIN_LOGOUT'     => $u_login_logout,
	'U_MEMBERLIST'       => "memberlist.php",
	'U_MODCP'            => "modcp.php",
	'U_OPTIONS'          => "profile.php?mode=editprofile",
	'U_PRIVATEMSGS'      => "privmsg.php?folder=inbox",
	'U_PROFILE'          => PROFILE_URL . $userdata['user_id'],
	'U_READ_PM'          => "privmsg.php?folder=inbox". (($userdata['user_newest_pm_id'] && $userdata['user_new_privmsg'] == 1) ? "&mode=read&p={$userdata['user_newest_pm_id']}" : ''),
	'U_REGISTER'         => "profile.php?mode=register",
	'U_SEARCH'           => "search.php",
	'U_SEND_PASSWORD'    => "profile.php?mode=sendpassword",
	'U_TERMS'            => $bb_cfg['terms_and_conditions_url'],
	'U_TRACKER'          => "tracker.php",
	'U_UPLOAD_IMAGE'     => ( $static_path == '//0.ivacuum.org' ) ? '//up.local.ivacuum.ru/' : '//up.ivacuum.ru/',

	'DEVELOPER'          => $_SERVER['REMOTE_ADDR'] == '192.168.1.1',
	'SHOW_ADMIN_OPTIONS' => $is_admin,
	'SHOW_MODER_OPTIONS' => ($is_admin || $is_mod),
	'SHOW_SIDEBAR1'      => (!empty($page_cfg['show_sidebar1'][BB_SCRIPT]) || $bb_cfg['show_sidebar1_on_every_page']),
	'SHOW_SIDEBAR2'      => (!empty($page_cfg['show_sidebar2'][BB_SCRIPT]) || $bb_cfg['show_sidebar2_on_every_page']),

	// Common urls
	'CAT_URL'            => CAT_URL,
	'DOWNLOAD_URL'       => DOWNLOAD_URL,
	'FORUM_URL'          => FORUM_URL,
	'GROUP_URL'          => GROUP_URL,
	'NEWEST_URL'         => '&amp;view=newest#newest',
	'POST_URL'           => POST_URL,
	'PROFILE_URL'        => PROFILE_URL,
	'TOPIC_URL'          => TOPIC_URL,

	'AJAX_HANDLER'       => '/ajax.php',

	'ONLY_NEW_POSTS'     => ONLY_NEW_POSTS,
	'ONLY_NEW_TOPICS'    => ONLY_NEW_TOPICS,

	// Misc
	'DEBUG'              => DEBUG,
	'BOT_UID'            => BOT_UID,
	'COOKIE_MARK'        => COOKIE_MARK,
	'SID'                => $userdata['session_id'],
	'SID_HIDDEN'         => '<input type="hidden" name="sid" value="'. $userdata['session_id'] .'" />',

	'CHECKED'            => HTML_CHECKED,
	'DISABLED'           => HTML_DISABLED,
	'READONLY'           => HTML_READONLY,
	'SELECTED'           => HTML_SELECTED,
	'HTML_WBR_TAG'       => HTML_WBR_TAG,

	'U_SEARCH_SELF_BY_LAST' => "search.php?uid={$userdata['user_id']}&amp;o=5",
));

if (!empty($page_cfg['dl_links_user_id']))
{
	$dl_link = "search.php?dlu={$page_cfg['dl_links_user_id']}&amp;";

	$template->assign_vars(array(
		'SHOW_SEARCH_DL'       => true,
		'U_SEARCH_DL_WILL'     => $dl_link .'dlw=1',
		'U_SEARCH_DL_DOWN'     => $dl_link .'dld=1',
		'U_SEARCH_DL_COMPLETE' => $dl_link .'dlc=1',
		'U_SEARCH_DL_CANCEL'   => $dl_link .'dla=1',
	));
}

if (!empty($page_cfg['show_torhelp'][BB_SCRIPT]) && !empty($userdata['torhelp']))
{
	$ignore_time = !empty($_COOKIE['torhelp']) ? (int) $_COOKIE['torhelp'] : 0;

	if (TIMENOW > $ignore_time)
	{
		if ($ignore_time)
		{
			bb_setcookie('torhelp', '', COOKIE_EXPIRED);
		}

		$sql = "
			SELECT topic_id, topic_title
			FROM ". TOPICS_TABLE ."
			WHERE topic_id IN(". $userdata['torhelp'] .")
			LIMIT 8
		";
		$torhelp_topics = array();

		foreach ($db->fetch_rowset($sql) as $row)
		{
			$torhelp_topics[] = '<a href="viewtopic.php?t='. $row['topic_id'] .'">'. $row['topic_title'] .'</a>';
		}

		$template->assign_vars(array(
			'TORHELP_TOPICS'  => join("</li>\n<li>", $torhelp_topics),
		));
	}
}

/*if (DBG_USER)
{
	$template->assign_vars(array(
		'INCLUDE_DEVELOP_JS' => true,
		'EDITOR_PATH'        => @addslashes($bb_cfg['dbg']['editor_path']),
		'EDITOR_ARGS'        => @addslashes($bb_cfg['dbg']['editor_args']),
	));
}*/

// Ads
if ($user->show_ads)
{
	$load_ads = array('trans');
	if (defined('BB_SCRIPT'))
	{
		$load_ads[] = BB_SCRIPT;
	}
	foreach ($ads->get($load_ads) as $block_id => $ad_html)
	{
		$template->assign_var("AD_BLOCK_{$block_id}", $ad_html);
	}
}

// Login box
$in_out = ($logged_in) ? 'in' : 'out';
$template->assign_block_vars("switch_user_logged_{$in_out}", array());

// Work around for "current" Apache 2 + PHP module which seems to not
// cope with private cache control setting
if (!empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2'))
{
	header('Cache-Control: no-cache, pre-check=0, post-check=0');
}
else
{
	header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
}
header('Expires: 0');
header('Pragma: no-cache');

$template->set_filenames(array('page_header' => 'page_header.tpl'));
$template->pparse('page_header');

define('PAGE_HEADER_SENT', true);

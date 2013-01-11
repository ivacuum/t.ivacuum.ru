<?php

if ( !defined('IN_PHPBB') )
{
	exit;
}

$datastore->enqueue(array(
	'ranks',
));

if (!$userdata['session_logged_in'])
{
	redirect(append_sid("login.$phpEx?redirect={$_SERVER['REQUEST_URI']}", TRUE));
}
if ( empty($_GET[POST_USERS_URL]) || $_GET[POST_USERS_URL] == ANONYMOUS )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
$profiledata = get_userdata($_GET[POST_USERS_URL]);

if( !$profiledata )
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

//
// Calculate the number of days this user has been a member ($memberdays)
// Then calculate their posts per day
//
$regdate = $profiledata['user_regdate'];
$memberdays = max(1, round( ( time() - $regdate ) / 86400 ));
$posts_per_day = $profiledata['user_posts'] / $memberdays;

$avatar_img = '';
if ( $profiledata['user_avatar_type'] && $profiledata['user_allowavatar'] )
{
	switch( $profiledata['user_avatar_type'] )
	{
		case USER_AVATAR_UPLOAD:
			$avatar_img = ( $board_config['allow_avatar_upload'] ) ? '<img src="' . $board_config['avatar_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" />' : '';
			break;
		case USER_AVATAR_REMOTE:
			$avatar_img = ( $board_config['allow_avatar_remote'] ) ? '<img src="' . $profiledata['user_avatar'] . '" alt="" border="0" />' : '';
			break;
		case USER_AVATAR_GALLERY:
			$avatar_img = ( $board_config['allow_avatar_local'] ) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0" />' : '';
			break;
	}
}

$ranks = $datastore->get('ranks');
$poster_rank = $rank_image = '';

if ($user_rank = $profiledata['user_rank'] AND isset($ranks[$user_rank]))
{
	$rank_image = ($ranks[$user_rank]['rank_image']) ? '<img src="'. $ranks[$user_rank]['rank_image'] .'" alt="" title="" border="0" />' : '';
	$poster_rank = $ranks[$user_rank]['rank_title'];
}

$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=" . $profiledata['user_id']);
$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';

$location = ($profiledata['user_from']) ? $profiledata['user_from'] : '';
$location .= ($profiledata['user_from_flag'] && $profiledata['user_from_flag'] != 'blank.gif') ? '&nbsp;<img align="absmiddle" src="http://static.ivacuum.ru/i/flags/48/'. $profiledata['user_from_flag'] .'" alt="'. $profiledata['user_from_flag'] . '" title="' . $profiledata['user_from_flag'] . '" />' : '';

$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

if ( bf($profiledata['user_opt'], 'user_opt', 'viewemail') || IS_ADMIN )
{
	$email_uri = ( $board_config['board_email_form'] ) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL .'=' . $profiledata['user_id']) : 'mailto:' . $profiledata['user_email'];
	$email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0" /></a>';
	$email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
}
else
{
	$email_img = '';
	$email = '';
}
$www_img = ( $profiledata['user_website'] ) ? '<a href="' . $profiledata['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0" /></a>' : '';
$www = ( $profiledata['user_website'] ) ? '<a href="' . $profiledata['user_website'] . '" target="_userwww">' . $profiledata['user_website'] . '</a>' : '';
if ( !empty($profiledata['user_icq']) )
{
	$icq_status_img = '<a href="http://www.icq.com/' . $profiledata['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $profiledata['user_icq'] . '&img=5" width="18" height="18" border="0" /></a>';
	$icq_img = '<a href="http://www.icq.com/' . $profiledata['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0" /></a>';
	$icq =  '<a href="http://www.icq.com/' . $profiledata['user_icq'] . '">' . $lang['ICQ'] . '</a>';
}
else
{
	$icq_status_img = '';
	$icq_img = '';
	$icq = '';
}
$aim_img = ( $profiledata['user_aim'] ) ? '<a href="aim:goim?screenname=' . $profiledata['user_aim'] . '&amp;message=Hello+Are+you+there?"><img src="' . $images['icon_aim'] . '" alt="' . $lang['AIM'] . '" title="' . $lang['AIM'] . '" border="0" /></a>' : '';
$aim = ( $profiledata['user_aim'] ) ? '<a href="aim:goim?screenname=' . $profiledata['user_aim'] . '&amp;message=Hello+Are+you+there?">' . $lang['AIM'] . '</a>' : '';
$msn_img = ( $profiledata['user_msnm'] ) ? $profiledata['user_msnm'] : '';
$msn = $msn_img;
$yim_img = ( $profiledata['user_yim'] ) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $profiledata['user_yim'] . '&amp;.src=pg"><img src="' . $images['icon_yim'] . '" alt="' . $lang['YIM'] . '" title="' . $lang['YIM'] . '" border="0" /></a>' : '';
$yim = ( $profiledata['user_yim'] ) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $profiledata['user_yim'] . '&amp;.src=pg">' . $lang['YIM'] . '</a>' : '';
$temp_url = append_sid("search.$phpEx?search_author=1&amp;uid={$profiledata['user_id']}");
$search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . $lang['Search_user_posts'] . '" title="' . sprintf($lang['Search_user_posts'], $profiledata['username']) . '" border="0" /></a>';
$search = '<a href="' . $temp_url . '">' . sprintf($lang['Search_user_posts'], $profiledata['username']) . '</a>';

//
// Generate page
//
/*
if ($profiledata['user_id'] == $userdata['user_id'] || IS_ADMIN)
{
	require($t_root_path . 'attach_mod/attachment_mod.php');
	display_upload_attach_box_limits($profiledata['user_id']);
}
*/

/**
* Отображение групп, в которых состоит пользователь
*/
$user_id = $userdata['user_id'];
$view_user_id = $profiledata['user_id'];
$groups = array();
$sql = '
	SELECT
		g.group_id,
		g.group_name,
		g.group_type
	FROM
		' . GROUPS_TABLE . ' as g,
		' . USER_GROUP_TABLE . ' as ug
	WHERE
		ug.user_pending = 0
	AND
		g.group_single_user = 0
	AND
		ug.user_id = ' . $profiledata['user_id'] . '
	AND
		g.group_id = ug.group_id
	ORDER BY
		g.group_name ASC,
		g.group_id ASC';
$result = $db->sql_query($sql);
$show_groups = false;

while( $row = $db->sql_fetchrow($result) )
{
	$is_ok = false;
	$show_groups = true;

	if( $row['group_type'] != GROUP_HIDDEN || $userdata['user_level'] == ADMIN )
	{
		$is_ok = true;
	}
	else
	{
		$sql = '
			SELECT
				*
			FROM
				' . USER_GROUP_TABLE . '
			WHERE
				group_id = ' . $row['group_id'] . '
			AND
				user_id = ' . $userdata['user_id'] . '
			AND
				user_pending = 0';
		$result2 = $db->sql_query($sql);
		$is_ok = ( $group = $db->sql_fetchrow($result2) );
		$db->sql_freeresult($result2);
	}

	if( $is_ok )
	{
		$template->assign_block_vars('groups', array(
			'TITLE' => $row['group_name'],
			'URL'   => append_sid('groupcp.php?g=' . $row['group_id']))
		);
	}
}

$db->sql_freeresult($result);

$template->assign_vars(array(
	'PAGE_TITLE' => $lang['Viewing_profile'],
	'USERNAME' => $profiledata['username'],
	'PROFILE_USER_ID' => $profiledata['user_id'],
	'USER_REGDATE' => bb_date($profiledata['user_regdate']),
	'POSTER_RANK' => $poster_rank,
	'RANK_IMAGE' => $rank_image,
	'POSTS_PER_DAY' => $posts_per_day,
	'POSTS' => $profiledata['user_posts'],
	'POST_DAY_STATS' => sprintf($lang['User_post_day_stats'], $posts_per_day),
	'SEARCH_IMG' => $search_img,
	'SEARCH' => $search,
	'PM_IMG' => $pm_img,
	'PM' => $pm,
	'EMAIL_IMG' => $email_img,
	'EMAIL' => $email,
	'WWW_IMG' => $www_img,
	'WWW' => $www,
	'ICQ_STATUS_IMG' => $icq_status_img,
	'ICQ_IMG' => $icq_img,
	'ICQ' => $icq,
	'AIM_IMG' => $aim_img,
	'AIM' => $aim,
	'MSN_IMG' => $msn_img,
	'MSN' => $msn,
	'YIM_IMG' => $yim_img,
	'YIM' => $yim,
	'LAST_VISIT_TIME' => ($profiledata['user_lastvisit']) ? bb_date($profiledata['user_lastvisit']) : $lang['Never'],
	'LAST_ACTIVITY_TIME' => ($profiledata['user_session_time']) ? bb_date($profiledata['user_session_time']) : $lang['Never'],
	'LOCATION' => $location,
	'IP' => ( $profiledata['user_ip'] ) ? $profiledata['user_ip'] : '---',
	'SHOW_GROUPS' => $show_groups,

	'USER_ACTIVE' => $profiledata['user_active'],

	'OCCUPATION' => ( $profiledata['user_occ'] ) ? $profiledata['user_occ'] : '',
	'INTERESTS' => ( $profiledata['user_interests'] ) ? $profiledata['user_interests'] : '',
	'AVATAR_IMG' => $avatar_img,

	'L_VIEWING_PROFILE' => sprintf($lang['Viewing_user_profile'], $profiledata['username']),
	'L_ABOUT_USER' => sprintf($lang['About_user'], $profiledata['username']),
	'L_AVATAR' => $lang['Avatar'],
	'L_POSTER_RANK' => $lang['Poster_rank'],
	'L_TOTAL_POSTS' => $lang['Total_posts'],
	'L_SEARCH_USER_POSTS' => sprintf($lang['Search_user_posts'], '<b>'. $profiledata['username'] .'</b>'),
	'L_CONTACT' => $lang['Contact'],
	'L_EMAIL_ADDRESS' => $lang['Email_address'],
	'L_OCCUPATION' => $lang['Occupation'],
	'L_INTERESTS' => $lang['Interests'],
	'L_USERGROUPS' => $lang['USERGROUPS'],

	'U_SEARCH_USER'     => "search.$phpEx?search_author=1&amp;uid={$profiledata['user_id']}",
	'U_SEARCH_RELEASES' => "tracker.$phpEx?rid={$profiledata['user_id']}#results",
	'L_SEARCH_RELEASES' => $lang['Search_user_releases'],

	'S_PROFILE_ACTION'  => "profile.$phpEx",
));

//bt
// Show users torrent-profile
define('IN_VIEWPROFILE', TRUE);
require($t_root_path .'includes/torrent_userprofile.php');
//bt end

$template->assign_vars(array(
	'SHOW_ACCESS_PRIVILEGE' => IS_ADMIN,
	'L_ACCESS'              => $lang['Access'],
	'L_ACCESS_SRV_LOAD'     => $lang['Access_srv_load'],
	'IGNORE_SRV_LOAD'       => ($profiledata['user_level'] != USER || $profiledata['ignore_srv_load']) ? $lang['NO'] : $lang['YES'],
	'IGNORE_SRV_LOAD_EDIT'  => ($profiledata['user_level'] == USER),
));

if (IS_ADMIN)
{
	$template->assign_vars(array(
		'EDITABLE_TPLS' => true,

		'U_MANAGE'      => "admin/admin_users.$phpEx?mode=edit&amp;u={$profiledata['user_id']}",
		'U_PERMISSIONS' => "admin/admin_ug_auth.$phpEx?mode=user&amp;u={$profiledata['user_id']}",

		'L_MANAGE'      => 'Profile',
		'L_PERMISSIONS' => 'Permissions',
	));
}

print_page('usercp_viewprofile.tpl');



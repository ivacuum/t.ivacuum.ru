<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('BB_SCRIPT', 'groupcp');
require('common.php');
require(SITE_DIR . 'includes/functions_group.php');

$s_member_groups = $s_pending_groups = $s_member_groups_opt = $s_pending_groups_opt = '';
$select_sort_mode = $select_sort_order = '';

// -------------------------
//
function generate_user_info(&$row, $date_format, $group_mod, &$from, &$posts, &$joined, &$poster_avatar, &$profile_img, &$profile, &$search_img, &$search, &$pm_img, &$pm, &$email_img, &$email, &$www_img, &$www, &$icq_status_img, &$icq_img, &$icq, &$aim_img, &$aim, &$msn_img, &$msn, &$yim_img, &$yim)
{
	global $lang, $images, $bb_cfg;

	$from = ( !empty($row['user_from']) ) ? $row['user_from'] : '&nbsp;';
	$joined = create_date($date_format, $row['user_regdate'], $bb_cfg['board_timezone']);
	$posts = ( $row['user_posts'] ) ? $row['user_posts'] : 0;

	$poster_avatar = '';
	if ( @$row['user_avatar_type'] && $row['user_id'] != ANONYMOUS && $row['user_allowavatar'] )
	{
		switch( $row['user_avatar_type'] )
		{
			case USER_AVATAR_UPLOAD:
				$poster_avatar = ( $bb_cfg['allow_avatar_upload'] ) ? '<img src="' . $bb_cfg['avatar_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
				break;
			case USER_AVATAR_REMOTE:
				$poster_avatar = ( $bb_cfg['allow_avatar_remote'] ) ? '<img src="' . $row['user_avatar'] . '" alt="" border="0" />' : '';
				break;
			case USER_AVATAR_GALLERY:
				$poster_avatar = ( $bb_cfg['allow_avatar_local'] ) ? '<img src="' . $bb_cfg['avatar_gallery_path'] . '/' . $row['user_avatar'] . '" alt="" border="0" />' : '';
				break;
		}
	}

	if ( bf($row['user_opt'], 'user_opt', 'viewemail') || $group_mod )
	{
		$email_uri = ( $bb_cfg['board_email_form'] ) ? "profile.php?mode=email&amp;u={$row['user_id']}" : 'mailto:' . $row['user_email'];

		$email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0" /></a>';
		$email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
	}
	else
	{
		$email_img = '&nbsp;';
		$email = '&nbsp;';
	}

	$temp_url = "profile.php?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id'];
	$profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0" /></a>';
	$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

	$temp_url = "privmsg.php?mode=post&amp;" . POST_USERS_URL . "=" . $row['user_id'];
	$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';
	$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

	$www_img = ( $row['user_website'] ) ? '<a href="' . $row['user_website'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0" /></a>' : '';
	$www = ( $row['user_website'] ) ? '<a href="' . $row['user_website'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';

	if ( !empty($row['user_icq']) )
	{
		$icq_status_img = '<a href="http://wwp.icq.com/' . $row['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $row['user_icq'] . '&img=5" width="18" height="18" border="0" /></a>';
		$icq_img = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0" /></a>';
		$icq =  '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '">' . $lang['ICQ'] . '</a>';
	}
	else
	{
		$icq_status_img = '';
		$icq_img = '';
		$icq = '';
	}

	$aim_img = ( $row['user_aim'] ) ? '<a href="aim:goim?screenname=' . $row['user_aim'] . '&amp;message=Hello+Are+you+there?"><img src="' . $images['icon_aim'] . '" alt="' . $lang['AIM'] . '" title="' . $lang['AIM'] . '" border="0" /></a>' : '';
	$aim = ( $row['user_aim'] ) ? '<a href="aim:goim?screenname=' . $row['user_aim'] . '&amp;message=Hello+Are+you+there?">' . $lang['AIM'] . '</a>' : '';

	$temp_url = "profile.php?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $row['user_id'];
	$msn_img = ( $row['user_msnm'] ) ? '<a href="' . $temp_url . '"><img src="' . $images['icon_msnm'] . '" alt="' . $lang['MSNM'] . '" title="' . $lang['MSNM'] . '" border="0" /></a>' : '';
	$msn = ( $row['user_msnm'] ) ? '<a href="' . $temp_url . '">' . $lang['MSNM'] . '</a>' : '';

	$yim_img = ( $row['user_yim'] ) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $row['user_yim'] . '&amp;.src=pg"><img src="' . $images['icon_yim'] . '" alt="' . $lang['YIM'] . '" title="' . $lang['YIM'] . '" border="0" /></a>' : '';
	$yim = ( $row['user_yim'] ) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $row['user_yim'] . '&amp;.src=pg">' . $lang['YIM'] . '</a>' : '';

	$temp_url = "search.php?search_author=1&amp;uid={$row['user_id']}";
	$search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . sprintf($lang['Search_user_posts'], $row['username']) . '" title="' . sprintf($lang['Search_user_posts'], $row['username']) . '" border="0" /></a>';
	$search = '<a href="' . $temp_url . '">' . sprintf($lang['Search_user_posts'], $row['username']) . '</a>';

	return;
}
//
// --------------------------

$user->session_start(array('req_login' => true));

$group_id = isset($_REQUEST[POST_GROUPS_URL]) ? intval($_REQUEST[POST_GROUPS_URL]) : null;
$start    = isset($_REQUEST['start']) ? abs(intval($_REQUEST['start'])) : 0;
$per_page = $bb_cfg['groupcp_members_per_page'];

$group_info = array();
$is_moderator = false;

if ($group_id)
{
	if (!$group_info = get_group_data($group_id))
	{
		bb_die($lang['Group_not_exist']);
	}
	if (!$group_info['group_id'] || !$group_info['group_moderator'] || !$group_info['moderator_name'])
	{
		bb_die("Invalid group data [group_id: $group_id]");
	}
	$is_moderator = ($userdata['user_id'] == $group_info['group_moderator'] || IS_ADMIN);
}

if (!$group_id)
{
	// Show the main screen where the user can select a group.
	$groups = array();
	$pending = 10;
	$member  = 20;

	$sql = "
		SELECT
			g.group_name, g.group_description, g.group_id, g.group_type,
			IF(ug.user_id IS NOT NULL, IF(ug.user_pending = 1, $pending, $member), 0) AS membership,
			g.group_moderator, u.username AS moderator_name,
			IF(g.group_moderator = ug.user_id, 1, 0) AS is_group_mod
		FROM
			". GROUPS_TABLE ." g
		LEFT JOIN
			". USER_GROUP_TABLE ." ug ON
			    ug.group_id = g.group_id
			AND ug.user_id = ". $userdata['user_id'] ."
		LEFT JOIN
			". USERS_TABLE ." u ON g.group_moderator = u.user_id
		WHERE
			g.group_single_user = 0
		ORDER BY
			is_group_mod DESC,
			membership   DESC,
			g.group_type ASC,
			g.group_name ASC
	";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		if ($row['is_group_mod'])
		{
			$type = 'mod';
		}
		else if ($row['membership'] == $member)
		{
			$type = 'member';
		}
		else if ($row['membership'] == $pending)
		{
			$type = 'pending';
		}
		else if ($row['group_type'] == GROUP_OPEN)
		{
			$type = 'open';
		}
		else if ($row['group_type'] == GROUP_CLOSED)
		{
			$type = 'closed';
		}
		else if ($row['group_type'] == GROUP_HIDDEN && IS_ADMIN)
		{
			$type = 'hidden';
		}
		else
		{
			continue;
		}
		$groups[$type][$row['group_name']] = $row['group_id'];
	}

	if ($groups)
	{
		$s_hidden_fields = '';

		foreach ($groups as $type => $grp)
		{
			$template->assign_block_vars('groups', array(
				'MEMBERSHIP'   => $lang["Group_member_{$type}"],
				'GROUP_SELECT' => build_select(POST_GROUPS_URL, $grp),
			));
		}

		$template->assign_vars(array(
			'SELECT_GROUP'       => true,
			'PAGE_TITLE'         => $lang['Group_Control_Panel'],
			'S_USERGROUP_ACTION' => "groupcp.php",
			'S_HIDDEN_FIELDS'    => $s_hidden_fields,
		));
	}
	else
	{
		bb_die($lang['No_groups_exist']);
	}
}
else if (!empty($_POST['groupstatus']))
{
	if (!$is_moderator)
	{
		bb_die($lang['Not_group_moderator']);
	}

	$new_group_type = (int) $_POST['group_type'];

	if (!in_array($new_group_type, array(GROUP_OPEN, GROUP_CLOSED, GROUP_HIDDEN), true))
	{
		bb_die("Invalid group type: $new_group_type");
	}

	$db->query("
		UPDATE ". GROUPS_TABLE ." SET
			group_type = $new_group_type
		WHERE group_id = $group_id
			AND group_single_user = 0
		LIMIT 1
	");

	$message = $lang['Group_type_updated'] .'<br /><br />';
	$message .= sprintf($lang['Click_return_group'], '<a href="'. GROUP_URL ."$group_id" .'">', '</a>') .'<br /><br />';
	$message .= sprintf($lang['Click_return_index'], '<a href="/">', '</a>');

	bb_die($message);
}
else if (@$_POST['joingroup'])
{
	if ($group_info['group_type'] != GROUP_OPEN)
	{
		bb_die($lang['This_closed_group']);
	}

	$sql = "SELECT g.group_id, g.group_name, ug.user_id, u.user_email, u.username, u.user_lang
		FROM ". GROUPS_TABLE ." g
		LEFT JOIN ". USERS_TABLE ." u ON(u.user_id = g.group_moderator)
		LEFT JOIN ". USER_GROUP_TABLE ." ug ON(ug.group_id = g.group_id AND ug.user_id = {$userdata['user_id']})
		WHERE g.group_id = $group_id
			AND group_single_user = 0
			AND g.group_type = ". GROUP_OPEN ."
		LIMIT 1";

	$row = $moderator = $db->fetch_row($sql);

	if (!$row['group_id'])
	{
		bb_die($lang['No_groups_exist']);
	}
	if ($row['user_id'])
	{
		bb_die($lang['Already_member_group']);
	}

	add_user_into_group($group_id, $userdata['user_id'], 1);

	if ($bb_cfg['groupcp_send_email'])
	{
		include(SITE_DIR . 'includes/emailer.php');
		$emailer = new emailer($bb_cfg['smtp_delivery']);

		$emailer->from($bb_cfg['board_email']);
		$emailer->replyto($bb_cfg['board_email']);

		$emailer->use_template('group_request', $moderator['user_lang']);
		$emailer->email_address($moderator['user_email']);
		$emailer->set_subject($lang['Group_request']);

		$emailer->assign_vars(array(
			'USER'            => $userdata['username'],
			'SITENAME'        => $bb_cfg['sitename'],
			'GROUP_MODERATOR' => $moderator['username'],
			'EMAIL_SIG'       => ($bb_cfg['board_email_sig']) ? str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']) : '',
			'U_GROUPCP'       => make_url(GROUP_URL . $group_id),
		));
		$emailer->send();
		$emailer->reset();
	}

	$message = $lang['Group_joined'] .'<br /><br />';
	$message .= sprintf($lang['Click_return_group'], '<a href="'. GROUP_URL ."$group_id" .'">', '</a>') .'<br /><br />';
	$message .= sprintf($lang['Click_return_index'], '<a href="/">', '</a>');

	bb_die($message);
}
else if (!empty($_POST['unsub']) || !empty($_POST['unsubpending']))
{
	delete_user_group($group_id, $userdata['user_id']);

	$message = $lang['Unsub_success'] .'<br /><br />';
	$message .= sprintf($lang['Click_return_group'], '<a href="'. GROUP_URL ."$group_id" .'">', '</a>') .'<br /><br />';
	$message .= sprintf($lang['Click_return_index'], '<a href="/">', '</a>');

	bb_die($message);
}
else
{
	// Handle Additions, removals, approvals and denials
	$group_moderator = $group_info['group_moderator'];

	if (!empty($_POST['add']) || !empty($_POST['remove']) || !empty($_POST['approve']) || !empty($_POST['deny']))
	{
		if (!$is_moderator)
		{
			bb_die($lang['Not_group_moderator']);
		}

		if (!empty($_POST['add']))
		{
			if (!$row = get_userdata(@$_POST['username'], true))
			{
				bb_die($lang['Could_not_add_user']);
			}

			add_user_into_group($group_id, $row['user_id']);

			if ($bb_cfg['groupcp_send_email'])
			{
				require(SITE_DIR . 'includes/emailer.php');
				$emailer = new emailer($bb_cfg['smtp_delivery']);

				$emailer->from($bb_cfg['board_email']);
				$emailer->replyto($bb_cfg['board_email']);

				$emailer->use_template('group_added', $row['user_lang']);
				$emailer->email_address($row['user_email']);
				$emailer->set_subject($lang['Group_added']);

				$emailer->assign_vars(array(
					'SITENAME'   => $bb_cfg['sitename'],
					'GROUP_NAME' => $group_info['group_name'],
					'EMAIL_SIG'  => ($bb_cfg['board_email_sig']) ? str_replace('<br />', "\n", "-- \n". $bb_cfg['board_email_sig']) : '',
					'U_GROUPCP'  => make_url(GROUP_URL . $group_id),
				));
				$emailer->send();
				$emailer->reset();
			}
		}
		else
		{
			if (((!empty($_POST['approve']) || !empty($_POST['deny'])) && !empty($_POST['pending_members'])) || (!empty($_POST['remove']) && !empty($_POST['members'])))
			{
				$members = (!empty($_POST['approve']) || !empty($_POST['deny'])) ? $_POST['pending_members'] : $_POST['members'];

				$sql_in = array();
				foreach ($members as $members_id)
				{
					$sql_in[] = (int) $members_id;
				}
				if (!$sql_in = join(',', $sql_in))
				{
					bb_die($lang['None_selected']);
				}

				if (!empty($_POST['approve']))
				{
					$db->query("
						UPDATE ". USER_GROUP_TABLE ." SET
							user_pending = 0
						WHERE user_id IN($sql_in)
							AND group_id = $group_id
					");

					update_user_level($sql_in);
				}
				else if (!empty($_POST['deny']) || !empty($_POST['remove']))
				{
					$db->query("
						DELETE FROM ". USER_GROUP_TABLE ."
						WHERE user_id IN($sql_in)
							AND group_id = $group_id
					");

					if (!empty($_POST['remove']))
					{
						update_user_level($sql_in);
					}
				}
				// Email users when they are approved
				if (!empty($_POST['approve']) && $bb_cfg['groupcp_send_email'])
				{
					$sql_select = "SELECT user_email
						FROM ". USERS_TABLE ."
						WHERE user_id IN($sql_in)";

					if (!$result = $db->sql_query($sql_select))
					{
						message_die(GENERAL_ERROR, 'Could not get user email information', '', __LINE__, __FILE__, $sql);
					}

					$bcc_list = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$bcc_list[] = $row['user_email'];
					}

					$group_name = $group_info['group_name'];

					require(SITE_DIR . 'includes/emailer.php');
					$emailer = new emailer($bb_cfg['smtp_delivery']);

					$emailer->from($bb_cfg['board_email']);
					$emailer->replyto($bb_cfg['board_email']);

					for ($i=0, $cnt=count($bcc_list); $i < $cnt; $i++)
					{
						$emailer->bcc($bcc_list[$i]);
					}

					$emailer->use_template('group_approved');
					$emailer->set_subject($lang['Group_approved']);

					$emailer->assign_vars(array(
						'SITENAME'   => $bb_cfg['sitename'],
						'GROUP_NAME' => $group_name,
						'EMAIL_SIG'  => ($bb_cfg['board_email_sig']) ? str_replace('<br />', "\n", "-- \n". $bb_cfg['board_email_sig']) : '',
						'U_GROUPCP'  => make_url(GROUP_URL . $group_id),
					));
					$emailer->send();
					$emailer->reset();
				}
			}
		}
	}
	// END approve or deny

	// Get moderator details for this group
	$group_moderator = $db->fetch_row("
		SELECT *
		FROM ". USERS_TABLE ."
		WHERE user_id = ". $group_info['group_moderator'] ."
	");

	// Get user information for this group
	$members_count = $modgroup_pending_count = 0;

	// Members
	$group_members = $db->fetch_rowset("
		SELECT u.username, u.user_id, u.user_opt, u.user_posts, u.user_regdate, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_msnm, ug.user_pending
		FROM ". USER_GROUP_TABLE ." ug, ". USERS_TABLE ." u
		WHERE ug.group_id = $group_id
			AND ug.user_pending = 0
			AND ug.user_id <> ". $group_moderator['user_id'] ."
			AND u.user_id = ug.user_id
		ORDER BY u.username
		LIMIT $start, ". ($per_page + 1) ."
	");
	$members_count = count($group_members);

	if ($members_count == $per_page + 1)
	{
		array_pop($group_members);
	}

	if ($members_count > $per_page)
	{
		$items_count = $start + ($per_page * 2);
		$pages = '?';
	}
	else
	{
		$items_count = $start + $members_count;
		$pages = (!$members_count) ? 1 : ceil($items_count / $per_page);
	}

	$template->assign_vars(array(
		'PAGINATION'  => generate_pagination(GROUP_URL . $group_id, $items_count, $per_page, $start),
		'PAGE_NUMBER' => sprintf($lang['Page_of'], floor($start / $per_page) + 1, $pages),
	));

	// Pending
	if ($is_moderator)
	{
		$modgroup_pending_list = $db->fetch_rowset("
			SELECT u.username, u.user_id, u.user_opt, u.user_posts, u.user_regdate, u.user_from, u.user_website, u.user_email, u.user_icq, u.user_aim, u.user_yim, u.user_msnm
			FROM ". USER_GROUP_TABLE ." ug, ". USERS_TABLE ." u
			WHERE ug.group_id = $group_id
				AND ug.user_pending = 1
				AND u.user_id = ug.user_id
			ORDER BY u.username
			LIMIT 200
		");
		$modgroup_pending_count = count($modgroup_pending_list);
	}

	// Current user membership
	$is_group_member = $is_group_pending_member = false;

	$sql = "SELECT user_pending
		FROM ". USER_GROUP_TABLE ."
		WHERE group_id = $group_id
			AND user_id = ". $userdata['user_id'] ."
		LIMIT 1";

	if ($row = $db->fetch_row($sql))
	{
		if ($row['user_pending'] == 0)
		{
			$is_group_member = true;
		}
		else
		{
			$is_group_pending_member = true;
		}
	}

	if ($userdata['user_id'] == $group_moderator['user_id'])
	{
		$group_details = $lang['Are_group_moderator'];
		$s_hidden_fields = '<input type="hidden" name="'. POST_GROUPS_URL .'" value="'. $group_id .'" />';
	}
	else if ($is_group_member || $is_group_pending_member)
	{
		$template->assign_vars(array(
			'SHOW_UNSUBSCRIBE_CONTROLS' => true,
			'CONTROL_NAME' => ($is_group_member) ? 'unsub' : 'unsubpending',
		));
		$group_details = ($is_group_pending_member) ? $lang['Pending_this_group'] : $lang['Member_this_group'];
		$s_hidden_fields = '<input type="hidden" name="'. POST_GROUPS_URL .'" value="'. $group_id .'" />';
	}
	else if (IS_GUEST)
	{
		$group_details = $lang['Login_to_join'];
		$s_hidden_fields = '';
	}
	else
	{
		if ($group_info['group_type'] == GROUP_OPEN)
		{
			$template->assign_var('SHOW_SUBSCRIBE_CONTROLS');

			$group_details = $lang['This_open_group'];
			$s_hidden_fields = '<input type="hidden" name="'. POST_GROUPS_URL .'" value="'. $group_id .'" />';
		}
		else if ($group_info['group_type'] == GROUP_CLOSED)
		{
			$group_details = $lang['This_closed_group'];
			$s_hidden_fields = '';
		}
		else if ($group_info['group_type'] == GROUP_HIDDEN)
		{
			$group_details = $lang['This_hidden_group'];
			$s_hidden_fields = '';
		}
	}

	// Add the moderator
	$username = $group_moderator['username'];
	$user_id = $group_moderator['user_id'];

	generate_user_info($group_moderator, $bb_cfg['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $icq_status_img, $icq_img, $icq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

	$template->assign_vars(array(
		'GROUP_INFO' => true,
		'PAGE_TITLE' => $lang['Group_Control_Panel'],

		'GROUP_NAME' => htmlCHR($group_info['group_name']),
		'GROUP_DESCRIPTION' => $group_info['group_description'],
		'GROUP_DETAILS' => $group_details,
		'MOD_USERNAME' => $username,
		'MOD_FROM' => $from,
		'MOD_JOINED' => $joined,
		'MOD_POSTS' => $posts,
		'MOD_AVATAR_IMG' => $poster_avatar,
		'MOD_PROFILE_IMG' => $profile_img,
		'MOD_PROFILE' => $profile,
		'MOD_SEARCH_IMG' => $search_img,
		'MOD_SEARCH' => $search,
		'MOD_PM_IMG' => $pm_img,
		'MOD_PM' => $pm,
		'MOD_EMAIL_IMG' => $email_img,
		'MOD_EMAIL' => $email,
		'MOD_WWW_IMG' => $www_img,
		'MOD_WWW' => $www,
		'MOD_ICQ_STATUS_IMG' => $icq_status_img,
		'MOD_ICQ_IMG' => $icq_img,
		'MOD_ICQ' => $icq,
		'MOD_AIM_IMG' => $aim_img,
		'MOD_AIM' => $aim,
		'MOD_MSN_IMG' => $msn_img,
		'MOD_MSN' => $msn,
		'MOD_YIM_IMG' => $yim_img,
		'MOD_YIM' => $yim,

		'U_MOD_VIEWPROFILE' => "profile.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id",
		'U_SEARCH_USER' => "search.php?mode=searchuser",

		'S_GROUP_OPEN_TYPE' => GROUP_OPEN,
		'S_GROUP_CLOSED_TYPE' => GROUP_CLOSED,
		'S_GROUP_HIDDEN_TYPE' => GROUP_HIDDEN,
		'S_GROUP_OPEN_CHECKED' => ($group_info['group_type'] == GROUP_OPEN) ? ' checked="checked"' : '',
		'S_GROUP_CLOSED_CHECKED' => ($group_info['group_type'] == GROUP_CLOSED) ? ' checked="checked"' : '',
		'S_GROUP_HIDDEN_CHECKED' => ($group_info['group_type'] == GROUP_HIDDEN) ? ' checked="checked"' : '',
		'S_HIDDEN_FIELDS' => $s_hidden_fields,
		'S_MODE_SELECT' => $select_sort_mode,
		'S_ORDER_SELECT' => $select_sort_order,
		'S_GROUPCP_ACTION' => "groupcp.php?" . POST_GROUPS_URL . "=$group_id",
	));

	// Dump out the remaining users
	foreach ($group_members as $i => $member)
	{
		$username = $member['username'];
		$user_id = $member['user_id'];

		generate_user_info($member, $bb_cfg['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $icq_status_img, $icq_img, $icq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

		if ($group_info['group_type'] != GROUP_HIDDEN || $is_group_member || $is_moderator)
		{
			$row_class = !($i % 2) ? 'row1' : 'row2';

			$template->assign_block_vars('member', array(
				'ROW_CLASS' => $row_class,
				'USERNAME' => $username,
				'FROM' => $from,
				'JOINED' => $joined,
				'POSTS' => $posts,
				'USER_ID' => $user_id,
				'AVATAR_IMG' => $poster_avatar,
				'PROFILE_IMG' => $profile_img,
				'PROFILE' => $profile,
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

				'U_VIEWPROFILE' => "profile.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id",
			));

			if ($is_moderator)
			{
				$template->assign_block_vars('member.switch_mod_option', array());
			}
		}
	}

	// No group members
	if (!$members_count)
	{
		$template->assign_block_vars('switch_no_members', array());
	}

	// No group members
	if ($group_info['group_type'] == GROUP_HIDDEN && !$is_group_member && !$is_moderator)
	{
		$template->assign_block_vars('switch_hidden_group', array());
	}

	//
	// We've displayed the members who belong to the group, now we
	// do that pending memebers...
	//
	if ($is_moderator && $modgroup_pending_list)
	{
		foreach ($modgroup_pending_list as $i => $member)
		{
			$username = $member['username'];
			$user_id = $member['user_id'];

			generate_user_info($member, $bb_cfg['default_dateformat'], $is_moderator, $from, $posts, $joined, $poster_avatar, $profile_img, $profile, $search_img, $search, $pm_img, $pm, $email_img, $email, $www_img, $www, $icq_status_img, $icq_img, $icq, $aim_img, $aim, $msn_img, $msn, $yim_img, $yim);

			$row_class = !($i % 2) ? 'row1' : 'row2';

			$user_select = '<input type="checkbox" name="member[]" value="'. $user_id .'">';

			$template->assign_block_vars('pending', array(
				'ROW_CLASS' => $row_class,
				'USERNAME' => $username,
				'FROM' => $from,
				'JOINED' => $joined,
				'POSTS' => $posts,
				'USER_ID' => $user_id,
				'AVATAR_IMG' => $poster_avatar,
				'PROFILE_IMG' => $profile_img,
				'PROFILE' => $profile,
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

				'U_VIEWPROFILE' => "profile.php?mode=viewprofile&amp;". POST_USERS_URL ."=$user_id",
			));
		}

		$template->assign_vars(array(
			'PENDING_USERS' => true,
		));
	}

	if ($is_moderator)
	{
		$template->assign_block_vars('switch_mod_option', array());
		$template->assign_block_vars('switch_add_member', array());
	}
}

print_page('groupcp.tpl');

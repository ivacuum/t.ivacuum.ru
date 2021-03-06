<?php

/**
* These are functions called directly from phpBB2 Files
*/

/**
* Include the FAQ-File (faq.php)
*/
function attach_faq_include($lang_file)
{
	global $board_config, $faq, $attach_config;

	if (intval($attach_config['disable_mod']))
	{
		return;
	}

	if ($lang_file == 'lang_faq')
	{
		$language = attach_mod_get_lang('lang_faq_attach');
		require SITE_DIR . 'language/lang_' . $language . '/lang_faq_attach.php';
	}
}

/**
* Setup Basic Authentication
*/
// moved to auth

/**
* Setup Forum Authentication (admin/admin_forumauth.php)
*/
//admin/admin_forumauth.php


/**
* Setup Usergroup Authentication
*/
//admin/admin_ug_auth.php

/**
* Setup s_auth_can in viewforum and viewtopic (viewtopic.php/viewforum.php)
*/
function attach_build_auth_levels($is_auth, &$s_auth_can)
{
	global $lang, $attach_config, $forum_id;

	if (intval($attach_config['disable_mod']))
	{
		return;
	}

	// If you want to have the rules window link within the forum view too, comment out the two lines, and comment the third line
//	$rules_link = '(<a href="attach_rules.php?f=' . $forum_id . '" target="_blank">Rules</a>)';
//	$s_auth_can .= ( ( $is_auth['auth_attachments'] ) ? $rules_link . ' ' . $lang['Rules_attach_can'] : $lang['Rules_attach_cannot'] ) . '<br />';
	$s_auth_can .= (($is_auth['auth_attachments']) ? $lang['Rules_attach_can'] : $lang['Rules_attach_cannot'] ) . '<br />';

	$s_auth_can .= (($is_auth['auth_download']) ? $lang['Rules_download_can'] : $lang['Rules_download_cannot'] ) . '<br />';
}

/**
* Called from admin_users.php and admin_groups.php in order to process Quota Settings (admin/admin_users.php:admin/admin_groups.php)
*/
function attachment_quota_settings($admin_mode, $submit = false, $mode)
{
	global $template, $db, $lang, $lang, $attach_config;

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if ($attach_config['upload_dir'][0] == '/' || ($attach_config['upload_dir'][0] != '/' && $attach_config['upload_dir'][1] == ':'))
		{
			$upload_dir = $attach_config['upload_dir'];
		}
		else
		{
			$upload_dir = SITE_DIR . $attach_config['upload_dir'];
		}
	}
	else
	{
		$upload_dir = $attach_config['download_path'];
	}

	require_once SITE_DIR . 'attach_mod/includes/functions_selects.php';
	require_once SITE_DIR . 'attach_mod/includes/functions_admin.php';

	$user_id = 0;

	if ($admin_mode == 'user')
	{
		// We overwrite submit here... to be sure
		$submit = (isset($_POST['submit'])) ? true : false;

		if (!$submit && $mode != 'save')
		{
			$user_id = get_var(POST_USERS_URL, 0);
			$u_name = get_var('username', '');

			if (!$user_id && !$u_name)
			{
				message_die(GENERAL_MESSAGE, $lang['No_user_id_specified'] );
			}

			if ($user_id)
			{
				$this_userdata['user_id'] = $user_id;
			}
			else
			{
				// Get userdata is handling the sanitizing of username
				$this_userdata = get_userdata($_POST['username'], true);
			}

			$user_id = (int) $this_userdata['user_id'];
		}
		else
		{
			$user_id = get_var('id', 0);

			if (!$user_id)
			{
				message_die(GENERAL_MESSAGE, $lang['No_user_id_specified'] );
			}
		}
	}

	if ($admin_mode == 'user' && !$submit && $mode != 'save')
	{
		// Show the contents
		$sql = 'SELECT quota_limit_id, quota_type FROM bb_attach_quota
			WHERE user_id = ' . (int) $user_id;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Unable to get Quota Settings', '', __LINE__, __FILE__, $sql);
		}

		$pm_quota = $upload_quota = 0;

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if ($row['quota_type'] == QUOTA_UPLOAD_LIMIT)
				{
					$upload_quota = $row['quota_limit_id'];
				}
				else if ($row['quota_type'] == QUOTA_PM_LIMIT)
				{
					$pm_quota = $row['quota_limit_id'];
				}
			}
			while ($row = $db->sql_fetchrow($result));
		}
		else
		{
			// Set Default Quota Limit
			$upload_quota = $attach_config['default_upload_quota'];
			$pm_quota = $attach_config['default_pm_quota'];

		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_SELECT_UPLOAD_QUOTA'		=> quota_limit_select('user_upload_quota', $upload_quota),
			'S_SELECT_PM_QUOTA'			=> quota_limit_select('user_pm_quota', $pm_quota),
		));
	}

	if ($admin_mode == 'user' && $submit && @$_POST['deleteuser'])
	{
		process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, 0);
		process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, 0);
	}
	else if ($admin_mode == 'user' && $submit && $mode == 'save')
	{
		// Get the contents
		$upload_quota = get_var('user_upload_quota', 0);
		$pm_quota = get_var('user_pm_quota', 0);

		process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, $upload_quota);
		process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, $pm_quota);
	}

	if ($admin_mode == 'group' && $mode == 'newgroup')
	{
		return;
	}

	if ($admin_mode == 'group' && !$submit && isset($_POST['edit']))
	{
		// Get group id again, we do not trust phpBB here, Mods may be installed ;)
		$group_id = get_var(POST_GROUPS_URL, 0);

		// Show the contents
		$sql = 'SELECT quota_limit_id, quota_type FROM bb_attach_quota
			WHERE group_id = ' . (int) $group_id;

		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Unable to get Quota Settings', '', __LINE__, __FILE__, $sql);
		}

		$pm_quota = $upload_quota = 0;

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				if ($row['quota_type'] == QUOTA_UPLOAD_LIMIT)
				{
					$upload_quota = $row['quota_limit_id'];
				}
				else if ($row['quota_type'] == QUOTA_PM_LIMIT)
				{
					$pm_quota = $row['quota_limit_id'];
				}
			}
			while ($row = $db->sql_fetchrow($result));
		}
		else
		{
			// Set Default Quota Limit
			$upload_quota = $attach_config['default_upload_quota'];
			$pm_quota = $attach_config['default_pm_quota'];
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_SELECT_UPLOAD_QUOTA'	=> quota_limit_select('group_upload_quota', $upload_quota),
			'S_SELECT_PM_QUOTA'		=> quota_limit_select('group_pm_quota', $pm_quota),
		));
	}

	if ($admin_mode == 'group' && $submit && isset($_POST['group_delete']))
	{
		$group_id = get_var(POST_GROUPS_URL, 0);

		process_quota_settings($admin_mode, $group_id, QUOTA_UPLOAD_LIMIT, 0);
		process_quota_settings($admin_mode, $group_id, QUOTA_PM_LIMIT, 0);
	}
	else if ($admin_mode == 'group' && $submit)
	{
		$group_id = get_var(POST_GROUPS_URL, 0);

		// Get the contents
		$upload_quota = get_var('group_upload_quota', 0);
		$pm_quota = get_var('group_pm_quota', 0);

		process_quota_settings($admin_mode, $group_id, QUOTA_UPLOAD_LIMIT, $upload_quota);
		process_quota_settings($admin_mode, $group_id, QUOTA_PM_LIMIT, $pm_quota);
	}

}

/**
* Called from usercp_viewprofile, displays the User Upload Quota Box, Upload Stats and a Link to the User Attachment Control Panel
* Groups are able to be grabbed, but it's not used within the Attachment Mod. ;)
* (includes/ucp/usercp_viewprofile.php)
*/
function display_upload_attach_box_limits($user_id, $group_id = 0)
{
	global $attach_config, $board_config, $lang, $db, $template, $userdata, $profiledata;

	if (intval($attach_config['disable_mod']))
	{
		return;
	}

	if (!IS_ADMIN && $userdata['user_id'] != $user_id)
	{
		return;
	}

	if (!$user_id)
	{
		return;
	}

	// Return if the user is not within the to be listed Group
	if ($group_id)
	{
		if (!user_in_group($user_id, $group_id))
		{
			return;
		}
	}

	$user_id = (int) $user_id;
	$group_id = (int) $group_id;

	$attachments = new attach_posting();
	$attachments->page = 0;

	// Get the assigned Quota Limit. For Groups, we are directly getting the value, because this Quota can change from user to user.
	if ($group_id)
	{
		$sql = 'SELECT l.quota_limit
			FROM bb_attach_quota q, bb_quota_limits l
			WHERE q.group_id = ' . (int) $group_id . '
				AND q.quota_type = ' . QUOTA_UPLOAD_LIMIT . '
				AND q.quota_limit_id = l.quota_limit_id
			LIMIT 1';

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not get Group Quota', '', __LINE__, __FILE__, $sql);
		}

		if ($db->sql_numrows($result) > 0)
		{
			$row = $db->sql_fetchrow($result);
			$attach_config['upload_filesize_limit'] = intval($row['quota_limit']);
			$db->sql_freeresult($result);
		}
		else
		{
			$db->sql_freeresult($result);

			// Set Default Quota Limit
			$quota_id = intval($attach_config['default_upload_quota']);

			if ($quota_id == 0)
			{
				$attach_config['upload_filesize_limit'] = $attach_config['attachment_quota'];
			}
			else
			{
				$sql = 'SELECT quota_limit
					FROM bb_quota_limits
					WHERE quota_limit_id = ' . (int) $quota_id . '
					LIMIT 1';

				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not get Quota Limit', '', __LINE__, __FILE__, $sql);
				}

				if ($db->sql_numrows($result) > 0)
				{
					$row = $db->sql_fetchrow($result);
					$attach_config['upload_filesize_limit'] = $row['quota_limit'];
				}
				else
				{
					$attach_config['upload_filesize_limit'] = $attach_config['attachment_quota'];
				}
				$db->sql_freeresult($result);
			}
		}
	}
	else
	{
		if (is_array($profiledata))
		{
			$attachments->get_quota_limits($profiledata, $user_id);
		}
		else
		{
			$attachments->get_quota_limits($userdata, $user_id);
		}
	}

	if (!$attach_config['upload_filesize_limit'])
	{
		$upload_filesize_limit = $attach_config['attachment_quota'];
	}
	else
	{
		$upload_filesize_limit = $attach_config['upload_filesize_limit'];
	}

	if ($upload_filesize_limit == 0)
	{
		$user_quota = $lang['Unlimited'];
	}
	else
	{
		$size_lang = ($upload_filesize_limit >= 1048576) ? $lang['MB'] : ( ($upload_filesize_limit >= 1024) ? $lang['KB'] : $lang['Bytes'] );

		if ($upload_filesize_limit >= 1048576)
		{
			$user_quota = (round($upload_filesize_limit / 1048576 * 100) / 100) . ' ' . $size_lang;
		}
		else if ($upload_filesize_limit >= 1024)
		{
			$user_quota = (round($upload_filesize_limit / 1024 * 100) / 100) . ' ' . $size_lang;
		}
		else
		{
			$user_quota = ($upload_filesize_limit) . ' ' . $size_lang;
		}
	}

	// Get all attach_id's the specific user posted, but only uploads to the board and not Private Messages
	$sql = 'SELECT attach_id
		FROM bb_attachments
		WHERE user_id_1 = ' . (int) $user_id . '
		GROUP BY attach_id';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
	}

	$attach_ids = $db->sql_fetchrowset($result);
	$num_attach_ids = $db->sql_numrows($result);
	$db->sql_freeresult($result);
	$attach_id = array();

	for ($j = 0; $j < $num_attach_ids; $j++)
	{
		$attach_id[] = intval($attach_ids[$j]['attach_id']);
	}

	$upload_filesize = (sizeof($attach_id) > 0) ? get_total_attach_filesize($attach_id) : 0;

	$size_lang = ($upload_filesize >= 1048576) ? $lang['MB'] : ( ($upload_filesize >= 1024) ? $lang['KB'] : $lang['Bytes'] );

	if ($upload_filesize >= 1048576)
	{
		$user_uploaded = (round($upload_filesize / 1048576 * 100) / 100) . ' ' . $size_lang;
	}
	else if ($upload_filesize >= 1024)
	{
		$user_uploaded = (round($upload_filesize / 1024 * 100) / 100) . ' ' . $size_lang;
	}
	else
	{
		$user_uploaded = ($upload_filesize) . ' ' . $size_lang;
	}

	$upload_limit_pct = ( $upload_filesize_limit > 0 ) ? round(( $upload_filesize / $upload_filesize_limit ) * 100) : 0;
	$upload_limit_img_length = ( $upload_filesize_limit > 0 ) ? round(( $upload_filesize / $upload_filesize_limit ) * $board_config['privmsg_graphic_length']) : 0;
	if ($upload_limit_pct > 100)
	{
		$upload_limit_img_length = $board_config['privmsg_graphic_length'];
	}
	$upload_limit_remain = ( $upload_filesize_limit > 0 ) ? $upload_filesize_limit - $upload_filesize : 100;

	$l_box_size_status = sprintf($lang['Upload_percent_profile'], $upload_limit_pct);

	$template->assign_block_vars('switch_upload_limits', array());

	$template->assign_vars(array(
		'L_UACP'			=> $lang['UACP'],
		'U_UACP'			=> 'uacp.php?u=' . $user_id . '&amp;sid=' . $userdata['session_id'],
		'UPLOADED' => sprintf($lang['User_uploaded_profile'], $user_uploaded),
		'QUOTA' => sprintf($lang['User_quota_profile'], $user_quota),
		'UPLOAD_LIMIT_IMG_WIDTH' => $upload_limit_img_length,
		'UPLOAD_LIMIT_PERCENT' => $upload_limit_pct,
		'PERCENT_FULL' => $l_box_size_status)
	);
}

/**
* Prune Attachments (includes/prune.php)
*/
function prune_attachments($sql_post)
{
	// prune it.
	delete_attachment($sql_post);
}



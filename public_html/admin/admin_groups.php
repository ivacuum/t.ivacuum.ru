<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['Groups']['Manage'] = basename(__FILE__);
	return;
}
require './pagestart.php';
// ACP Header - END

require SITE_DIR . 'includes/functions_group.php';

$group_id = (@$_REQUEST[POST_GROUPS_URL]) ? intval($_REQUEST[POST_GROUPS_URL]) : 0;
$mode     = (@$_REQUEST['mode']) ? strval($_REQUEST['mode']) : '';

attachment_quota_settings('group', @$_POST['group_update'], $mode);

if (!empty($_POST['edit']) || !empty($_POST['new']))
{
	if (!empty($_POST['edit']))
	{
		if (!$row = get_group_data($group_id))
		{
			bb_die($lang['Group_not_exist']);
		}
		$group_info = array(
			'group_name'        => $row['group_name'],
			'group_description' => $row['group_description'],
			'group_moderator'   => $row['group_moderator'],
			'group_mod_name'    => $row['moderator_name'],
			'group_type'        => $row['group_type'],
		);
		$mode = 'editgroup';
		$template->assign_block_vars('group_edit', array());
	}
	else if (!empty($_POST['new']))
	{
		$group_info = array(
			'group_name'        => '',
			'group_description' => '',
			'group_moderator'   => '',
			'group_mod_name'    => '',
			'group_type'        => GROUP_OPEN,
		);
		$mode = 'newgroup';
	}

	// Ok, now we know everything about them, let's show the page.
	$s_hidden_fields = '
		<input type="hidden" name="mode" value="'. $mode .'" />
		<input type="hidden" name="'. POST_GROUPS_URL .'" value="'. $group_id .'" />
	';

	$template->assign_vars(array(
		'TPL_EDIT_GROUP'         => true,

		'GROUP_NAME'             => htmlspecialchars($group_info['group_name']),
		'GROUP_DESCRIPTION'      => htmlspecialchars($group_info['group_description']),
		'GROUP_MODERATOR'        => replace_quote($group_info['group_mod_name']),
		'T_GROUP_EDIT_DELETE'    => ($mode == 'newgroup') ? $lang['CREATE_NEW_GROUP'] : $lang['Edit_group'],
		'U_SEARCH_USER'          => append_sid("/search.php?mode=searchuser"),
		'S_GROUP_OPEN_TYPE'      => GROUP_OPEN,
		'S_GROUP_CLOSED_TYPE'    => GROUP_CLOSED,
		'S_GROUP_HIDDEN_TYPE'    => GROUP_HIDDEN,
		'S_GROUP_OPEN_CHECKED'   => ($group_info['group_type'] == GROUP_OPEN) ? HTML_CHECKED : '',
		'S_GROUP_CLOSED_CHECKED' => ($group_info['group_type'] == GROUP_CLOSED) ? HTML_CHECKED : '',
		'S_GROUP_HIDDEN_CHECKED' => ($group_info['group_type'] == GROUP_HIDDEN ) ? HTML_CHECKED : '',
		'S_GROUP_ACTION'         => append_sid("admin_groups.php"),
		'S_HIDDEN_FIELDS'        => $s_hidden_fields,
	));
}
else if (!empty($_POST['group_update']))
{
	if (!empty($_POST['group_delete']))
	{
		if (!$group_info = get_group_data($group_id))
		{
			bb_die($lang['Group_not_exist']);
		}
		// Delete Group
		delete_group($group_id);

		$message = $lang['Deleted_group'] .'<br /><br />';
		$message .= sprintf($lang['Click_return_groupsadmin'], '<a href="'. append_sid("admin_groups.php") .'">', '</a>') .'<br /><br />';
		$message .= sprintf($lang['Click_return_admin_index'], '<a href="'. append_sid("index.php?pane=right") .'">', '</a>');

		bb_die($message);
	}
	else
	{
		$group_type = isset($_POST['group_type']) ? intval($_POST['group_type']) : GROUP_OPEN;
		$group_name = isset($_POST['group_name']) ? trim($_POST['group_name']) : '';
		$group_desc = isset($_POST['group_description']) ? trim($_POST['group_description']) : '';

		$group_moderator = isset($_POST['username']) ? $_POST['username'] : '';

		if ($group_name === '')
		{
			bb_die($lang['No_group_name']);
		}
		else if ($group_moderator === '')
		{
			bb_die($lang['No_group_moderator']);
		}
		$this_userdata = get_userdata($group_moderator, true);

		if (!$group_moderator = $this_userdata['user_id'])
		{
			bb_die($lang['No_group_moderator']);
		}

		$sql_ary = array(
			'group_type'        => (int) $group_type,
			'group_name'        => (string) $group_name,
			'group_description' => (string) $group_desc,
			'group_moderator'   => (int) $group_moderator,
			'group_single_user' => 0,
		);

		if ($mode == "editgroup")
		{
			if (!$group_info = get_group_data($group_id))
			{
				bb_die($lang['Group_not_exist']);
			}

			if ($group_info['group_moderator'] != $group_moderator)
			{
				// Create user_group for new group's moderator
				add_user_into_group($group_id, $group_moderator);

				// Delete old moderator's user_group
				if (@$_POST['delete_old_moderator'])
				{
					delete_user_group($group_id, $group_info['group_moderator']);
				}
			}

			$sql_args = $db->build_array('UPDATE', $sql_ary);

			// Update group's data
			$db->query("UPDATE bb_groups SET $sql_args WHERE group_id = $group_id");

			$message = $lang['Updated_group'] .'<br /><br />';
			$message .= sprintf($lang['Click_return_groupsadmin'], '<a href="'. append_sid("admin_groups.php") .'">', '</a>') .'<br /><br />';
			$message .= sprintf($lang['Click_return_admin_index'], '<a href="'. append_sid("index.php?pane=right") .'">', '</a>');

			bb_die($message);
		}
		else if ($mode == 'newgroup')
		{
			$sql_args = $db->build_array('INSERT', $sql_ary);

			// Create new group
			$db->query("INSERT INTO bb_groups $sql_args");
			$new_group_id = $db->sql_nextid();

			// Create user_group for group's moderator
			add_user_into_group($new_group_id, $group_moderator);

			$message = $lang['Added_new_group'] .'<br /><br />';
			$message .= sprintf($lang['Click_return_groupsadmin'], '<a href="'. append_sid("admin_groups.php") .'">', '</a>') .'<br /><br />';
			$message .= sprintf($lang['Click_return_admin_index'], '<a href="'. append_sid("index.php?pane=right") .'">', '</a>');

			bb_die($message);
		}
		else
		{
			bb_die($lang['No_group_action']);
		}
	}
}
else
{
	$template->assign_vars(array(
		'TPL_GROUP_SELECT' => true,

		'S_GROUP_ACTION'   => append_sid("admin_groups.php"),
		'S_GROUP_SELECT'   => get_select('groups'),
	));
}

print_page('admin_groups.tpl', 'admin');

<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['Attachments']['Control_Panel'] = basename(__FILE__);
	return;
}
require './pagestart.php';
// ACP Header - END

$total_attachments = 0;

if (!intval($attach_config['allow_ftp_upload']))
{
	if ( ($attach_config['upload_dir'][0] == '/') || ( ($attach_config['upload_dir'][0] != '/') && ($attach_config['upload_dir'][1] == ':') ) )
	{
		$upload_dir = $attach_config['upload_dir'];
	}
	else
	{
		$upload_dir = '../' . $attach_config['upload_dir'];
	}
}
else
{
	$upload_dir = $attach_config['download_path'];
}

require SITE_DIR . 'attach_mod/includes/functions_selects.php';

// Check if the language got included
if (!isset($lang['Test_settings_successful']))
{
	// include_once is used within the function
	include_attach_lang();
}

// Init Variables
$start = get_var('start', 0);
$sort_order = get_var('order', 'ASC');
$sort_order = ($sort_order == 'ASC') ? 'ASC' : 'DESC';
$mode = get_var('mode', '');
$view = get_var('view', '');
$uid = (isset($_POST['u_id'])) ? get_var('u_id', 0) : get_var('uid', 0);

$view = (isset($_POST['search']) && $_POST['search']) ? 'attachments' : $view;

// process modes based on view
if ($view == 'username')
{
	$mode_types_text = array($lang['Sort_Username'], $lang['Sort_Attachments'], $lang['Sort_Size']);
	$mode_types = array('username', 'attachments', 'filesize');

	if (!$mode)
	{
		$mode = 'attachments';
		$sort_order = 'DESC';
	}
}
else if ($view == 'attachments')
{
	$mode_types_text = array($lang['Sort_Filename'], $lang['Sort_Comment'], $lang['Sort_Extension'], $lang['Sort_Size'], $lang['Sort_Downloads'], $lang['Sort_Posttime'], /*$lang['Sort_Posts']*/);
	$mode_types = array('real_filename', 'comment', 'extension', 'filesize', 'downloads', 'post_time'/*, 'posts'*/);

	if (!$mode)
	{
		$mode = 'real_filename';
		$sort_order = 'ASC';
	}
}
else if ($view == 'search')
{
	$mode_types_text = array($lang['Sort_Filename'], $lang['Sort_Comment'], $lang['Sort_Extension'], $lang['Sort_Size'], $lang['Sort_Downloads'], $lang['Sort_Posttime'], /*$lang['Sort_Posts']*/);
	$mode_types = array('real_filename', 'comment', 'extension', 'filesize', 'downloads', 'post_time'/*, 'posts'*/);

	$sort_order = 'DESC';
}
else
{
	$view = 'stats';
	$mode_types_text = array();
	$sort_order = 'ASC';
}


// Pagination ?
$do_pagination = ($view != 'stats' && $view != 'search') ? true : false;

// Set Order
$order_by = '';

if ($view == 'username')
{
	switch($mode)
	{
		case 'username':
			$order_by = 'ORDER BY u.username ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'attachments':
			$order_by = 'ORDER BY total_attachments ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'filesize':
			$order_by = 'ORDER BY total_size ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		default:
			$mode = 'attachments';
			$sort_order = 'DESC';
			$order_by = 'ORDER BY total_attachments ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
	}
}
else if ($view == 'attachments')
{
	switch($mode)
	{
		case 'filename':
			$order_by = 'ORDER BY a.real_filename ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'comment':
			$order_by = 'ORDER BY a.comment ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'extension':
			$order_by = 'ORDER BY a.extension ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'filesize':
			$order_by = 'ORDER BY a.filesize ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'downloads':
			$order_by = 'ORDER BY a.download_count ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		case 'post_time':
			$order_by = 'ORDER BY a.filetime ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
		default:
			$mode = 'a.real_filename';
			$sort_order = 'ASC';
			$order_by = 'ORDER BY a.real_filename ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
			break;
	}
}

// Set select fields
$view_types_text = array($lang['View_Statistic'], $lang['View_Search'], $lang['View_Username'], $lang['View_Attachments']);
$view_types = array('stats', 'search', 'username', 'attachments');

$select_view = '<select name="view">';

for($i = 0; $i < count($view_types_text); $i++)
{
	$selected = ($view == $view_types[$i]) ? ' selected="selected"' : '';
	$select_view .= '<option value="' . $view_types[$i] . '"' . $selected . '>' . $view_types_text[$i] . '</option>';
}
$select_view .= '</select>';

if (count($mode_types_text) > 0)
{
	$select_sort_mode = '<select name="mode">';

	for($i = 0; $i < count($mode_types_text); $i++)
	{
		$selected = ($mode == $mode_types[$i]) ? ' selected="selected"' : '';
		$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
	}
	$select_sort_mode .= '</select>';
}

$select_sort_order = '<select name="order">';
if ($sort_order == 'ASC')
{
	$select_sort_order .= '<option value="ASC" selected="selected">' . $lang['ASC'] . '</option><option value="DESC">' . $lang['DESC'] . '</option>';
}
else
{
	$select_sort_order .= '<option value="ASC">' . $lang['ASC'] . '</option><option value="DESC" selected="selected">' . $lang['DESC'] . '</option>';
}
$select_sort_order .= '</select>';

$submit_change = ( isset($_POST['submit_change']) ) ? TRUE : FALSE;
$delete = ( isset($_POST['delete']) ) ? TRUE : FALSE;
$delete_id_list = get_var('delete_id_list', array(0));

$confirm = isset($_POST['confirm']);

if ($confirm && sizeof($delete_id_list) > 0)
{
	$attachments = array();

	delete_attachment(0, $delete_id_list);
}
else if ($delete && sizeof($delete_id_list) > 0)
{
	// Not confirmed, show confirmation message
	$hidden_fields = '<input type="hidden" name="view" value="' . $view . '" />';
	$hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';
	$hidden_fields .= '<input type="hidden" name="order" value="' . $sort_order . '" />';
	$hidden_fields .= '<input type="hidden" name="u_id" value="' . $uid . '" />';
	$hidden_fields .= '<input type="hidden" name="start" value="' . $start . '" />';

	for ($i = 0; $i < sizeof($delete_id_list); $i++)
	{
		$hidden_fields .= '<input type="hidden" name="delete_id_list[]" value="' . $delete_id_list[$i] . '" />';
	}

	print_confirmation(array(
		'QUESTION'      => $lang['Confirm_delete_attachments'],
		'FORM_ACTION'   => "admin_attach_cp.php",
		'HIDDEN_FIELDS' => $hidden_fields,
	));
}

// Assign Default Template Vars
$template->assign_vars(array(
	'L_VIEW' => $lang['View'],
	'L_CONTROL_PANEL_TITLE' => $lang['Control_panel_title'],
	'L_CONTROL_PANEL_EXPLAIN' => $lang['Control_panel_explain'],

	'S_VIEW_SELECT' => $select_view,
	'S_MODE_ACTION' => append_sid('admin_attach_cp.php'))
);

if ($submit_change && $view == 'attachments')
{
	$attach_change_list = get_var('attach_id_list', array(0));
	$attach_comment_list = get_var('attach_comment_list', array(''));
	$attach_download_count_list = get_var('attach_count_list', array(0));

	// Generate correct Change List
	$attachments = array();

	for ($i = 0; $i < count($attach_change_list); $i++)
	{
		$attachments['_' . $attach_change_list[$i]]['comment'] = $attach_comment_list[$i];
		$attachments['_' . $attach_change_list[$i]]['download_count'] = $attach_download_count_list[$i];
	}

	$sql = 'SELECT *
		FROM bb_attachments_desc
		ORDER BY attach_id';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Couldn\'t get Attachment informations', '', __LINE__, __FILE__, $sql);
	}

	while ( $attachrow = $db->sql_fetchrow($result) )
	{
		if ( isset($attachments['_' . $attachrow['attach_id']]) )
		{
			if ($attachrow['comment'] != $attachments['_' . $attachrow['attach_id']]['comment'] || $attachrow['download_count'] != $attachments['_' . $attachrow['attach_id']]['download_count'])
			{
				$sql = "UPDATE bb_attachments_desc
					SET comment = '" . attach_mod_sql_escape($attachments['_' . $attachrow['attach_id']]['comment']) . "', download_count = " . (int) $attachments['_' . $attachrow['attach_id']]['download_count'] . "
					WHERE attach_id = " . (int) $attachrow['attach_id'];

				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Couldn\'t update Attachments Informations', '', __LINE__, __FILE__, $sql);
				}
			}
		}
	}
	$db->sql_freeresult($result);
}

// Statistics
if ($view == 'stats')
{
	$upload_dir_size = get_formatted_dirsize();

	if ($attach_config['attachment_quota'] >= 1048576)
	{
		$attachment_quota = round($attach_config['attachment_quota'] / 1048576 * 100) / 100 . ' ' . $lang['MB'];
	}
	else if ($attach_config['attachment_quota'] >= 1024)
	{
		$attachment_quota = round($attach_config['attachment_quota'] / 1024 * 100) / 100 . ' ' . $lang['KB'];
	}
	else
	{
		$attachment_quota = $attach_config['attachment_quota'] . ' ' . $lang['Bytes'];
	}

	// number_of_attachments
	$row = $db->fetch_row("
		SELECT COUNT(*) AS total FROM bb_attachments_desc
	");
	$number_of_attachments = $number_of_posts = $row['total'];

	$number_of_pms = 0;

	// number_of_topics
	$row = $db->fetch_row("
		SELECT COUNT(*) AS topics FROM bb_topics WHERE topic_attachment = 1
	");
	$number_of_topics = $row['topics'];

	// number_of_users
	$row = $db->fetch_row("
		SELECT COUNT(DISTINCT user_id_1) AS users FROM bb_attachments WHERE post_id != 0
	");
	$number_of_users = $row['users'];

	$template->assign_vars(array(
		'TPL_ATTACH_STATISTICS' => true,

		'L_STATISTIC' => $lang['Statistic'],
		'L_VALUE' => $lang['Value'],
		'L_NUMBER_OF_ATTACHMENTS' => $lang['Number_of_attachments'],
		'L_TOTAL_FILESIZE' => $lang['Total_filesize'],
		'L_ATTACH_QUOTA' => $lang['Attach_quota'],
		'L_NUMBER_OF_POSTS' => $lang['Number_posts_attach'],
		'L_NUMBER_OF_PMS' => $lang['Number_pms_attach'],
		'L_NUMBER_OF_TOPICS' => $lang['Number_topics_attach'],
		'L_NUMBER_OF_USERS' => $lang['Number_users_attach'],

		'TOTAL_FILESIZE' => $upload_dir_size,
		'ATTACH_QUOTA' => $attachment_quota,
		'NUMBER_OF_ATTACHMENTS' => $number_of_attachments,
		'NUMBER_OF_POSTS' => $number_of_posts,
		'NUMBER_OF_PMS' => $number_of_pms,
		'NUMBER_OF_TOPICS' => $number_of_topics,
		'NUMBER_OF_USERS' => $number_of_users)
	);

}

// Search
if ($view == 'search')
{
	// Get Forums and Categories
	//sf - add [, f.forum_parent]
	$sql = "SELECT c.cat_title, c.cat_id, f.forum_name, f.forum_id, f.forum_parent
	FROM bb_categories c, bb_forums f
	WHERE f.cat_id = c.cat_id
	ORDER BY c.cat_id, f.forum_order";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain forum_name/forum_id', '', __LINE__, __FILE__, $sql);
	}

	$s_forums = '';
	while ($row = $db->sql_fetchrow($result))
	{ //sf
		$s_forums .= '<option value="' . $row['forum_id'] . '">' . (($row['forum_parent']) ? HTML_SF_SPACER : '') . htmlCHR($row['forum_name']) . '</option>';

		if( empty($list_cat[$row['cat_id']]) )
		{
			$list_cat[$row['cat_id']] = $row['cat_title'];
		}
	}

	if( $s_forums != '' )
	{
		$s_forums = '<option value="0">' . $lang['All_available'] . '</option>' . $s_forums;

		// Category to search
		$s_categories = '<option value="0">' . $lang['All_available'] . '</option>';

		foreach ($list_cat as $cat_id => $cat_title)
		{
			$s_categories .= '<option value="' . $cat_id . '">' . htmlCHR($cat_title) . '</option>';
		}
	}
	else
	{
		message_die(GENERAL_MESSAGE, $lang['No_searchable_forums']);
	}

	$template->assign_vars(array(
		'TPL_ATTACH_SEARCH' => true,

		'L_ATTACH_SEARCH_QUERY' => $lang['Attach_search_query'],
		'L_FILENAME' => $lang['File_name'],
		'L_COMMENT' => $lang['File_comment'],
		'L_WILDCARD_EXPLAIN' => $lang['Search_wildcard_explain'],
		'L_SIZE_SMALLER_THAN' => $lang['Size_smaller_than'],
		'L_SIZE_GREATER_THAN' => $lang['Size_greater_than'],
		'L_COUNT_SMALLER_THAN' => $lang['Count_smaller_than'],
		'L_COUNT_GREATER_THAN' => $lang['Count_greater_than'],
		'L_MORE_DAYS_OLD' => $lang['More_days_old'],

		'S_FORUM_OPTIONS' => $s_forums,
		'S_CATEGORY_OPTIONS' => $s_categories,
		'S_SORT_OPTIONS' => $select_sort_mode,
		'S_SORT_ORDER' => $select_sort_order)
	);
}

// Username
if ($view == 'username')
{
	$template->assign_vars(array(
		'TPL_ATTACH_USER' => true,

		'L_TOTAL_SIZE' => $lang['Size_in_kb'],
		'L_ATTACHMENTS' => $lang['Attachments'],

		'S_MODE_SELECT' => $select_sort_mode,
		'S_ORDER_SELECT' => $select_sort_order)
	);
	$total_rows = 0;
	bb_die('removed');
}

// Attachments
if ($view == 'attachments')
{
	$user_based = ($uid) ? TRUE : FALSE;
	$search_based = (isset($_POST['search']) && $_POST['search']) ? TRUE : FALSE;

	$hidden_fields = '';

	$template->assign_vars(array(
		'TPL_ATTACH_ATTACHMENTS' => true,

		'L_FILENAME' => $lang['File_name'],
		'L_FILECOMMENT' => $lang['File_comment_cp'],
		'L_EXTENSION' => $lang['Extension'],
		'L_SIZE' => $lang['Size_in_kb'],
		'L_DOWNLOADS' => $lang['Downloads'],
		'L_POST_TIME' => $lang['Post_time'],
		'L_POSTED_IN_TOPIC' => $lang['Posted_in_topic'],
		'L_DELETE_MARKED' => $lang['Delete_marked'],
		'L_SUBMIT_CHANGES' => $lang['Submit_changes'],
		'L_MARK_ALL' => $lang['Mark_all'],
		'L_UNMARK_ALL' => $lang['Unmark_all'],

		'S_MODE_SELECT' => $select_sort_mode,
		'S_ORDER_SELECT' => $select_sort_order)
	);

	$total_rows = 0;

	// Are we called from Username ?
	if ($user_based)
	{
		$sql = "SELECT username
		FROM bb_users
		WHERE user_id = " . intval($uid);

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error getting username', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$username = $row['username'];

		$s_hidden = '<input type="hidden" name="u_id" value="' . intval($uid) . '" />';

		$template->assign_block_vars('switch_user_based', array());

		$template->assign_vars(array(
			'S_USER_HIDDEN' => $s_hidden,
			'L_STATISTICS_FOR_USER' => sprintf($lang['Statistics_for_user'], $username))
		);

		$sql = "SELECT attach_id
		FROM bb_attachments
		WHERE user_id_1 = " . intval($uid) . "
		GROUP BY attach_id";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
		}

		$attach_ids = $db->sql_fetchrowset($result);
		$num_attach_ids = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ($num_attach_ids == 0)
		{
			message_die(GENERAL_MESSAGE, 'For some reason no Attachments are assigned to the User "' . $username . '".');
		}

		$total_rows = $num_attach_ids;

		$attach_id = array();

		for ($j = 0; $j < $num_attach_ids; $j++)
		{
			$attach_id[] = intval($attach_ids[$j]['attach_id']);
		}

		$sql = "SELECT a.*
		FROM bb_attachments_desc a
		WHERE a.attach_id IN (" . implode(', ', $attach_id) . ") " .
		$order_by;

	}
	else if ($search_based)
	{
		// we are called from search
		$attachments = search_attachments($order_by, $total_rows);
	}
	else
	{
		bb_die('removed');
	}

	if (!$search_based)
	{
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
		}

		$attachments = $db->sql_fetchrowset($result);
		$num_attach = $db->sql_numrows($result);
		$db->sql_freeresult($result);
	}

	if (sizeof($attachments) > 0)
	{
		for ($i = 0; $i < sizeof($attachments); $i++)
		{
			$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" />';

			for ($j = 0; $j < count($delete_id_list); $j++)
			{
				if ($delete_id_list[$j] == $attachments[$i]['attach_id'])
				{
					$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" checked="checked" />';
					break;
				}
			}

			$row_class = !($i % 2) ? 'row1' : 'row2';

			// Is the Attachment assigned to more than one post ?
			// If it's not assigned to any post, it's an private message thingy. ;)
			$post_titles = array();

			$sql = "SELECT *
			FROM bb_attachments
			WHERE attach_id = " . intval($attachments[$i]['attach_id']);

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
			}

			$ids = $db->sql_fetchrowset($result);
			$num_ids = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			for ($j = 0; $j < $num_ids; $j++)
			{
				if ($ids[$j]['post_id'] != 0)
				{
					$sql = "SELECT t.topic_title
					FROM bb_topics t, bb_posts p
					WHERE p.post_id = " . intval($ids[$j]['post_id']) . " AND p.topic_id = t.topic_id
					GROUP BY t.topic_id, t.topic_title";

					if ( !($result = $db->sql_query($sql)) )
					{
						message_die(GENERAL_ERROR, 'Couldn\'t query topic', '', __LINE__, __FILE__, $sql);
					}

					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);
					$post_title = $row['topic_title'];

					if (strlen($post_title) > 32)
					{
						$post_title = substr($post_title, 0, 30) . '...';
					}

					$view_topic = append_sid('viewtopic.php?' . POST_POST_URL . '=' . $ids[$j]['post_id'] . '#' . $ids[$j]['post_id']);

					$post_titles[] = '<a href="' . $view_topic . '" class="gen" target="_blank">' . $post_title . '</a>';
				}
				else
				{
					$post_titles[] = $lang['PRIVATE_MESSAGE'];
				}
			}

			$post_titles = implode('<br />', $post_titles);

			$hidden_field = '<input type="hidden" name="attach_id_list[]" value="' . intval($attachments[$i]['attach_id']) . '" />';

			$template->assign_block_vars('attachrow', array(
				'ROW_NUMBER' => $i + ( @$_GET['start'] + 1 ),
				'ROW_CLASS' => $row_class,

				'FILENAME'		=> htmlspecialchars($attachments[$i]['real_filename']),
				'COMMENT'		=> htmlspecialchars($attachments[$i]['comment']),
				'EXTENSION' => $attachments[$i]['extension'],
				'SIZE' => round(($attachments[$i]['filesize'] / MEGABYTE), 2),
				'DOWNLOAD_COUNT' => $attachments[$i]['download_count'],
				'POST_TIME' => create_date($board_config['default_dateformat'], $attachments[$i]['filetime'], $board_config['board_timezone']),
				'POST_TITLE' => $post_titles,

				'S_DELETE_BOX' => $delete_box,
				'S_HIDDEN' => $hidden_field,
				'U_VIEW_ATTACHMENT'	=> append_sid('download.php?id=' . $attachments[$i]['attach_id']))
//				'U_VIEW_POST' => ($attachments[$i]['post_id'] != 0) ? append_sid("../viewtopic.php?" . POST_POST_URL . "=" . $attachments[$i]['post_id'] . "#" . $attachments[$i]['post_id']) : '')
			);

		}
	}

	if (!$search_based && !$user_based)
	{
		if ($total_attachments == 0)
		{
			$sql = "SELECT attach_id FROM bb_attachments_desc";

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query Attachment Description Table', '', __LINE__, __FILE__, $sql);
			}

			$total_rows = $db->sql_numrows($result);
			$db->sql_freeresult($result);
		}
	}
}

// Generate Pagination
if ($do_pagination && $total_rows > $board_config['topics_per_page'])
{
	$pagination = generate_pagination('admin_attach_cp.php?view=' . $view . '&amp;mode=' . $mode . '&amp;order=' . $sort_order . '&amp;uid=' . $uid, $total_rows, $board_config['topics_per_page'], $start).'&nbsp;';

	$template->assign_vars(array(
		'PAGINATION' => $pagination,
		'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $board_config['topics_per_page'] ) + 1 ), ceil( $total_rows / $board_config['topics_per_page'] )),
	));
}

print_page('admin_attach_cp.tpl', 'admin');


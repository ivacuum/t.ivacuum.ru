<?php

require 'common.php';
require SITE_DIR . 'attach_mod/attachment_mod.php';

// session id check
$sid = $app['request']->variable('sid', '');

// Start session management
$user->session_start(array('req_login' => true));

// Obtain initial var settings
$user_id = $app['request']->variable(POST_USERS_URL, 0);

if (!$user_id)
{
	message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}

$profiledata = get_userdata($user_id);

if ($profiledata['user_id'] != $userdata['user_id'] && !IS_ADMIN)
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

$language = $board_config['default_lang'];

if (!file_exists(SITE_DIR . 'language/lang_' . $language . '/lang_admin_attach.php'))
{
	$language = $attach_config['board_lang'];
}

require SITE_DIR . 'language/lang_' . $language . '/lang_admin_attach.php';

$start = $app['request']->variable('start', 0);
$sort_order = $app['request']->variable('order', 'ASC');
$sort_order = ($sort_order == 'ASC') ? 'ASC' : 'DESC';
$mode = $app['request']->variable('mode', '');

$mode_types_text = array($lang['Sort_Filename'], $lang['Sort_Comment'], $lang['Sort_Extension'], $lang['Sort_Size'], $lang['Sort_Downloads'], $lang['Sort_Posttime'], /*$lang['Sort_Posts']*/);
$mode_types = array('real_filename', 'comment', 'extension', 'filesize', 'downloads', 'post_time'/*, 'posts'*/);

if (!$mode)
{
	$mode = 'real_filename';
	$sort_order = 'ASC';
}

// Pagination?
$do_pagination = true;

// Set Order
$order_by = '';

switch ($mode)
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

// Set select fields
$select_sort_mode = $select_sort_order = '';

if (sizeof($mode_types_text) > 0)
{
	$select_sort_mode = '<select name="mode">';

	for ($i = 0; $i < sizeof($mode_types_text); $i++)
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

$delete = (isset($_POST['delete'])) ? true : false;
$delete_id_list = (isset($_POST['delete_id_list'])) ? array_map('intval', $_POST['delete_id_list']) : array();

$confirm = (isset($_POST['confirm']) && $_POST['confirm']) ? true : false;

if ($confirm && sizeof($delete_id_list) > 0)
{
	$attachments = array();

	for ($i = 0; $i < sizeof($delete_id_list); $i++)
	{
		$sql = 'SELECT post_id
			FROM bb_attachments
			WHERE attach_id = ' . intval($delete_id_list[$i]) . '
				AND user_id_1 = ' . intval($profiledata['user_id']);
		$result = $db->sql_query($sql);

		if ($result)
		{
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			delete_attachment(0, intval($delete_id_list[$i]));
		}
	}
}
else if ($delete && sizeof($delete_id_list) > 0)
{
	// Not confirmed, show confirmation message
	$hidden_fields = '<input type="hidden" name="view" value="' . @$view . '" />';
	$hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';
	$hidden_fields .= '<input type="hidden" name="order" value="' . $sort_order . '" />';
	$hidden_fields .= '<input type="hidden" name="' . POST_USERS_URL . '" value="' . intval($profiledata['user_id']) . '" />';
	$hidden_fields .= '<input type="hidden" name="start" value="' . $start . '" />';
	$hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

	for ($i = 0; $i < sizeof($delete_id_list); $i++)
	{
		$hidden_fields .= '<input type="hidden" name="delete_id_list[]" value="' . intval($delete_id_list[$i]) . '" />';
	}

	print_confirmation(array(
		'QUESTION'      => $lang['Confirm_delete_attachments'],
		'FORM_ACTION'   => "uacp.php",
		'HIDDEN_FIELDS' => $hidden_fields,
	));
}

$hidden_fields = '';

$total_rows = 0;

$username = $profiledata['username'];

$s_hidden = '<input type="hidden" name="' . POST_USERS_URL . '" value="' . intval($profiledata['user_id']) . '">';
$s_hidden .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

// Assign Template Vars
$template->assign_vars(array(
	'PAGE_TITLE' => $lang['User_acp_title'],
	'L_UACP' => $lang['UACP'],
	'L_FILENAME' => $lang['File_name'],
	'L_FILECOMMENT' => $lang['File_comment_cp'],
	'L_EXTENSION' => $lang['Extension'],
	'L_SIZE' => $lang['Size_in_kb'],
	'L_DOWNLOADS' => $lang['Downloads'],
	'L_POST_TIME' => $lang['Post_time'],
	'L_POSTED_IN_TOPIC' => $lang['Posted_in_topic'],
	'L_DELETE_MARKED' => $lang['Delete_marked'],
	'L_MARK_ALL' => $lang['Mark_all'],
	'L_UNMARK_ALL' => $lang['Unmark_all'],

	'USERNAME' => $profiledata['username'],

	'S_USER_HIDDEN' => $s_hidden,
	'S_MODE_ACTION'		=> append_sid('uacp.php'),
	'S_MODE_SELECT' => $select_sort_mode,
	'S_ORDER_SELECT' => $select_sort_order)
);

$sql = "SELECT attach_id
	FROM bb_attachments
	WHERE user_id_1 = " . intval($profiledata['user_id']) . "
	GROUP BY attach_id";

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
}

$attach_ids = $db->sql_fetchrowset($result);
$num_attach_ids = $db->sql_numrows($result);
$db->sql_freeresult($result);

$total_rows = $num_attach_ids;

$attachments = array();

if ($num_attach_ids > 0)
{
	$attach_id = array();

	for ($j = 0; $j < $num_attach_ids; $j++)
	{
		$attach_id[] = (int) $attach_ids[$j]['attach_id'];
	}

	$sql = "SELECT a.*
		FROM bb_attachments_desc a
		WHERE a.attach_id IN (" . join(', ', $attach_id) . ") " .
		$order_by;

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't query attachments", '', __LINE__, __FILE__, $sql);
	}

	$attachments = $db->sql_fetchrowset($result);
	$num_attach = $db->sql_numrows($result);
	$db->sql_freeresult($result);
}

if (sizeof($attachments) > 0)
{
	for ($i = 0; $i < sizeof($attachments); $i++)
	{
		$row_class = !($i % 2) ? 'row1' : 'row2';

		// Is the Attachment assigned to more than one post?
		// If it's not assigned to any post, it's an private message thingy. ;)
		$post_titles = array();

		$sql = 'SELECT *
			FROM bb_attachments
			WHERE attach_id = ' . (int) $attachments[$i]['attach_id'];

		if (!($result = $db->sql_query($sql)))
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
					WHERE p.post_id = " . (int) $ids[$j]['post_id'] . " AND p.topic_id = t.topic_id
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
		}

		// Iron out those Attachments assigned to us, but not more controlled by us. ;) (PM's)
		if (sizeof($post_titles) > 0)
		{
			$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . (int) $attachments[$i]['attach_id'] . '" />';

			for ($j = 0; $j < sizeof($delete_id_list); $j++)
			{
				if ($delete_id_list[$j] == $attachments[$i]['attach_id'])
				{
					$delete_box = '<input type="checkbox" name="delete_id_list[]" value="' . (int) $attachments[$i]['attach_id'] . '" checked />';
					break;
				}
			}

			$post_titles = join('<br />', $post_titles);

			$hidden_field = '<input type="hidden" name="attach_id_list[]" value="' . (int) $attachments[$i]['attach_id'] . '">';
			$hidden_field .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

			$comment = str_replace("\n", '<br />', $attachments[$i]['comment']);

			$template->assign_block_vars('attachrow', array(
				'ROW_NUMBER'		=> $i + ($start + 1 ),
				'ROW_CLASS'			=> $row_class,

				'FILENAME'     => htmlspecialchars($attachments[$i]['real_filename']),
				'COMMENT'			=> htmlspecialchars($comment),
				'EXTENSION'			=> $attachments[$i]['extension'],
				'SIZE'				=> round(($attachments[$i]['filesize'] / MEGABYTE), 2),
				'DOWNLOAD_COUNT'	=> $attachments[$i]['download_count'],
				'POST_TIME'			=> create_date($board_config['default_dateformat'], $attachments[$i]['filetime'], $board_config['board_timezone']),
				'POST_TITLE'		=> $post_titles,

				'S_DELETE_BOX' => $delete_box,
				'S_HIDDEN' => $hidden_field,
				'U_VIEW_ATTACHMENT' => append_sid('download.php?id=' . $attachments[$i]['attach_id']))
	//			'U_VIEW_POST' => ($attachments[$i]['post_id'] != 0) ? append_sid("../viewtopic.php?" . POST_POST_URL . "=" . $attachments[$i]['post_id'] . "#" . $attachments[$i]['post_id']) : '')
			);
		}
	}
}

// Generate Pagination
if ($do_pagination && $total_rows > $board_config['topics_per_page'])
{
	$pagination = generate_pagination('uacp.php?mode=' . $mode . '&amp;order=' . $sort_order . '&amp;' . POST_USERS_URL . '=' . $profiledata['user_id'] . '&amp;sid=' . $userdata['session_id'], $total_rows, $board_config['topics_per_page'], $start).'&nbsp;';

	$template->assign_vars(array(
		'PAGINATION'	=> $pagination,
		'PAGE_NUMBER'	=> sprintf($lang['Page_of'], (floor($start / $board_config['topics_per_page']) + 1), ceil($total_rows / $board_config['topics_per_page'])),
	));
}

print_page('uacp.tpl');

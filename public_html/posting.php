<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_PHPBB', true);
require('common.php');
require(SITE_DIR . 'includes/bbcode.php');
require(SITE_DIR . 'includes/functions_post.php');
require(SITE_DIR . 'attach_mod/attachment_mod.php');

$page_cfg['load_tpl_vars'] = array(
	'post_icons',
);

$submit      = (bool) @$_REQUEST['post'];
$preview     = (bool) @$_REQUEST['preview'];
$delete      = (bool) @$_REQUEST['delete'];
$poll_delete = (bool) @$_REQUEST['poll_delete'];
$poll_add    = (bool) @$_REQUEST['add_poll_option'];
$poll_edit   = (bool) @$_REQUEST['edit_poll_option'];
$topic_tpl   = (bool) @$_REQUEST['tpl'];

$forum_id = (int) @$_REQUEST[POST_FORUM_URL];
$topic_id = (int) @$_REQUEST[POST_TOPIC_URL];
$post_id  = (int) @$_REQUEST[POST_POST_URL];

$mode = (string) @$_REQUEST['mode'];

$confirm = isset($_POST['confirm']);

$poll_id = null;

$refresh = $preview || $poll_add || $poll_edit || $poll_delete;
$orig_word = $replacement_word = array();

// Set topic type
$topic_type = (@$_POST['topictype']) ? (int) $_POST['topictype'] : POST_NORMAL;
$topic_type = in_array($topic_type, array(POST_NORMAL, POST_STICKY, POST_ANNOUNCE)) ? $topic_type : POST_NORMAL;

if ($mode == 'smilies')
{
	generate_smilies('window');
	exit;
}

$tracking_topics = get_tracks('topic');
$tracking_forums = get_tracks('forum');

// Start session management
$user->session_start();

// Quick Reply
$template->assign_vars(array(
	'L_FONT_SEL'         => $lang['QR_Font_sel'],
	'L_FONT_COLOR_SEL'   => $lang['QR_Color_sel'],
	'L_FONT_SIZE_SEL'    => $lang['QR_Size_sel'],
	'L_STEEL_BLUE'       => $lang['color_steel_blue'],
	'L_COLOR_GRAY'       => $lang['color_gray'],
	'L_COLOR_DARK_GREEN' => $lang['color_dark_green'],
));

// What auth type do we need to check?
$is_auth = array();
switch ($mode)
{
	case 'newtopic':
		if ($topic_type == POST_ANNOUNCE)
		{
			$is_auth_type = 'auth_announce';
		}
		else if ($topic_type == POST_STICKY)
		{
			$is_auth_type = 'auth_sticky';
		}
		else
		{
			$is_auth_type = 'auth_post';
		}
		break;
	case 'reply':
	case 'quote':
		$is_auth_type = 'auth_reply';
		break;
	case 'editpost':
		$is_auth_type = 'auth_edit';
		break;
	case 'delete':
	case 'poll_delete':
		$is_auth_type = 'auth_delete';
		break;
	case 'vote':
		$is_auth_type = 'auth_vote';
		break;
	default:
		message_die(GENERAL_MESSAGE, $lang['No_post_mode']);
		break;
}

// Here we do various lookups to find topic_id, forum_id, post_id etc.
// Doing it here prevents spoofing (eg. faking forum_id, topic_id or post_id
$error_msg = '';
$post_data = array();
switch ($mode)
{
	case 'newtopic':
		if (!$forum_id)
		{
			message_die(GENERAL_MESSAGE, $lang['Forum_not_exist']);
		}
		$sql = "SELECT * FROM ". FORUMS_TABLE ." WHERE forum_id = $forum_id LIMIT 1";
		break;

	case 'reply':
	case 'vote':
		if (!$topic_id)
		{
			message_die(GENERAL_MESSAGE, $lang['No_topic_id']);
		}
		$sql = "SELECT f.*, t.*
			FROM ". FORUMS_TABLE ." f, ". TOPICS_TABLE ." t
			WHERE t.topic_id = $topic_id
				AND f.forum_id = t.forum_id
			LIMIT 1";
		break;

	case 'quote':
	case 'editpost':
	case 'delete':
	case 'poll_delete':
		if (!$post_id)
		{
			message_die(GENERAL_MESSAGE, $lang['No_post_id']);
		}

		$select_sql = 'SELECT f.*, t.*, p.*';
		$select_sql .= (!$submit) ? ', pt.*, u.username, u.user_id' : '';

		$from_sql = "FROM ". POSTS_TABLE ." p, ". TOPICS_TABLE ." t, ". FORUMS_TABLE ." f";
		$from_sql .= (!$submit) ? ", " . POSTS_TEXT_TABLE . " pt, " . USERS_TABLE . " u" : '';

		$where_sql = "
			WHERE p.post_id = $post_id
			AND t.topic_id = p.topic_id
			AND f.forum_id = p.forum_id
		";
		$where_sql .= (!$submit) ? "
			AND pt.post_id = p.post_id
			AND u.user_id = p.poster_id
		" : '';

		$sql = "$select_sql $from_sql $where_sql LIMIT 1";
		break;

	default:
		message_die(GENERAL_MESSAGE, $lang['No_valid_mode']);
}

if ($post_info = $db->fetch_row($sql))
{
	$forum_id = $post_info['forum_id'];
	$forum_name = $post_info['forum_name'];

	$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $post_info);

	if ($post_info['forum_status'] == FORUM_LOCKED && !$is_auth['auth_mod'])
	{
	   message_die(GENERAL_MESSAGE, $lang['Forum_locked']);
	}
	else if ($mode != 'newtopic' && $post_info['topic_status'] == TOPIC_LOCKED && !$is_auth['auth_mod'])
	{
	   message_die(GENERAL_MESSAGE, $lang['Topic_locked']);
	}
	elseif( $mode == 'newtopic' && $post_info['allow_reg_tracker'] && !$is_auth['auth_mod'] )
	{
		/**
		* Проверка наличия недооформленных раздач
		*/
		$sql = '
			SELECT
				bt.tor_status,
				t.topic_id,
				t.topic_title
			FROM
				' . BT_TORRENTS_TABLE . ' bt,
				' . TOPICS_TABLE . ' t
			WHERE
				bt.poster_id = ' . $userdata['user_id'] . '
			AND
				bt.topic_id = t.topic_id
			AND
				bt.tor_status IN(4,5)';
		$result = $db->sql_query($sql);
		$message_text = '';
		$torrents_count = 0;

		while( $row = $db->sql_fetchrow($result) )
		{
			$message_text .= '<a href="viewtopic.php?t=' . $row['topic_id'] . '"><b>' . $row['topic_title'] . '</b></a><br />';
			$torrents_count++;
		}

		$db->sql_freeresult($result);

		if( $torrents_count > 1 )
		{
			message_die(GENERAL_MESSAGE, 'У вас есть недооформленные раздачи. Вы не можете создавать новые раздачи, пока не исправите существующие.<br /><br />' . $message_text);
		}
	}

	if ($mode == 'editpost' || $mode == 'delete' || $mode == 'poll_delete')
	{
		$topic_id = $post_info['topic_id'];

		$post_data['poster_post'] = ($post_info['poster_id'] == $userdata['user_id']);
		$post_data['first_post'] = ($post_info['topic_first_post_id'] == $post_id);
		$post_data['last_post'] = ($post_info['topic_last_post_id'] == $post_id);
		$post_data['last_topic'] = ($post_info['forum_last_post_id'] == $post_id);
		$post_data['has_poll'] = (bool) $post_info['topic_vote'];
		$post_data['topic_type'] = $post_info['topic_type'];
		$post_data['poster_id'] = $post_info['poster_id'];

		if ($post_data['first_post'] && $post_data['has_poll'])
		{
			$sql = "SELECT *
				FROM ". VOTE_DESC_TABLE ." vd, ". VOTE_RESULTS_TABLE ." vr
				WHERE vd.topic_id = $topic_id
					AND vr.vote_id = vd.vote_id
				ORDER BY vr.vote_option_id";

			if (!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not obtain vote data for this topic', '', __LINE__, __FILE__, $sql);
			}

			$poll_options = array();
			$poll_results_sum = 0;
			if ($row = $db->sql_fetchrow($result))
			{
				$poll_title = $row['vote_text'];
				$poll_id = $row['vote_id'];
				$poll_length = $row['vote_length'] / 86400;

				do
				{
					$poll_options[$row['vote_option_id']] = $row['vote_option_text'];
					$poll_results_sum += $row['vote_result'];
				}
				while ($row = $db->sql_fetchrow($result));
			}
			$post_data['edit_poll'] = ((!$poll_results_sum || $is_auth['auth_mod']) && $post_data['first_post']);
		}
		else
		{
			$post_data['edit_poll'] = ($post_data['first_post'] && $is_auth['auth_pollcreate']);
		}

		// Can this user edit/delete the post/poll?
		if ($post_info['poster_id'] != $userdata['user_id'] && !$is_auth['auth_mod'])
		{
			$message = ($delete || $mode == 'delete') ? $lang['Delete_own_posts'] : $lang['Edit_own_posts'];
			$message .= '<br /><br />'. sprintf($lang['Click_return_topic'], '<a href="'. append_sid(TOPIC_URL ."$topic_id") .'">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		}
		else if (!$post_data['last_post'] && !$is_auth['auth_mod'] && ($mode == 'delete' || $delete))
		{
			message_die(GENERAL_MESSAGE, $lang['Cannot_delete_replied']);
		}
		else if (!$post_data['edit_poll'] && !$is_auth['auth_mod'] && ($mode == 'poll_delete' || $poll_delete))
		{
			message_die(GENERAL_MESSAGE, $lang['Cannot_delete_poll']);
		}
	}
	else
	{
		if ($mode == 'quote')
		{
			$topic_id = $post_info['topic_id'];
		}
		if ($mode == 'newtopic')
		{
			$post_data['topic_type'] = POST_NORMAL;
		}
		$post_data['first_post'] = ($mode == 'newtopic');
		$post_data['last_post']  = false;
		$post_data['has_poll']   = false;
		$post_data['edit_poll']  = false;
	}
	if ($mode == 'poll_delete' && !$poll_id)
	{
		message_die(GENERAL_MESSAGE, $lang['No_such_post']);
	}
}
else
{
	message_die(GENERAL_MESSAGE, $lang['No_such_post']);
}

// The user is not authed, if they're not logged in then redirect
// them, else show them an error message
if (!$is_auth[$is_auth_type])
{
	if (!IS_GUEST)
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Sorry_'. $is_auth_type], $is_auth[$is_auth_type .'_type']));
	}

	switch ($mode)
	{
		case 'newtopic':
			$redirect = "mode=newtopic&amp;f=$forum_id";
			break;
		case 'reply':
			$redirect = "mode=reply&amp;t=$topic_id";
			break;
		case 'quote':
		case 'editpost':
			$redirect = "mode=quote&amp;p=$post_id";
			break;
		default:
			$redirect = '';
	}
	redirect("login.php?redirect=posting.php&amp;$redirect");
}

if ($mode == 'newtopic' && $topic_tpl && $post_info['topic_tpl_id'])
{
	require(SITE_DIR . 'includes/topic_templates.php');
}

// BBCode
if (!$bb_cfg['allow_bbcode'])
{
	$bbcode_on = 0;
}
else
{
	$bbcode_on = ($submit || $refresh) ? (int) empty($_POST['disable_bbcode']) : $bb_cfg['allow_bbcode'];
}

// Smilies
if (!$bb_cfg['allow_smilies'])
{
	$smilies_on = 0;
}
else
{
	$smilies_on = ($submit || $refresh) ? (int) empty($_POST['disable_smilies']) : $bb_cfg['allow_smilies'];
}

// Notify
if ($submit || $refresh)
{
	$notify_user = (int) !empty($_POST['notify']);
}
else
{
	if (!IS_GUEST && $mode != 'newtopic' && !$userdata['user_notify'])
	{
		$notify_user = (int) $db->fetch_row("
			SELECT topic_id
			FROM ". TOPICS_WATCH_TABLE ."
			WHERE topic_id = $topic_id
			  AND user_id = ". $userdata['user_id'] ."
		");
	}
	else
	{
		$notify_user = $userdata['user_notify'];
	}
}

$attach_sig = ($submit || $refresh) ? (int) !empty($_POST['attach_sig']) : bf($userdata['user_opt'], 'user_opt', 'attachsig');
$update_post_time = !empty($_POST['update_post_time']);

execute_posting_attachment_handling();

// если за время пока вы писали ответ, в топике появились новые сообщения, перед тем как ваше сообщение будет отправлено, выводится предупреждение с обзором этих сообщений
$topic_has_new_posts = false;

if (!IS_GUEST && $mode != 'newtopic' && ($submit || $preview || $mode == 'quote' || $mode == 'reply') && isset($_COOKIE[COOKIE_TOPIC]))
{
	if ($topic_last_read = max(intval(@$tracking_topics[$topic_id]), intval(@$tracking_forums[$forum_id])))
	{
		$sql = "SELECT p.*, pt.post_text, pt.bbcode_uid, u.username
			FROM ". POSTS_TABLE ." p, ". POSTS_TEXT_TABLE ." pt, ". USERS_TABLE ." u
			WHERE p.topic_id = ". (int) $topic_id ."
				AND u.user_id = p.poster_id
				AND pt.post_id = p.post_id
				AND p.post_time > $topic_last_read
			ORDER BY p.post_time
			LIMIT ". $bb_cfg['posts_per_page'];

		if ($rowset = $db->fetch_rowset($sql))
		{
			$topic_has_new_posts = true;

			foreach ($rowset as $i => $row)
			{
				if ($row['poster_id'] == ANONYMOUS)
				{
					$new_post_username = (!$row['post_username']) ? $lang['Guest'] : $row['post_username'];
				}
				else
				{
					$new_post_username = $row['username'];
				}

				$template->assign_block_vars('new_posts', array(
					'ROW_CLASS'      => !($i % 2) ? 'row1' : 'row2',
					'POSTER_NAME'    => $new_post_username,
					'POSTER_NAME_JS' => addslashes($new_post_username),
					'POST_DATE'      => create_date($bb_cfg['post_date_format'], $row['post_time']),
					'MESSAGE'        => get_parsed_post($row),
				));
			}
			$template->assign_vars(array(
				'TPL_SHOW_NEW_POSTS'  => true,
			));

			set_tracks(COOKIE_TOPIC, $tracking_topics, $topic_id);
			unset($rowset);
		}
	}
}

// --------------------
//  What shall we do?
//
if ( ( $delete || $poll_delete || $mode == 'delete' ) && !$confirm )
{
	if (isset($_POST['cancel']))
	{
		redirect(POST_URL . "$post_id#$post_id");
	}
	//
	// Confirm deletion
	//
	$hidden_fields = array(
		'p'    => $post_id,
		'mode' => ($delete || $mode == "delete") ? 'delete' : 'poll_delete',
	);

	print_confirmation(array(
		'QUESTION'      => ($delete || $mode == 'delete') ? $lang['Confirm_delete'] : $lang['Confirm_delete_poll'],
		'FORM_ACTION'   => "posting.php",
		'HIDDEN_FIELDS' => build_hidden_fields($hidden_fields),
	));
}
else if ( $mode == 'vote' )
{
	//
	// Vote in a poll
	//
	if ( !empty($_POST['vote_id']) )
	{
		$vote_option_id = intval($_POST['vote_id']);

		$sql = "SELECT vd.vote_id
			FROM " . VOTE_DESC_TABLE . " vd, " . VOTE_RESULTS_TABLE . " vr
			WHERE vd.topic_id = $topic_id
				AND vr.vote_id = vd.vote_id
				AND vr.vote_option_id = $vote_option_id
			GROUP BY vd.vote_id";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain vote data for this topic', '', __LINE__, __FILE__, $sql);
		}

		if ( $vote_info = $db->sql_fetchrow($result) )
		{
			$vote_id = $vote_info['vote_id'];

			$sql = "SELECT *
				FROM " . VOTE_USERS_TABLE . "
				WHERE vote_id = $vote_id
					AND vote_user_id = " . $userdata['user_id'];
			if ( !($result2 = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain user vote data for this topic', '', __LINE__, __FILE__, $sql);
			}

			if ( !($row = $db->sql_fetchrow($result2)) )
			{
				$sql = "UPDATE " . VOTE_RESULTS_TABLE . "
					SET vote_result = vote_result + 1
					WHERE vote_id = $vote_id
						AND vote_option_id = $vote_option_id";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not update poll result', '', __LINE__, __FILE__, $sql);
				}

				$sql = "INSERT INTO " . VOTE_USERS_TABLE . " (vote_id, vote_user_id, vote_user_ip)
					VALUES ($vote_id, " . $userdata['user_id'] . ", '". USER_IP ."')";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, "Could not insert user_id for poll", "", __LINE__, __FILE__, $sql);
				}

				$message = $lang['Vote_cast'];
			}
			else
			{
				$message = $lang['Already_voted'];
			}
			$db->sql_freeresult($result2);
		}
		else
		{
			$message = $lang['No_vote_option'];
		}
		$db->sql_freeresult($result);

		meta_refresh(1, append_sid('viewtopic.php?' . POST_TOPIC_URL . '=' . $topic_id));
		$message .=  '<br /><br />' . sprintf($lang['Click_return_topic'], '<a href="' . append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id") . '">', '</a>');
		message_die(GENERAL_MESSAGE, $message);
	}
	else
	{
		redirect(append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id", true));
	}
}
//snp
// else if ( $submit || $confirm )
else if ( ($submit || $confirm) && !$topic_has_new_posts )
//snp end
{
	//
	// Submit post/vote (newtopic, edit, reply, etc.)
	//
	$return_message = '';
	$return_meta = '';

	switch ( $mode )
	{
		case 'editpost':
		case 'newtopic':
		case 'reply':
			$username = ( !empty($_POST['username']) ) ? $_POST['username'] : '';
			$subject = ( !empty($_POST['subject']) ) ? trim($_POST['subject']) : '';
			$message = ( !empty($_POST['message']) ) ? $_POST['message'] : '';
			$poll_title = ( isset($_POST['poll_title']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_title'] : '';
			$poll_options = ( isset($_POST['poll_option_text']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_option_text'] : '';
			$poll_length = ( isset($_POST['poll_length']) && $is_auth['auth_pollcreate'] ) ? $_POST['poll_length'] : '';
			$bbcode_uid = '';

			prepare_post($mode, $post_data, $bbcode_on, $smilies_on, $error_msg, $username, $bbcode_uid, $subject, $message, $poll_title, $poll_options, $poll_length);

			if (!$error_msg)
			{
				$topic_type = ( isset($post_data['topic_type']) && $topic_type != $post_data['topic_type'] && !$is_auth['auth_sticky'] && !$is_auth['auth_announce'] ) ? $post_data['topic_type'] : $topic_type;

				submit_post($mode, $post_data, $return_message, $return_meta, $forum_id, $topic_id, $post_id, $poll_id, $topic_type, $bbcode_on, $smilies_on, $attach_sig, $bbcode_uid, str_replace("\'", "''", $username), str_replace("\'", "''", $subject), str_replace("\'", "''", $message), str_replace("\'", "''", $poll_title), $poll_options, $poll_length, $update_post_time);
			}
			break;

		case 'delete':
		case 'poll_delete':
			require_once(SITE_DIR . 'includes/functions_admin.php');
			delete_post($mode, $post_data, $return_message, $return_meta, $forum_id, $topic_id, $post_id, $poll_id);
			break;
	}

	if (!$error_msg)
	{
		if (!in_array($mode, array('editpost', 'delete', 'poll_delete')))
		{
			$user_id = ( $mode == 'reply' || $mode == 'newtopic' ) ? $userdata['user_id'] : $post_data['poster_id'];
			update_post_stats($mode, $post_data, $forum_id, $topic_id, $post_id, $user_id);
		}
		$attachment_mod['posting']->insert_attachment($post_id);

		if (!$error_msg && $mode != 'poll_delete')
		{
			user_notification($mode, $post_data, $post_info['topic_title'], $forum_id, $topic_id, $post_id, $notify_user);
		}

		if ($mode == 'newtopic' || $mode == 'reply')
		{
			set_tracks(COOKIE_TOPIC, $tracking_topics, $topic_id);
		}

		$torrent_ext = (@$attachment_mod['posting']->extension === TORRENT_EXT || @$attachment_mod['posting']->attachment_extension_list[0] === TORRENT_EXT);
		$torrent_attach = ($torrent_ext && defined('TORRENT_ATTACH_ID') && 1 == count($attachment_mod['posting']->attachment_list));

		if ($torrent_attach && $bb_cfg['bt_newtopic_auto_reg'] && $mode == 'newtopic' && !$error_msg)
		{
			include_once($phpbb_root_path .'includes/functions_torrent.php');
			tracker_register(TORRENT_ATTACH_ID, 'newtopic');
		}

		if ($mode == 'reply' && $post_info['topic_status'] == TOPIC_LOCKED)
		{
			$locked_warn = '
				<div class="warnColor1">
					<b>'. $lang['locked_warn'] .'</b>
				</div>
				<br /><hr /><br />
			';
			$return_message = $locked_warn . $return_message;
		}

		message_die(GENERAL_MESSAGE, $return_message);
	}
}

//snp
//if( $refresh || isset($_POST['del_poll_option']) || $error_msg != '' )
if( $refresh || isset($_POST['del_poll_option']) || $error_msg || ($submit && $topic_has_new_posts) )
//snp end
{
	$username = ( !empty($_POST['username']) ) ? htmlspecialchars(trim($_POST['username'])) : '';
	$subject = ( !empty($_POST['subject']) ) ? htmlspecialchars(trim($_POST['subject'])) : '';
	$message = ( !empty($_POST['message']) ) ? htmlspecialchars(trim($_POST['message'])) : '';

	$poll_title = ( !empty($_POST['poll_title']) ) ? htmlspecialchars(trim($_POST['poll_title'])) : '';
	$poll_length = ( isset($_POST['poll_length']) ) ? max(0, intval($_POST['poll_length'])) : 0;

	$poll_options = array();
	if ( !empty($_POST['poll_option_text']) )
	{
#		while( list($option_id, $option_text) = @each($_POST['poll_option_text']) )
		foreach ($_POST['poll_option_text'] as $option_id => $option_text)
		{
			if( isset($_POST['del_poll_option'][$option_id]) )
			{
				unset($poll_options[$option_id]);
			}
			else if ( !empty($option_text) )
			{
				$poll_options[$option_id] = htmlspecialchars(trim($option_text));
			}
		}
	}

	if ( $poll_add && !empty($_POST['add_poll_option_text']) )
	{
		$poll_options[] = htmlspecialchars(trim($_POST['add_poll_option_text']));
	}

	if ($preview)
	{
		$bbcode_uid = ($bbcode_on) ? make_bbcode_uid() : '';
		$preview_message = prepare_message(unprepare_message($message), $bbcode_on, $smilies_on, $bbcode_uid);
		$preview_subject = $subject;
		$preview_username = $username;

		// Finalise processing as per viewtopic
		if ($bbcode_on)
		{
			$preview_message = bbencode_second_pass($preview_message, $bbcode_uid);
		}
		$preview_message = make_clickable($preview_message);

		if ($smilies_on)
		{
			$preview_message = smilies_pass($preview_message);
		}
		$preview_message = nl2br($preview_message);

		$template->assign_vars(array(
			'TPL_PREVIEW_POST' => true,
			'TOPIC_TITLE'    => wbr($preview_subject),
			'POST_SUBJECT'   => $preview_subject,
			'POSTER_NAME'    => $preview_username,
			'POST_DATE'      => create_date($bb_cfg['default_dateformat'], time(), $bb_cfg['board_timezone']),
			'PREVIEW_MSG'    => $preview_message,

			'L_POST'         => $lang['Post'],
		));
	}
	else if ($error_msg)
	{
		$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
	}
}
else
{
	// User default entry point
	if ( $mode == 'newtopic' )
	{
		$username = ($userdata['session_logged_in']) ? $userdata['username'] : '';
		$poll_title = '';
		$poll_length = '';
		$subject = '';
		$message = '';
	}
	else if ( $mode == 'reply' )
	{
		$username = ( $userdata['session_logged_in'] ) ? $userdata['username'] : '';
		$subject = '';
		$message = '';
	}
	else if ( $mode == 'quote' || $mode == 'editpost' )
	{
		$subject = ( $post_data['first_post'] ) ? $post_info['topic_title'] : $post_info['post_subject'];
		$message = ( $mode == 'quote' && $post_info['topic_first_post_id'] == $post_info['post_id'] ) ? $post_info['topic_title'] : $post_info['post_text'];

		if ( $mode == 'editpost' )
		{
			$attach_sig = $post_info['enable_sig'];
			$bbcode_on = $post_info['enable_bbcode'];
			$smilies_on = $post_info['enable_smilies'];
		}
		else
		{
			$attach_sig = bf($userdata['user_opt'], 'user_opt', 'attachsig');
		}

		if ( $post_info['bbcode_uid'] != '' )
		{
			$message = preg_replace('/\:(([a-z0-9]:)?)' . $post_info['bbcode_uid'] . '/s', '', $message);
		}

		$message = str_replace('<', '&lt;', $message);
		$message = str_replace('>', '&gt;', $message);
		$message = str_replace('<br />', "\n", $message);

		if ( $mode == 'quote' )
		{
			if (!defined('WORD_LIST_OBTAINED'))
			{
				$orig_word = array();
				$replace_word = array();
				obtain_word_list($orig_word, $replace_word);
				define('WORD_LIST_OBTAINED', TRUE);
			}

			$msg_date = @create_date($bb_cfg['default_dateformat'], $postrow['post_time'], $bb_cfg['board_timezone']);

			// Use trim to get rid of spaces placed there by MS-SQL 2000
			$quote_username = ( trim($post_info['post_username']) != '' ) ? $post_info['post_username'] : $post_info['username'];
			$message = '[quote="' . $quote_username . '"]' . $message . '[/quote]';
			// hide user passkey
			$message = preg_replace('#(?<=\?uk=)[a-zA-Z0-9]{10}(?=&)#', 'passkey', $message);
			// hide sid
			$message = preg_replace('#(?<=[\?&;]sid=)[a-zA-Z0-9]{12}#', 'sid', $message);

			if ( !empty($orig_word) )
			{
				$subject = ( !empty($subject) ) ? preg_replace($orig_word, $replace_word, $subject) : '';
				$message = ( !empty($message) ) ? preg_replace($orig_word, $replace_word, $message) : '';
			}

			if ( !preg_match('/^Re:/', $subject) && strlen($subject) > 0 )
			{
				$subject = 'Re: ' . $subject;
			}

			$mode = 'reply';
		}
		else
		{
			$username = ( $post_info['user_id'] == ANONYMOUS && !empty($post_info['post_username']) ) ? $post_info['post_username'] : '';
		}
	}
}

if (IS_GUEST || ($mode == 'editpost' && $post_info['poster_id'] == ANONYMOUS))
{
	$template->assign_var('POSTING_USERNAME');
}

//
// Notify checkbox
//
if (!IS_GUEST)
{
	if ($mode != 'editpost' || ($mode == 'editpost' && $post_info['poster_id'] != ANONYMOUS))
	{
		$template->assign_var('SHOW_NOTIFY_CHECKBOX');
	}
}

//
// Topic type selection
//
$topic_type_toggle = '';
if ( $mode == 'newtopic' || ( $mode == 'editpost' && $post_data['first_post'] ) )
{
	$template->assign_block_vars('switch_type_toggle', array());

	if( $is_auth['auth_sticky'] )
	{
		$topic_type_toggle .= '<input type="radio" name="topictype" value="' . POST_STICKY . '"';
		if ( isset($post_data['topic_type']) && ($post_data['topic_type'] == POST_STICKY || $topic_type == POST_STICKY) )
		{
			$topic_type_toggle .= ' checked="checked"';
		}
		$topic_type_toggle .= ' /> ' . $lang['POST_STICKY'] . '&nbsp;&nbsp;';
	}

	if( $is_auth['auth_announce'] )
	{
		$topic_type_toggle .= '<input type="radio" name="topictype" value="' . POST_ANNOUNCE . '"';
		if ( isset($post_data['topic_type']) && ($post_data['topic_type'] == POST_ANNOUNCE || $topic_type == POST_ANNOUNCE) )
		{
			$topic_type_toggle .= ' checked="checked"';
		}
		$topic_type_toggle .= ' /> ' . $lang['POST_ANNOUNCEMENT'] . '&nbsp;&nbsp;';
	}

	if ( $topic_type_toggle != '' )
	{
		$topic_type_toggle = $lang['Post_topic_as'] . ': <input type="radio" name="topictype" value="' . POST_NORMAL .'"' . ( (!isset($post_data['topic_type']) || $post_data['topic_type'] == POST_NORMAL || $topic_type == POST_NORMAL) ? ' checked="checked"' : '' ) . ' /> ' . $lang['POST_NORMAL'] . '&nbsp;&nbsp;' . $topic_type_toggle;
	}
}
//bt
$topic_dl_type = (isset($post_info['topic_dl_type'])) ? $post_info['topic_dl_type'] : 0;

if ($topic_dl_type || $post_info['allow_dl_topic'] || $is_auth['auth_mod'])
{
	if (!$topic_type_toggle)
	{
		$topic_type_toggle = $lang['Post_topic_as'] . ': ';
	}

	$dl_ds = $dl_ch = $dl_hid = '';
	$dl_type_name = 'topic_dl_type';
	$dl_type_val = ($topic_dl_type) ? 1 : 0;

	if (!$post_info['allow_dl_topic'] && !$is_auth['auth_mod'])
	{
		$dl_ds = ' disabled="disabled" ';
		$dl_hid = '<input type="hidden" name="topic_dl_type" value="'. $dl_type_val .'" />';
		$dl_type_name = '';
	}

	$dl_ch = ($mode == 'editpost' && $post_data['first_post'] && $topic_dl_type) ? ' checked="checked" ' : '';

	$topic_type_toggle .= '<nobr><input type="checkbox" name="'. $dl_type_name .'" id="topic_dl_type_id" '. $dl_ds . $dl_ch .' /><label for="topic_dl_type_id"> Download</label></nobr>';
	$topic_type_toggle .= $dl_hid;
}
//bt end

$hidden_form_fields = '<input type="hidden" name="mode" value="' . $mode . '" />';

switch( $mode )
{
	case 'newtopic':
		$page_title = $lang['Post_a_new_topic'];
		$hidden_form_fields .= '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';
		break;

	case 'reply':
		$page_title = $lang['Post_a_reply'];
		$hidden_form_fields .= '<input type="hidden" name="' . POST_TOPIC_URL . '" value="' . $topic_id . '" />';
		break;

	case 'editpost':
		$page_title = $lang['Edit_post'];
		$hidden_form_fields .= '<input type="hidden" name="' . POST_POST_URL . '" value="' . $post_id . '" />';
		break;
}

// Generate smilies listing for page output
generate_smilies('inline');

$template->set_filenames(array(
	'body' => 'posting.tpl',
));

$template->assign_vars(array(
	'FORUM_NAME' => htmlCHR($forum_name),
	'PAGE_TITLE' => $page_title,
	'POSTING_TYPE_TITLE' => $page_title,
	'POSTING_TOPIC_ID' => ($mode != 'newtopic') ? $topic_id : '',
	'POSTING_TOPIC_TITLE' => ($mode != 'newtopic') ? wbr($post_info['topic_title']) : '',

	'SHOW_VIRTUAL_KEYBOARD' => $bb_cfg['show_virtual_keyboard'],

	'U_VIEW_FORUM' => append_sid("viewforum.php?" . POST_FORUM_URL . "=$forum_id"))
);

if ($mode == 'newtopic' || $post_data['first_post'])
{
	$template->assign_var('POSTING_SUBJECT');
}

// Update post time
if ($mode == 'editpost' && $post_data['last_post'] && !$post_data['first_post'])
{
	$template->assign_vars(array(
		'SHOW_UPDATE_POST_TIME'    => ($is_auth['auth_mod'] || ($post_data['poster_post'] && $post_info['post_time'] + 3600*3 > TIMENOW)),
		'UPDATE_POST_TIME_CHECKED' => ($post_data['poster_post'] && ($post_info['post_time'] + 3600*2 > TIMENOW)),
	));
}

//
// Output the data to the template
//
$bbcode_status = ($bb_cfg['allow_bbcode']) ? $lang['BBCode_is_ON'] : $lang['BBCode_is_OFF'];

$template->assign_vars(array(
	'USERNAME' => @$username,
	'SUBJECT' => $subject,
	'MESSAGE' => $message,
	'BBCODE_STATUS' => sprintf($bbcode_status, '<a href="'."faq.php?mode=bbcode".'" target="_phpbbcode">', '</a>'),
	'SMILIES_STATUS' => ($bb_cfg['allow_smilies']) ? $lang['Smilies_are_ON'] : $lang['Smilies_are_OFF'],

	'L_SUBJECT' => $lang['Subject'],
	'L_MESSAGE_BODY' => $lang['Message_body'],
	'L_SPELLCHECK' => $lang['Spellcheck'],
	'L_CONFIRM_DELETE' => $lang['Confirm_delete'],
	'L_DISABLE_BBCODE' => $lang['Disable_BBCode_post'],
	'L_DISABLE_SMILIES' => $lang['Disable_Smilies_post'],
	'L_NOTIFY_ON_REPLY' => $lang['Notify'],
	'L_DELETE_POST' => $lang['Delete_post'],
	'L_UPDATE_POST_TIME' => $lang['Update_post_time'],

	'L_BBCODE_B_HELP' => $lang['bbcode_b_help'],
	'L_BBCODE_I_HELP' => $lang['bbcode_i_help'],
	'L_BBCODE_U_HELP' => $lang['bbcode_u_help'],
	'L_BBCODE_Q_HELP' => $lang['bbcode_q_help'],
	'L_BBCODE_C_HELP' => $lang['bbcode_c_help'],
	'L_BBCODE_L_HELP' => $lang['bbcode_l_help'],
	'L_BBCODE_O_HELP' => $lang['bbcode_o_help'],
	'L_BBCODE_P_HELP' => $lang['bbcode_p_help'],
	'L_BBCODE_W_HELP' => $lang['bbcode_w_help'],
	'L_BBCODE_A_HELP' => $lang['bbcode_a_help'],
	'L_BBCODE_S_HELP' => $lang['bbcode_s_help'],
	'L_BBCODE_F_HELP' => $lang['bbcode_f_help'],
	'L_EMPTY_MESSAGE' => $lang['Empty_message'],

	'L_FONT_COLOR' => $lang['Font_color'],
	'L_COLOR_DEFAULT' => $lang['color_default'],
	'L_COLOR_DARK_RED' => $lang['color_dark_red'],
	'L_COLOR_RED' => $lang['color_red'],
	'L_COLOR_ORANGE' => $lang['color_orange'],
	'L_COLOR_BROWN' => $lang['color_brown'],
	'L_COLOR_YELLOW' => $lang['color_yellow'],
	'L_COLOR_GREEN' => $lang['color_green'],
	'L_COLOR_OLIVE' => $lang['color_olive'],
	'L_COLOR_CYAN' => $lang['color_cyan'],
	'L_COLOR_BLUE' => $lang['color_blue'],
	'L_COLOR_DARK_BLUE' => $lang['color_dark_blue'],
	'L_COLOR_INDIGO' => $lang['color_indigo'],
	'L_COLOR_VIOLET' => $lang['color_violet'],
	'L_COLOR_WHITE' => $lang['color_white'],
	'L_COLOR_BLACK' => $lang['color_black'],

	'L_FONT_SIZE' => $lang['Font_size'],
	'L_FONT_TINY' => $lang['font_tiny'],
	'L_FONT_SMALL' => $lang['font_small'],
	'L_FONT_NORMAL' => $lang['font_normal'],
	'L_FONT_LARGE' => $lang['font_large'],
	'L_FONT_HUGE' => $lang['font_huge'],

	'L_BBCODE_CLOSE_TAGS' => $lang['Close_Tags'],
	'L_STYLES_TIP' => $lang['Styles_tip'],

	'U_VIEWTOPIC' => ( $mode == 'reply' ) ? append_sid("viewtopic.php?" . POST_TOPIC_URL . "=$topic_id&amp;postorder=desc") : '',

	'S_BBCODE_CHECKED' => ( !$bbcode_on ) ? 'checked="checked"' : '',
	'S_SMILIES_CHECKED' => ( !$smilies_on ) ? 'checked="checked"' : '',
	'S_SIGNATURE_CHECKED' => ( $attach_sig ) ? 'checked="checked"' : '',
	'S_NOTIFY_CHECKED' => ( $notify_user ) ? 'checked="checked"' : '',
	'S_TYPE_TOGGLE' => $topic_type_toggle,
	'S_TOPIC_ID' => $topic_id,
	'S_POST_ACTION' => append_sid("posting.php"),
	'S_HIDDEN_FORM_FIELDS' => $hidden_form_fields)
);

// Output the data to the template (for MAIL.RU Keyboard)
$template->assign_vars(array(
	'L_KB_TITLE' => $lang['kb_title'],
	'L_LAYOUT' => $lang['kb_rus_keylayout'],
	'L_NONE' => $lang['kb_none'],
	'L_TRANSLIT' => $lang['kb_translit'],
	'L_TRADITIONAL' => $lang['kb_traditional'],
	'L_RULES' => $lang['kb_rules'],
	'L_SHOW' => $lang['kb_show'],
	'L_CLOSE' =>  $lang['kb_close'],
	'L_TRANSLIT_OPERA7' => $lang['kb_translit_opera7'],
	'L_TRANSLIT_MOZILLA' => $lang['kb_translit_mozilla'],
	'S_VISIBILITY_RULES' => 'position:absolute;visibility:hidden;',
	'S_VISIBILITY_KEYB' => 'position:absolute;visibility:hidden;',
	'S_VISIBILITY_OFF' => '')
);
//
// Poll entry switch/output
//
if( ( $mode == 'newtopic' || ( $mode == 'editpost' && $post_data['edit_poll']) ) && $is_auth['auth_pollcreate'] )
{
	$template->assign_vars(array(
		'L_ADD_A_POLL' => $lang['Add_poll'],
		'L_ADD_POLL_EXPLAIN' => $lang['Add_poll_explain'],
		'L_POLL_QUESTION' => $lang['Poll_question'],
		'L_POLL_OPTION' => $lang['Poll_option'],
		'L_ADD_OPTION' => $lang['Add_option'],
		'L_POLL_LENGTH' => $lang['Poll_for'],
		'L_DAYS' => $lang['Days'],
		'L_POLL_LENGTH_EXPLAIN' => $lang['Poll_for_explain'],
		'L_POLL_DELETE' => $lang['Delete_poll'],

		'POLL_TITLE' => @$poll_title,
		'POLL_LENGTH' => @$poll_length)
	);

	if( $mode == 'editpost' && $post_data['edit_poll'] && $post_data['has_poll'])
	{
		$template->assign_block_vars('switch_poll_delete_toggle', array());
	}

	if( !empty($poll_options) )
	{
		while( list($option_id, $option_text) = each($poll_options) )
		{
			$template->assign_block_vars('poll_option_rows', array(
				'POLL_OPTION' => str_replace('"', '&quot;', $option_text),

				'S_POLL_OPTION_NUM' => $option_id)
			);
		}
	}

	$template->assign_var('POLLBOX');
}

//
// Topic review
//
if( $mode == 'reply' && $is_auth['auth_read'] )
{
	topic_review($topic_id);
}

require(PAGE_HEADER);

$template->pparse('body');

require(PAGE_FOOTER);


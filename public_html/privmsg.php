<?php
/**
*
* @package t.ivacuum.ru
* @copyright (c) 2011
*
*/

define('BB_SCRIPT', 'pm');
define('IN_PM',     true);
$t_root_path = __DIR__ . '/';
require($t_root_path . 'common.php');
require($t_root_path . 'includes/bbcode.php');
require($t_root_path . 'includes/functions_post.php');

$privmsg_sent_id = $l_box_name = $to_username = $privmsg_subject = $privmsg_message = $error_msg = '';

$page_cfg['load_tpl_vars'] = array(
	'pm_icons',
);

//
// Is PM disabled?
//
if ( !empty($bb_cfg['privmsg_disable']) )
{
	message_die(GENERAL_MESSAGE, 'PM_disabled');
}

$html_entities_match = array('#&(?!(\#[0-9]+;))#', '#<#', '#>#', '#"#');
$html_entities_replace = array('&amp;', '&lt;', '&gt;', '&quot;');

//
// Parameters
//
$submit = request_var('post', false);
$submit_search = request_var('usersubmit', false);
$submit_msgdays = request_var('submit_msgdays', false);
$cancel = request_var('cancel', false);
$preview = request_var('preview', false);
$confirmed = request_var('confirm', false);
$delete = request_var('delete', false);
$delete_all = request_var('deleteall', false);
$save = request_var('save', false);
$mode = request_var('mode', '');

$refresh = $preview || $submit_search;

$mark_list = ( !empty($_POST['mark']) ) ? $_POST['mark'] : 0;

if ($folder =& $_REQUEST['folder'])
{
	if ($folder != 'inbox' && $folder != 'outbox' && $folder != 'sentbox' && $folder != 'savebox')
	{
		$folder = 'inbox';
	}
}
else
{
	$folder = 'inbox';
}

// Start session management
$user->session_start(array('req_login' => true));

if (IS_ADMIN || IS_MOD)
{
	$bb_cfg['max_inbox_privmsgs']   += 1000;
	$bb_cfg['max_sentbox_privmsgs'] += 1000;
	$bb_cfg['max_savebox_privmsgs'] += 1000;
}
else if (IS_GROUP_MEMBER)
{
	$bb_cfg['max_inbox_privmsgs']   += 200;
	$bb_cfg['max_sentbox_privmsgs'] += 200;
	$bb_cfg['max_savebox_privmsgs'] += 200;
}

$template->assign_vars(array(
	'IN_PM'              => true,
	'L_COLOR_DARK_GREEN' => $lang['color_dark_green'],
	'L_COLOR_GRAY'       => $lang['color_gray'],
	'L_FONT_COLOR_SEL'   => $lang['QR_Color_sel'],
	'L_FONT_SEL'         => $lang['QR_Font_sel'],
	'L_FONT_SIZE_SEL'    => $lang['QR_Size_sel'],
	'L_STEEL_BLUE'       => $lang['color_steel_blue'],
	'QUICK_REPLY'        => ($bb_cfg['show_quick_reply'] && $folder == 'inbox' && $mode == 'read'),
));

//
// Cancel
//
if ( $cancel )
{
	redirect(append_sid("privmsg.$phpEx?folder=$folder", true));
}

//
// Var definitions
//
$start = isset($_REQUEST['start']) ? abs(intval($_REQUEST['start'])) : 0;

if ( isset($_POST[POST_POST_URL]) || isset($_GET[POST_POST_URL]) )
{
	$privmsg_id = ( isset($_POST[POST_POST_URL]) ) ? intval($_POST[POST_POST_URL]) : intval($_GET[POST_POST_URL]);
}
else
{
	$privmsg_id = '';
}

$error = FALSE;

//
// Define the box image links
//
$inbox_url = ( $folder != 'inbox' || $mode != '' ) ? '<a href="'."privmsg.$phpEx?folder=inbox".'">'. $lang['Inbox'] .'</a>' : $lang['Inbox'];

$outbox_url = ( $folder != 'outbox' || $mode != '' ) ? '<a href="'."privmsg.$phpEx?folder=outbox".'">'. $lang['Outbox'] .'</a>' : $lang['Outbox'];

$sentbox_url = ( $folder != 'sentbox' || $mode != '' ) ? '<a href="'."privmsg.$phpEx?folder=sentbox".'">'. $lang['Sentbox'] .'</a>' : $lang['Sentbox'];

$savebox_url = ( $folder != 'savebox' || $mode != '' ) ? '<a href="'."privmsg.$phpEx?folder=savebox".'">'. $lang['Savebox'] .'</a>' : $lang['Savebox'];

// ----------
// Start main
//

$template->assign_var('POSTING_SUBJECT');

if ( $mode == 'read' )
{
	if ( !empty($_GET[POST_POST_URL]) )
	{
		$privmsgs_id = intval($_GET[POST_POST_URL]);
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['No_post_id']);
	}

	//
	// SQL to pull appropriate message, prevents nosey people
	// reading other peoples messages ... hopefully!
	//
	switch( $folder )
	{
		case 'inbox':
			$l_box_name = $lang['Inbox'];
			$pm_sql_user = "AND pm.privmsgs_to_userid = " . $userdata['user_id'] . "
				AND ( pm.privmsgs_type = " . PRIVMSGS_READ_MAIL . "
					OR pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
					OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
			break;
		case 'outbox':
			$l_box_name = $lang['Outbox'];
			$pm_sql_user = "AND pm.privmsgs_from_userid =  " . $userdata['user_id'] . "
				AND ( pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
					OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) ";
			break;
		case 'sentbox':
			$l_box_name = $lang['Sentbox'];
			$pm_sql_user = "AND pm.privmsgs_from_userid =  " . $userdata['user_id'] . "
				AND pm.privmsgs_type = " . PRIVMSGS_SENT_MAIL;
			break;
		case 'savebox':
			$l_box_name = $lang['Savebox'];
			$pm_sql_user = "AND ( ( pm.privmsgs_to_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
				OR ( pm.privmsgs_from_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " )
				)";
			break;
		default:
			message_die(GENERAL_ERROR, $lang['No_such_folder']);
			break;
	}

	//
	// Major query obtains the message ...
	//
	$sql = "SELECT u.username AS username_1, u.user_id AS user_id_1, u2.username AS username_2, u2.user_id AS user_id_2, u.user_posts, u.user_from, u.user_email, u.user_regdate, u.user_rank, u.user_avatar, pm.*, pmt.privmsgs_bbcode_uid, pmt.privmsgs_text
		FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u, " . USERS_TABLE . " u2
		WHERE pm.privmsgs_id = $privmsgs_id
			AND pmt.privmsgs_text_id = pm.privmsgs_id
			$pm_sql_user
			AND u.user_id = pm.privmsgs_from_userid
			AND u2.user_id = pm.privmsgs_to_userid";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query private message post information', '', __LINE__, __FILE__, $sql);
	}

	//
	// Did the query return any data?
	//
	if ( !($privmsg = $db->sql_fetchrow($result)) )
	{
		redirect(append_sid("privmsg.$phpEx?folder=$folder", true));
	}

	$privmsg_id = $privmsg['privmsgs_id'];

	//
	// Is this a new message in the inbox? If it is then save
	// a copy in the posters sent box
	//
	if (($privmsg['privmsgs_type'] == PRIVMSGS_NEW_MAIL || $privmsg['privmsgs_type'] == PRIVMSGS_UNREAD_MAIL) && $folder == 'inbox')
	{
		// Update appropriate counter
		switch ($privmsg['privmsgs_type'])
		{
			case PRIVMSGS_NEW_MAIL:
				$sql = "user_new_privmsg = IF(user_new_privmsg, user_new_privmsg - 1, 0)";
				break;
			case PRIVMSGS_UNREAD_MAIL:
				$sql = "user_unread_privmsg = IF(user_unread_privmsg, user_unread_privmsg - 1, 0)";
				break;
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET $sql
			WHERE user_id = " . $userdata['user_id'];
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update private message read status for user', '', __LINE__, __FILE__, $sql);
		}
		if ($db->sql_affectedrows())
		{
			cache_rm_userdata($userdata);
		}

		$sql = "UPDATE " . PRIVMSGS_TABLE . "
			SET privmsgs_type = " . PRIVMSGS_READ_MAIL . "
			WHERE privmsgs_id = " . $privmsg['privmsgs_id'];
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not update private message read status', '', __LINE__, __FILE__, $sql);
		}

		// Check to see if the poster has a 'full' sent box
		$sql = "SELECT COUNT(privmsgs_id) AS sent_items, MIN(privmsgs_date) AS oldest_post_time
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_type = " . PRIVMSGS_SENT_MAIL . "
				AND privmsgs_from_userid = " . $privmsg['privmsgs_from_userid'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain sent message info for sendee', '', __LINE__, __FILE__, $sql);
		}

		if ( $sent_info = $db->sql_fetchrow($result) )
		{
			if ($bb_cfg['max_sentbox_privmsgs'] && $sent_info['sent_items'] >= $bb_cfg['max_sentbox_privmsgs'])
			{
				$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_type = " . PRIVMSGS_SENT_MAIL . "
						AND privmsgs_date = " . $sent_info['oldest_post_time'] . "
						AND privmsgs_from_userid = " . $privmsg['privmsgs_from_userid'];
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not find oldest privmsgs', '', __LINE__, __FILE__, $sql);
				}
				$old_privmsgs_id = $db->sql_fetchrow($result);
				$old_privmsgs_id = (int) $old_privmsgs_id['privmsgs_id'];

				$sql = "DELETE FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs (sent)', '', __LINE__, __FILE__, $sql);
				}

				$sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
					WHERE privmsgs_text_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs text (sent)', '', __LINE__, __FILE__, $sql);
				}
			}
		}

		//
		// This makes a copy of the post and stores it as a SENT message from the sendee. Perhaps
		// not the most DB friendly way but a lot easier to manage, besides the admin will be able to
		// set limits on numbers of storable posts for users ... hopefully!
		//
		$sql = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip, privmsgs_enable_bbcode, privmsgs_enable_smilies)
			VALUES (" . PRIVMSGS_SENT_MAIL . ", " . $db->check_value($privmsg['privmsgs_subject']) . ", " . $privmsg['privmsgs_from_userid'] . ", " . $privmsg['privmsgs_to_userid'] . ", " . $privmsg['privmsgs_date'] . ", '" . $privmsg['privmsgs_ip'] . "', " . $privmsg['privmsgs_enable_bbcode'] . ", " . $privmsg['privmsgs_enable_smilies'] . ")";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not insert private message sent info', '', __LINE__, __FILE__, $sql);
		}

		$privmsg_sent_id = $db->sql_nextid();

		$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_bbcode_uid, privmsgs_text)
			VALUES ($privmsg_sent_id, '" . $privmsg['privmsgs_bbcode_uid'] . "', " . $db->check_value($privmsg['privmsgs_text']) . ")";
		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Could not insert private message sent text', '', __LINE__, __FILE__, $sql);
		}
	}

	//
	// Pick a folder, any folder, so long as it's one below ...
	//
	$post_urls = array(
		'post' => append_sid("privmsg.$phpEx?mode=post"),
		'reply' => append_sid("privmsg.$phpEx?mode=reply&amp;" . POST_POST_URL . "=$privmsg_id"),
		'quote' => append_sid("privmsg.$phpEx?mode=quote&amp;" . POST_POST_URL . "=$privmsg_id"),
		'edit' => append_sid("privmsg.$phpEx?mode=edit&amp;" . POST_POST_URL . "=$privmsg_id")
	);
	$post_icons = array(
		'post_img' => '<a href="' . $post_urls['post'] . '"><img src="' . $images['pm_postmsg'] . '" alt="' . $lang['Post_new_pm'] . '" border="0" /></a>',
		'post' => '<a href="' . $post_urls['post'] . '">' . $lang['Post_new_pm'] . '</a>',
		'reply_img' => '<a href="' . $post_urls['reply'] . '"><img src="' . $images['pm_replymsg'] . '" alt="' . $lang['Post_reply_pm'] . '" border="0" /></a>',
		'reply' => '<a href="' . $post_urls['reply'] . '">' . $lang['Post_reply_pm'] . '</a>',
		'quote_img' => '<a href="' . $post_urls['quote'] . '"><img src="' . $images['pm_quotemsg'] . '" alt="' . $lang['Post_quote_pm'] . '" border="0" /></a>',
		'quote' => '<a href="' . $post_urls['quote'] . '">' . $lang['Post_quote_pm'] . '</a>',
		'edit_img' => '<a href="' . $post_urls['edit'] . '"><img src="' . $images['pm_editmsg'] . '" alt="' . $lang['Edit_pm'] . '" border="0" /></a>',
		'edit' => '<a href="' . $post_urls['edit'] . '">' . $lang['Edit_pm'] . '</a>'
	);

	if ( $folder == 'inbox' )
	{
		$post_img = $post_icons['post_img'];
		$reply_img = $post_icons['reply_img'];
		$quote_img = $post_icons['quote_img'];
		$edit_img = '';
		$post = $post_icons['post'];
		$reply = $post_icons['reply'];
		$quote = $post_icons['quote'];
		$edit = '';
		$l_box_name = $lang['Inbox'];
	}
	else if ( $folder == 'outbox' )
	{
		$post_img = $post_icons['post_img'];
		$reply_img = '';
		$quote_img = '';
		$edit_img = $post_icons['edit_img'];
		$post = $post_icons['post'];
		$reply = '';
		$quote = '';
		$edit = $post_icons['edit'];
		$l_box_name = $lang['Outbox'];
	}
	else if ( $folder == 'savebox' )
	{
		if ( $privmsg['privmsgs_type'] == PRIVMSGS_SAVED_IN_MAIL )
		{
			$post_img = $post_icons['post_img'];
			$reply_img = $post_icons['reply_img'];
			$quote_img = $post_icons['quote_img'];
			$edit_img = '';
			$post = $post_icons['post'];
			$reply = $post_icons['reply'];
			$quote = $post_icons['quote'];
			$edit = '';
		}
		else
		{
			$post_img = $post_icons['post_img'];
			$reply_img = '';
			$quote_img = '';
			$edit_img = '';
			$post = $post_icons['post'];
			$reply = '';
			$quote = '';
			$edit = '';
		}
		$l_box_name = $lang['Saved'];
	}
	else if ( $folder == 'sentbox' )
	{
		$post_img = $post_icons['post_img'];
		$reply_img = '';
		$quote_img = '';
		$edit_img = '';
		$post = $post_icons['post'];
		$reply = '';
		$quote = '';
		$edit = '';
		$l_box_name = $lang['Sent'];
	}

	$s_hidden_fields = '<input type="hidden" name="mark[]" value="' . $privmsgs_id . '" />';

	$page_title = $lang['Read_pm'];

	//
	// Load templates
	//
	$template->set_filenames(array(
		'body' => 'privmsgs_read.tpl')
	);

	$template->assign_vars(array(
		'INBOX' => $inbox_url,

		'POST_PM_IMG' => $post_img,
		'REPLY_PM_IMG' => $reply_img,
		'EDIT_PM_IMG' => $edit_img,
		'QUOTE_PM_IMG' => $quote_img,
		'POST_PM' => $post,
		'REPLY_PM' => $reply,
		'EDIT_PM' => $edit,
		'QUOTE_PM' => $quote,

		'SENTBOX' => $sentbox_url,
		'OUTBOX' => $outbox_url,
		'SAVEBOX' => $savebox_url,

		'BOX_NAME' => $l_box_name,

		'L_INBOX' => $lang['Inbox'],
		'L_OUTBOX' => $lang['Outbox'],
		'L_SENTBOX' => $lang['Sent'],
		'L_SAVEBOX' => $lang['Saved'],
		'L_FLAG' => $lang['Flag'],
		'L_SUBJECT' => $lang['Subject'],
		'L_DATE' => $lang['Date'],
		'L_SAVE_MSG' => $lang['Save_message'],
		'L_DELETE_MSG' => $lang['Delete_message'],

		'S_PRIVMSGS_ACTION' => append_sid("privmsg.$phpEx?folder=$folder"),
		'S_HIDDEN_FIELDS' => $s_hidden_fields)
	);

	$username_from = $privmsg['username_1'];
	$user_id_from = $privmsg['user_id_1'];
	$username_to = $privmsg['username_2'];
	$user_id_to = $privmsg['user_id_2'];

	$post_date = create_date($bb_cfg['default_dateformat'], $privmsg['privmsgs_date'], $bb_cfg['board_timezone']);

	$temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . '=' . $user_id_from);
	$profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0" /></a>';
	$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

	$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=$user_id_from");
	$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0" /></a>';
	$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

	$temp_url = append_sid("search.$phpEx?search_author=1&amp;uid=$user_id_from");
	$search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . sprintf($lang['Search_user_posts'], $username_from) . '" title="' . sprintf($lang['Search_user_posts'], $username_from) . '" border="0" /></a>';
	$search = '<a href="' . $temp_url . '">' . sprintf($lang['Search_user_posts'], $username_from) . '</a>';

	//
	// Processing of post
	//
	$post_subject = htmlCHR($privmsg['privmsgs_subject']);

	$private_message = $privmsg['privmsgs_text'];
	$bbcode_uid = $privmsg['privmsgs_bbcode_uid'];

	if ( $bbcode_uid != '' )
	{
		$private_message = ( $bb_cfg['allow_bbcode'] ) ? bbencode_second_pass($private_message, $bbcode_uid) : preg_replace('/\:[0-9a-z\:]+\]/si', ']', $private_message);
	}

	$private_message = make_clickable($private_message);

	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);

	if ( count($orig_word) )
	{
		$post_subject = preg_replace($orig_word, $replacement_word, $post_subject);
		$private_message = preg_replace($orig_word, $replacement_word, $private_message);
	}

	if ( $bb_cfg['allow_smilies'] && $privmsg['privmsgs_enable_smilies'] )
	{
		$private_message = smilies_pass($private_message);
	}

	$private_message = str_replace("\n", '<br />', $private_message);

	//
	// Dump it to the templating engine
	//
	$template->assign_vars(array(
		'TO_USER_ID'     => $user_id_to,
		'TO_USER_NAME'   => $username_to,
		'FROM_USER_ID'   => $user_id_from,
		'FROM_USER_NAME' => $username_from,

		'QR_SUBJECT' => ((!preg_match('/^Re:/', $post_subject)) ? 'Re: ' : '') . $post_subject,
		'MESSAGE_TO' => $username_to,
		'MESSAGE_FROM' => $username_from,
		'RANK_IMAGE' => (@$rank_image) ? $rank_image : '',
		'POSTER_JOINED' => (@$poster_joined) ? $poster_joined : '',
		'POSTER_POSTS' => (@$poster_posts) ? $poster_posts : '',
		'POSTER_FROM' => (@$poster_from) ? $poster_from : '',
		'POSTER_AVATAR' => (@$poster_avatar) ? $poster_avatar : '',
		'POST_SUBJECT' => $post_subject,
		'POST_DATE' => $post_date,
		'PM_MESSAGE' => $private_message,

		'PROFILE_IMG' => $profile_img,
		'PROFILE' => $profile,
		'SEARCH_IMG' => $search_img,
		'SEARCH' => $search,
	));
}
else if ( ( $delete && $mark_list ) || $delete_all )
{
	if ( isset($mark_list) && !is_array($mark_list) )
	{
		// Set to empty array instead of '0' if nothing is selected.
		$mark_list = array();
	}

	if (!$confirmed)
	{
		$delete = isset($_POST['delete']) ? 'delete' : 'deleteall';

		$hidden_fields = array(
			'mode'  => $mode,
			$delete => 1,
		);
		foreach ($mark_list as $pm_id)
		{
			$hidden_fields['mark'][] = (int) $pm_id;
		}

		print_confirmation(array(
			'QUESTION'      => (count($mark_list) == 1) ? $lang['Confirm_delete_pm'] : $lang['Confirm_delete_pms'],
			'FORM_ACTION'   => "privmsg.$phpEx?folder=$folder",
			'HIDDEN_FIELDS' => build_hidden_fields($hidden_fields),
		));
	}
	else if ( $confirmed )
	{
		$delete_sql_id = '';

		if (!$delete_all)
		{
			for ($i = 0; $i < count($mark_list); $i++)
			{
				$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
			}
			$delete_sql_id = "AND privmsgs_id IN ($delete_sql_id)";
		}

		switch($folder)
		{
			case 'inbox':
				$delete_type = "privmsgs_to_userid = " . $userdata['user_id'] . " AND (
				privmsgs_type = " . PRIVMSGS_READ_MAIL . " OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
				break;

			case 'outbox':
				$delete_type = "privmsgs_from_userid = " . $userdata['user_id'] . " AND ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
				break;

			case 'sentbox':
				$delete_type = "privmsgs_from_userid = " . $userdata['user_id'] . " AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
				break;

			case 'savebox':
				$delete_type = "( ( privmsgs_from_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " )
				OR ( privmsgs_to_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) )";
				break;
		}

		$sql = "SELECT privmsgs_id
			FROM " . PRIVMSGS_TABLE . "
		WHERE $delete_type $delete_sql_id";
		if ( !($result = $db->sql_query($sql)) )
		{
		message_die(GENERAL_ERROR, 'Could not obtain id list to delete messages', '', __LINE__, __FILE__, $sql);
		}

		$mark_list = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$mark_list[] = $row['privmsgs_id'];
		}

		unset($delete_type);

		if ( count($mark_list) )
		{
			$delete_sql_id = '';
			for ($i = 0; $i < sizeof($mark_list); $i++)
			{
				$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
			}

			if ($folder == 'inbox' || $folder == 'outbox')
			{
				switch ($folder)
				{
					case 'inbox':
						$sql = "privmsgs_to_userid = " . $userdata['user_id'];
						break;
					case 'outbox':
						$sql = "privmsgs_from_userid = " . $userdata['user_id'];
						break;
				}

				// Get information relevant to new or unread mail
				// so we can adjust users counters appropriately
				$sql = "SELECT privmsgs_to_userid, privmsgs_type
					FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_id IN ($delete_sql_id)
						AND $sql
						AND privmsgs_type IN (" . PRIVMSGS_NEW_MAIL . ", " . PRIVMSGS_UNREAD_MAIL . ")";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not obtain user id list for outbox messages', '', __LINE__, __FILE__, $sql);
				}

				if ( $row = $db->sql_fetchrow($result))
				{
					$update_users = $update_list = array();

					do
					{
						switch ($row['privmsgs_type'])
						{
							case PRIVMSGS_NEW_MAIL:
								@$update_users['new'][$row['privmsgs_to_userid']]++;
								break;

							case PRIVMSGS_UNREAD_MAIL:
								@$update_users['unread'][$row['privmsgs_to_userid']]++;
								break;
						}
					}
					while ($row = $db->sql_fetchrow($result));

					if (sizeof($update_users))
					{
						while (list($type, $users) = each($update_users))
						{
							while (list($user_id, $dec) = each($users))
							{
								$update_list[$type][$dec][] = $user_id;
							}
						}
						unset($update_users);

						while (list($type, $dec_ary) = each($update_list))
						{
							switch ($type)
							{
								case 'new':
									$type = "user_new_privmsg";
									break;

								case 'unread':
									$type = "user_unread_privmsg";
									break;
							}

							while (list($dec, $user_ary) = each($dec_ary))
							{
								$user_ids = join(', ', $user_ary);

								$sql = "UPDATE " . USERS_TABLE . "
									SET $type = $type - $dec
									WHERE user_id IN ($user_ids)";
								if ( !$db->sql_query($sql) )
								{
									message_die(GENERAL_ERROR, 'Could not update user pm counters', '', __LINE__, __FILE__, $sql);
								}
							}
						}
						unset($update_list);
					}
				}
				$db->sql_freeresult($result);
			}

			// Delete the messages
			$delete_text_sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
				WHERE privmsgs_text_id IN ($delete_sql_id)";
			$delete_sql = "DELETE FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_id IN ($delete_sql_id)
					AND ";

			switch( $folder )
			{
				case 'inbox':
					$delete_sql .= "privmsgs_to_userid = " . $userdata['user_id'] . " AND (
						privmsgs_type = " . PRIVMSGS_READ_MAIL . " OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
					break;

				case 'outbox':
					$delete_sql .= "privmsgs_from_userid = " . $userdata['user_id'] . " AND (
						privmsgs_type = " . PRIVMSGS_NEW_MAIL . " OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
					break;

				case 'sentbox':
					$delete_sql .= "privmsgs_from_userid = " . $userdata['user_id'] . " AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
					break;

				case 'savebox':
					$delete_sql .= "( ( privmsgs_from_userid = " . $userdata['user_id'] . "
						AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " )
					OR ( privmsgs_to_userid = " . $userdata['user_id'] . "
						AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " ) )";
					break;
			}

			if ( !$db->sql_query($delete_sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete private message info', '', __LINE__, __FILE__, $delete_sql);
			}

			if ( !$db->sql_query($delete_text_sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete private message text', '', __LINE__, __FILE__, $delete_text_sql);
			}

			pm_message_die($lang['Delete_posts_succesfully']);
		}
		else
		{
			pm_message_die($lang['None_selected']);
		}
	}
}
else if ( $save && $mark_list && $folder != 'savebox' && $folder != 'outbox' )
{
	if (sizeof($mark_list))
	{
		// See if recipient is at their savebox limit
		$sql = "SELECT COUNT(privmsgs_id) AS savebox_items, MIN(privmsgs_date) AS oldest_post_time
			FROM " . PRIVMSGS_TABLE . "
			WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
				OR ( privmsgs_from_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") )";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain sent message info for sendee', '', __LINE__, __FILE__, $sql);
		}

		if ( $saved_info = $db->sql_fetchrow($result) )
		{
			if ($bb_cfg['max_savebox_privmsgs'] && $saved_info['savebox_items'] >= $bb_cfg['max_savebox_privmsgs'] )
			{
				$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . "
					WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . "
								AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
							OR ( privmsgs_from_userid = " . $userdata['user_id'] . "
								AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") )
						AND privmsgs_date = " . $saved_info['oldest_post_time'];
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not find oldest privmsgs (save)', '', __LINE__, __FILE__, $sql);
				}
				$old_privmsgs_id = $db->sql_fetchrow($result);
				$old_privmsgs_id = (int) $old_privmsgs_id['privmsgs_id'];

				$sql = "DELETE FROM " . PRIVMSGS_TABLE . "
					WHERE privmsgs_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs (save)', '', __LINE__, __FILE__, $sql);
				}

				$sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
					WHERE privmsgs_text_id = $old_privmsgs_id";
				if ( !$db->sql_query($sql) )
				{
					message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs text (save)', '', __LINE__, __FILE__, $sql);
				}
			}
		}

		$saved_sql_id = '';
		for ($i = 0; $i < sizeof($mark_list); $i++)
		{
			$saved_sql_id .= (($saved_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
		}

		// Process request
		$saved_sql = "UPDATE " . PRIVMSGS_TABLE;

		// Decrement read/new counters if appropriate
		if ($folder == 'inbox' || $folder == 'outbox')
		{
			switch ($folder)
			{
				case 'inbox':
					$sql = "privmsgs_to_userid = " . $userdata['user_id'];
					break;
				case 'outbox':
					$sql = "privmsgs_from_userid = " . $userdata['user_id'];
					break;
			}

			// Get information relevant to new or unread mail
			// so we can adjust users counters appropriately
			$sql = "SELECT privmsgs_to_userid, privmsgs_type
				FROM " . PRIVMSGS_TABLE . "
				WHERE privmsgs_id IN ($saved_sql_id)
					AND $sql
					AND privmsgs_type IN (" . PRIVMSGS_NEW_MAIL . ", " . PRIVMSGS_UNREAD_MAIL . ")";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain user id list for outbox messages', '', __LINE__, __FILE__, $sql);
			}

			if ( $row = $db->sql_fetchrow($result))
			{
				$update_users = $update_list = array();

				do
				{
					switch ($row['privmsgs_type'])
					{
						case PRIVMSGS_NEW_MAIL:
							@$update_users['new'][$row['privmsgs_to_userid']]++;
							break;

						case PRIVMSGS_UNREAD_MAIL:
							@$update_users['unread'][$row['privmsgs_to_userid']]++;
							break;
					}
				}
				while ($row = $db->sql_fetchrow($result));

				if (sizeof($update_users))
				{
					while (list($type, $users) = each($update_users))
					{
						while (list($user_id, $dec) = each($users))
						{
							$update_list[$type][$dec][] = $user_id;
						}
					}
					unset($update_users);

					while (list($type, $dec_ary) = each($update_list))
					{
						switch ($type)
						{
							case 'new':
								$type = "user_new_privmsg";
								break;

							case 'unread':
								$type = "user_unread_privmsg";
								break;
						}

						while (list($dec, $user_ary) = each($dec_ary))
						{
							$user_ids = join(', ', $user_ary);

							$sql = "UPDATE " . USERS_TABLE . "
								SET $type = $type - $dec
								WHERE user_id IN ($user_ids)";
							if ( !$db->sql_query($sql) )
							{
								message_die(GENERAL_ERROR, 'Could not update user pm counters', '', __LINE__, __FILE__, $sql);
							}
						}
					}
					unset($update_list);
				}
			}
			$db->sql_freeresult($result);
		}

		switch ($folder)
		{
			case 'inbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . "
					WHERE privmsgs_to_userid = " . $userdata['user_id'] . "
						AND ( privmsgs_type = " . PRIVMSGS_READ_MAIL . "
							OR privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
							OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . ")";
				break;

			case 'outbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . "
					WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
						AND ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
							OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) ";
				break;

			case 'sentbox':
				$saved_sql .= " SET privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . "
					WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
						AND privmsgs_type = " . PRIVMSGS_SENT_MAIL;
				break;
		}

		$saved_sql .= " AND privmsgs_id IN ($saved_sql_id)";

		if ( !$db->sql_query($saved_sql) )
		{
			message_die(GENERAL_ERROR, 'Could not save private messages', '', __LINE__, __FILE__, $saved_sql);
		}

		redirect(append_sid("privmsg.$phpEx?folder=savebox", true));
	}
}
else if ( $submit || $refresh || $mode != '' )
{
	//
	// Toggles
	//
	if ( !$bb_cfg['allow_bbcode'] )
	{
		$bbcode_on = 0;
	}
	else
	{
		$bbcode_on = ($submit || $refresh) ? (int) empty($_POST['disable_bbcode']) : $bb_cfg['allow_bbcode'];
	}

	if ( !$bb_cfg['allow_smilies'] )
	{
		$smilies_on = 0;
	}
	else
	{
		$smilies_on = ($submit || $refresh) ? (int) empty($_POST['disable_smilies']) : $bb_cfg['allow_smilies'];
	}

	if (IS_USER && $submit && $mode != 'edit')
	{
		//
		// Flood control
		//
		$sql = "SELECT MAX(privmsgs_date) AS last_post_time
			FROM " . PRIVMSGS_TABLE . "
			WHERE privmsgs_from_userid = " . $userdata['user_id'];
		if ( $result = $db->sql_query($sql) )
		{
			$db_row = $db->sql_fetchrow($result);

			$last_post_time = $db_row['last_post_time'];
			$current_time = time();

			if ( ( $current_time - $last_post_time ) < $bb_cfg['flood_interval'])
			{
				message_die(GENERAL_MESSAGE, $lang['Flood_Error']);
			}
		}
		//
		// End Flood control
		//
	}

	if ($submit && $mode == 'edit')
	{
		$sql = 'SELECT privmsgs_from_userid
			FROM ' . PRIVMSGS_TABLE . '
			WHERE privmsgs_id = ' . (int) $privmsg_id . '
				AND privmsgs_from_userid = ' . $userdata['user_id'];

		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, "Could not obtain message details", "", __LINE__, __FILE__, $sql);
		}

		if (!($row = $db->sql_fetchrow($result)))
		{
			message_die(GENERAL_MESSAGE, $lang['No_such_post']);
		}
		$db->sql_freeresult($result);

		unset($row);
	}

	if ( $submit )
	{
		if ( !empty($_POST['username']) )
		{
			$to_username = phpbb_clean_username($_POST['username']);
			// DelUsrKeepPM
			$to_username_sql = str_replace("\'", "''", $to_username);

			$sql = "SELECT user_id, user_notify_pm, user_email, user_lang, user_active
				FROM " . USERS_TABLE . "
				WHERE username = '$to_username_sql'";

			$to_userdata = $db->sql_fetchrow($db->sql_query($sql));

			if (!$to_userdata || $to_userdata['user_id'] == ANONYMOUS)
			{
				$error = TRUE;
				$error_msg = $lang['No_such_user'];
			}
			// DelUsrKeepPM end
		}
		else
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $lang['No_to_user'];
		}

		$privmsg_subject = trim(strip_tags($_POST['subject']));
		if ( empty($privmsg_subject) )
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $lang['Empty_subject'];
		}

		if ( !empty($_POST['message']) )
		{
			if ( !$error )
			{
				$bbcode_uid = ($bbcode_on) ? make_bbcode_uid() : '';

				$privmsg_message = prepare_message($_POST['message'], $bbcode_on, $smilies_on, $bbcode_uid);

			}
		}
		else
		{
			$error = TRUE;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $lang['Empty_message'];
		}
	}

	if ( $submit && !$error )
	{
		//
		// Has admin prevented user from sending PM's?
		//
		if ( !$userdata['user_allow_pm'] )
		{
			$message = $lang['Cannot_send_privmsg'];
			message_die(GENERAL_MESSAGE, $message);
		}

		$msg_time = time();

		if ( $mode != 'edit' )
		{
			//
			// See if recipient is at their inbox limit
			//
			$sql = "SELECT COUNT(privmsgs_id) AS inbox_items, MIN(privmsgs_date) AS oldest_post_time
				FROM " . PRIVMSGS_TABLE . "
				WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
						OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "
						OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )
					AND privmsgs_to_userid = " . $to_userdata['user_id'];
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_MESSAGE, $lang['No_such_user']);
			}

			if ( $inbox_info = $db->sql_fetchrow($result) )
			{
				if ($bb_cfg['max_inbox_privmsgs'] && $inbox_info['inbox_items'] >= $bb_cfg['max_inbox_privmsgs'])
				{
					$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . "
						WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
								OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "
								OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . "  )
							AND privmsgs_date = " . $inbox_info['oldest_post_time'] . "
							AND privmsgs_to_userid = " . $to_userdata['user_id'];
					if ( !$result = $db->sql_query($sql) )
					{
						message_die(GENERAL_ERROR, 'Could not find oldest privmsgs (inbox)', '', __LINE__, __FILE__, $sql);
					}
					$old_privmsgs_id = $db->sql_fetchrow($result);
					$old_privmsgs_id = (int) $old_privmsgs_id['privmsgs_id'];

					$sql = "DELETE FROM " . PRIVMSGS_TABLE . "
						WHERE privmsgs_id = $old_privmsgs_id";
					if ( !$db->sql_query($sql) )
					{
						message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs (inbox)'.$sql, '', __LINE__, __FILE__, $sql);
					}

					$sql = "DELETE FROM " . PRIVMSGS_TEXT_TABLE . "
						WHERE privmsgs_text_id = $old_privmsgs_id";
					if ( !$db->sql_query($sql) )
					{
						message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs text (inbox)', '', __LINE__, __FILE__, $sql);
					}
				}
			}

			$sql_info = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip, privmsgs_enable_bbcode, privmsgs_enable_smilies)
				VALUES (" . PRIVMSGS_NEW_MAIL . ", " . $db->check_value($privmsg_subject) . ", " . $userdata['user_id'] . ", " . $to_userdata['user_id'] . ", $msg_time, '". USER_IP ."', $bbcode_on, $smilies_on)";
		}
		else
		{
			$sql_info = "UPDATE " . PRIVMSGS_TABLE . "
				SET privmsgs_type = " . PRIVMSGS_NEW_MAIL . ", privmsgs_subject = " . $db->check_value($privmsg_subject) . ", privmsgs_from_userid = " . $userdata['user_id'] . ", privmsgs_to_userid = " . $to_userdata['user_id'] . ", privmsgs_date = $msg_time, privmsgs_ip = '". USER_IP ."', privmsgs_enable_bbcode = $bbcode_on, privmsgs_enable_smilies = $smilies_on
				WHERE privmsgs_id = $privmsg_id";
		}

		if ( !($result = $db->sql_query($sql_info)) )
		{
			message_die(GENERAL_ERROR, "Could not insert/update private message sent info.", "", __LINE__, __FILE__, $sql_info);
		}

		if ( $mode != 'edit' )
		{
			$privmsg_sent_id = $db->sql_nextid();

			$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_bbcode_uid, privmsgs_text)
				VALUES ($privmsg_sent_id, '" . $bbcode_uid . "', " . $db->check_value($privmsg_message) . ")";
		}
		else
		{
			$sql = "UPDATE " . PRIVMSGS_TEXT_TABLE . "
				SET privmsgs_text = " . $db->check_value($privmsg_message) . ", privmsgs_bbcode_uid = '$bbcode_uid'
				WHERE privmsgs_text_id = $privmsg_id";
		}

		if ( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Could not insert/update private message sent text.", "", __LINE__, __FILE__, $sql_info);
		}

		if ( $mode != 'edit' )
		{
			$timenow = TIMENOW;
			//
			// Add to the users new pm counter
			//
			$sql = "UPDATE ". USERS_TABLE ." SET
					user_new_privmsg = user_new_privmsg + 1,
					user_last_privmsg = $timenow,
					user_newest_pm_id = $privmsg_sent_id
				WHERE user_id = {$to_userdata['user_id']}
				LIMIT 1";

			if ( !$status = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not update private message new/read status for user', '', __LINE__, __FILE__, $sql);
			}

			if ( $to_userdata['user_notify_pm'] && !empty($to_userdata['user_email']) && $to_userdata['user_active'] && $bb_cfg['pm_notify_enabled'] )
			{
				$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($bb_cfg['script_path']));
				$script_name = ( $script_name != '' ) ? $script_name . '/privmsg.'.$phpEx : 'privmsg.'.$phpEx;
				$server_name = trim($bb_cfg['server_name']);
				$server_protocol = ( $bb_cfg['cookie_secure'] ) ? 'https://' : 'http://';
				$server_port = ( $bb_cfg['server_port'] <> 80 ) ? ':' . trim($bb_cfg['server_port']) . '/' : '/';

				include($phpbb_root_path . 'includes/emailer.'.$phpEx);
				$emailer = new emailer($bb_cfg['smtp_delivery']);

				$emailer->from($bb_cfg['board_email']);
				$emailer->replyto($bb_cfg['board_email']);

				$emailer->use_template('privmsg_notify', $to_userdata['user_lang']);
				$emailer->email_address($to_userdata['user_email']);
				$emailer->set_subject($lang['Notification_subject']);

				$emailer->assign_vars(array(
					'USERNAME' => $to_username,
					'NAME_FROM' => $userdata['username'],
					'MSG_SUBJECT' => $privmsg_subject,
					'SITENAME' => $bb_cfg['sitename'],
					'EMAIL_SIG' => (!empty($bb_cfg['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']) : '',

					'U_INBOX' => $server_protocol . $server_name . $server_port . $script_name . '?folder=inbox&mode=read&p=' . $privmsg_sent_id)
				);

				$emailer->send();
				$emailer->reset();
			}
		}

		pm_message_die($lang['Message_sent']);
	}
	else if ( $preview || $refresh || $error )
	{

		//
		// If we're previewing or refreshing then obtain the data
		// passed to the script, process it a little, do some checks
		// where neccessary, etc.
		//
		$to_username = (isset($_POST['username']) ) ? trim(htmlspecialchars($_POST['username'])) : '';

		$privmsg_subject = ( isset($_POST['subject']) ) ? trim(strip_tags($_POST['subject'])) : '';
		$privmsg_message = ( isset($_POST['message']) ) ? trim($_POST['message']) : '';

		//
		// Do mode specific things
		//
		if ( $mode == 'post' )
		{
			$page_title = $lang['Post_new_pm'];
		}
		else if ( $mode == 'reply' )
		{
			$page_title = $lang['Post_reply_pm'];
		}
		else if ( $mode == 'edit' )
		{
			$page_title = $lang['Edit_pm'];

			$sql = "SELECT u.user_id
				FROM " . PRIVMSGS_TABLE . " pm, " . USERS_TABLE . " u
				WHERE pm.privmsgs_id = $privmsg_id
					AND u.user_id = pm.privmsgs_from_userid";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, "Could not obtain post and post text", "", __LINE__, __FILE__, $sql);
			}

			if ( $postrow = $db->sql_fetchrow($result) )
			{
				if ( $userdata['user_id'] != $postrow['user_id'] )
				{
					message_die(GENERAL_MESSAGE, $lang['Edit_own_posts']);
				}
			}
		}
	}
	else
	{
		if ( !$privmsg_id && ( $mode == 'reply' || $mode == 'edit' || $mode == 'quote' ) )
		{
			message_die(GENERAL_ERROR, $lang['No_post_id']);
		}

		if ( !empty($_GET[POST_USERS_URL]) )
		{
			$user_id = intval($_GET[POST_USERS_URL]);

			$sql = "SELECT username
				FROM " . USERS_TABLE . "
				WHERE user_id = $user_id
					AND user_id <> " . ANONYMOUS;
			if ( !($result = $db->sql_query($sql)) )
			{
				$error = TRUE;
				$error_msg = $lang['No_such_user'];
			}

			if ( $row = $db->sql_fetchrow($result) )
			{
				$to_username = $row['username'];
			}
		}

		else if ( $mode == 'edit' )
		{
			$sql = "SELECT pm.*, pmt.privmsgs_bbcode_uid, pmt.privmsgs_text, u.username, u.user_id
				FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u
				WHERE pm.privmsgs_id = $privmsg_id
					AND pmt.privmsgs_text_id = pm.privmsgs_id
					AND pm.privmsgs_from_userid = " . $userdata['user_id'] . "
					AND ( pm.privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
						OR pm.privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )
					AND u.user_id = pm.privmsgs_to_userid";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain private message for editing', '', __LINE__, __FILE__, $sql);
			}

			if ( !($privmsg = $db->sql_fetchrow($result)) )
			{
				redirect(append_sid("privmsg.$phpEx?folder=$folder", true));
			}

			$privmsg_subject = $privmsg['privmsgs_subject'];
			$privmsg_message = $privmsg['privmsgs_text'];
			$privmsg_bbcode_uid = $privmsg['privmsgs_bbcode_uid'];
			$privmsg_bbcode_enabled = ($privmsg['privmsgs_enable_bbcode'] == 1);

			if ( $privmsg_bbcode_enabled )
			{
				$privmsg_message = preg_replace("/\:(([a-z0-9]:)?)$privmsg_bbcode_uid/si", '', $privmsg_message);
			}

			$privmsg_message = str_replace('<br />', "\n", $privmsg_message);

			$to_username = $privmsg['username'];
			$to_userid = $privmsg['user_id'];

		}
		else if ( $mode == 'reply' || $mode == 'quote' )
		{

			$sql = "SELECT pm.privmsgs_subject, pm.privmsgs_date, pmt.privmsgs_bbcode_uid, pmt.privmsgs_text, u.username, u.user_id
				FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u
				WHERE pm.privmsgs_id = $privmsg_id
					AND pmt.privmsgs_text_id = pm.privmsgs_id
					AND pm.privmsgs_to_userid = " . $userdata['user_id'] . "
					AND u.user_id = pm.privmsgs_from_userid";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain private message for editing', '', __LINE__, __FILE__, $sql);
			}

			if ( !($privmsg = $db->sql_fetchrow($result)) )
			{
				redirect(append_sid("privmsg.$phpEx?folder=$folder", true));
			}

			$privmsg_subject = ( ( !preg_match('/^Re:/', $privmsg['privmsgs_subject']) ) ? 'Re: ' : '' ) . $privmsg['privmsgs_subject'];

			$to_username = $privmsg['username'];
			$to_userid = $privmsg['user_id'];

			if ( $mode == 'quote' )
			{
				$privmsg_message = $privmsg['privmsgs_text'];
				$privmsg_bbcode_uid = $privmsg['privmsgs_bbcode_uid'];

				$privmsg_message = preg_replace("/\:(([a-z0-9]:)?)$privmsg_bbcode_uid/si", '', $privmsg_message);
				$privmsg_message = str_replace('<br />', "\n", $privmsg_message);

				$msg_date =  create_date($bb_cfg['default_dateformat'], $privmsg['privmsgs_date'], $bb_cfg['board_timezone']);

				$privmsg_message = '[quote="' . $to_username . '"]' . $privmsg_message . '[/quote]';

				$mode = 'reply';
			}
		}
		else
		{
			$privmsg_subject = $privmsg_message = $to_username = '';
		}
	}

	//
	// Has admin prevented user from sending PM's?
	//
	if ( !$userdata['user_allow_pm'] && $mode != 'edit' )
	{
		$message = $lang['Cannot_send_privmsg'];
		message_die(GENERAL_MESSAGE, $message);
	}

	//
	// Start output, first preview, then errors then post form
	//
	$page_title = $lang['Send_private_message'];

	if ( $preview && !$error )
	{
		$orig_word = array();
		$replacement_word = array();
		obtain_word_list($orig_word, $replacement_word);

		$bbcode_uid = ($bbcode_on) ? make_bbcode_uid() : '';

		$preview_message = prepare_message($privmsg_message, $bbcode_on, $smilies_on, $bbcode_uid);
		$privmsg_message = preg_replace($html_entities_match, $html_entities_replace, $privmsg_message);

		//
		// Finalise processing as per viewtopic
		//
		if ( $bbcode_on )
		{
			$preview_message = bbencode_second_pass($preview_message, $bbcode_uid);
		}

		if ( count($orig_word) )
		{
			$preview_subject = preg_replace($orig_word, $replacement_word, $privmsg_subject);
			$preview_message = preg_replace($orig_word, $replacement_word, $preview_message);
		}
		else
		{
			$preview_subject = $privmsg_subject;
		}

		if ( $smilies_on )
		{
			$preview_message = smilies_pass($preview_message);
		}

		$preview_message = make_clickable($preview_message);
		$preview_message = str_replace("\n", '<br />', $preview_message);

		$s_hidden_fields = '<input type="hidden" name="folder" value="' . $folder . '" />';
		$s_hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';

		if ( isset($privmsg_id) )
		{
			$s_hidden_fields .= '<input type="hidden" name="' . POST_POST_URL . '" value="' . $privmsg_id . '" />';
		}

		$template->assign_vars(array(
			'TPL_PREVIEW_POST' => true,
			'TOPIC_TITLE' => wbr($preview_subject),
			'POST_SUBJECT' => $preview_subject,
			'MESSAGE_TO' => $to_username,
			'MESSAGE_FROM' => $userdata['username'],
			'POST_DATE' => create_date($bb_cfg['default_dateformat'], time(), $bb_cfg['board_timezone']),
			'PREVIEW_MSG' => $preview_message,

			'S_HIDDEN_FIELDS' => $s_hidden_fields,

			'L_SUBJECT' => $lang['Subject'],
			'L_DATE' => $lang['Date'],
		));
	}

	//
	// Start error handling
	//
	if ($error)
	{
		$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
	}

	//
	// Load templates
	//
	$template->set_filenames(array(
		'body' => 'posting.tpl')
	);

	//
	// Enable extensions in posting_body
	//
	$template->assign_block_vars('switch_privmsg', array());
	$template->assign_var('POSTING_USERNAME');

	//
	// BBCode toggle selection
	//
	if ( $bb_cfg['allow_bbcode'] )
	{
		$bbcode_status = $lang['BBCode_is_ON'];
		$template->assign_block_vars('switch_bbcode_checkbox', array());
	}
	else
	{
		$bbcode_status = $lang['BBCode_is_OFF'];
	}

	//
	// Smilies toggle selection
	//
	if ( $bb_cfg['allow_smilies'] )
	{
		$smilies_status = $lang['Smilies_are_ON'];
		$template->assign_block_vars('switch_smilies_checkbox', array());
	}
	else
	{
		$smilies_status = $lang['Smilies_are_OFF'];
	}

	$post_a = '&nbsp;';
	if ( $mode == 'post' )
	{
		$post_a = $lang['Send_a_new_message'];
	}
	else if ( $mode == 'reply' )
	{
		$post_a = $lang['Send_a_reply'];
		$mode = 'post';
	}
	else if ( $mode == 'edit' )
	{
		$post_a = $lang['Edit_message'];
	}

	$s_hidden_fields = '<input type="hidden" name="folder" value="' . $folder . '" />';
	$s_hidden_fields .= '<input type="hidden" name="mode" value="' . $mode . '" />';
	if ( $mode == 'edit' )
	{
		$s_hidden_fields .= '<input type="hidden" name="' . POST_POST_URL . '" value="' . $privmsg_id . '" />';
	}

	//
	// Send smilies to template
	//
	generate_smilies('inline');

	$privmsg_subject = preg_replace($html_entities_match, $html_entities_replace, $privmsg_subject);
	$privmsg_subject = str_replace('"', '&quot;', $privmsg_subject);

	$template->assign_vars(array(
		'SUBJECT' => htmlCHR($privmsg_subject),
		'USERNAME' => $to_username,
		'MESSAGE' => $privmsg_message,
		'SMILIES_STATUS' => $smilies_status,
		'BBCODE_STATUS' => sprintf($bbcode_status, '<a href="' . append_sid("faq.$phpEx?mode=bbcode") . '" target="_phpbbcode">', '</a>'),
		'FORUM_NAME' => $lang['PRIVATE_MESSAGE'],

		'BOX_NAME' => $l_box_name,
		'INBOX' => $inbox_url,
		'SENTBOX' => $sentbox_url,
		'OUTBOX' => $outbox_url,
		'SAVEBOX' => $savebox_url,

		'L_SUBJECT' => $lang['Subject'],
		'L_MESSAGE_BODY' => $lang['Message_body'],
		'L_SPELLCHECK' => $lang['Spellcheck'],
		'POSTING_TYPE_TITLE' => $post_a,
		'L_FIND' => $lang['Find'],
		'L_DISABLE_BBCODE' => $lang['Disable_BBCode_pm'],
		'L_DISABLE_SMILIES' => $lang['Disable_Smilies_pm'],

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

		'S_BBCODE_CHECKED' => ( !$bbcode_on ) ? ' checked="checked"' : '',
		'S_SMILIES_CHECKED' => ( !$smilies_on ) ? ' checked="checked"' : '',
		'S_HIDDEN_FORM_FIELDS' => $s_hidden_fields,
		'S_POST_ACTION' => append_sid("privmsg.$phpEx"),

		'U_SEARCH_USER' => append_sid("search.$phpEx?mode=searchuser"),
		'U_VIEW_FORUM' => append_sid("privmsg.$phpEx"))
	);

	// Output the data to the template (for MAIL.RU Keyboard)
	$template->assign_vars(array(
		'SHOW_VIRTUAL_KEYBOARD' => $bb_cfg['show_virtual_keyboard'],
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
}
else
{
	//
	// Default page
	//

	//
	// Reset PM counters
	//
	$userdata['user_new_privmsg'] = 0;
	$userdata['user_unread_privmsg'] = $userdata['user_new_privmsg'] + $userdata['user_unread_privmsg'];
	$userdata['user_last_privmsg'] = $userdata['session_start'];

	//
	// Update unread status
	//
	db_update_userdata($userdata, array(
		'user_unread_privmsg' => 'user_unread_privmsg + user_new_privmsg',
		'user_new_privmsg'    => 0,
		'user_last_privmsg'   => $userdata['session_start'],
	));

	$sql = "UPDATE " . PRIVMSGS_TABLE . "
		SET privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . "
		WHERE privmsgs_type = " . PRIVMSGS_NEW_MAIL . "
			AND privmsgs_to_userid = " . $userdata['user_id'];
	if ( !$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update private message new/read status (2) for user', '', __LINE__, __FILE__, $sql);
	}

	//
	// Generate page
	//
	$page_title = $lang['Private_Messaging'];

	//
	// Load templates
	//
	$template->set_filenames(array(
		'body' => 'privmsgs.tpl')
	);

	$orig_word = array();
	$replacement_word = array();
	obtain_word_list($orig_word, $replacement_word);

	//
	// New message
	//
	$post_new_mesg_url = '<a href="' . append_sid("privmsg.$phpEx?mode=post") . '"><img src="' . $images['post_new'] . '" alt="' . $lang['Send_a_new_message'] . '" border="0" /></a>';

	//
	// General SQL to obtain messages
	//
	$sql_tot = "SELECT COUNT(privmsgs_id) AS total
		FROM " . PRIVMSGS_TABLE . " ";
	$sql = "SELECT pm.privmsgs_type, pm.privmsgs_id, pm.privmsgs_date, pm.privmsgs_subject, u.user_id, u.username
		FROM " . PRIVMSGS_TABLE . " pm, " . USERS_TABLE . " u ";
	switch( $folder )
	{
		case 'inbox':
			$sql_tot .= "WHERE privmsgs_to_userid = " . $userdata['user_id'] . "
				AND ( privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
					OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "
					OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";

			$sql .= "WHERE pm.privmsgs_to_userid = " . $userdata['user_id'] . "
				AND u.user_id = pm.privmsgs_from_userid
				AND ( pm.privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
					OR pm.privmsgs_type = " . PRIVMSGS_READ_MAIL . "
					OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
			break;

		case 'outbox':
			$sql_tot .= "WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
				AND ( privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
					OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";

			$sql .= "WHERE pm.privmsgs_from_userid = " . $userdata['user_id'] . "
				AND u.user_id = pm.privmsgs_to_userid
				AND ( pm.privmsgs_type =  " . PRIVMSGS_NEW_MAIL . "
					OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " )";
			break;

		case 'sentbox':
			$sql_tot .= "WHERE privmsgs_from_userid = " . $userdata['user_id'] . "
				AND privmsgs_type =  " . PRIVMSGS_SENT_MAIL;

			$sql .= "WHERE pm.privmsgs_from_userid = " . $userdata['user_id'] . "
				AND u.user_id = pm.privmsgs_to_userid
				AND pm.privmsgs_type =  " . PRIVMSGS_SENT_MAIL;
			break;

		case 'savebox':
			$sql_tot .= "WHERE ( ( privmsgs_to_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
				OR ( privmsgs_from_userid = " . $userdata['user_id'] . "
					AND privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . ") )";

			$sql .= "WHERE u.user_id = pm.privmsgs_from_userid
				AND ( ( pm.privmsgs_to_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_IN_MAIL . " )
				OR ( pm.privmsgs_from_userid = " . $userdata['user_id'] . "
					AND pm.privmsgs_type = " . PRIVMSGS_SAVED_OUT_MAIL . " ) )";
			break;

		default:
			message_die(GENERAL_MESSAGE, $lang['No_such_folder']);
			break;
	}

	//
	// Show messages over previous x days/months
	//
	if ( $submit_msgdays && ( !empty($_POST['msgdays']) || !empty($_GET['msgdays']) ) )
	{
		$msg_days = ( !empty($_POST['msgdays']) ) ? intval($_POST['msgdays']) : intval($_GET['msgdays']);
		$min_msg_time = time() - ($msg_days * 86400);

		$limit_msg_time_total = " AND privmsgs_date > $min_msg_time";
		$limit_msg_time = " AND pm.privmsgs_date > $min_msg_time ";

		if ( !empty($_POST['msgdays']) )
		{
			$start = 0;
		}
	}
	else
	{
		$limit_msg_time = $limit_msg_time_total = '';
		$msg_days = 0;
	}

	$sql .= $limit_msg_time . " ORDER BY pm.privmsgs_date DESC LIMIT $start, " . $bb_cfg['topics_per_page'];
	$sql_all_tot = $sql_tot;
	$sql_tot .= $limit_msg_time_total;

	//
	// Get messages
	//
	if ( !($result = $db->sql_query($sql_tot)) )
	{
		message_die(GENERAL_ERROR, 'Could not query private message information', '', __LINE__, __FILE__, $sql_tot);
	}

	$pm_total = ( $row = $db->sql_fetchrow($result) ) ? $row['total'] : 0;

	if ( !($result = $db->sql_query($sql_all_tot)) )
	{
		message_die(GENERAL_ERROR, 'Could not query private message information', '', __LINE__, __FILE__, $sql_tot);
	}

	$pm_all_total = ( $row = $db->sql_fetchrow($result) ) ? $row['total'] : 0;

	//
	// Build select box
	//
	$previous_days = array(0, 1, 7, 14, 30, 90, 180, 364);
	$previous_days_text = array($lang['All_Posts'], $lang['1_Day'], $lang['7_Days'], $lang['2_Weeks'], $lang['1_Month'], $lang['3_Months'], $lang['6_Months'], $lang['1_Year']);

	$select_msg_days = '';
	for($i = 0; $i < count($previous_days); $i++)
	{
		$selected = ( $msg_days == $previous_days[$i] ) ? ' selected="selected"' : '';
		$select_msg_days .= '<option value="' . $previous_days[$i] . '"' . $selected . '>' . $previous_days_text[$i] . '</option>';
	}

	//
	// Define correct icons
	//
	switch ( $folder )
	{
		case 'inbox':
			$l_box_name = $lang['Inbox'];
			break;
		case 'outbox':
			$l_box_name = $lang['Outbox'];
			break;
		case 'savebox':
			$l_box_name = $lang['Savebox'];
			break;
		case 'sentbox':
			$l_box_name = $lang['Sentbox'];
			break;
	}
	$post_pm = append_sid("privmsg.$phpEx?mode=post");
	$post_pm_img = '<a href="' . $post_pm . '"><img src="' . $images['pm_postmsg'] . '" alt="' . $lang['Post_new_pm'] . '" border="0" /></a>';
	$post_pm = '<a href="' . $post_pm . '">' . $lang['Post_new_pm'] . '</a>';

	//
	// Output data for inbox status
	//
	$box_limit_img_length = $box_limit_percent = $l_box_size_status = '';
	$max_pm = ($folder != 'outbox') ? $bb_cfg["max_{$folder}_privmsgs"] : null;

	if ($max_pm)
	{
		$box_limit_percent    = min(round(($pm_all_total / $max_pm) * 100), 100);
		$box_limit_img_length = min(round(($pm_all_total / $max_pm) * $bb_cfg['privmsg_graphic_length']), $bb_cfg['privmsg_graphic_length']);
		$box_limit_remain     = max(($max_pm - $pm_all_total), 0);

		$template->assign_var('PM_BOX_SIZE_INFO');

		switch( $folder )
		{
			case 'inbox':
				$l_box_size_status = sprintf($lang['Inbox_size'], $box_limit_percent);
				break;
			case 'sentbox':
				$l_box_size_status = sprintf($lang['Sentbox_size'], $box_limit_percent);
				break;
			case 'savebox':
				$l_box_size_status = sprintf($lang['Savebox_size'], $box_limit_percent);
				break;
			default:
				$l_box_size_status = '';
				break;
		}
	}

	//
	// Dump vars to template
	//
	$template->assign_vars(array(
		'BOX_NAME' => $l_box_name,
		'BOX_EXPL' => ($folder == 'outbox') ? $lang['Outbox_expl'] : '',
		'INBOX' => $inbox_url,
		'SENTBOX' => $sentbox_url,
		'OUTBOX' => $outbox_url,
		'SAVEBOX' => $savebox_url,

		'POST_PM_IMG' => $post_pm_img,
		'POST_PM' => $post_pm,

		'INBOX_LIMIT_IMG_WIDTH' => max(4, $box_limit_img_length),
		'INBOX_LIMIT_PERCENT' => $box_limit_percent,

		'BOX_SIZE_STATUS' => ($l_box_size_status) ? $l_box_size_status : '',

		'L_INBOX' => $lang['Inbox'],
		'L_OUTBOX' => $lang['Outbox'],
		'L_SENTBOX' => $lang['Sent'],
		'L_SAVEBOX' => $lang['Saved'],
		'L_MARK' => $lang['Mark'],
		'L_FLAG' => $lang['Flag'],
		'L_SUBJECT' => $lang['Subject'],
		'L_DATE' => $lang['Date'],
		'L_DISPLAY_MESSAGES' => $lang['Display_messages'],
		'L_FROM_OR_TO' => ( $folder == 'inbox' || $folder == 'savebox' ) ? $lang['FROM'] : $lang['TO'],
		'L_MARK_ALL' => $lang['Mark_all'],
		'L_UNMARK_ALL' => $lang['Unmark_all'],
		'L_DELETE_MARKED' => $lang['Delete_marked'],
		'L_DELETE_ALL' => $lang['Delete_all'],
		'L_SAVE_MARKED' => $lang['Save_marked'],

		'S_PRIVMSGS_ACTION' => append_sid("privmsg.$phpEx?folder=$folder"),
		'S_HIDDEN_FIELDS' => '',
		'S_POST_NEW_MSG' => $post_new_mesg_url,
		'S_SELECT_MSG_DAYS' => $select_msg_days,

		'U_POST_NEW_TOPIC' => append_sid("privmsg.$phpEx?mode=post"))
	);

	//
	// Okay, let's build the correct folder
	//
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query private messages', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$privmsg_id = $row['privmsgs_id'];

			$flag = $row['privmsgs_type'];

			$icon_flag = ( $flag == PRIVMSGS_NEW_MAIL || $flag == PRIVMSGS_UNREAD_MAIL ) ? $images['pm_unreadmsg'] : $images['pm_readmsg'];
			$icon_flag_alt = ( $flag == PRIVMSGS_NEW_MAIL || $flag == PRIVMSGS_UNREAD_MAIL ) ? $lang['Unread_message'] : $lang['Read_message'];

			$msg_userid = $row['user_id'];
			$msg_username = $row['username'];

			$u_from_user_profile = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$msg_userid");

			$msg_subject = $row['privmsgs_subject'];

			if ( count($orig_word) )
			{
				$msg_subject = preg_replace($orig_word, $replacement_word, $msg_subject);
			}

			$u_subject = append_sid("privmsg.$phpEx?folder=$folder&amp;mode=read&amp;" . POST_POST_URL . "=$privmsg_id");

			$msg_date = create_date($bb_cfg['default_dateformat'], $row['privmsgs_date'], $bb_cfg['board_timezone']);

			if ( $flag == PRIVMSGS_NEW_MAIL && $folder == 'inbox' )
			{
				$msg_subject = '<b>' . $msg_subject . '</b>';
				$msg_date = '<b>' . $msg_date . '</b>';
				$msg_username = '<b>' . $msg_username . '</b>';
			}

			$row_class = !($i & 1) ? 'prow1' : 'prow2';
			$i++;

			$template->assign_block_vars('listrow', array(
				'ROW_CLASS' => $row_class,
				'FROM' => $msg_username,
				'SUBJECT' => htmlCHR($msg_subject),
				'DATE' => $msg_date,

				'PRIVMSG_FOLDER_IMG' => $icon_flag,

				'L_PRIVMSG_FOLDER_ALT' => $icon_flag_alt,

				'S_MARK_ID' => $privmsg_id,

				'U_READ' => $u_subject,
				'U_FROM_USER_PROFILE' => $u_from_user_profile)
			);
		}
		while( $row = $db->sql_fetchrow($result) );

		$template->assign_vars(array(
			'PAGINATION' => generate_pagination("privmsg.$phpEx?folder=$folder", $pm_total, $bb_cfg['topics_per_page'], $start),
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $bb_cfg['topics_per_page'] ) + 1 ), ceil( $pm_total / $bb_cfg['topics_per_page'] )),
		));

	}
	else
	{
		$template->assign_vars(array(
			'L_NO_MESSAGES' => $lang['No_messages_folder'])
		);

		$template->assign_block_vars("switch_no_messages", array() );
	}
}

$template->assign_vars(array('PAGE_TITLE' => @$page_title));

require(PAGE_HEADER);

$template->pparse('body');

require(PAGE_FOOTER);

//
// Functions
//
function pm_message_die ($msg)
{
	global $lang, $phpEx;

	$msg .= '<br /><br />';
	$msg .= sprintf($lang['Click_return_inbox'], '<a href="'."privmsg.$phpEx?folder=inbox".'">', '</a> ');
	$msg .= sprintf($lang['Click_return_sentbox'], '<a href="'."privmsg.$phpEx?folder=sentbox".'">', '</a> ');
	$msg .= sprintf($lang['Click_return_outbox'], '<a href="'."privmsg.$phpEx?folder=outbox".'">', '</a> ');
	$msg .= sprintf($lang['Click_return_savebox'], '<a href="'."privmsg.$phpEx?folder=savebox".'">', '</a> ');
	$msg .= '<br /><br />';
	$msg .= sprintf($lang['Click_return_index'], '<a href="'."index.$phpEx".'">', '</a>');

	message_die(GENERAL_MESSAGE, $msg);
}

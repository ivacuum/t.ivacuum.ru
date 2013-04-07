<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

require('common.php');

$forum_id  = (@$_REQUEST[POST_FORUM_URL]) ? (int) $_REQUEST[POST_FORUM_URL] : 0;
$topic_id  = (@$_REQUEST[POST_TOPIC_URL]) ? (int) $_REQUEST[POST_TOPIC_URL] : 0;
$mode      = (@$_REQUEST['mode']) ? (string) $_REQUEST['mode'] : '';
$confirmed = isset($_POST['confirm']);

// Get new DL-status
if ($mode == 'set_dl_status' || $mode == 'set_topics_dl_status')
{
	if (isset($_POST['dl_set_will']))
	{
		$new_dl_status = DL_STATUS_WILL;
		$dl_key = 'dlw';
	}
	else if (isset($_POST['dl_set_down']))
	{
		$new_dl_status = DL_STATUS_DOWN;
		$dl_key = 'dld';
	}
	else if (isset($_POST['dl_set_complete']))
	{
		$new_dl_status = DL_STATUS_COMPLETE;
		$dl_key = 'dlc';
	}
	else if (isset($_POST['dl_set_cancel']))
	{
		$new_dl_status = DL_STATUS_CANCEL;
		$dl_key = 'dla';
	}
	else
	{
		message_die(GENERAL_ERROR, 'Invalid download status');
	}
}

// Define redirect URL
$full_url = (@$_POST['full_url']) ? str_replace('&amp;', '&', htmlspecialchars($_POST['full_url'])) : '';

if (@$_POST['redirect_type'] == 'search')
{
	$redirect_type = "search.php";
	$redirect = ($full_url) ? $full_url : "$dl_key=1";
}
else
{
	$redirect_type = (!$topic_id) ? "viewforum.php" : "viewtopic.php";
	$redirect = ($full_url) ? $full_url : ((!$topic_id) ? POST_FORUM_URL ."=$forum_id" : POST_TOPIC_URL ."=$topic_id");
}

// Start session management
$user->session_start();

// Check if user logged in
if (!$userdata['session_logged_in'])
{
	redirect("login.php?redirect=$redirect_type&$redirect");
}

if ($bb_cfg['bt_min_ratio_dl_button'] && $btu = get_bt_userdata($user->id))
{
	if (($user_ratio = get_bt_ratio($btu)) < $bb_cfg['bt_min_ratio_dl_button'])
	{
		bb_die($lang['Bt_Low_ratio_func']);
	}
}

// Check if user did not confirm
if (@$_POST['cancel'])
{
	redirect("$redirect_type?$redirect");
}

//
// Delete DL-list
//
if ($mode == 'dl_delete' && $topic_id)
{
	if (!IS_ADMIN)
	{
		$sql = "SELECT forum_id
			FROM bb_topics
				WHERE topic_id = $topic_id
			LIMIT 1";

		if (!$row = $db->sql_fetchrow($db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not obtain forum_id for this topic', '', __LINE__, __FILE__, $sql);
		}

		$is_auth = auth(AUTH_ALL, $row['forum_id'], $userdata);

		if (!$is_auth['auth_mod'])
		{
			message_die(GENERAL_MESSAGE, $lang['Not_Moderator'], $lang['Not_Authorised']);
		}
	}

	if (!$confirmed)
	{
		$hidden_fields = array(
			't'    => $topic_id,
			'mode' => 'dl_delete',
		);

		print_confirmation(array(
			'QUESTION'      => $lang['DL_List_Del_Confirm'],
			'FORM_ACTION'   => "dl_list.php",
			'HIDDEN_FIELDS' => build_hidden_fields($hidden_fields),
		));
	}

	clear_dl_list($topic_id);
	redirect("$redirect_type?$redirect");
}

//
// Update DL status
//

$req_topics_ary = $topics_ary = array();

// Get topics selected by user
if ($mode == 'set_topics_dl_status')
{
	if (!isset($_POST['dl_topics_id_list']) || !is_array($_POST['dl_topics_id_list']))
	{
		message_die(GENERAL_MESSAGE, $lang['None_selected']);
	}

	foreach ($_POST['dl_topics_id_list'] as $topic_id)
	{
		$req_topics_ary[] = (int) $topic_id;
	}
}
else if ($mode == 'set_dl_status')
{
	$req_topics_ary[] = (int) $topic_id;
}

// Get existing topics
if ($req_topics_sql = join(',', $req_topics_ary))
{
	$sql = "SELECT topic_id FROM bb_topics WHERE topic_id IN($req_topics_sql)";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$topics_ary[] = $row['topic_id'];
	}
}

if ($topics_ary && ($mode == 'set_dl_status' || $mode == 'set_topics_dl_status'))
{
	$new_dlstatus_ary = array();

	foreach ($topics_ary as $topic_id)
	{
		$new_dlstatus_ary[] = array(
			'user_id'     => (int) $user->id,
			'topic_id'    => (int) $topic_id,
			'user_status' => (int) $new_dl_status,
		);
	}
	$new_dlstatus_sql = $db->build_array('MULTI_INSERT', $new_dlstatus_ary);

	$db->query("REPLACE INTO bb_bt_dlstatus_main $new_dlstatus_sql");

	/*$db->query("
		DELETE FROM bb_bt_dlstatus_main
		WHERE user_id = {$user->id}
			AND topic_id IN(". join(',', $topics_ary) .")
	");*/

	redirect("$redirect_type?$redirect");
}

redirect("/");

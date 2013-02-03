<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_PHPBB', true);
$t_root_path = __DIR__ . '/';
require($t_root_path . 'common.php');
require($t_root_path . 'attach_mod/attachment_mod.php');
require($t_root_path . 'includes/functions_torrent.php');

// Start session management
$user->session_start();

// Check if user logged in
if (!$userdata['session_logged_in'])
{
	redirect(append_sid("login.php?redirect=index.php", true));
}

$sid = request_var('sid', '');
$confirm = isset($_POST['confirm']);

// Set received variables
// Strings
$input_vars_str  = array(
	'mode' => 'mode'
);

// Numeric
$input_vars_num = array(
	'attach_id' => 'id',
	'req_uid' => 'u'
);

// Strings
foreach ($input_vars_str as $var => $param)
{
	$$var = (isset($_REQUEST[$param])) ? $_REQUEST[$param] : '';
}
// Numeric
foreach ($input_vars_num as $var => $param)
{
	$$var = (isset($_REQUEST[$param])) ? intval($_REQUEST[$param]) : '';
}

if (($mode == 'reg' || $mode == 'unreg' || !empty($_POST['tor_action'])) && !$attach_id)
{
	message_die(GENERAL_ERROR, 'Invalid attach_id');
}

// Show users torrent-profile
if ($mode == 'userprofile')
{
	redirect(append_sid("profile.php?mode=viewprofile&u=$req_uid"), true);
}

// check SID
if ($sid == '' || $sid !== $userdata['session_id'])
{
//message_die(GENERAL_ERROR, 'Invalid_session');
}

// Register torrent on tracker
if ($mode == 'reg')
{
	tracker_register($attach_id, 'request');
	exit;
}

// Unregister torrent from tracker
if ($mode == 'unreg')
{
	tracker_unregister($attach_id, 'request');
	exit;
}

if (!empty($_POST['tor_action']) && $confirm)
{
	// Delete torrent
	if ($_POST['tor_action'] === 'del_torrent')
	{
		delete_torrent($attach_id, 'request');
		redirect("viewtopic.php?t=$topic_id");
	}
	// Delete torrent and move topic
	if ($_POST['tor_action'] === 'del_torrent_move_topic')
	{
		delete_torrent($attach_id, 'request');
		redirect("modcp.php?t=$topic_id&mode=move&sid={$userdata['session_id']}");
	}
	// Freeze/Unfreeze torrent
	if ($_POST['tor_action'] === 'freeze' || $_POST['tor_action'] === 'unfreeze')
	{
		$new_tor_status = ($_POST['tor_action'] === 'freeze') ? TOR_STATUS_FROZEN : TOR_STATUS_NORMAL;
		change_tor_status($attach_id, $new_tor_status);
		redirect("viewtopic.php?t=$topic_id");
	}
}

// Generate passkey
if ($mode == 'gen_passkey')
{
	if (($req_uid == $user->id || IS_ADMIN) && $sid === $userdata['session_id'])
	{
		$force_generate = (IS_ADMIN);

		if (!generate_passkey($req_uid, $force_generate))
		{
			message_die(GENERAL_ERROR, 'Could not insert passkey', '', __LINE__, __FILE__, $sql);
		}
		tracker_rm_user($req_uid);
		message_die(GENERAL_MESSAGE, $lang['Bt_Gen_Passkey_OK']);
	}
	else
	{
		message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
	}
}

message_die(GENERAL_ERROR, 'Not confirmed or invalid mode');


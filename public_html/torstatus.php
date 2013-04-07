<?php

include('common.php');
require(SITE_DIR . 'attach_mod/attachment_mod.php');
require(SITE_DIR . 'includes/functions_torrent.php');

// Start session management
$user->session_start();

// Check if user logged in
if (!$userdata['session_logged_in'])
{
	redirect(append_sid('login.php?redirect=/', true));
}

$sid = request_var('sid', '');
$confirm = isset($_POST['status_confirm']);

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

if (!empty($_POST['tor_status']) && $confirm)
{
		$new_tor_status = $_POST['tor_status'];
		change_tor_status($attach_id, $new_tor_status);
		$sql = "update bb_bt_torrents set checked_user_id=". $userdata['user_id'] .", checked_time=". time() ." WHERE attach_id=". $attach_id;
		$db->sql_query($sql);
		redirect("viewtopic.php?t=$topic_id");


//end torrent status mod


//$userdata = session_pagestart($user_ip, PAGE_INDEX);
//init_userprefs($userdata);


//$attach_id = $_GET['a'];
/*
if( $userdata['user_id'] != ANONYMOUS && is_numeric($attach_id) ) {

  $sql = 'SELECT p.forum_id
	FROM bb_attachments a join bb_posts p on a.post_id=p.post_id
	WHERE a.attach_id='. $attach_id;

  if( $result = $db->sql_query($sql) ) {

    $row = $db->sql_fetchrow($result);
    $forum_id = $row['forum_id'];
    $is_auth = array();
    $is_auth = auth(AUTH_ALL, $forum_id, $userdata);
    if( $is_auth['auth_mod'] ) {

	$sql = "update bb_bt_torrents set checked_user_id=". $userdata['user_id'] .", checked_time=". time()
		." WHERE attach_id=". $attach_id;

	if( $db->sql_query($sql) ) {
		echo 'var vb=document.getElementById("VA'. $attach_id .'");vb.innerHTML="Одобрено";';
	} else {
#		echo "alert('SQL Update Error');";
	}

    } else {
	echo "alert('Unauthorized');";
    }

  } else {
#	echo "alert('SQL Forum_ID Error');";
  }
}*/
}

<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

require 'common.php';

// Init userdata
$user->session_start();

require SITE_DIR . 'includes/bbcode.php';
require LANG_DIR . 'lang_callseed.php';

function topic_info($topic_id)
{
	global $db;

	$query = "  SELECT tor.poster_id, tor.forum_id, tor.attach_id, t.topic_title, f.forum_name
				FROM bb_bt_torrents tor , bb_topics t, bb_forums f
				WHERE tor.topic_id = $topic_id
					AND t.topic_id = tor.topic_id
					AND f.forum_id = tor.forum_id
				LIMIT 1";
	$row = $db->fetch_row($query);

	$t = array(
		"topic_title"  => $row['topic_title'],
		"forum_title"  => $row['forum_name'],
		"attach_id"    => $row['attach_id'],
		"topic_poster" => $row['poster_id']
	);

	return $t;
}

function send_pm($topic_id, $t_info, $to_user_id)
{
	global $db, $userdata, $lang, $msg_error, $cur_time;

	$enc_ip = encode_ip($_SERVER['REMOTE_ADDR']);

	$query = "UPDATE bb_bt_torrents SET call_seed_time=". $cur_time ." WHERE topic_id=$topic_id";
	$rez_T = $db->sql_query($query);
	if ($rez_T === false) $msg_error = "TIME";

	$subj = sprintf ($lang['Callseed_subj'], $t_info['topic_title']);
	$text = sprintf ($lang['Callseed_text'], $topic_id, $t_info['forum_title'], $t_info['topic_title'], $t_info['attach_id']);
	$text = $db->escape($text);

	$query = "INSERT INTO bb_privmsgs
	(privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip) VALUES
	(" . PRIVMSGS_UNREAD_MAIL . ",'" . $subj . "'," . $userdata['user_id'] ."," . $to_user_id. "," . $cur_time . ",'" . $enc_ip . "')";
	$rez_a = $db->sql_query($query);
	if ($rez_a === false) $msg_error = "MSG";

	$id = $db->sql_nextid();

	$query = "INSERT INTO bb_privmsgs_text VALUES($id, '" . make_bbcode_uid() . "', '$text')";
	$rez_b = $db->sql_query($query);
 	if ($rez_b === false) $msg_error = "MSG_TEXT";

	$query = "UPDATE bb_users SET
		user_new_privmsg = user_new_privmsg + 1,
		user_last_privmsg = $cur_time,
		user_newest_pm_id = $id
		WHERE user_id = $to_user_id";
	$rez_c = $db->sql_query($query);
	if ($rez_c === false) $msg_error = "POPUP";
}

$u_id = array();
$topic_id = $app['request']->variable('t', 0);
$t_info = topic_info($topic_id);
$cur_time = time();

$msg_error = "OK";

$query = "SELECT call_seed_time FROM bb_bt_torrents WHERE topic_id = $topic_id LIMIT 1";
$row = $db->fetch_row($query);

if(!empty($row))
{
	$pr_time = $row['call_seed_time'];
	$pause = 86400; //1 day
	$cp = $cur_time - $pr_time;
	$pcp = $pause - $cp;
	if($cp <= $pause)
	{
		$cur_pause_hour = floor($pcp/3600);
		$cur_pause_min = floor($pcp/60)/*-($cur_pause_hour*60)*/;
		$msg_error = "SPAM";
	}
}

$query = "SELECT user_id FROM bb_bt_dlstatus_main WHERE topic_id=" . $topic_id;
/*$row = $db->fetch_rowset($query);*/

foreach($db->fetch_rowset($query) as $row)
{
	$u_id[] = $row['user_id'];
}
if (!in_array($t_info['topic_poster'], $u_id))
{
	$u_id[] = $t_info['topic_poster'];
}
array_unique($u_id);

foreach($u_id as $i=>$user_id)
{
	if ($msg_error !== "OK") break;

	send_pm($topic_id, $t_info, $user_id);
}

$msg = '';
meta_refresh_tracker(8, append_sid('viewtopic.php?t=' . $topic_id));
$return_to = sprintf ($lang['Callseed_RETURN'], $topic_id);

switch($msg_error) {
	case "OK":
		$msg .= $lang['Callseed_MSG_OK'];
		break;
	case "SPAM":
		$msg .= sprintf ($lang['Callseed_MSG_SPAM'], $cur_pause_hour, $cur_pause_min);
		break;
	case "MSG":
		$msg .= $lang['Callseed_MSG_MSG'];
		break;
	case "MSG_TEXT":
		$msg .= $lang['Callseed_MSG_MSG_TEXT'];
		break;
	case "POPUP":
		$msg .= $lang['Callseed_MSG_POPUP'];
		break;
	case "TIME":
		$msg .= $lang['Callseed_MSG_TIME'];
		break;
}
$msg .= $return_to;
message_die(GENERAL_MESSAGE, $msg);

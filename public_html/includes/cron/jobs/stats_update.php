<?php

if (!defined('SITE_DIR'))
{
	exit;
}

/**
* Определяем переменные
*/
$trend_time          = time();
$trend_peers         = 0;
$trend_posts         = 0;
$trend_users         = 0;
$trend_seeders       = 0;
$trend_leechers      = 0;
$trend_users_online  = 0;
$trend_visitors      = 0;
$trend_torrents      = 0;
$trend_torrents_size = 0;
$trend_speed         = 0;
$trend_traffic       = 0;

$trend_download             = 0;
$trend_upload               = 0;
$trend_active_torrents      = 0;
$trend_active_torrents_size = 0;

//up_down total
$sql = '
	SELECT
		ROUND(SUM(u_up_total)) AS upl,
		ROUND(SUM(u_down_total)) AS donl
	FROM
		' . BT_USERS_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_download = $row['donl'];
$trend_upload   = $row['upl'];
$trend_traffic  = $row['donl'] * 2;

// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['pumped']."' WHERE `name` = 'total_dl_ul' LIMIT 1");
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['upl']."' WHERE `name` = 'upload' LIMIT 1");
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['donl']."' WHERE `name` = 'download' LIMIT 1");
//up_down total end

//active seeders begin
$sql = '
	SELECT
		COUNT(DISTINCT user_id) AS st
	FROM
		' . BT_TRACKER_TABLE . '
	WHERE
		seeder = 1';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_seeders = $row['st'];

// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['st']."' WHERE `name` = 'active_seeders' LIMIT 1");
//active seeders end

//active leechers begin
$sql = '
	SELECT
		COUNT(DISTINCT user_id) AS lt
	FROM
		' . BT_TRACKER_TABLE . '
	WHERE
		seeder = 0';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_leechers = $row['lt'];

// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['lt']."' WHERE `name` = 'active_leechers' LIMIT 1");
//active leechers end

/* Peers */
$sql = '
	SELECT
		COUNT(*) AS peers
	FROM
		' . BT_TRACKER_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_peers = $row['peers'];

// $db->sql_query('UPDATE bb_status_update SET value = ' . $trend_peers . ' WHERE name = "peers"');

// Speed
$sql = "SELECT SUM(speed_up) as speed_up, SUM(speed_down) as speed_down FROM ". BT_TRACKER_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_speed = max($row['speed_down'], $row['speed_up']) * 2;

// $db->sql_query("UPDATE `bb_status_update` SET `value` = '" . intval($trend_speed / 2) . "' WHERE `name` = 'speed_up' LIMIT 1");
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '" . intval($trend_speed / 2) . "' WHERE `name` = 'speed_down' LIMIT 1");
// All peers end

//active torrents begin
$sql = 'SELECT COUNT(DISTINCT tor.topic_id) AS tt , SUM(tor.size) AS ts
        FROM '. BT_TRACKER_TABLE .'  tr , '. BT_TORRENTS_TABLE .' tor
        WHERE tr.topic_id = tor.topic_id';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_active_torrents      = $row['tt'];
$trend_active_torrents_size = $row['ts'];

// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['tt']."' WHERE `name` = 'active_tor' LIMIT 1");
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['ts']."' WHERE `name` = 'active_tor_size' LIMIT 1");
//active torrents end

//all torrents begin
$sql = ' SELECT COUNT(*) as tn, SUM(size) AS tos
         FROM '.BT_TORRENTS_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_torrents = $row['tn'];
$trend_torrents_size = $row['tos'];
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['tn']."' WHERE `name` = 'all_tor' LIMIT 1");
// $db->sql_query("UPDATE `bb_status_update` SET `value` = '".$row['tos']."' WHERE `name` = 'all_tor_size' LIMIT 1");
//all torrents end

$sql = '
	SELECT
		COUNT(*) as total
	FROM
		' . POSTS_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_posts = $row['total'];

$sql = '
	SELECT
		COUNT(*) as total
	FROM
		' . USERS_TABLE;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_users = $row['total'];

$sql = '
	SELECT
		COUNT(*) AS users_online
	FROM
		' . SESSIONS_TABLE . ' s,
		' . USERS_TABLE . ' u
	WHERE
		s.session_time > ' . (time() - 300) . '
	AND
		u.user_id = s.session_user_id';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_users_online = $row['users_online'];

$sql = '
	SELECT
		COUNT(*) AS visitors
	FROM
		' . USERS_TABLE . '
	WHERE
		user_session_time >= ' . strtotime(date('Y-m-d'));
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_visitors = $row['visitors'];

$sql = 'INSERT INTO `bb_stats_trend` (`trend_time`, `trend_posts`, `trend_users`, `trend_peers`, `trend_seeders`, `trend_leechers`, `trend_users_online`, `trend_visitors`, `trend_torrents`, `trend_torrents_size`, `trend_speed`, `trend_traffic`) VALUES ("' . $trend_time . '", "' . $trend_posts . '", "' . $trend_users . '", ' . $trend_peers . ', "' . $trend_seeders . '", "' . $trend_leechers . '", "' . $trend_users_online . '", "' . $trend_visitors . '", "' . $trend_torrents . '", "' . $trend_torrents_size . '", "' . $trend_speed . '", "' . $trend_traffic . '")';
$db->sql_query($sql);

$data_stats = array(
	'active_leechers' => $trend_leechers,
	'active_seeders'  => $trend_seeders,
	'active_tor'      => $trend_active_torrents,
	'active_tor_size' => $trend_active_torrents_size,
	'all_tor'         => $trend_torrents,
	'all_tor_size'    => $trend_torrents_size,
	'download'        => $trend_download,
	'peers'           => $trend_peers,
	'posts'           => $trend_posts,
	'speed'           => $trend_speed,
	'total_dl_ul'     => $trend_traffic,
	'upload'          => $trend_upload,
	'users_online'    => $trend_users_online,
	'visitors'        => $trend_visitors
);

$datastore->store('stats_update', $data_stats);

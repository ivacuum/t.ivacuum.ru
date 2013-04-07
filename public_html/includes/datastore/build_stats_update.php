<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db;

/**
* Определяем переменные
*/
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
		ROUND(SUM(u_up_total+u_down_total)) AS pumped,
		ROUND(SUM(u_up_total)) AS upl,
		ROUND(SUM(u_down_total)) AS donl
	FROM
		bb_bt_users';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_download = $row['donl'];
$trend_upload   = $row['upl'];
$trend_traffic  = $row['pumped'];
//up_down total end

//active seeders begin
$sql = '
	SELECT
		COUNT(DISTINCT user_id) AS st
	FROM
		bb_bt_tracker
	WHERE
		seeder = 1';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_seeders = $row['st'];
//active seeders end

//active leechers begin
$sql = '
	SELECT
		COUNT(DISTINCT user_id) AS lt
	FROM
		bb_bt_tracker
	WHERE
		seeder = 0';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_leechers = $row['lt'];
//active leechers end

/* Peers */
$sql = '
	SELECT
		COUNT(*) AS peers
	FROM
		bb_bt_tracker';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_peers = $row['peers'];

// Speed
$sql = "SELECT SUM(speed_up) as speed_up, SUM(speed_down) as speed_down FROM bb_bt_tracker";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_speed = max($row['speed_down'], $row['speed_up']) * 2;

//active torrents begin
$sql = 'SELECT COUNT(DISTINCT tor.topic_id) AS tt , SUM(tor.size) AS ts
        FROM bb_bt_tracker tr , bb_bt_torrents tor
        WHERE tr.topic_id = tor.topic_id';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_active_torrents      = $row['tt'];
$trend_active_torrents_size = $row['ts'];
//active torrents end

//all torrents begin
$sql = ' SELECT COUNT(*) as tn, SUM(size) AS tos
         FROM bb_bt_torrents';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_torrents = $row['tn'];
$trend_torrents_size = $row['tos'];
//all torrents end

$sql = '
	SELECT
		COUNT(*) as total
	FROM
		bb_posts';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_posts = $row['total'];

$sql = '
	SELECT
		COUNT(*) as total
	FROM
		bb_users';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_users = $row['total'];

$sql = '
	SELECT
		COUNT(*) AS users_online
	FROM
		bb_sessions s,
		bb_users u
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
		bb_users
	WHERE
		user_session_time >= ' . strtotime(date('Y-m-d'));
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$trend_visitors = $row['visitors'];

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

$this->store('stats_update', $data_stats);

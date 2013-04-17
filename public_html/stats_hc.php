<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

if( $_SERVER['REMOTE_ADDR'] != '192.168.1.1' )
{
	exit;
}

define('BB_SCRIPT', 'stats');
require 'common.php';

// Session start
$user->session_start();

$mode = $app['request']->variable('mode', 'peers');
$time = $app['request']->variable('time', 'day');

/**
* Определяем переменные
*/
$ary = array();
// $max_trend_time = $max_trend_posts = $max_trend_users = $max_trend_peers = $max_trend_seeders = $max_trend_leechers = $max_trend_users_online = $max_trend_visitors = $max_trend_torrents = $max_trend_torrents_size = $max_trend_speed = $max_trend_traffic = 0;
$order_by = 'ASC';
$select_what = $select_where = '';
$trend_time = $trend_posts = $trend_users = $trend_peers = $trend_seeders = $trend_leechers = $trend_users_online = $trend_visitors = $trend_torrents = $trend_torrents_size = $trend_speed = $trend_traffic = '';

switch( $time )
{
	case 'day': $date_format = '%H:%M'; $points_count = 96; $threshold = 8; break;
	case 'week': $date_format = '%d %b %H:%M'; $points_count = 672; $threshold = 2; break;
	case 'month': $date_format = '%d.%m'; $points_count = 2880; $threshold = 1; break;
	case 'all': $date_format = '%d.%m'; $points_count = 10000034560; $threshold = 10; break;

	default:
	
		$points_count = 1000000000;

		message_die(GENERAL_MESSAGE, 'Неверный запрос');

	break;
}

$template->assign_vars(array(
	'MODE' => $mode,
	'TIME' => $time
));

switch( $mode )
{
	case 'peers':

		$select_what = 'trend_peers, trend_time * 1000 AS date';
		
		// if( false !== $trend_peers = $app['cache']->get('trend_peers') )
		// {
		// 	$template->assign_vars(array(
		// 		'TREND_PEERS' => $trend_peers
		// 	));
		// 	
		// 	print_page('stats_hc.tpl');
		// }

	break;
	case 'posts':

		$date_format  = '%d.%m';
		$order_by     = 'DESC';
		$points_count = 31;
		$select_what  = 'MAX(trend_posts) as trend_posts, FROM_UNIXTIME(trend_time,"%Y-%m-%d") AS date';
		$select_where = 'GROUP BY date';

	break;
	case 'speed':

		$select_what  = 'trend_speed / 1048576 AS trend_speed, FROM_UNIXTIME(trend_time) AS date';

	break;
	case 'torrents':

		$date_format  = '%d.%m';
		$order_by     = 'DESC';
		$points_count = 31;
		$select_what  = 'MAX(trend_torrents) as trend_torrents, FROM_UNIXTIME(trend_time,"%Y-%m-%d") AS date';
		$select_where = 'GROUP BY date';

	break;
	case 'traffic':

		$select_what  = 'trend_traffic / 1099511627776 AS trend_traffic, FROM_UNIXTIME(trend_time) AS date';

	break;
	case 'users':

		$date_format  = '%d.%m';
		$order_by     = 'DESC';
		$points_count = 31;
		$select_what  = 'MAX(trend_users) as trend_users, FROM_UNIXTIME(trend_time,"%Y-%m-%d") AS date';
		$select_where = 'GROUP BY date';

	break;
	default:

		message_die(GENERAL_MESSAGE, 'Неверный запрос');

	break;
}

$sql = '
	SELECT
		' . $select_what . '
	FROM
		bb_stats_trend
	' . $select_where . '
	ORDER BY
		trend_time ' . $order_by . '
	LIMIT
		0, ' . $points_count;
$result = $db->sql_query($sql);

while( $row = $db->sql_fetchrow($result) )
{
	foreach( $row as $key => $value )
	{
		if( $key == 'date' )
		{
			continue;
		}
		
		${$key} = sprintf('%s,[%d,%s]', ${$key}, $row['date'], $value);
	}
}

$db->sql_freeresult($result);

$trend_peers = substr($trend_peers, 1);

$app['cache']->set('trend_peers', $trend_peers, 1440);

if( $points_count == 31 )
{
	array_shift($ary);
}

// $ary = array_reverse($ary);

// for( $i = 0, $len = sizeof($ary); $i < $len; $i++ )
// {
// 	if( ($mode == 'posts' || $mode == 'torrents' || $mode == 'users') && $i == 0 )
// 	{
// 		/* Расчет разницы необходимо начать со второго элемента */
// 		continue;
// 	}
// 
// 	foreach( $ary[$i] as $key => $value )
// 	{
// 		if( $key == 'date' )
// 		{
// 			continue;
// 		}
// 
// 		if( $mode == 'posts' || $mode == 'torrents' || $mode == 'users' )
// 		{
// 			$value = $value - $ary[$i - 1][$key];
// 			$value = ( $value < 0 ) ? 0 : $value;
// 		}
// 
// 		${$key} = sprintf('%s[%d,%s],', ${$key}, $ary[$i]['date'], $value);
// 
// 		// if( $value > ${'max_' . $key} )
// 		// {
// 		// 	${'max_' . $key} = $value;
// 		// }
// 	}
// }

$template->assign_vars(array(
	'DATE_FORMAT'            => $date_format,
	// 'MAX_TREND_LEECHERS'     => $max_trend_leechers,
	// 'MAX_TREND_PEERS'        => $max_trend_peers,
	// 'MAX_TREND_POSTS'        => $max_trend_posts,
	// 'MAX_TREND_SEEDERS'      => $max_trend_seeders,
	// 'MAX_TREND_SPEED'        => $max_trend_speed,
	// 'MAX_TREND_TORRENTS'     => $max_trend_torrents,
	// 'MAX_TREND_TRAFFIC'      => $max_trend_traffic,
	// 'MAX_TREND_USERS'        => $max_trend_users,
	// 'MAX_TREND_USERS_ONLINE' => $max_trend_users_online,
	// 'MAX_TREND_VISITORS'     => $max_trend_visitors,
	'MODE'                   => $mode,
	'THRESHOLD'              => $threshold,
	'TIME'                   => $time,
	'TREND_LEECHERS'         => substr($trend_leechers, 0, -1),
	'TREND_PEERS'            => substr($trend_peers, 0),
	'TREND_POSTS'            => substr($trend_posts, 1),
	'TREND_SEEDERS'          => substr($trend_seeders, 0, -1),
	'TREND_SPEED'            => substr($trend_speed, 0, -1),
	'TREND_TORRENTS'         => substr($trend_torrents, 0, -1),
	'TREND_TRAFFIC'          => substr($trend_traffic, 0, -1),
	'TREND_USERS'            => substr($trend_users, 0, -1),
	'TREND_USERS_ONLINE'     => substr($trend_users_online, 0, -1),
	'TREND_VISITORS'         => substr($trend_visitors, 0, -1))
);

print_page('stats_hc.tpl');

<?php

if( !defined('BB_ROOT') ) die(basename(__FILE__));

$sql = '
	SELECT
		COUNT(*) as online,
		CURRENT_DATE() as date
	FROM
		' . USERS_TABLE . '
	WHERE
		user_session_time >= UNIX_TIMESTAMP(CURRENT_DATE())';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);

$online = $row['online'];
$date   = $row['date'];

$db->sql_freeresult($result);

if( $online > $bb_cfg['maximum_visitors'] )
{
	bb_update_config(array(
		'maximum_visitors'      => $online,
		'maximum_visitors_date' => $date)
	);
}

$bb_cache->rm('config_bb_config');

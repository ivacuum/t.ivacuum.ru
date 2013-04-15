<?php

if (!defined('SITE_DIR'))
{
	exit;
}

$sql = '
	SELECT
		COUNT(*) as online,
		CURRENT_DATE() as date
	FROM
		bb_users
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

$app['cache']->delete('config_bb_config');

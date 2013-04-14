<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('BB_SCRIPT', 'stats');
require 'common.php';

// Session start
$user->session_start(array('req_login' => $bb_cfg['bt_tor_browse_only_reg']));

$sort = request_var('sort', '');

$sort_sql = ( $sort == 'download' ) ? 'speed_down DESC, speed_up DESC' : 'speed_up DESC, speed_down DESC';

$sql = '
	SELECT
		t.user_id,
		SUM(t.speed_up) as speed_up,
		SUM(t.speed_down) as speed_down,
		u.username,
		u.user_level
	FROM
		bb_bt_tracker t,
		bb_users u
	WHERE
		t.user_id = u.user_id
	AND
		(speed_up != 0 OR speed_down != 0)
	GROUP BY
		t.user_id
	ORDER BY
		' . $sort_sql . '
	LIMIT
		0, 50';
$result = $db->sql_query($sql);

$i = 1;

while( $row = $db->sql_fetchrow($result) )
{
	$username = $row['username'];

	if( $row['user_level'] == ADMIN )
	{
		$username = '<b class="colorAdmin">' . $username . '</b>';
	}
	elseif( $row['user_level'] == MOD )
	{
		$username = '<b class="colorMod">' . $username . '</b>';
	}
	elseif( $row['user_level'] == GROUP_MEMBER )
	{
		$username = '<b class="colorGroup">' . $username . '</b>';
	}

	$template->assign_block_vars('memberrow', array(
		'DOWNLOAD'   => ( $row['speed_down'] ) ? humn_size($row['speed_down']) . '/с' : '&mdash;',
		'ROW_NUMBER' => $i,
		'ROW_CLASS'  => !($i % 2) ? 'row2' : 'row1',
		'UPLOAD'     => ( $row['speed_up'] ) ? humn_size($row['speed_up']) . '/с' : '&mdash;',
		'USERNAME'   => $username,

		'U_VIEWPROFILE' => append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL ."=" . $row['user_id']))
	);

	$i++;
}

$db->sql_freeresult($result);

$template->assign_vars(array(
	'SORT_BY' => ( $sort == 'download' ) ? 'dl' : 'up',

	'U_SORT_BY_DOWNLOAD' => append_sid('top_speed.php?sort=download'),
	'U_SORT_BY_UPLOAD'   => append_sid('top_speed.php?sort=upload'))
);

print_page('top_speed.tpl');

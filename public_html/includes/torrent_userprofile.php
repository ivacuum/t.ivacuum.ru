<?php

if (!defined('IN_VIEWPROFILE')) die(basename(__FILE__));

if( !$profiledata['user_id'] || $profiledata['user_id'] == ANONYMOUS )
{
	message_die(GENERAL_ERROR, 'Invalid user_id');
}

$owning = $seeding = $leeching = array();

$profile_user_id = intval($profiledata['user_id']);
$current_time = (isset($_GET['time']) && $_GET['time'] == 'all') ? 0 : time();

// Get username
if( !$username = $profiledata['username'] )
{
	message_die(GENERAL_ERROR, 'Tried obtaining data for a non-existent user');
}

if( $profile_user_id == $userdata['user_id'] )
{
	$template->assign_vars(array(
		'EDIT_PROF'			=> true,
		'L_EDIT_PROF'		=> $lang['Edit_profile'],
		'EDIT_PROF_HREF'	=> append_sid("profile.$phpEx?mode=editprofile"))
	);
}
else
{
	$template->assign_vars(array(
		'EDIT_PROF' => false)
	);
}

// Set tpl vars for bt_userdata
show_bt_userdata($profile_user_id);

if( IS_ADMIN )
{
	$template->assign_vars(array(
		'SHOW_PASSKEY'		=> true,
		'S_GEN_PASSKEY'		=> "<a href=\"torrent.$phpEx?mode=gen_passkey&amp;u=". $profile_user_id .'&amp;sid='. $userdata['session_id'] .'">'. $lang['Bt_Gen_Passkey_Url'] .'</a>',
		'CAN_EDIT_RATIO'	=> IS_SUPER_ADMIN,
	));
}
else
{
	$template->assign_vars(array(
		'CAN_EDIT_RATIO' => false,
	));
}

// Auth
$not_auth_forums_sql = ($f = $user->get_not_auth_forums(AUTH_READ)) ? "AND f.forum_id NOT IN($f)" : '';
$datastore->rm('cat_forums');

// Released size
$sql = '
	SELECT
		SUM(size) as released_size
	FROM
		' . BT_TORRENTS_TABLE . '
	WHERE
		poster_id = ' . $profile_user_id;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$released_size = $row['released_size'];

// Get users active torrents
$sql = '
	SELECT
		f.forum_id,
		f.forum_name,
		t.topic_id,
		t.topic_title,
		tor.size,
		tor.speed_up,
		tor.seeders,
		tor.leechers,
		tor.poster_id,
		tr.seeder,
		tr.remain
	FROM
		' . FORUMS_TABLE . ' f,
		' . TOPICS_TABLE . ' t,
		' . BT_TRACKER_TABLE . ' tr,
		' . BT_TORRENTS_TABLE . ' tor
	WHERE
		tr.user_id = ' . $profile_user_id . '
	AND
		tr.topic_id = tor.topic_id
	AND
		tor.topic_id = t.topic_id
	AND
		t.forum_id = f.forum_id
	' . $not_auth_forums_sql . '
	GROUP BY
		tr.topic_id
	ORDER BY
		f.forum_name,
		t.topic_title';
if( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, 'Could not query users torrent profile information', '', __LINE__, __FILE__, $sql);
}

$share_size = 0;

while( $row = $db->sql_fetchrow($result) )
{
	if( $row['poster_id'] == $profile_user_id )
	{
		/* Свои раздачи */
		$owning[] = $row;
		$share_size += $row['size'];
	}
	elseif( $row['seeder'] )
	{
		/* Сид */
		$seeding[] = $row;
		$share_size += $row['size'];
	}
	else
	{
		/* Лич */
		$leeching[] = $row;
	}
}

$db->sql_freeresult($result);

if( $owning_count = sizeof($owning) )
{
	$template->assign_block_vars('own', array());

	for( $i = 0; $i < $owning_count; $i++ )
	{
		$template->assign_block_vars('own.ownrow', array(
			'FORUM_NAME'   => htmlCHR($owning[$i]['forum_name']),
			'LEECHERS'     => $owning[$i]['leechers'],
			'SEEDERS'      => $owning[$i]['seeders'],
			'SPEED'        => ( $owning[$i]['speed_up'] > 0 ) ? humn_size($owning[$i]['speed_up'], null, 'КБ') . '/с' : '-',
			'TOPIC_TITLE'  => wbr($owning[$i]['topic_title']),
			'U_VIEW_FORUM' => sprintf('viewforum.php?%s=%d', POST_FORUM_URL, $owning[$i]['forum_id']),
			'U_VIEW_TOPIC' => sprintf('viewtopic.php?%s=%d&spmode=full#seeders', POST_TOPIC_URL, $owning[$i]['topic_id']))
		);
	}
}
else
{
	$template->assign_block_vars('switch_owning_none', array());
}

if( $seeding_count = sizeof($seeding) )
{
	$template->assign_block_vars('seed', array());

	for ($i=0; $i<$seeding_count; $i++)
	{
		$template->assign_block_vars('seed.seedrow', array(
			'FORUM_NAME'   => htmlCHR($seeding[$i]['forum_name']),
			'LEECHERS'     => $seeding[$i]['leechers'],
			'SEEDERS'      => $seeding[$i]['seeders'],
			'SPEED'        => ( $seeding[$i]['speed_up'] > 0 ) ? humn_size($seeding[$i]['speed_up'], null, 'KБ') . '/с' : '-',
			'TOPIC_TITLE'  => wbr($seeding[$i]['topic_title']),
			'U_VIEW_FORUM' => "viewforum.$phpEx?". POST_FORUM_URL .'='. $seeding[$i]['forum_id'],
			'U_VIEW_TOPIC' => "viewtopic.$phpEx?". POST_TOPIC_URL .'='. $seeding[$i]['topic_id'] .'&amp;spmode=full#seeders',
		));
	}
}
else
{
	$template->assign_block_vars('switch_seeding_none', array());
}

if ($leeching_count = count($leeching))
{
	$template->assign_block_vars('leech', array());

	for ($i=0; $i<$leeching_count; $i++)
	{
		$compl_size = ($leeching[$i]['remain'] && $leeching[$i]['size'] && $leeching[$i]['size'] > $leeching[$i]['remain']) ? ($leeching[$i]['size'] - $leeching[$i]['remain']) : 0;
		$compl_perc = ($compl_size) ? floor($compl_size * 100 / $leeching[$i]['size']) : 0;

		$template->assign_block_vars('leech.leechrow', array(
			'FORUM_NAME'   => htmlCHR($leeching[$i]['forum_name']),
			'LEECHERS'     => $leeching[$i]['leechers'],
			'SEEDERS'      => $leeching[$i]['seeders'],
			'SPEED'        => ( $leeching[$i]['speed_up'] > 0 ) ? humn_size($leeching[$i]['speed_up'], null, 'KБ') . '/с' : '-',
			'TOPIC_TITLE'  => wbr($leeching[$i]['topic_title']),
			'U_VIEW_FORUM' => "viewforum.$phpEx?". POST_FORUM_URL .'='. $leeching[$i]['forum_id'],
			'U_VIEW_TOPIC' => "viewtopic.$phpEx?". POST_TOPIC_URL .'='. $leeching[$i]['topic_id'] .'&amp;spmode=full#leechers',
			'COMPL_PERC'   => $compl_perc,
		));
	}
}
else
{
	$template->assign_block_vars('switch_leeching_none', array());
}

$template->assign_vars(array(
	'USERNAME'   => $username,
	'L_NONE'     => $lang['None'],
	'L_OWNING'   => '<b>Свои</b>' . ( ($owning_count) ? '<br />[ <b>' . $owning_count . '</b> ]' : ''),
	'L_SEEDING'  => '<b>'. $lang['Seeding'] .'</b>'. (($seeding_count) ? "<br />[ <b>$seeding_count</b> ]" : ''),
	'L_LEECHING' => '<b>'. $lang['Leeching'] .'</b>'. (($leeching_count) ? "<br />[ <b>$leeching_count</b> ]" : ''),
	'OWNING_COUNT' => ( $owning_count ) ? $owning_count : 0,
	'SEEDING_COUNT' => ( $seeding_count ) ? $seeding_count : 0,
	'LEECHING_COUNT' => ( $leeching_count ) ? $leeching_count : 0,

	'L_VIEW_TOR_PROF'  => sprintf($lang['Viewing_user_bt_profile'], $username),
	'L_CUR_ACTIVE_DLS' => $lang['Cur_active_dls'],
	'OWN_ROWSPAN'      => ($owning_count) ? 'rowspan="' . ($owning_count + 1) . '"' : '',
	'SEED_ROWSPAN'     => ($seeding_count) ? 'rowspan="'. ($seeding_count + 1) .'"' : '',
	'LEECH_ROWSPAN'    => ($leeching_count) ? 'rowspan="'. ($leeching_count + 1) .'"' : '',
	'SHARE_SIZE'       => humn_size($share_size),
	'RELEASED_SIZE'    => humn_size($released_size)
));

$template->assign_vars(array('SHOW_SEARCH_DL' => false));

if (!IS_USER || $profile_user_id == $userdata['user_id'])
{
	$page_cfg['dl_links_user_id'] = $profile_user_id;
}

$template->assign_vars(array(
	'U_TORRENT_PROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;u=". $profiledata['user_id']) . '#torrent',
	'L_TORRENT_PROFILE' => $lang['View_torrent_profile'],
));

$sql = '
	SELECT
		SUM(speed_up) as speed_up,
		SUM(speed_down) as speed_down
	FROM
		' . BT_TRACKER_TABLE . '
	WHERE
		user_id = ' . $profile_user_id;

if( $row = $db->sql_fetchrow($db->sql_query($sql)) )
{
	$speed_up = ($row['speed_up']) ? humn_size($row['speed_up']) . '/сек' : '-';
	$speed_down = ($row['speed_down']) ? humn_size($row['speed_down']) . '/сек' : '-';
}

$template->assign_vars(array(
	'SPEED_UP' => $speed_up,
	'SPEED_DOWN' => $speed_down)
);

?>
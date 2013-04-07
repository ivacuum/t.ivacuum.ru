<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

require('common.php');

$user->session_start(array('req_login' => true));

$start		= abs(request_var('start', 0));
$mode		= request_var('mode', 'joined');
$sort_order = ( request_var('order', 'ASC') == 'ASC' ) ? 'ASC' : 'DESC';
$username	= request_var('username', '');

//
// Auth check
//
/*if ( !in_array($userdata['user_level'], array(ADMIN,MOD)) )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Moderator'], $lang['Not_Authorised']);
}*/

//
// Memberlist sorting
//
$mode_types_text = array(
	$lang['Sort_Joined'],
	$lang['Sort_Username'],
	$lang['Sort_Location'],
	$lang['Sort_Posts'],
	mb_strtolower($lang['BT_RATIO']),
	'сидирует',
	'качает',
	mb_strtolower($lang['UPLOADED']),
	mb_strtolower($lang['DOWNLOADED'])
);

$mode_types = array(
	'joined',
	'username',
	'location',
	'posts',
	'ratio',
	'seeding',
	'leeching',
	'uploaded',
	'downloaded'
);

// <select> mode
$select_sort_mode = '<select name="mode">';

for( $i = 0, $cnt = count($mode_types_text); $i < $cnt; $i++ )
{
	$selected = ( $mode == $mode_types[$i] ) ? ' selected="selected"' : '';
	$select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
}
$select_sort_mode .= '</select>';

// <select> order
$select_sort_order = '<select name="order">';

if ($sort_order == 'ASC')
{
	$select_sort_order .= '<option value="ASC" selected="selected">' . $lang['ASC'] . '</option><option value="DESC">' . $lang['DESC'] . '</option>';
}
else
{
	$select_sort_order .= '<option value="ASC">' . $lang['ASC'] . '</option><option value="DESC" selected="selected">' . $lang['DESC'] . '</option>';
}
$select_sort_order .= '</select>';

//
// Generate page
//
$template->assign_vars(array(
	'S_MODE_SELECT' => $select_sort_mode,
	'S_ORDER_SELECT' => $select_sort_order,
	'S_MODE_ACTION' => append_sid("memberlist.php"))
);

switch( $mode )
{
	case 'joined':
		$order_by = "u.user_id $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'username':
		$order_by = "u.username $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'location':
		$order_by = "u.user_from $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'posts':
		$order_by = "u.user_posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'email':
		$order_by = "u.user_email $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'website':
		$order_by = "u.user_website $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		break;
	case 'topten':
		$order_by = "u.user_posts $sort_order LIMIT 10";
		break;
	case 'ratio':
		$order_by = 'ratio ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'seeding':
		$order_by = 'bu.seeding ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'leeching':
		$order_by = 'bu.leeching ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'uploaded':
		$order_by = 'bu.u_up_total ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	case 'downloaded':
		$order_by = 'bu.u_down_total ' . $sort_order . ' LIMIT ' . $start . ', ' . $board_config['topics_per_page'];
		break;
	default:
		$order_by = "u.user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
		$mode = 'joined';
		break;
}

// per-letter selection
$by_letter = 'all';
$letters_range = 'a-zа-я';
$select_letter = $letter_sql = '';

$by_letter_req = (@$_REQUEST['letter']) ? strtolower(trim($_REQUEST['letter'])) : false;

if ($by_letter_req)
{
	if ($by_letter_req === 'all')
	{
		$by_letter = 'all';
		$letter_sql = '';
	}
	else if ($by_letter_req === 'others')
	{
		$by_letter = 'others';
		$letter_sql = "u.username REGEXP '^[!-@\\[-`].*$'";
	}
	else if ($letter_req = preg_replace("#[^$letters_range]#", '', $by_letter_req[0]))
	{
		$by_letter = $db->escape($letter_req);
		$letter_sql = "LOWER(u.username) LIKE '$by_letter%'";
	}
}

// ENG
for ($i=ord('A'), $cnt=ord('Z'); $i <= $cnt; $i++)
{
	$select_letter .= ($by_letter == chr($i)) ? '<b>'. chr($i) .'</b>&nbsp;' : '<a class="genmed" href="'. append_sid("memberlist.php?letter=". chr($i) ."&amp;mode=$mode&amp;order=$sort_order") .'">'. chr($i) .'</a>&nbsp;';
}
// RUS
$select_letter .= ': ';
for ($i=ord('А'), $cnt=ord('Я'); $i <= $cnt; $i++)
{
	$select_letter .= ($by_letter == chr($i)) ? '<b>'. chr($i) .'</b>&nbsp;' : '<a class="genmed" href="'. append_sid("memberlist.php?letter=". chr($i) ."&amp;mode=$mode&amp;order=$sort_order") .'">'. chr($i) .'</a>&nbsp;';
}

$select_letter .= ':&nbsp;';
$select_letter .= ($by_letter == 'others') ? '<b>'. $lang['Others'] .'</b>&nbsp;' : '<a class="genmed" href="'. append_sid("memberlist.php?letter=others&amp;mode=$mode&amp;order=$sort_order") .'">'. $lang['Others'] .'</a>&nbsp;';
$select_letter .= ':&nbsp;';
$select_letter .= ($by_letter == 'all') ? '<b>'. $lang['All'] .'</b>' : '<a class="genmed" href="'. append_sid("memberlist.php?letter=all&amp;mode=$mode&amp;order=$sort_order") .'">'. $lang['All'] .'</a>';

$template->assign_vars(array(
	'L_SORT_PER_LETTER' => $lang['Sort_per_letter'],
	'S_LETTER_SELECT'   => $select_letter,
	'S_LETTER_HIDDEN'   => '<input type="hidden" name="letter" value="'. $by_letter .'">',
));
// per-letter selection end

$sql = '
	SELECT
		u.username,
		u.user_id,
		u.user_opt,
		u.user_posts,
		u.user_regdate,
		u.user_from,
		u.user_from_flag,
		u.user_website,
		u.user_email,
		u.user_icq,
		u.user_aim,
		u.user_yim,
		u.user_msnm,
		u.user_avatar,
		u.user_avatar_type,
		u.user_allowavatar,
		bu.u_up_total,
		bu.u_down_total,
		bu.u_up_release,
		bu.u_up_bonus,
		bu.seeding,
		bu.leeching,
		IF(bu.u_down_total > ' . MIN_DL_FOR_RATIO . ', ROUND(( bu.u_up_total + bu.u_up_release + bu.u_up_bonus ) / bu.u_down_total, 2), 0) as ratio
	FROM
		bb_users u,
		bb_bt_users bu
	WHERE
		u.user_id = bu.user_id
	AND
		u.user_id > 0';
$sql .= ($letter_sql) ? " AND $letter_sql" : '';
$sql .= " ORDER BY $order_by";

$result = $db->sql_query($sql) OR message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);

if ( $row = $db->sql_fetchrow($result) )
{
	$i = 0;
	do
	{
		$username = $row['username'];
		$user_id = $row['user_id'];
		$from = $row['user_from'];
// FLAGHACK-start
		$flag = ($row['user_from_flag'] && $row['user_from_flag'] != 'blank.gif') ? '<img src="' . $static_path . '/i/flags/24/' . $row['user_from_flag'] . '" alt="'. $row['user_from_flag'] .'" title="' . $row['user_from_flag'] . '" />' : '';
// FLAGHACK-end

		$joined = create_date($lang['DATE_FORMAT'], $row['user_regdate'], $board_config['board_timezone']);
		$posts = $row['user_posts'];
		$poster_avatar = false;

		if ($row['user_avatar_type'] && $user_id != ANONYMOUS && $row['user_allowavatar'])
		{
			switch ($row['user_avatar_type'])
			{
				case USER_AVATAR_UPLOAD:
					$poster_avatar = ($board_config['allow_avatar_upload']) ? '<img src="'. $board_config['avatar_path'] .'/'. $row['user_avatar'] .'" alt="" border="0" />' : false;
					break;
				case USER_AVATAR_REMOTE:
					$poster_avatar = ($board_config['allow_avatar_remote']) ? '<img src="'. $row['user_avatar'] .'" alt="" border="0" />' : false;
					break;
				case USER_AVATAR_GALLERY:
					$poster_avatar = ($board_config['allow_avatar_local']) ? '<img src="'. $board_config['avatar_gallery_path'] .'/'. $row['user_avatar'] .'" alt="" border="0" />' : false;
					break;
			}
		}

		$pm = '<a class="txtb" href="'. append_sid("privmsg.php?mode=post&amp;". POST_USERS_URL ."=$user_id") .'">'. $lang['Send_pm_txtb'] .'</a>';
		$email = ($board_config['board_email_form']) ? '<a class="txtb" href="'. append_sid("profile.php?mode=email&amp;". POST_USERS_URL ."=$user_id") .'">'. $lang['Send_email_txtb'] .'</a>' : false;
		$temp_url = append_sid("profile.php?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id");
		$profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';
		$www = ($row['user_website']) ? '<a class="txtb" href="'. $row['user_website'] .'" target="_userwww">'. $lang['Visit_website_txtb'] .'</a>' : false;

		$temp_url = append_sid("search.php?search_author=1&amp;uid=$user_id");
		$search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . $lang['Search_user_posts'] . '" title="' . $lang['Search_user_posts'] . '" border="0" /></a>';
		$search = '<a href="' . $temp_url . '">' . $lang['Search_user_posts'] . '</a>';

		$row_class = !($i % 2) ? 'row1' : 'row2';

		$template->assign_block_vars('memberrow', array(
			'ROW_NUMBER'	=> $i + ( $start + 1 ),
			'ROW_CLASS'		=> $row_class,
			'USERNAME'		=> $username,
			'FROM'			=> $from,
			'FLAG'			=> $flag,
			'JOINED'		=> $joined,
			'POSTS'			=> $posts,
			'AVATAR_IMG'	=> $poster_avatar,
			'SEARCH'		=> $search,
			'PM'			=> $pm,
			'EMAIL'			=> $email,
			'WWW'			=> $www,
			'RATIO'			=> ( $row['ratio'] > 0 ) ? $row['ratio'] : '-',
			'SEEDING'       => $row['seeding'],
			'LEECHING'      => $row['leeching'],
			'UPLOAD'		=> ( $row['u_up_total'] ) ? humn_size($row['u_up_total']) : '-',
			'DOWNLOAD'		=> ( $row['u_down_total'] ) ? humn_size($row['u_down_total']) : '-',
			'U_PM'          => append_sid("privmsg.php?mode=post&amp;". POST_USERS_URL ."=$user_id"),
			'U_VIEWPROFILE'	=> append_sid("profile.php?mode=viewprofile&amp;". POST_USERS_URL ."=$user_id"))
		);
		$i++;
	}
	while ( $row = $db->sql_fetchrow($result) );
	$db->sql_freeresult($result);
}
if ( $mode != 'topten' || $board_config['topics_per_page'] < 10 )
{
	$sql = 'SELECT COUNT(*) AS total FROM bb_users';
	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
	}
	if ($total = $db->sql_fetchrow($result))
	{
		$total_members = $total['total'];
		$pagination = generate_pagination("memberlist.php?mode=$mode&amp;order=$sort_order&amp;letter=$by_letter", $total_members, $board_config['topics_per_page'], $start). '&nbsp;';
	}
	$db->sql_freeresult($result);
}
else
{
	$pagination = '&nbsp;';
	$total_members = 10;
}
$template->assign_vars(array(
	'PAGE_TITLE' => $lang['MEMBERLIST'],
	'PAGINATION' => $pagination,
	'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $board_config['topics_per_page'] ) + 1 ), ceil( $total_members / $board_config['topics_per_page'] )),
));

print_page('memberlist.tpl');

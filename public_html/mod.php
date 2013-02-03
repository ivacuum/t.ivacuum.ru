<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

require('common.php');

$page_cfg['load_tpl_vars'] = array(
	'post_icons',
);

$user->session_start(array('req_login' => true));

// Сортировка в таблицах
$page_cfg['use_tablesorter'] = true;
// Предпросмотр на ajax'е
$page_cfg['include_bbcode_js'] = true;

$tracking_topics = get_tracks('topic');

// Кол-во тем на странице
$tor_topics_per_page = $bb_cfg['posts_per_page'];
if( $req_tpp = abs(intval(@$_REQUEST['tpp'])) and in_array($req_tpp, $bb_cfg['allowed_posts_per_page']) )
{
	$tor_topics_per_page = $req_tpp;
}

$select_tpp = '';
foreach( $bb_cfg['allowed_posts_per_page'] as $tpp )
{
	$select_tpp[$tpp] = $tpp;
}

$start   = isset($_GET['start']) ? abs(intval($_GET['start'])) : '0';
$status  = request_var('st', 0);
$user_id = $userdata['user_id'];

$where_status = 'AND tor.tor_status = ' . $status;

if( $userdata['user_level'] == USER || $userdata['user_level'] == GROUP_MEMBER )
{
	meta_refresh(2, './');
	bb_die($lang['Not_Moderator']);
}

// Исключаем вывод с ненужных форумов
$trash_forums = $bb_cfg['archive_forum'] ? $bb_cfg['archive_forum'] : 0;

$url = append_sid('mod.php?st=' . $status);

// Основная ф-ция обновления статуса и типа раздачи
if( isset($_POST['topic_id']) )
{
	// print implode(',', $_POST['topic_id']);
	// exit;
	$topic_ids = implode(',', $_POST['topic_id']);
	$status    = request_var('status', 0);

	switch( $status )
	{
		case 'lock':
		case 'unlock':

			$lock = ($status == 'lock');
			$new_topic_status = ($lock) ? TOPIC_LOCKED : TOPIC_UNLOCKED;

			$sql = '
				UPDATE
					' . TOPICS_TABLE . '
				SET
					topic_status = ' . $new_topic_status . '
				WHERE
					topic_id IN (' . $topic_ids . ')';
			$db->sql_query($sql);

			$status_text = ($lock) ? $lang['Topics_Locked'] : $lang['Topics_Unlocked'];
			meta_refresh(2, $url);
			message_die(GENERAL_MESSAGE, sprintf($lang['MODERATE_PANEL_TEXT'], $status_text, '<a class="gen" href="'. $url .'">', '</a>'));

		break;
		case 'down':
		case 'undown':

			$set_download = ($status == 'down');
			$new_dl_type  = ($set_download) ? TOPIC_DL_TYPE_DL : TOPIC_DL_TYPE_NORMAL;

			$sql = '
				UPDATE
					' . TOPICS_TABLE . '
				SET
					topic_dl_type = ' . $new_dl_type . '
				WHERE
					topic_id IN (' . $topic_ids . ')';
			$db->sql_query($sql);

			$status_text = ($lock) ? $lang['Topics_Down_Sets'] : $lang['Topics_Down_Unsets'];
			meta_refresh(2, $url);
			message_die(GENERAL_MESSAGE, sprintf($lang['MODERATE_PANEL_TEXT'], $status_text, '<a class="gen" href="'. $url .'">', '</a>'));

		break;
		case 'delete':

			require_once(SITE_DIR . 'includes/functions_admin.php');

			topic_delete($topic_ids);
			meta_refresh(2, $url);
			message_die(GENERAL_MESSAGE, sprintf($lang['MODERATE_PANEL_TEXT'], $lang['Topics_Removed'], '<a class="gen" href="'. $url .'">', '</a>'));

		break;
		case 'tor_delete':

			require_once(SITE_DIR . 'includes/functions_torrent.php');

			$sql = '
				SELECT
					attach_id
				FROM
					' . BT_TORRENTS_TABLE . '
				WHERE
					topic_id IN (' . $topic_ids . ')';
			$result = $db->sql_query($sql);

			while( $row = $db->sql_fetchrow($result) )
			{
				tracker_unregister($row['attach_id']);
			}

			$db->sql_freeresult($result);

			meta_refresh(2, $url);
			message_die(GENERAL_MESSAGE, sprintf($lang['MODERATE_PANEL_TOR_DEL'], $lang['Topics_Removed'], '<a class="gen" href="'. $url .'">', '</a>'));

		break;
		default:

			$sql = '
				UPDATE
					' . BT_TORRENTS_TABLE . '
				SET
					tor_status = ' . $status . ',
					checked_time = ' . time() . ',
					checked_user_id = ' . $user_id . '
				WHERE
					topic_id IN (' . $topic_ids . ')';
			$db->sql_query($sql);

			switch( $status )
			{
				case 0: $status_text = '<span class="tor-not-approved">' . $lang['TOR_STATUS_NOT_CHECKED'] . '</span>';  break;
				case 1: $status_text = '<span class="tor-closed">' . $lang['TOR_STATUS_CLOSED'] . '</span>'; break;
				case 2: $status_text = '<span class="tor-approved">' . $lang['TOR_STATUS_CHECKED'] . '</span>'; break;
				case 3: $status_text = '<span class="tor-dup">' . $lang['TOR_STATUS_D'] . '</span>'; break;
				case 4: $status_text = '<span class="tor-no-desc">' . $lang['TOR_STATUS_NOT_PERFECT'] . '</span>'; break;
				case 5: $status_text = '<span class="tor-need-edit">' . $lang['TOR_STATUS_PART_PERFECT'] . '</span>'; break;
				case 6: $status_text = '<span class="tor-consumed">' . $lang['TOR_STATUS_FISHILY'] . '</span>'; break;
				case 7: $status_text = '<span class="tor-closed-cp">' . $lang['TOR_STATUS_COPY'] . '</span>'; break;
			}

			meta_refresh(2, $url);
			message_die(GENERAL_MESSAGE, sprintf($lang['MODERATE_PANEL_TYPE'], $status_text, '<a class="gen" href="'. $url .'">', '</a>'));

		break;
	}
}

$auth_table  = ($userdata['user_level'] == ADMIN) ? '' : ', ' . AUTH_ACCESS_SNAP_TABLE . ' aa';
$auth_access = ($userdata['user_level'] == ADMIN) ? '' : 'AND aa.user_id = ' . $user_id . ' AND tor.forum_id = aa.forum_id AND aa.forum_perm = 8';

$sql = '
	SELECT
		COUNT(tor.topic_id) as tor_count
	FROM
		' . BT_TORRENTS_TABLE . ' tor
		' . $auth_table . '
	WHERE
		tor.forum_id != (' . $trash_forums . ')
		' . $auth_access . '
		' . $where_status;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$tor_count = ( $row['tor_count'] ) ? $row['tor_count'] : 0;
$db->sql_freeresult($result);

if( $tor_count )
{
	$sql = '
		SELECT
			tor.*,
			t.*,
			f.forum_name,
			u.username,
			u.user_level
		FROM
			' . BT_TORRENTS_TABLE . ' tor,
			' . TOPICS_TABLE . ' t,
			' . FORUMS_TABLE . ' f,
			' . USERS_TABLE . ' u
			' . $auth_table . '
		WHERE
			t.topic_id = tor.topic_id
		AND
			t.topic_poster = u.user_id
		AND
			f.forum_id = t.forum_id
		AND
			f.forum_id != (' . $trash_forums . ')
			' . $auth_access . '
			' . $where_status . '
		GROUP BY
			tor.topic_id
		ORDER BY
			tor.reg_time DESC
		LIMIT
			' . $start . ', ' . $tor_topics_per_page;
	$result = $db->sql_query($sql);
	$tor = $db->sql_fetchrowset($result);

	if( $tor )
	{
		$template->assign_block_vars('tor_topics', array());

		for( $i = 0, $len = sizeof($tor); $i < $len; $i++)
		{
			//$username =  colornick_level($tor[$i]['username'], $tor[$i]['user_level']);
			$username = $tor[$i]['username'];
			$topic_poster = ( $tor[$i]['topic_poster'] == ANONYMOUS ) ? ( ($username != '' ) ? $username . ' ' : $lang['Guest'] . ' ' ) : '<a class="genmed" href="' . append_sid(BB_ROOT . "profile.php?mode=viewprofile&amp;" . POST_USERS_URL . '='  . $tor[$i]['topic_poster']) . '">' . $username . '</a>';

			// Иконка темы
			$is_unread = is_unread($tor[$i]['topic_last_post_time'], $tor[$i]['topic_id'], $tor[$i]['forum_id']);

			$row_class = (!($i % 2)) ? 'row2' : 'row1';

			$template->assign_block_vars('tor', array(
				'ROW_CLASS'     => $row_class,

				'POST_ID'       => $tor[$i]['post_id'],
				'TOPIC_ID'      => $tor[$i]['topic_id'],
				'TOPIC_TITLE'   => wbr($tor[$i]['topic_title']),
				'TOPIC_REPLIES' => $tor[$i]['topic_replies'],
				'REG_TIME'      => create_date($bb_cfg['default_dateformat'], $tor[$i]['reg_time'], $bb_cfg['board_timezone']),
				'REG_TIME_BACK' => delta_time($tor[$i]['reg_time']),

				'FORUM_TITLE'   => wbr($tor[$i]['forum_name']),

				'TOPIC_POSTER'  => $topic_poster,

				'U_FORUM'       => append_sid(BB_ROOT . "viewforum.php?" . POST_FORUM_URL . '=' . $tor[$i]['forum_id']),
				'U_TOPIC'       => append_sid(BB_ROOT . "viewtopic.php?"  . POST_TOPIC_URL . '=' . $tor[$i]['topic_id']),

				'TOR_STATUS'    => $tor[$i]['tor_status'],
				'TOR_FROZEN'    => ($tor[$i]['tor_status'] == TOR_STATUS_FROZEN || $tor[$i]['tor_status'] == 3 || $tor[$i]['tor_status'] == 4 || $tor[$i]['tor_status'] == 7),
				'DL_CLASS'      => isset($tor[$i]['dl_status']) ? $dl_link_css[$tor[$i]['dl_status']] : 'genmed',

				'IS_UNREAD'     => $is_unread,
				'TOPIC_ICON'    => get_topic_icon($tor[$i], $is_unread),
				'PAGINATION'    => ($tor[$i]['topic_status'] == TOPIC_MOVED) ? '' : build_topic_pagination(TOPIC_URL . $tor[$i]['topic_id'], $tor[$i]['topic_replies'], $bb_cfg['posts_per_page']),
			));
		}

		$pagination = generate_pagination($url, $tor_count, $tor_topics_per_page, $start);

		$template->assign_vars(array(
			'PAGINATION'  => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $tor_topics_per_page ) + 1 ), ceil( $tor_count / $tor_topics_per_page )),
			'U_ACTION'    => $url,
			'PER_PAGE'    => $tor_topics_per_page,
		));
	}

	$db->sql_freeresult($result);
}
else
{
	$template->assign_block_vars('no_tor_topics', array());
}

$template->assign_vars(array(
	'PAGE_TITLE'   => $lang['MODERATE_PANEL'],
	'SELECT_TPP'   => ($select_tpp) ? build_select('tpp', $select_tpp, $tor_topics_per_page, null, null, 'onchange="$(\'#tpp\').submit();"') : '',
	'L_NO_MATCH'   => $lang['No_match'],
	'L_DOWN'       => $lang['Set_DL_Status'],
	'L_UNDOWN'     => $lang['Unset_DL_Status'],
	'L_TOR_DELETE' => $lang['Bt_Unreg_from_tracker'],

	'ST_0' => ( $status == 0 ) ? 'selected="selected"' : '',
	'ST_1' => ( $status == 1 ) ? 'selected="selected"' : '',
	'ST_2' => ( $status == 2 ) ? 'selected="selected"' : '',
	'ST_3' => ( $status == 3 ) ? 'selected="selected"' : '',
	'ST_4' => ( $status == 4 ) ? 'selected="selected"' : '',
	'ST_5' => ( $status == 5 ) ? 'selected="selected"' : '',
	'ST_6' => ( $status == 6 ) ? 'selected="selected"' : '',
	'ST_7' => ( $status == 7 ) ? 'selected="selected"' : '',
));

print_page('mod.tpl');

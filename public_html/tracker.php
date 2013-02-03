<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('BB_SCRIPT', 'tracker');
require('common.php');

// Page config
$page_cfg['include_bbcode_js'] = true;
$page_cfg['use_tablesorter']   = true;

$page_cfg['load_tpl_vars'] = array(
	'post_icons',
);

// Session start
$user->session_start(array('req_login' => $bb_cfg['bt_tor_browse_only_reg']));

$ctime               = time();
$empty_request       = empty($_GET) && empty($_POST);
$tor_search_limit    = 500;
$forum_select_size   = (UA_OPERA) ? 18 : 21;   // forum select box max rows
$max_forum_name_len  = 60;                     // inside forum select box
$title_match_max_len = 60;
$poster_name_max_len = 25;
$tor_colspan         = 13;                     // torrents table colspan with all columns
$per_page            = $bb_cfg['topics_per_page'];
$tracker_url         = basename(__FILE__);

$time_format  = 'H:i';
$date_format  = 'j-M-y';
$row_class_1  = 'prow1';
$row_class_2  = 'prow2';

$start = isset($_REQUEST['start']) ? abs(intval($_REQUEST['start'])) : 0;

$set_default = isset($_GET['def']);
$user_id     = $userdata['user_id'];
$lastvisit   = (!IS_GUEST) ? $userdata['user_lastvisit'] : '';
$search_id   = (isset($_GET['search_id']) && verify_id($_GET['search_id'], SEARCH_ID_LENGTH)) ? $_GET['search_id'] : '';
$session_id  = $userdata['session_id'];

$cat_forum = $tor_to_show = array();
$title_match_sql = $title_match_q = $search_in_forums_csv = '';
$tr_error = $poster_error = false;
$row_num = $tor_count = 0;

$torrents_tbl = BT_TORRENTS_TABLE     .' tor';
$cat_tbl      = CATEGORIES_TABLE      .' c';
$forums_tbl   = FORUMS_TABLE          .' f';
$topics_tbl   = TOPICS_TABLE          .' t';
$users_tbl    = USERS_TABLE           .' u';
$tracker_tbl  = BT_TRACKER_TABLE      .' tr';
$tr_snap_tbl  = BT_TRACKER_SNAP_TABLE .' sn';
$dl_stat_tbl  = BT_DLSTATUS_TABLE     .' dl';

//
// Search options
//
// Key values
$search_all = -1;
$never      = -2;

$sort_asc   = 1;
$sort_desc  = 2;

$ord_posted   = 1;
// $ord_name     = 2;
$ord_compl    = 4;
$ord_repl     = 5;
$ord_views    = 6;
$ord_size     = 7;
$ord_last_p   = 8;
$ord_last_s   = 9;
$ord_seeders  = 10;
$ord_leechers = 11;
// $ord_sp_up    = 12;
// $ord_sp_down  = 13;
$ord_last_dl  = 14;

/* Варианты сортировки */
$order_opt = array(
	$ord_posted => array(
		'lang'   => $lang['IS_REGISTERED'],
		'sql'    => 'tor.reg_time',
		'sphinx' => 'reg_time',
	),
	/*
	$ord_name => array(
		'lang'   => $lang['Bt_Topic_Title'],
		'sql'    => 't.topic_title',
		'sphinx' => 'topic_title',
	),
	*/
	$ord_compl => array(
		'lang'   => 'Количество скачиваний',
		'sql'    => 'tor.complete_count',
		'sphinx' => 'complete_count',
	),
	$ord_seeders => array(
		'lang'   => 'Количество сидов',
		'sql'    => 'tor.seeders',
		'sphinx' => 'seeders',
	),
	$ord_leechers => array(
		'lang'   => 'Количество личей',
		'sql'    => 'tor.leechers',
		'sphinx' => 'leechers',
	),
	/*
	$ord_sp_up => array(
		'lang'   => 'Скорость отдачи',
		'sql'    => 'tor.speed_up',
		'sphinx' => 'speed_up',
	),
	$ord_sp_down => array(
		'lang'   => 'Скорость загрузки',
		'sql'    => 'tor.speed_down',
		'sphinx' => 'speed_down',
	),
	*/
	$ord_repl => array(
		'lang'   => 'Количеству сообщений',
		'sql'    => 't.topic_replies',
		'sphinx' => 'topic_replies',
	),
	$ord_views => array(
		'lang'   => $lang['Bt_Views'],
		'sql'    => 't.topic_views',
		'sphinx' => 'topic_views',
	),
	$ord_size => array(
		'lang'   => $lang['SIZE'],
		'sql'    => 'tor.size',
		'sphinx' => 'size',
	),
	$ord_last_p => array(
		'lang'   => $lang['Bt_Last_post'],
		'sql'    => 't.topic_last_post_id',
		'sphinx' => 'topic_last_post_id',
	),
	$ord_last_s => array(
		'lang'   => 'Последний сид',
		'sql'    => 'tor.seeder_last_seen',
		'sphinx' => 'seeder_last_seen',
	),
	$ord_last_dl => array(
		'lang'   => 'Последнее скачивание',
		'sql'    => 'tor.last_dl_time',
		'sphinx' => 'last_dl_time',
	),
);

$order_select = array();

foreach( $order_opt as $val => $opt )
{
	$order_select[$opt['lang']] = $val;
}

/* Направления сортировки */
$sort_opt = array(
	$sort_asc => array(
		'lang' => $lang['ASC'],
		'sql'  => 'ASC',
	),
	$sort_desc => array(
		'lang' => $lang['DESC'],
		'sql'  => 'DESC',
	),
);

/* За какое время выводить раздачи */
$time_opt = array(
	$search_all => array(
		'lang' => $lang['Bt_All_Days_for'],
		'sql'  => 0,
	),
	1  => array(
		'lang' => $lang['Bt_1_Day_for'],
		'sql'  => TIMENOW - 86400,
	),
	3  => array(
		'lang' => $lang['Bt_3_Day_for'],
		'sql'  => TIMENOW - 86400*3,
	),
	7  => array(
		'lang' => $lang['Bt_7_Days_for'],
		'sql'  => TIMENOW - 86400*7,
	),
	14 => array(
		'lang' => $lang['Bt_2_Weeks_for'],
		'sql'  => TIMENOW - 86400*14,
	),
	30 => array(
		'lang' => $lang['Bt_1_Month_for'],
		'sql'  => TIMENOW - 86400*30,
	),
);

$time_select = array();

foreach( $time_opt as $val => $opt )
{
	$time_select[$opt['lang']] = $val;
}

/**
* Как долго не было сидов на раздаче
* TODO: удалить
*/
$s_not_seen_opt = array(
	$search_all => array(
		'lang' => $lang['Bt_Disregard'],
		'sql'  => 0,
	),
);

$s_not_seen_select = array();

foreach( $s_not_seen_opt as $val => $opt )
{
	$s_not_seen_select[$opt['lang']] = $val;
}

/* Восстанавливаем предыдущие параметры поиска пользователя */
$previous_settings = array();

if( !IS_GUEST )
{
	$sql = '
		SELECT
			tor_search_set,
			last_modified
		FROM
			' . BT_USER_SETTINGS_TABLE . '
		WHERE
			user_id = ' . $user_id . '
		LIMIT
			0, 1';
	if( $row = $db->fetch_row($sql) and $tmp = unserialize($row['tor_search_set']) )
	{
		$previous_settings = $tmp;
	}
	// Touch "last_modified"
	if( $bb_cfg['tr_settings_days_keep'] && ($row['last_modified'] + 86400) < TIMENOW )
	{
		$db->query("UPDATE ". BT_USER_SETTINGS_TABLE ." SET last_modified = ". TIMENOW ." WHERE user_id = $user_id LIMIT 1");
	}
	unset($row, $tmp);
}

$GPC = array(
#	 var_name               key_name def_value   GPC type
	'all_words'     => array('allw', 1,           CHBOX),
	'active'        => array('a',    0,           CHBOX),
	'cat'           => array('c',    null,        REQUEST),
	'dl_cancel'     => array('dla',  0,           CHBOX),
	'dl_compl'      => array('dlc',  0,           CHBOX),
	'dl_down'       => array('dld',  0,           CHBOX),
	'dl_will'       => array('dlw',  0,           CHBOX),
	'forum'         => array('f',    $search_all, REQUEST),
	'my'            => array('my',   0,           CHBOX),
	'new'           => array('new',  0,           CHBOX),
	'title_match'   => array('nm',   null,        REQUEST),
	'order'         => array('o',    $ord_posted, SELECT),
	'poster_id'     => array('pid',  null,        GET),
	'poster_name'   => array('pn',   null,        REQUEST),
	'user_releases' => array('rid',  null,        GET),
	'sort'          => array('s',    $sort_desc,  SELECT),
	'seed_exist'    => array('sd',   0,           CHBOX),
	'show_author'   => array('da',   1,           CHBOX),
	'show_cat'      => array('dc',   0,           CHBOX),
	'show_forum'    => array('df',   1,           CHBOX),
	'show_speed'    => array('ds',   0,           CHBOX),
	's_not_seen'    => array('sns',  $search_all, SELECT),
	'time'          => array('tm',   $search_all, SELECT),
);

// Define all GPC vars with default values
foreach( $GPC as $name => $params )
{
	${"{$name}_key"} = $params[KEY_NAME];
	${"{$name}_val"} = $params[DEF_VAL];
}

if( isset($_GET[$user_releases_key]) )
{
	// Search releases by user
	$_GET[$poster_id_key] = (int) $_GET[$user_releases_key];
	$_REQUEST[$forum_key] = $search_all;
}
elseif( !empty($_REQUEST['max']) )
{
	$_REQUEST[$forum_key] = $search_all;
}
else
{
	// Get "checkbox" and "select" vars
	foreach( $GPC as $name => $params )
	{
		if( $params[GPC_TYPE] == CHBOX )
		{
			checkbox_get_val($params[KEY_NAME], ${"{$name}_val"}, $params[DEF_VAL]);
		}
		elseif( $params[GPC_TYPE] == SELECT )
		{
			select_get_val($params[KEY_NAME], ${"{$name}_val"}, ${"{$name}_opt"}, $params[DEF_VAL]);
		}
	}
}

// Restore torrents list and search settings if we have valid $search_id
$tor_list_ary = array();
$tor_list_sql = '';

if( $search_id )
{
	$row = $bb_cache->get(sprintf('search_%d_%s', SEARCH_TYPE_TRACKER, $session_id));

	if( !$row || $row['search_id'] != $search_id )
	{
		bb_die('Сессия поиска устарела.');
	}

	$previous_settings = unserialize($row['search_settings']);
	$tor_list_sql = $row['search_array'];
	$tor_list_ary = explode(',', $tor_list_sql);
	$tor_count    = count($tor_list_ary);
	unset($row);
}

// Get allowed for searching forums list
if( !$forums = $datastore->get('cat_forums') )
{
	$datastore->update('cat_forums');
	$forums = $datastore->get('cat_forums');
}
$cat_title_html = $forums['cat_title_html'];
$forum_name_html = $forums['forum_name_html'];

$excluded_forums_csv = $user->get_excluded_forums(AUTH_READ);
$allowed_forums = array_diff(explode(',', $forums['tracker_forums']), explode(',', $excluded_forums_csv));

foreach ($allowed_forums as $forum_id)
{
	$f = $forums['f'][$forum_id];
	$cat_forum['c'][$f['cat_id']][] = $forum_id;

	if ($f['forum_parent'])
	{
		$cat_forum['subforums'][$forum_id] = true;
		$cat_forum['forums_with_sf'][$f['forum_parent']] = true;
	}
}
unset($forums);
$datastore->rm('cat_forums');

// Get current search settings
if( !$set_default )
{
	// Search in forum or category
	// Get requested cat_id
	$search_in_forums_fary = array();

	if( $req_cat_id =& $_REQUEST[$cat_key] )
	{
		if( isset($cat_forum['c'][$req_cat_id]) )
		{
			$valid_forums = $cat_forum['c'][$req_cat_id];
			$forum_val = join(',', $valid_forums);
		}
	}
	// Get requested forum_id(s)
	elseif( $req_forums =& $_REQUEST[$forum_key] )
	{
		if( $req_forums != $search_all )
		{
			$req_forums = (array) $req_forums;
			array_deep($req_forums, 'intval');
			$valid_forums = array_intersect($req_forums, $allowed_forums);
			$forum_val = join(',', $valid_forums);
		}
	}
	elseif( isset($previous_settings[$forum_key]) )
	{
		$valid_forums = array_intersect(explode(',', $previous_settings[$forum_key]), $allowed_forums);
		$forum_val = join(',', $valid_forums);

		if( $previous_settings[$forum_key] != -1 )
		{
			$empty_request = false;
		}
	}

	if( $forum_val && $forum_val != $search_all )
	{
		$search_in_forums_csv = $forum_val;
		$search_in_forums_fary = array_flip(explode(',', $forum_val));
	}
	else
	{
		$forum_val = $search_all;
	}

	// Get poster_id
	if( !$my_val )
	{
		$req_poster_id = '';

		if( isset($_GET[$poster_id_key]) && !$search_id )
		{
			$req_poster_id = intval($_GET[$poster_id_key]);
		}
		elseif( isset($_POST[$poster_name_key]) && !$search_id )
		{
			if( $req_poster_name = phpbb_clean_username($_POST[$poster_name_key]) )
			{
				$poster_name_sql = str_replace("\\'", "''", $req_poster_name);

				if( $poster_id = get_user_id($poster_name_sql) )
				{
					$poster_id_val = $poster_id;
					$poster_name_val = html_entity_decode($req_poster_name);
				}
				else
				{
					$poster_name_val = $lang['Bt_User_not_found'];
					$tr_error = $poster_error = true;
				}
			}
		}
		elseif( $search_id && $previous_settings[$poster_id_key] )
		{
			$poster_id_val = intval($previous_settings[$poster_id_key]);
			$poster_name_val = ($previous_settings[$poster_name_key]) ? $previous_settings[$poster_name_key] : '';
		}

		if( $req_poster_id )
		{
			if ($req_poster_id == ANONYMOUS)
			{
				$poster_id_val = ANONYMOUS;
				$poster_name_val = $lang['Guest'];
			}
			elseif( $poster_name_val = get_username($req_poster_id) )
			{
				$poster_name_val = html_entity_decode($poster_name_val);
				$poster_id_val = $req_poster_id;
			}
		}
	}

	if( isset($_REQUEST[$title_match_key]) )
	{
		if( $tmp = mb_substr(trim($_REQUEST[$title_match_key]), 0, $title_match_max_len) )
		{
			$title_match_val = htmlCHR($tmp);

			if( $bb_cfg['sphinx']['enabled'] )
			{
				$tmp = preg_replace('#(?<=\S)\-#', ' ', $tmp);                    // "1-2-3" -> "1 2 3"
				$tmp = preg_replace('#[^\da-zA-Z\x{7f}-\x{ff}\-_*|]#', ' ', $tmp);    // допустимые символы (кроме " которые отдельно)
				$tmp = str_replace('-', ' -', $tmp);                              // - только в начале слова
				$tmp = str_replace('*', '* ', $tmp);                              // * только в конце слова
				$tmp = preg_replace('#\s*\|\s*#', '|', $tmp);                     // "| " -> "|"
				$tmp = preg_replace('#\|+#', ' | ', $tmp);                        // "||" -> "|"
				$tmp = preg_replace('#(?<=\s)[\-*]+\s#', ' ', $tmp);              // одиночные " - ", " * "
				$tmp = trim($tmp, ' -|');
				$title_match_q = str_compact($tmp);
			}
			else
			{
				$title_match_q   = str_compact($tmp);
				$title_match_sql = clean_text_match($tmp, $all_words_val, false, false);
			}
		}
	}
}

$dl_status = array();
if ($dl_cancel_val) $dl_status[] = DL_STATUS_CANCEL;
if ($dl_compl_val)  $dl_status[] = DL_STATUS_COMPLETE;
if ($dl_down_val)   $dl_status[] = DL_STATUS_DOWN;
if ($dl_will_val)   $dl_status[] = DL_STATUS_WILL;
$dl_status_csv = join(',', $dl_status);

// Switches
$only_new    = ($new_val && !IS_GUEST);
$seed_exist  = (bool) $seed_exist_val;
$only_active = ($active_val || $seed_exist);
$dl_search   = ($dl_status && !IS_GUEST);
$only_my     = ($my_val && !IS_GUEST && !$dl_search);
$prev_days   = ($time_val != $search_all);
$poster_id   = (bool) $poster_id_val;
$title_match = (bool) $title_match_sql;
$s_not_seen  = ($s_not_seen_val != $search_all);

$hide_cat    = intval(!$show_cat_val);
$hide_forum  = intval(!$show_forum_val);
$hide_author = intval(!$show_author_val);
$hide_speed  = intval(!$show_speed_val);

if ($s_not_seen_val != $search_all)
{
	$seed_exist_val = 0;
}
if ($seed_exist_val)
{
	$active_val = 1;
}
if ($dl_search)
{
	$my_val = 0;
}

if ($allowed_forums)
{
	// Save current search settings
	$save_in_db = array(
		'all_words',
		'active',
		'dl_cancel',
		'dl_compl',
		'dl_down',
		'dl_will',
		'forum',
		'my',
		'new',
		'order',
		'poster_id',
		'poster_name',
		's_not_seen',
		'seed_exist',
		'show_author',
		'show_cat',
		'show_forum',
		'show_speed',
		'sort',
		'time',
	);
	$curr_set = array();
	foreach ($save_in_db as $name)
	{
		$curr_set[${"{$name}_key"}] = ${"{$name}_val"};
	}
	$curr_set_sql = $db->escape(serialize($curr_set));
	$curr_set_sph = serialize($curr_set);

	// Store search settings
	if (!$tr_error && !IS_GUEST && array_diff_assoc($curr_set, $previous_settings))
	{
		$columns = 'user_id,  tor_search_set, last_modified';
		$values = "$user_id, '$curr_set_sql', ". TIMENOW;

		$db->query("REPLACE INTO ". BT_USER_SETTINGS_TABLE ." ($columns) VALUES ($values)");
	}
	unset($columns, $values, $curr_set, $previous_settings);

	// Get torrents list
	if( !$tr_error && !$tor_list_sql )
	{
		$reg_time      = $time_opt[$time_val]['sql'];
		$poster_id_sql = (int) $poster_id_val;

		/**
		* Если параметры не по умолчанию, то надо производить поисковый запрос
		*/
		if( $my_val || $seed_exist || $new_val || $order_val != $ord_posted || $sort_val != $sort_desc || $time_val != $search_all )
		{
			$empty_request = false;
		}

		/* Иначе берем кэшированный список 500 последних раздач */
		if( $empty_request )
		{
			$tor_list_ary = $bb_cache->get('tracker_last');
		}

		if( empty($tor_list_ary) && $bb_cfg['sphinx']['enabled'] )
		{
			require(SITE_DIR . 'includes/db/sphinx.php');

			$sphinx = new db_sphinx();
			$sphinx->connect($bb_cfg['sphinx']['host'], $bb_cfg['sphinx']['port'], $bb_cfg['sphinx']['socket']);

			if( $title_match_q != '' && false === strpos($title_match_q, ' ') && false === strpos($title_match_q, '*') )
			{
				$query = $title_match_q . '*';
			}
			else
			{
				$query = $title_match_q;
			}

			$sql = "
				SELECT
					id
				FROM
					" . $bb_cfg['sphinx']['db_torrents'] . "
				WHERE
					MATCH('" . $query . "')";
			if( $search_in_forums_csv )
			{
				$sql .= ' AND forum_id IN (' . $search_in_forums_csv . ')';
			}
			if( $excluded_forums_csv )
			{
				$sql .= ' AND forum_id NOT IN(' . $excluded_forums_csv . ')';
			}
			if( $poster_id )
			{
				$sql .= ' AND topic_poster = ' . $poster_id_sql;
			}
			if( $only_new )
			{
				$sql .= ' AND reg_time > ' . $lastvisit;
			}
			if( $prev_days )
			{
				$sql .= ' AND reg_time > ' . $reg_time;
			}
			/**
			* Не реализовано
			*
			if ($s_not_seen)
			{
				$SQL['WHERE'][] = "tor.seeder_last_seen $s_seen_sign $s_seen_time $s_seen_exclude";
			}
			*/
			if( $only_my )
			{
				$sql .= ' AND topic_poster = ' . $user_id;
			}
			if( $only_active || $seed_exist )
			{
				$sql .= ' AND seeders > 0';
			}
			$sql .= ' ORDER BY ' . $order_opt[$order_val]['sphinx'] . ' ' . $sort_opt[$sort_val]['sql'];
			$sql .= ' LIMIT 0, ' . $tor_search_limit;
			$sql .= ' OPTION ranker = none';

			$result = $sphinx->query($sql);

			while( $row = $sphinx->fetchrow($result) )
			{
				$tor_list_ary[] = $row['id'];
			}

			$sphinx->freeresult($result);
			$sphinx->close();

			$tor_list_sql = join(',', $tor_list_ary);
			$tor_count = sizeof($tor_list_ary);

			if( $empty_request )
			{
				$bb_cache->set('tracker_last', $tor_list_ary, 300);
			}

			if( $title_match_q && $userdata['user_id'] != 2 )
			{
				$sql = 'INSERT INTO ' . SEARCH_QUERIES_TABLE . ' (user_id, search_query, search_time, search_results) VALUES (' . $user_id . ', "' . $db->escape($title_match_q) . '", ' . time() . ', ' . $tor_count . ')';
				$db->sql_query($sql);
			}
		}
		elseif( empty($tor_list_ary) )
		{
			$s_seen_time      = $s_not_seen_opt[$s_not_seen_val]['sql'];
			$s_seen_sign      = ($s_not_seen_val == $never) ? '=' : '<';
			$s_seen_exclude   = ($s_not_seen_val == $never) ? '' : "AND tor.seeder_last_seen != 0";
			$search_bool_mode = ($bb_cfg['bt_search_bool_mode']) ? " IN BOOLEAN MODE" : '';
			$order_by_peers   = ($order_val == $ord_seeders || $order_val == $ord_leechers);
			$order_by_speed   = ($order_val == $ord_sp_up || $order_val == $ord_sp_down);

			$join_t  = in_array($order_val, array($ord_name, $ord_repl, $ord_views, $ord_last_p, $title_match));
			$join_sn = ($only_active || $order_by_peers || $order_by_speed);
			$join_dl = $dl_search;

			// Start building SQL
			$SQL = $db->get_empty_sql_array();

			// SELECT
			$SQL['SELECT'][] = "tor.topic_id";

			// FROM
			$SQL['FROM'][] = $torrents_tbl;

			if ($join_t)
			{
				$SQL['INNER JOIN'][] = "$topics_tbl ON(t.topic_id = tor.topic_id)";
			}
			/*
			if ($join_sn)
			{
				$SQL['LEFT JOIN'][] = "$tr_snap_tbl ON(sn.topic_id = tor.topic_id)";
			}
			*/
			if ($join_dl)
			{
				$SQL['INNER JOIN'][] = "$dl_stat_tbl ON(
						dl.topic_id = tor.topic_id
					AND dl.user_id = $user_id
					AND dl.user_status IN($dl_status_csv)
				)";
			}

			// WHERE
			if ($search_in_forums_csv)
			{
				$SQL['WHERE'][] = "tor.forum_id IN($search_in_forums_csv)";
			}
			if ($excluded_forums_csv)
			{
				$SQL['WHERE'][] = "tor.forum_id NOT IN($excluded_forums_csv)";
			}
			if ($poster_id)
			{
				$SQL['WHERE'][] = "tor.poster_id = $poster_id_sql";
			}
			if ($only_new)
			{
				$SQL['WHERE'][] = "tor.reg_time > $lastvisit";
			}
			if ($prev_days)
			{
				$SQL['WHERE'][] = "tor.reg_time > $reg_time";
			}
			if ($s_not_seen)
			{
				$SQL['WHERE'][] = "tor.seeder_last_seen $s_seen_sign $s_seen_time $s_seen_exclude";
			}
			if ($only_my)
			{
				$SQL['WHERE'][] = "tor.poster_id = $user_id";
			}
			if ($only_active)
			{
				$SQL['WHERE'][] = "tor.topic_id IS NOT NULL";
			}
			if ($seed_exist)
			{
				$SQL['WHERE'][] = "tor.seeders >= 1";
			}
			if ($title_match)
			{
				$SQL['WHERE'][] = "MATCH (t.topic_title) AGAINST ('$title_match_sql'". $search_bool_mode .")";
			}

			// ORDER
			$SQL['ORDER BY'][] = "{$order_opt[$order_val]['sql']} {$sort_opt[$sort_val]['sql']}";

			// LIMIT
			$SQL['LIMIT'][] = $tor_search_limit;

			foreach ($db->fetch_rowset($SQL) as $row)
			{
				$tor_list_ary[] = $row['topic_id'];
			}
			$tor_list_sql = join(',', $tor_list_ary);
			$tor_count = sizeof($tor_list_ary);

			if( $empty_request )
			{
				$bb_cache->set('tracker_last', $tor_list_ary, 300);
			}

			if( $title_match_q )
			{
				$sql = 'INSERT INTO ' . SEARCH_QUERIES_TABLE . ' (user_id, search_query, search_time, search_results) VALUES (' . $user_id . ', "' . $db->escape($title_match_q) . '", ' . time() . ', ' . $tor_count . ')';
				$db->sql_query($sql);
			}
		}
		elseif( !empty($tor_list_ary) )
		{
			$tor_list_sql = join(',', $tor_list_ary);
			$tor_count = sizeof($tor_list_ary);
		}
	}

	if( !$tor_list_sql || $start > $tor_count )
	{
		$template->assign_vars(array(
			'TOR_NOT_FOUND' => true,
			'NO_MATCH_MSG'  => $lang['No_match'])
		);
	}
	else
	{
		// Save result in DB
		if( $tor_count > $per_page && !$search_id )
		{
			$search_id = make_rand_str(SEARCH_ID_LENGTH);
			$search_type = SEARCH_TYPE_TRACKER;

			$bb_cache->set(sprintf('search_%d_%s', SEARCH_TYPE_TRACKER, $session_id), array(
				'search_id'       => $search_id,
				'search_settings' => $curr_set_sph,
				'search_array'    => $tor_list_sql
			), 1800);
		}
		unset($columns, $values, $curr_set_sql, $tor_list_sql);

		$tor_to_show = ($tor_count > $per_page) ? array_slice($tor_list_ary, $start, $per_page) : $tor_list_ary;

		if (!$tor_to_show = join(',', $tor_to_show))
		{
			bb_die($lang['No_search_match']);
		}

		// SELECT
		$select = "
			SELECT
				tor.topic_id, tor.post_id, tor.attach_id, tor.size, tor.reg_time, tor.complete_count, tor.seeder_last_seen, tor.tor_status, tor.seeders, tor.leechers,
				t.topic_title, t.topic_replies, t.topic_views
		";
		$select .= (!$hide_speed)  ? ", tor.speed_up, tor.speed_down" : '';
		$select .= (!$hide_forum)  ? ", tor.forum_id" : '';
		$select .= (!$hide_cat)    ? ", f.cat_id" : '';
		$select .= (!$hide_author) ? ", tor.poster_id, u.username" : '';
		$select .= (!IS_GUEST)     ? ", dl.user_status AS dl_status" : '';

		// FROM
		$from = "
			FROM $torrents_tbl
			LEFT JOIN $topics_tbl ON(t.topic_id = tor.topic_id)
		";
		$from .= (!$hide_cat) ? "
			LEFT JOIN $forums_tbl ON(f.forum_id = t.forum_id)
		" : '';
		$from .= (!$hide_author) ? "
			LEFT JOIN $users_tbl ON(u.user_id = tor.poster_id)
		" : '';
		$from .= (!IS_GUEST) ? "
			LEFT JOIN $dl_stat_tbl ON(dl.topic_id = tor.topic_id AND dl.user_id = $user_id)
		" : '';
		// $from .= "LEFT JOIN $tr_snap_tbl ON(sn.topic_id = tor.topic_id)";

		// WHERE
		$where = "
			WHERE tor.topic_id IN($tor_to_show)
		";

		// ORDER
		$order = "ORDER BY ". $order_opt[$order_val]['sql'];

		// SORT
		$sort = $sort_opt[$sort_val]['sql'];

		// LIMIT
		$limit = "LIMIT $per_page";

		$sql = "
			$select
			$from
			$where
			$order
				$sort
			$limit
		";

		// Build torrents table
		foreach ($db->fetch_rowset($sql) as $tor)
		{
			$dl = isset($tor['speed_down']) ? $tor['speed_down'] : 0;
			$ul = isset($tor['speed_up']) ? $tor['speed_up'] : 0;

			$seeds  = $tor['seeders'];
			$leechs = $tor['leechers'];
			$s_last = $tor['seeder_last_seen'];
			$att_id = $tor['attach_id'];
			$size   = $tor['size'];
			$compl  = $tor['complete_count'];
			$dl_sp  = ($dl) ? humn_size($dl, 0, 'KБ') .'/c' : '0 KБ/c';
			$ul_sp  = ($ul) ? humn_size($ul, 0, 'KБ') .'/c' : '0 KБ/c';

			$dl_class  = isset($tor['dl_status']) ? $dl_link_css[$tor['dl_status']] : 'genmed';
			$row_class = !($row_num & 1) ? $row_class_1 : $row_class_2;
			$row_num++;

			$cat_id    = (!$hide_cat && isset($tor['cat_id'])) ? $tor['cat_id'] : '';
			$forum_id  = (!$hide_forum && isset($tor['forum_id'])) ? $tor['forum_id'] : '';
			$poster_id = (!$hide_author && isset($tor['poster_id'])) ? $tor['poster_id'] : '';

			$template->assign_block_vars('tor', array(
				'CAT_ID'       => $cat_id,
				'CAT_TITLE'    => ($cat_id) ? $cat_title_html[$cat_id] : '',
				'FORUM_ID'     => $forum_id,
				'FORUM_NAME'   => ($forum_id) ? $forum_name_html[$forum_id] : '',
				'TOPIC_ID'     => $tor['topic_id'],
				'TOPIC_TITLE'  => wbr($tor['topic_title']),
				'POST_ID'      => $tor['post_id'],
				'POSTER_ID'    => $poster_id,
				'USERNAME'     => isset($tor['username']) ? wbr($tor['username']) : '',

				'ROW_CLASS'    => $row_class,
				'ROW_NUM'      => $row_num,
				'DL_CLASS'     => $dl_class,
				'IS_NEW'       => (!IS_GUEST && $tor['reg_time'] > $lastvisit),
				'USER_AUTHOR'  => (!IS_GUEST && $poster_id == $user_id),

				'ATTACH_ID'    => $att_id,
				'TOR_FROZEN'   => ($tor['tor_status'] == TOR_STATUS_FROZEN || $tor['tor_status'] == 3 || $tor['tor_status'] == 4 || $tor['tor_status'] == 7),

				// torrent status mod
				'TOR_STATUS'   => ($tor['tor_status']),
				//end torrent status mod

				'TOR_SIZE_RAW' => $size,
				'TOR_SIZE'     => humn_size($size),
				'UL_SPEED'     => $ul_sp,
				'DL_SPEED'     => $dl_sp,
				'SEEDS'        => ($seeds) ? $seeds : 0,
				'SEEDS_TITLE'  => ($seeds) ? 'Seeders' : (" Last seen: \n ". (($s_last) ? create_date($date_format, $s_last) : 'Never')),
				'LEECHS'       => ($leechs) ? $leechs : 0,
				'COMPLETED'    => ($compl) ? $compl : 0,
				'REPLIES'      => $tor['topic_replies'],
				'VIEWS'        => $tor['topic_views'],
				'ADDED_RAW'    => $tor['reg_time'],
				'ADDED_TIME'   => create_date($time_format, $tor['reg_time']),
				'ADDED_DATE'   => create_date($date_format, $tor['reg_time']),
				'HOT'          => $ctime - $tor['reg_time'] < 86400
			));
		}
	}
}
else
{
	$template->assign_vars(array(
		'TOR_NOT_FOUND' => true,
		'NO_MATCH_MSG'  => $lang['Bt_No_searchable_forums'])
	);
}

// Pagination
if( $tor_count )
{
	$base_url = "$tracker_url?search_id=$search_id";
	$base_url .= ($title_match_val) ? sprintf('&%s=%s', $title_match_key, $title_match_val) : '';
	$search_matches = ($tor_count == 1) ? sprintf($lang['Found_search_match'], $tor_count) : sprintf($lang['Found_search_matches'], $tor_count);
	$search_max = "(max: $tor_search_limit)";

	$template->assign_vars(array(
		'MATCHES'     => $search_matches,
		'SERACH_MAX'  => $search_max,
		'PAGINATION'  => generate_pagination($base_url, $tor_count, $per_page, $start),
		'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / $per_page) + 1), ceil($tor_count / $per_page)))
	);
}

if( empty($cat_forum) )
{
	message_die(GENERAL_MESSAGE, $lang['Bt_No_searchable_forums']);
}

// Forum select
$opt = '';

if( isset( $cat_forum['c'] ) )
{
	foreach( $cat_forum['c'] as $cat_id => $forums_ary )
	{
		$opt .= '<optgroup label="&nbsp;'. $cat_title_html[$cat_id] ."\">\n";

		foreach ($forums_ary as $forum_id)
		{
			$forum_name = $forum_name_html[$forum_id];
			if (mb_strlen($forum_name) > $max_forum_name_len)
			{
				$forum_name = mb_substr($forum_name, 0, $max_forum_name_len) . '..';
			}
			$style = '';
			if (!isset($cat_forum['subforums'][$forum_id]))
			{
				$class = 'root_forum';
				$class .= isset($cat_forum['forums_with_sf'][$forum_id]) ? ' has_sf' : '';
				$style = " class=\"$class\"";
			}
			$selected = (isset($search_in_forums_fary[$forum_id])) ? HTML_SELECTED : '';
			$opt .= '<option id="fs-'. $forum_id .'" value="'. $forum_id .'"'. $style . $selected .'>'. (isset($cat_forum['subforums'][$forum_id]) ? HTML_SF_SPACER : '') . $forum_name ."&nbsp;</option>\n";
		}

		$opt .= "</optgroup>\n";
	}
}
$search_all_opt = '<option id="f-' . $search_all . '" value="'. $search_all .'"'. (($forum_val == $search_all) ? HTML_SELECTED : '') .'>&nbsp;'. htmlCHR($lang['All_available']) ."</option>\n";
$cat_forum_select = "\n".'<select id="fs-main" style="width: 100%;" name="'. $forum_key .'[]" multiple="multiple" size="'. $forum_select_size ."\">\n". $search_all_opt . $opt ."</select>\n";

// Sort dir
$template->assign_vars(array(
	'SORT_NAME'         => $sort_key,
	'SORT_ASC'          => $sort_asc,
	'SORT_DESC'         => $sort_desc,
	'SORT_ASC_CHECKED'  => ($sort_val == $sort_asc) ? HTML_CHECKED : '',
	'SORT_DESC_CHECKED' => ($sort_val == $sort_desc) ? HTML_CHECKED : '',
));

// Displaying options
$template->assign_vars(array(
	'SHOW_CAT_CHBOX'    => build_checkbox($show_cat_key,    $lang['Bt_Show_Cat'],        $show_cat_val),
	'SHOW_FORUM_CHBOX'  => build_checkbox($show_forum_key,  $lang['Bt_Show_Forum'],      $show_forum_val),
	'SHOW_AUTHOR_CHBOX' => build_checkbox($show_author_key, $lang['Bt_Show_Author'],     $show_author_val),
	'SHOW_SPEED_CHBOX'  => build_checkbox($show_speed_key,  $lang['Bt_Show_Speed'],      $show_speed_val),
	'ALL_WORDS_CHBOX'   => build_checkbox($all_words_key,   $lang['Search_all_words'],   $all_words_val),

	'ONLY_MY_CHBOX'     => build_checkbox($my_key,          $lang['Bt_Only_My'],         $only_my,       IS_GUEST),
	'ONLY_ACTIVE_CHBOX' => build_checkbox($active_key,      $lang['Bt_Only_Active'],     $active_val),
	'SEED_EXIST_CHBOX'  => build_checkbox($seed_exist_key,  $lang['Bt_Seed_exist'],      $seed_exist),
	'ONLY_NEW_CHBOX'    => build_checkbox($new_key,         $lang['Bt_Only_New'],        $only_new,      IS_GUEST),

	'DL_CANCEL_CHBOX'   => build_checkbox($dl_cancel_key,   $lang['SEARCH_DL_CANCEL'],   $dl_cancel_val, IS_GUEST, 'dlCancel'),
	'DL_COMPL_CHBOX'    => '<input type="checkbox" name="dlc" value="0" disabled="disabled" />&nbsp;<a href="search.php?dlu=' . $user_id . '&amp;dlc=1" class="gen">' . $lang['SEARCH_DL_COMPLETE_DOWNLOADS'] . '&nbsp;</a>',
	'DL_DOWN_CHBOX'     => build_checkbox($dl_down_key,     $lang['SEARCH_DL_DOWN'],     $dl_down_val,   IS_GUEST, 'dlDown'),
	'DL_WILL_CHBOX'     => '<input type="checkbox" name="dlw" value="0" disabled="disabled" />&nbsp;<a href="search.php?dlu=' . $user_id . '&amp;dlw=1" class="gen">' . $lang['SEARCH_DL_WILL_DOWNLOADS'] . '&nbsp;</a>',

	'POSTER_NAME_NAME' => $poster_name_key,
	'POSTER_NAME_VAL'  => htmlCHR($poster_name_val),
	'TITLE_MATCH_NAME' => $title_match_key,
	'TITLE_MATCH_VAL'  => $title_match_val,

	'AJAX_TOPICS'      => $user->opt_js['tr_t_ax'],
	'U_SEARCH_USER'    => "search.php?mode=searchuser&input_name=$poster_name_key",
));

// Hidden fields
$save_through_pages = array(
	'all_words',
	'active',
	'dl_cancel',
	'dl_compl',
	'dl_down',
	'dl_will',
	'my',
	'new',
	'seed_exist',
	'show_author',
	'show_cat',
	'show_forum',
	'show_speed',
);

$hidden_fields = array();

foreach( $save_through_pages as $name )
{
	$hidden_fields['prev_'. ${"{$name}_key"}] = ${"{$name}_val"};
}

// Set colspan
$tor_colspan = $tor_colspan - $hide_cat - $hide_forum - $hide_author - $hide_speed;

$template->assign_vars(array(
	'PAGE_TITLE'        => $lang['TRACKER'],
	'S_HIDDEN_FIELDS'   => build_hidden_fields($hidden_fields),
	'CAT_FORUM_SELECT'  => $cat_forum_select,
	'ORDER_SELECT'      => build_select($order_key, $order_select, $order_val),
	'TIME_SELECT'       => build_select($time_key, $time_select, $time_val),
	'S_NOT_SEEN_SELECT' => build_select($s_not_seen_key, $s_not_seen_select, $s_not_seen_val),
	'TOR_SEARCH_ACTION' => $tracker_url,
	'TOR_COLSPAN'       => $tor_colspan,
	'TITLE_MATCH_MAX'   => $title_match_max_len,
	'POSTER_NAME_MAX'   => $poster_name_max_len,
	'POSTER_ERROR'      => $poster_error,
	'SEARCH_TEXT'       => $title_match_val,
	'SHOW_SEARCH_OPT'   => (bool) $allowed_forums,
	'SHOW_CAT'          => $show_cat_val,
	'SHOW_FORUM'        => $show_forum_val,
	'SHOW_AUTHOR'       => $show_author_val,
	'SHOW_SPEED'        => $show_speed_val,

	'TR_CAT_URL'        => "$tracker_url?$cat_key=",
	'TR_FORUM_URL'      => "$tracker_url?" . (($title_match_val) ? sprintf('%s=%s&', $title_match_key, $title_match_val) : '') . "$forum_key=",
	'TR_POSTER_URL'     => "$tracker_url?$poster_id_key=",
));

print_page('tracker.tpl');
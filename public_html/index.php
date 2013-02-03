<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('BB_SCRIPT', 'index');
require('common.php');

$page_cfg['load_tpl_vars'] = array(
	'post_icons'
);

$show_last_topic    = true;
$last_topic_max_len = 28;
$show_online_users  = true;
$show_subforums     = true;

$datastore->enqueue(array(
	'stats',
	'moderators',
	'stats_update',
));

if( $bb_cfg['show_latest_news'] )
{
	$datastore->enqueue('latest_news');
}
if( $bb_cfg['t_last_added_num'] )
{
	$datastore->enqueue('last_added');
}

// Init userdata
$user->session_start();

// Init main vars
$viewcat = isset($_GET['c']) ? (int) $_GET['c'] : 0;
$lastvisit = (IS_GUEST) ? TIMENOW : $userdata['user_lastvisit'];

// Caching output
$req_page = 'index_page';
$req_page .= ($viewcat) ? "_c{$viewcat}" : '';

/*
define('REQUESTED_PAGE', $req_page);
caching_output(IS_GUEST, 'send', REQUESTED_PAGE . '_guest');
*/

// Topics read tracks
$tracking_topics = get_tracks('topic');
$tracking_forums = get_tracks('forum');

// Statistics
$stats = $datastore->get('stats');

// Forums data
$forums = $datastore->get('cat_forums');
$cat_title_html = $forums['cat_title_html'];
$forum_name_html = $forums['forum_name_html'];

$anon = ANONYMOUS;
$excluded_forums_csv = $user->get_excluded_forums(AUTH_VIEW);
$only_new = $user->opt_js['only_new'];

// Validate requested category id
if( $viewcat AND !$viewcat =& $forums['c'][$viewcat]['cat_id'] )
{
	redirect('/');
}
// Forums
$forums_join_sql = 'f.cat_id = c.cat_id';
$forums_join_sql .= ($viewcat) ? "
	AND f.cat_id = $viewcat
" : '';
$forums_join_sql .= ($excluded_forums_csv) ? "
	AND f.forum_id NOT IN($excluded_forums_csv)
	AND f.forum_parent NOT IN($excluded_forums_csv)
" : '';

// Posts
$posts_join_sql = "p.post_id = f.forum_last_post_id";
$posts_join_sql .= ($only_new == ONLY_NEW_POSTS) ? "
	AND p.post_time > $lastvisit
" : '';
$join_p_type = ($only_new == ONLY_NEW_POSTS) ? 'INNER JOIN' : 'LEFT JOIN';

// Topics
$topics_join_sql = "t.topic_last_post_id = p.post_id";
$topics_join_sql .= ($only_new == ONLY_NEW_TOPICS) ? "
	AND t.topic_time > $lastvisit
" : '';
$join_t_type = ($only_new == ONLY_NEW_TOPICS) ? 'INNER JOIN' : 'LEFT JOIN';

$sql = "
	SELECT
		f.cat_id, f.forum_id, f.forum_status, f.forum_parent, f.show_on_index, f.forum_redirects,
		p.post_id AS last_post_id, p.post_time AS last_post_time,
		t.topic_id AS last_topic_id, t.topic_title AS last_topic_title,
		u.user_id AS last_post_user_id,
		IF(p.poster_id = $anon, p.post_username, u.username) AS last_post_username
	FROM       ". CATEGORIES_TABLE ." c
	INNER JOIN ". FORUMS_TABLE     ." f ON($forums_join_sql)
$join_p_type ". POSTS_TABLE      ." p ON($posts_join_sql)
$join_t_type ". TOPICS_TABLE     ." t ON($topics_join_sql)
	 LEFT JOIN ". USERS_TABLE      ." u ON(u.user_id = p.poster_id)
	ORDER BY c.cat_order, f.forum_order
";
$cat_forums = array();

$replace_in_parent = array(
	'last_post_id',
	'last_post_time',
	'last_post_user_id',
	'last_post_username',
	'last_topic_title',
	'last_topic_id',
);

foreach ($db->fetch_rowset($sql) as $row)
{
	if (!$cat_id = $row['cat_id'] OR !$forum_id = $row['forum_id'])
	{
		continue;
	}

	if ($parent_id = $row['forum_parent'])
	{
		if (!$parent =& $cat_forums[$cat_id]['f'][$parent_id])
		{
			$parent = $forums['f'][$parent_id];
			$parent['last_post_time'] = 0;
		}
		if ($row['last_post_time'] > $parent['last_post_time'])
		{
			foreach ($replace_in_parent as $key)
			{
				$parent[$key] = $row[$key];
			}
		}
		if ($show_subforums && $row['show_on_index'])
		{
			$parent['last_sf_id'] = $forum_id;
		}
		else
		{
			continue;
		}
	}
	else
	{
		$f =& $forums['f'][$forum_id];
		$row['forum_desc']   = $f['forum_desc'];
		$row['forum_posts']  = $f['forum_posts'];
		$row['forum_topics'] = $f['forum_topics'];
	}

	$cat_forums[$cat_id]['f'][$forum_id] = $row;
}
unset($forums);
$datastore->rm('cat_forums');

// Obtain list of moderators
/*
$moderators = array();
$mod = $datastore->get('moderators');

foreach ($mod['mod_users'] as $forum_id => $user_ids)
{
	foreach ($user_ids as $user_id)
	{
		$moderators[$forum_id][] = '<a href="'. (PROFILE_URL . $user_id) .'">'. $mod['name_users'][$user_id] .'</a>';
	}
}
foreach ($mod['mod_groups'] as $forum_id => $group_ids)
{
	foreach ($group_ids as $group_id)
	{
		$moderators[$forum_id][] = '<a href="'. (GROUP_URL . $group_id) .'">'. $mod['name_groups'][$group_id] .'</a>';
	}
}

unset($mod);
$datastore->rm('moderators');
*/

if( !$forums_count = count($cat_forums) AND $viewcat )
{
	redirect('/');
}

$template->assign_vars(array(
	'SHOW_FORUMS'           => $forums_count,
	'PAGE_TITLE'            => $lang['Index'],
	'NO_FORUMS_MSG'         => ($only_new) ? $lang['NO_NEW_POSTS'] : $lang['NO_FORUMS'],

	'TOTAL_POSTS'           => num_format($stats['postcount']),
	'TOTAL_USERS'           => num_format($stats['usercount']),
	'NEWEST_USER'           => sprintf($lang['Newest_user'], '<a href="'. PROFILE_URL . $stats['newestuser']['user_id'] .'">', $stats['newestuser']['username'], '</a>'),

	'FORUM_IMG'             => $images['forum'],
	'FORUM_NEW_IMG'         => $images['forum_new'],
	'FORUM_LOCKED_IMG'      => $images['forum_locked'],

	'SHOW_ONLY_NEW_MENU'    => true,
	'ONLY_NEW_POSTS_ON'     => ($only_new == ONLY_NEW_POSTS),
	'ONLY_NEW_TOPICS_ON'    => ($only_new == ONLY_NEW_TOPICS),

	'U_SEARCH_NEW'          => 'search.php?new=1',
	'U_SEARCH_SELF_BY_MY'   => 'search.php?uid=' . $userdata['user_id'] . '&amp;o=1',
	'U_SEARCH_LATEST'       => 'search.php?search_id=latest',
	'U_SEARCH_UNANSWERED'   => 'search.php?search_id=unanswered',

	'SHOW_LAST_TOPIC'       => $show_last_topic)
);

/**
* Последние раздачи
*/
if( $bb_cfg['t_last_added_num'] )
{
	$template->assign_vars(array(
		'LAST_ADDED_ON' => true)
	);

	$last_added = $datastore->get('last_added');
	foreach( $last_added as $last_add )
	{
		$template->assign_block_vars('t_last_added', array(
			'LEECHERS'    => $last_add['leechers'],
			'SEEDERS'     => $last_add['seeders'],
			'SPEED'       => ( $last_add['speed_up'] ) ? humn_size($last_add['speed_up']) . '/сек' : 0,
			'SHORT_TITLE' => ( mb_strlen($last_add['topic_title']) >= 88 ) ? mb_substr($last_add['topic_title'], 0, 88) . '...' : $last_add['topic_title'],
			'TIME'        => delta_time($last_add['reg_time']),
			'IMAGE'       => ( $last_add['forum_icon'] ) ? $last_add['forum_icon'] : 'film',
			'TITLE'       => $last_add['topic_title'],
			'TOPIC_ID'    => $last_add['topic_id'],
			'FORUM'       => $last_add['forum_name'],
			'FORUM_ID'    => $last_add['forum_id'],
			'POSTER'      => $last_add['username'],
			'POSTER_ID'   => $last_add['user_id'],

			'U_DOWNLOAD' => append_sid('download.php?id=' . $last_add['attach_id']))
		);
	}
}

// Build index page
foreach ($cat_forums as $cid => $c)
{
	$template->assign_block_vars('c', array(
		'CAT_ID'    => $cid,
		'CAT_TITLE' => $cat_title_html[$cid],
		'U_VIEWCAT' => "/?c=$cid",
	));

	foreach ($c['f'] as $fid => $f)
	{
		if (!$fname_html =& $forum_name_html[$fid])
		{
			continue;
		}
		$is_sf = $f['forum_parent'];

		$new = is_unread($f['last_post_time'], $f['last_topic_id'], $f['forum_id']) ? '_new' : '';
		$folder_image = ($is_sf) ? $images["icon_minipost{$new}"] : $images["forum{$new}"];

		if ($f['forum_status'] == FORUM_LOCKED)
		{
			$folder_image = ($is_sf) ? $images['icon_minipost'] : $images['forum_locked'];
		}

		if ($is_sf)
		{
			$template->assign_block_vars('c.f.sf', array(
				'NEW'		=> is_unread($f['last_post_time'], $f['last_topic_id'], $f['forum_id']),
				'SF_ID'		=> $fid,
				'SF_NAME'	=> $fname_html,
				'SF_IMG'	=> $folder_image,
			));
			continue;
		}

		$template->assign_block_vars('c.f',	array(
			'FORUM_FOLDER_IMG' => $folder_image,

			'FORUM_ID'   => $fid,
			'FORUM_NAME' => $fname_html,
			'FORUM_DESC' => $f['forum_desc'],
			'POSTS'      => commify($f['forum_posts']),
			'TOPICS'     => commify($f['forum_topics']),
			'LAST_SF_ID' => isset($f['last_sf_id']) ? $f['last_sf_id'] : null,
			'REDIRECTS'  => isset($f['forum_redirects']) ? num_format($f['forum_redirects']) : 0,

			'MODERATORS'  => isset($moderators[$fid]) ? join(', ', $moderators[$fid]) : '',
			'FORUM_FOLDER_ALT' => ($new) ? 'new' : 'old',
		));

		if ($f['last_post_id'])
		{
			$template->assign_block_vars('c.f.last', array(
				'LAST_TOPIC_ID'       => $f['last_topic_id'],
				'LAST_TOPIC_TIP'      => $f['last_topic_title'],
				'LAST_TOPIC_TITLE'    => wbr(str_short($f['last_topic_title'], $last_topic_max_len)),

				'LAST_POST_TIME'      => create_date($bb_cfg['last_post_date_format'], $f['last_post_time']),
				'LAST_POST_USER_ID'   => ($f['last_post_user_id'] != ANONYMOUS) ? $f['last_post_user_id'] : false,
				'LAST_POST_USER_NAME' => ($f['last_post_username']) ? str_short($f['last_post_username'], 15) : $lang['Guest'],
			));
		}
	}
}

// Set tpl vars for bt_userdata
if( $bb_cfg['bt_show_dl_stat_on_index'] && !IS_GUEST )
{
	show_bt_userdata($userdata['user_id']);
}

/**
* Последние новости
*/
if( $bb_cfg['show_latest_news'] )
{
	$latest_news = $datastore->get('latest_news');

	$template->assign_vars(array(
		'SHOW_LATEST_NEWS' => true,
	));

	foreach( $latest_news as $news )
	{
		$template->assign_block_vars('news', array(
			'NEWS_TOPIC_ID' => $news['topic_id'],
			'NEWS_TITLE'    => $news['topic_title'],
			'NEWS_TIME'     => create_date('d-M', $news['topic_time']),
			'NEWS_IS_NEW'   => $news['topic_time'] > $lastvisit,
		));
	}
}

// Display page
define('SHOW_ONLINE', $show_online_users);

$data_stats = $datastore->get('stats_update');

$template->assign_vars(array(
	'MAXIMUM_VISITORS'      => $bb_cfg['maximum_visitors'],
	'MAXIMUM_VISITORS_DATE' => $bb_cfg['maximum_visitors_date'],

	'SU_TOTAL_DL_UL'     => humn_size($data_stats['download'] * 2),
	'SU_UPLOAD'          => humn_size($data_stats['upload']),
	'SU_DOWNLOAD'        => humn_size($data_stats['download']),
	'SU_ACTIVE_SEEDERS'  => $data_stats['active_seeders'],
	'SU_ACTIVE_LEECHERS' => $data_stats['active_leechers'],
	'SU_ACTIVE_TOR'      => num_format($data_stats['active_tor']),
	'SU_ACTIVE_TOR_SIZE' => humn_size($data_stats['active_tor_size']),
	'SU_ALL_TOR'         => num_format($data_stats['all_tor']),
	'SU_ALL_TOR_SIZE'    => humn_size($data_stats['all_tor_size']),
	'SU_SPEED'           => isset($data_stats['speed']) ? humn_size($data_stats['speed']) . '/сек' : 0,
	'SU_VISITORS'        => isset($data_stats['visitors']) ? $data_stats['visitors'] : 0,

	'TOP_LEECHERS'  => ( $bb_cfg['t_top_leechers'] ) ? true : false,
	'TOP_RELEASERS' => ( $bb_cfg['t_top_releasers'] ) ? true : false,
	'TOP_SEEDERS'   => ( $bb_cfg['t_top_seeders'] ) ? true : false,
	'TOP_SHARE'     => ( $bb_cfg['t_top_share'] ) ? true : false,

	'U_TOP_SPEED' => append_sid('top_speed.php'))
);

$forecast = $bb_cache->get('rss_forecast');

if( !empty($forecast) )
{
	foreach( $forecast as $entry )
	{
		$template->assign_block_vars('forecast', array_change_key_case($entry, CASE_UPPER));
	}
}

$currency = $bb_cache->get('rss_currency');

if( !empty($currency) )
{
	$template->assign_vars(array(
		'EURO'  => $currency['EUR']['value'],
		'POUND' => $currency['GBP']['value'],
		'USD'   => $currency['USD']['value'],
	));
}

/**
* Афиша "Синема Стар"
*/
$afisha = array();// $bb_cache->get('rss_cinemastar');

if( !empty($afisha) )
{
	$template->assign_vars(array(
		'AFISHA_DATE' => $afisha['date'])
	);

	foreach( $afisha['movies'] as $entry )
	{
		$template->assign_block_vars('afisha', array(
			'FORMAT'   => $entry['format'],
			'NEW'      => $entry['new'],
			'SESSIONS' => implode(', ', $entry['sessions']),
			'TITLE'    => $entry['title'],

			'U_SEARCH' => 'tracker.php?nm=' . rawurlencode($entry['title']))
		);
	}
}

$template->assign_vars(array(
	'AFISHA_AVAILABLE' => !empty($afisha))
);

/**
* Новости Калуги
*/
$city_news = $bb_cache->get('rss_gorodka.ru');

if( !empty($city_news) )
{
	$template->assign_vars(array('SHOW_CITY_NEWS' => true));

	foreach( $city_news as $entry )
	{
		$entry['time'] = create_date('d-M', $entry['time']);
		
		$template->assign_block_vars('city_news', array_change_key_case($entry, CASE_UPPER));
	}
}

print_page('index.tpl');

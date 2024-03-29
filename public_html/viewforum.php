<?php

define('BB_SCRIPT', 'forum');
require 'common.php';

$show_last_topic     = true;
$last_topic_max_len  = 40;
$title_match_key     = 'nm';
$title_match_max_len = 60;

$datastore->enqueue(array(
	'moderators',
));

$page_cfg['load_tpl_vars'] = array(
	'post_icons',
	'topic_icons',
);

// Init request vars
$forum_id  = $app['request']->variable('f', 0);
$start     = abs($app['request']->variable('start', 0));
$mark_read = ( $app['request']->variable('mark', '') === 'topics' );

$anon = ANONYMOUS;

// Start session
$user->session_start();

$lastvisit = (IS_GUEST) ? TIMENOW : $userdata['user_lastvisit'];

if( $forum_id == 191 )
{
	$sql = '
		UPDATE
			bb_forums
		SET
			forum_redirects = forum_redirects + 1
		WHERE
			forum_id = 191';
	$db->sql_query($sql);

	$template->assign_vars(array(
		'META' => '<meta http-equiv="refresh" content="1;url=http://pron.ivacuum.ru" />')
	);

	bb_die('Переадресация на pron.ivacuum.ru...');
}

// Caching output
$req_page = "forum_f{$forum_id}";
$req_page .= ($start) ? "_start{$start}" : '';

// Check if the user has actually sent a forum ID
$sql = "SELECT * FROM bb_forums WHERE forum_id = $forum_id LIMIT 1";

if (!$forum_id OR !$forum_data = $db->fetch_row($sql))
{
	bb_die($lang['Forum_not_exist']);
}

// Only new
$only_new = $user->opt_js['only_new'];
$only_new_sql = '';
if ($only_new == ONLY_NEW_POSTS)
{
	$only_new_sql = "AND t.topic_last_post_time > $lastvisit";
}
else if ($only_new == ONLY_NEW_TOPICS)
{
	$only_new_sql = "AND t.topic_time > $lastvisit";
}

// Auth
$is_auth = auth(AUTH_ALL, $forum_id, $userdata, $forum_data);

$moderation = (!empty($_REQUEST['mod']) && $is_auth['auth_mod']);

if (!$is_auth['auth_view'])
{
	if (IS_GUEST)
	{
		$redirect = "f=$forum_id";
		$redirect .= ($start) ? "&start=$start" : '';
		redirect("login.php?redirect=viewforum.php&$redirect");
	}
	// The user is not authed to read this forum ...
	$message = sprintf($lang['Sorry_auth_view'], $is_auth['auth_view_type']);
	bb_die($message);
}

// Redirect to login page if not admin session
$mod_redirect_url = '';

if ($is_auth['auth_mod'])
{
	$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : $_SERVER['REQUEST_URI'];
	$redirect = url_arg($redirect, 'mod', 1, '&');
	$mod_redirect_url = "login.php?redirect=$redirect&admin=1";

	if ($moderation && !$userdata['session_admin'])
	{
		redirect($mod_redirect_url);
	}
}

// Topics read tracks
$tracking_topics = get_tracks('topic');
$tracking_forums = get_tracks('forum');

if ($mark_read && !IS_GUEST)
{
	set_tracks(COOKIE_FORUM, $tracking_forums, $forum_id);

	$message = $lang['Topics_marked_read'] .'<br /><br />';
	$message .= sprintf($lang['Click_return_forum'], '<a href="'. FORUM_URL . $forum_id .'">', '</a>');
	$message .= '<br /><br />';
	$message .= sprintf($lang['Click_return_index'], '<a href="/">', '</a>');
	bb_die($message);
}

// Subforums
$show_subforums = ($bb_cfg['sf_on_first_page_only']) ? !$start : true;

$forums = $datastore->get('cat_forums');

$forum_data['cat_title'] = $forums['c'][$forum_data['cat_id']]['cat_title'];

if (!$forum_data['forum_parent'] && isset($forums['f'][$forum_id]['subforums']) && $show_subforums)
{
	$not_auth_forums = ($bb_cfg['sf_check_view_permissions']) ? $user->get_not_auth_forums(AUTH_VIEW) : '';
	$ignore_forum_sql = ($not_auth_forums) ? "AND f.forum_id NOT IN($not_auth_forums)" : '';

	$sql = "
		SELECT
			f.forum_id, f.forum_status, f.forum_last_post_id, f.forum_posts, f.forum_topics,
			t.topic_last_post_time, t.topic_id AS last_topic_id, t.topic_title AS last_topic_title,
			p.poster_id AS sf_last_user_id, IF(p.poster_id = $anon, p.post_username, u.username) AS sf_last_username
		FROM      bb_forums f
		LEFT JOIN bb_topics t ON(f.forum_last_post_id = t.topic_last_post_id)
		LEFT JOIN bb_posts p ON(f.forum_last_post_id = p.post_id)
		LEFT JOIN bb_users u ON(p.poster_id = u.user_id)
		WHERE f.forum_parent = $forum_id
			$only_new_sql
			$ignore_forum_sql
		ORDER BY f.forum_order
	";

	if ($rowset = $db->fetch_rowset($sql))
	{
		$template->assign_vars(array(
			'SHOW_SUBFORUMS'   => true,
			'FORUM_IMG'        => $images['forum'],
			'FORUM_NEW_IMG'    => $images['forum_new'],
			'FORUM_LOCKED_IMG' => $images['forum_locked'],
		));
	}
	foreach ($rowset as $sf_data)
	{
		$sf_forum_id  = $sf_data['forum_id'];
		$sf_last_tid  = $sf_data['last_topic_id'];
		$folder_image = $images['forum'];
		$last_post    = $lang['NO_POSTS'];

		if (!$fname_html =& $forums['forum_name_html'][$sf_forum_id])
		{
			continue;
		}

		if ($sf_data['forum_status'] == FORUM_LOCKED)
		{
			$folder_image = $images['forum_locked'];
		}
		else if (is_unread($sf_data['topic_last_post_time'], $sf_last_tid, $sf_forum_id))
		{
			$folder_image = $images['forum_new'];
		}

		$last_post_username = ($sf_data['sf_last_username']) ? $sf_data['sf_last_username'] : $lang['Guest'];

		if ($sf_data['forum_last_post_id'])
		{
			$last_post = create_date($bb_cfg['last_post_date_format'], $sf_data['topic_last_post_time']);
			$last_post .= '<br />';
			$last_post .= ($sf_data['sf_last_user_id'] != ANONYMOUS) ? '<a href="'. PROFILE_URL . $sf_data['sf_last_user_id'] .'">'. $last_post_username .'</a>' : $last_post_username;
			$last_post .= '<a href="'. POST_URL . $sf_data['forum_last_post_id'] .'#'. $sf_data['forum_last_post_id'] .'"><img src="'. $images['icon_latest_reply'] .'" class="icon2" alt="latest" title="'. $lang['View_latest_post'] .'" /></a>';
		}

		$template->assign_block_vars('f',	array(
			'FORUM_FOLDER_IMG' => $folder_image,

			'FORUM_NAME'  => $fname_html,
			'FORUM_DESC'  => $forums['f'][$sf_forum_id]['forum_desc'],
			'U_VIEWFORUM' => FORUM_URL . $sf_forum_id,
			'TOPICS'      => commify($sf_data['forum_topics']),
			'POSTS'       => commify($sf_data['forum_posts']),
			'LAST_POST'   => $last_post,
			'MODERATORS'  => '',
		));

		if ($sf_data['forum_last_post_id'])
		{
			$template->assign_block_vars('f.last', array(
				'FORUM_LAST_POST'     => true,
				'SHOW_LAST_TOPIC'     => $show_last_topic,
				'LAST_TOPIC_ID'       => $sf_data['last_topic_id'],
				'LAST_TOPIC_TIP'      => $sf_data['last_topic_title'],
				'LAST_TOPIC_TITLE'    => str_short($sf_data['last_topic_title'], $last_topic_max_len),
				'LAST_POST_TIME'      => create_date($bb_cfg['last_post_date_format'], $sf_data['topic_last_post_time']),
				'LAST_POST_ID'        => $sf_data['forum_last_post_id'],
				'LAST_POST_USER_NAME' => $last_post_username,
				'LAST_POST_USER_ID'   => ($sf_data['sf_last_user_id'] != ANONYMOUS) ? $sf_data['sf_last_user_id'] : '',
				'ICON_LATEST_REPLY'   => $images['icon_latest_reply'],
			));
		}
		else
		{
			$template->assign_block_vars('f.last', array('FORUM_LAST_POST' => false));
		}
	}
}
else if ($parent_id = $forum_data['forum_parent'])
{
	$template->assign_vars(array(
		'HAS_PARENT_FORUM'  => true,
		'PARENT_FORUM_HREF'	=> FORUM_URL . $forum_data['forum_parent'],
		'PARENT_FORUM_NAME' => $forums['forum_name_html'][$parent_id],
	));
}
unset($forums, $rowset);
$datastore->rm('cat_forums');

// Topics per page
$topics_per_page = $bb_cfg['topics_per_page'];
$select_tpp = '';

if ($is_auth['auth_mod'])
{
	if ($req_tpp = abs(intval($_REQUEST['tpp'] ?? 0)) AND in_array($req_tpp, $bb_cfg['allowed_topics_per_page']))
	{
		$topics_per_page = $req_tpp;
	}

	$select_tpp = array();
	foreach ($bb_cfg['allowed_topics_per_page'] as $tpp)
	{
		$select_tpp[$tpp] = $tpp;
	}
}

// Obtain list of moderators
$moderators = array();
$mod = $datastore->get('moderators');

if (isset($mod['mod_users'][$forum_id]))
{
	foreach ($mod['mod_users'][$forum_id] as $user_id)
	{
		$moderators[] = '<a href="'. PROFILE_URL . $user_id .'">'. $mod['name_users'][$user_id] .'</a>';
	}
}
if (isset($mod['mod_groups'][$forum_id]))
{
	foreach ($mod['mod_groups'][$forum_id] as $group_id)
	{
		$moderators[] = '<a href="'. "groupcp.php?". POST_GROUPS_URL ."=". $group_id .'">'. $mod['name_groups'][$group_id] .'</a>';
	}
}

$template->assign_vars(array(
	'MODERATORS'  => ($moderators) ? join(', ', $moderators) : $lang['None'],
));

unset($moderators, $mod);
$datastore->rm('moderators');

// Generate a 'Show topics in previous x days' select box.
$topic_days   = 0; // all the time
$forum_topics = $forum_data['forum_topics'];

$sel_previous_days = array(
	0   => $lang['All_Posts'],
	1   => $lang['1_Day'],
	7   => $lang['7_Days'],
	14  => $lang['2_Weeks'],
	30  => $lang['1_Month'],
	90  => $lang['3_Months'],
	180 => $lang['6_Months'],
	364 => $lang['1_Year'],
);

if (!empty($_REQUEST['topicdays']))
{
	if ($req_topic_days = abs(intval($_REQUEST['topicdays'])) AND isset($sel_previous_days[$req_topic_days]))
	{
		$sql = "
			SELECT COUNT(*) AS forum_topics
			FROM bb_topics
			WHERE forum_id = $forum_id
				AND topic_last_post_time > ". (TIMENOW - 86400*$req_topic_days) ."
		";

		if ($row = $db->fetch_row($sql))
		{
			$topic_days = $req_topic_days;
			$forum_topics = $row['forum_topics'];
		}
	}
}
// Correct $start value
if ($start > $forum_topics)
{
	redirect("viewforum.php?f=$forum_id");
}

// Generate SORT and ORDER selects
$sort_value = isset($_REQUEST['sort']) ? (int) $_REQUEST['sort'] : $forum_data['forum_display_sort'];
$order_value = isset($_REQUEST['order']) ? (int) $_REQUEST['order'] : $forum_data['forum_display_order'];
$sort_list = '<select name="sort">'. get_forum_display_sort_option($sort_value, 'list', 'sort') .'</select>';
$order_list = '<select name="order">'. get_forum_display_sort_option($order_value, 'list', 'order') .'</select>';
$s_display_order = '&nbsp;'. $lang['SORT_BY'] .':&nbsp;'. $sort_list . $order_list .'&nbsp;';

// Selected SORT and ORDER methods
$sort_method = get_forum_display_sort_option($sort_value, 'field', 'sort');
$order_method = get_forum_display_sort_option($order_value, 'field', 'order');

$order_sql = "ORDER BY t.topic_type DESC, $sort_method $order_method";

$limit_topics_time_sql = ($topic_days) ? "AND t.topic_last_post_time > ". (TIMENOW - 86400*$topic_days) : '';

$select_tor_sql = $join_tor_sql = '';
$join_dl = ($bb_cfg['show_dl_status_in_forum'] && !IS_GUEST);

if ($forum_data['allow_reg_tracker'])
{
	$select_tor_sql = ',
		tor.size AS tor_size, tor.reg_time, tor.complete_count, tor.seeder_last_seen, tor.attach_id, tor.tor_status, tor.seeders, tor.leechers
	';
	$select_tor_sql .= ($join_dl) ? ', dl.user_status AS dl_status' : '';

	$join_tor_sql = "
		LEFT JOIN bb_bt_torrents tor ON(t.topic_id = tor.topic_id)
	";
	$join_tor_sql .= ($join_dl) ? " LEFT JOIN bb_bt_dlstatus_main dl ON(dl.user_id = {$userdata['user_id']} AND dl.topic_id = t.topic_id)" : '';
}

// Title match
$title_match_sql = '';

if ($title_match =& $_REQUEST[$title_match_key])
{
	if ($title_match = substr(trim($title_match), 0, $title_match_max_len))
	{
		$search_bool_mode = ($bb_cfg['allow_search_in_bool_mode']) ? " IN BOOLEAN MODE" : '';
		$search_text_sql = $db->escape($title_match);
		// $title_match_sql = "
		// 	AND MATCH (t.topic_title) AGAINST ('$search_text_sql'". $search_bool_mode .")
		// ";
		$title_match_sql = " AND t.topic_title = '$search_text_sql' ";
		$start = 0;
		$forum_topics = $topics_per_page;
	}
}

// Get topics
$topic_ids = $topic_rowset = array();

// IDs
$sql = "
	SELECT t.topic_id
	FROM bb_topics t
	WHERE t.forum_id = $forum_id
		$only_new_sql
		$title_match_sql
		$limit_topics_time_sql
	$order_sql
	LIMIT $start, $topics_per_page
";
foreach ($db->fetch_rowset($sql) as $row)
{
	$topic_ids[] = $row['topic_id'];
}

// Titles, posters etc.
if ($topics_csv = join(',', $topic_ids))
{
	$topic_rowset = $db->fetch_rowset("
		SELECT
			t.*, t.topic_poster AS first_user_id,
			IF(t.topic_poster = $anon, p1.post_username, u1.username) AS first_username,
			p2.poster_id AS last_user_id,
			IF(p2.poster_id = $anon, p2.post_username, u2.username) AS last_username
				$select_tor_sql
		FROM      bb_topics t
		LEFT JOIN bb_posts p1 ON(t.topic_first_post_id = p1.post_id)
		LEFT JOIN bb_users u1 ON(t.topic_poster = u1.user_id)
		LEFT JOIN bb_posts p2 ON(t.topic_last_post_id = p2.post_id)
		LEFT JOIN bb_users u2 ON(p2.poster_id = u2.user_id)
			$join_tor_sql
		WHERE t.topic_id IN($topics_csv)
		$order_sql
	");
}

$found_topics = count($topic_rowset);

// Define censored word matches
$orig_word = $replacement_word = array();
obtain_word_list($orig_word, $replacement_word);

$post_new_topic_url = "posting.php?mode=newtopic&amp;f=$forum_id";
$post_new_topic_url .= ($forum_data['topic_tpl_id']) ? '&tpl=1' : '';

// Post URL generation for templating vars
$template->assign_vars(array(
	'U_POST_NEW_TOPIC'    => $post_new_topic_url,
	'S_SELECT_TOPIC_DAYS' => build_select('topicdays', array_flip($sel_previous_days), $topic_days),
	'S_POST_DAYS_ACTION'  => "viewforum.php?f=$forum_id&amp;start=$start",
	'S_DISPLAY_ORDER'     => $s_display_order,
));

// User authorisation levels output
$u_auth = array();
$u_auth[] = ($is_auth['auth_post']) ? $lang['Rules_post_can'] : $lang['Rules_post_cannot'];
$u_auth[] = ($is_auth['auth_reply']) ? $lang['Rules_reply_can'] : $lang['Rules_reply_cannot'];
$u_auth[] = ($is_auth['auth_edit']) ? $lang['Rules_edit_can'] : $lang['Rules_edit_cannot'];
$u_auth[] = ($is_auth['auth_delete']) ? $lang['Rules_delete_can'] : $lang['Rules_delete_cannot'];
$u_auth[] = ($is_auth['auth_vote']) ? $lang['Rules_vote_can'] : $lang['Rules_vote_cannot'];
$u_auth[] = ($is_auth['auth_attachments']) ? $lang['Rules_attach_can'] : $lang['Rules_attach_cannot'];
$u_auth[] = ($is_auth['auth_download']) ? $lang['Rules_download_can'] : $lang['Rules_download_cannot'];
$u_auth[] = ($is_auth['auth_mod']) ? $lang['Rules_moderate'] : '';
$u_auth = join("<br />\n", $u_auth);

$template->assign_vars(array(
	'SHOW_JUMPBOX'        => true,
	'PAGE_TITLE'          => htmlCHR($forum_data['forum_name']),
	'FORUM_CAT_TITLE'		=> $forum_data['cat_title'],
	'FORUM_ID'            => $forum_id,
	'FORUM_NAME'          => htmlCHR($forum_data['forum_name']),
	'TORRENTS'            => $forum_data['allow_reg_tracker'],
	'POST_IMG'            => ($forum_data['forum_status'] == FORUM_LOCKED) ? $images['post_locked'] : $images['post_new'],

	'FOLDER_IMG'          => $images['folder'],
	'FOLDER_NEW_IMG'      => $images['folder_new'],
	'FOLDER_LOCKED_IMG'   => $images['folder_locked'],
	'FOLDER_STICKY_IMG'   => $images['folder_sticky'],
	'FOLDER_ANNOUNCE_IMG' => $images['folder_announce'],
	'FOLDER_DOWNLOAD_IMG' => $images['folder_dl'],

	'SHOW_ONLY_NEW_MENU'  => true,
	'ONLY_NEW_POSTS_ON'   => ($only_new == ONLY_NEW_POSTS),
	'ONLY_NEW_TOPICS_ON'  => ($only_new == ONLY_NEW_TOPICS),

	'TITLE_MATCH'         => htmlCHR($title_match),
	'SELECT_TPP'          => ($select_tpp) ? build_select('tpp', $select_tpp, $topics_per_page, null, null, 'onchange="$(\'#tpp\').submit();"') : '',
	'T_POST_NEW_TOPIC'    => ($forum_data['forum_status'] == FORUM_LOCKED) ? $lang['Forum_locked'] : $lang['Post_new_topic'],
	'S_AUTH_LIST'         => $u_auth,
	'U_VIEW_FORUM'        => FORUM_URL . $forum_id,
	'U_VIEW_FORUM_CAT'	  => append_sid('/?c=' . $forum_data['cat_id']),
	'U_MARK_READ'         => FORUM_URL . $forum_id ."&amp;mark=topics",
));

// Okay, lets dump out the page ...
foreach ($topic_rowset as $topic)
{
	$topic_id = $topic['topic_id'];
	$moved    = ($topic['topic_status'] == TOPIC_MOVED);
	$replies  = $topic['topic_replies'];
	$t_hot    = ($replies >= $bb_cfg['hot_threshold']);
	$t_type   = $topic['topic_type'];
	$separator = '';
	$is_unread = is_unread($topic['topic_last_post_time'], $topic_id, $forum_id);

	if ($t_type == POST_ANNOUNCE && !defined('ANNOUNCE_SEP'))
	{
		define('ANNOUNCE_SEP', true);
		$separator = $lang['Topics_Announcement'];
	}
	else if ($t_type == POST_STICKY && !defined('STICKY_SEP'))
	{
		define('STICKY_SEP', true);
		$separator = $lang['Topics_Sticky'];
	}
	else if ($t_type == POST_NORMAL && !defined('NORMAL_SEP'))
	{
		if (defined('ANNOUNCE_SEP') || defined('STICKY_SEP'))
		{
			define('NORMAL_SEP', true);
			$separator = $lang['Topics_Normal'];
		}
	}

	$template->assign_block_vars('t', array(
		'FORUM_ID'         => $forum_id,
		'TOPIC_ID'         => $topic_id,
		'HREF_TOPIC_ID'    => ($moved) ? $topic['topic_moved_id'] : $topic['topic_id'],
		'TOPIC_TITLE'      => wbr($topic['topic_title']),
		'TOPICS_SEPARATOR' => $separator,
		'IS_UNREAD'        => $is_unread,
		'TOPIC_ICON'       => get_topic_icon($topic, $is_unread),
		'PAGINATION'       => ($moved) ? '' : build_topic_pagination(TOPIC_URL . $topic_id, $replies, $bb_cfg['posts_per_page']),
		'REPLIES'          => $replies,
		'VIEWS'            => $topic['topic_views'],
		'TOR_STALED'       => ($forum_data['allow_reg_tracker'] && !($t_type == POST_ANNOUNCE || $t_type == POST_STICKY || $topic['tor_size'])),
		'TOR_FROZEN'       => (isset($topic['tor_status']) && ($topic['tor_status'] == TOR_STATUS_FROZEN || $topic['tor_status'] == 3 || $topic['tor_status'] == 4 || $topic['tor_status'] == 7)),

		// Torrent status mod
		//'TOR_STATUS'       => ($forum_data['allow_reg_tracker'] && ($topic['tor_status'] == 1 || $topic['tor_status'] == 3)),
		// end torrent status mod

		'TOR_STATUS'		=> ( $forum_data['allow_reg_tracker'] && isset($topic['tor_status']) ) ? $topic['tor_status'] : false,

		'ATTACH'			=> $topic['topic_attachment'],
		'STATUS'			=> $topic['topic_status'],
		'TYPE'				=> $topic['topic_type'],
		'DL'				=> ($topic['topic_dl_type'] == TOPIC_DL_TYPE_DL && !$forum_data['allow_reg_tracker']),
		'POLL'				=> $topic['topic_vote'],
		'DL_CLASS'			=> isset($topic['dl_status']) ? $dl_link_css[$topic['dl_status']] : '',

		'TOPIC_AUTHOR_ID'	=> ($topic['first_user_id'] != ANONYMOUS) ? $topic['first_user_id'] : '',
		'TOPIC_AUTHOR_NAME'	=> ($topic['first_username']) ? wbr($topic['first_username']) : $lang['Guest'],
		'LAST_POSTER_HREF'	=> ($topic['last_user_id'] != ANONYMOUS) ? $topic['last_user_id'] : '',
		'LAST_POSTER_NAME'	=> ($topic['last_username']) ? str_short($topic['last_username'], 15) : $lang['Guest'],
		'LAST_POST_TIME'	=> bb_date($topic['topic_last_post_time']),
		'LAST_POST_ID'		=> $topic['topic_last_post_id'],
	));

	if (isset($topic['tor_size']))
	{
		$template->assign_block_vars('t.tor', array(
			'SEEDERS'    => (int) $topic['seeders'],
			'LEECHERS'   => (int) $topic['leechers'],
			'TOR_SIZE'   => humn_size($topic['tor_size']),
			'COMPL_CNT'  => (int) $topic['complete_count'],
			'ATTACH_ID'  => $topic['attach_id'],
		));
	}
}
unset($topic_rowset);

$pg_url = FORUM_URL . $forum_id;
$pg_url .= ($topic_days)  ? "&amp;topicdays=$topic_days" : '';
$pg_url .= ($sort_value)  ? "&amp;sort=$sort_value" : '';
$pg_url .= ($order_value) ? "&amp;order=$order_value" : '';
$pg_url .= ($moderation)  ? "&amp;mod=1" : '';
$pg_url .= ($topics_per_page != $bb_cfg['topics_per_page'])  ? "&amp;tpp=$topics_per_page" : '';

if ($found_topics)
{
	$template->assign_vars(array(
		'PAGINATION'  => generate_pagination($pg_url, $forum_topics, $topics_per_page, $start),
		'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / $topics_per_page) + 1), ceil($forum_topics / $topics_per_page)),
	));
}
else
{
	if ($only_new)
	{
		$no_topics_msg = $lang['NO_NEW_POSTS'];
	}
	else
	{
		$no_topics_msg = ($topic_days || $title_match) ? $lang['No_search_match'] : $lang['No_topics_post_one'];
	}
	$template->assign_vars(array(
		'NO_TOPICS' => $no_topics_msg,
	));
}

$template->assign_vars(array(
	'PAGE_URL'         => $pg_url,
	'PAGE_URL_TPP'     => url_arg($pg_url, 'tpp', null),
	'FOUND_TOPICS'     => $found_topics,

	'AUTH_MOD'         => $is_auth['auth_mod'],
	'SESSION_ADMIN'    => $userdata['session_admin'],
	'MOD_REDIRECT_URL' => $mod_redirect_url,
	'MODERATION_ON'    => $moderation,
));

print_page('viewforum.tpl');

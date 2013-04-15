<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db, $bf, $bb_cfg;

//
// cat_forums
//
$data = array(
	'not_auth_forums' => array(
		'guest_view' => array(),
		'guest_read' => array(),
		'user_view'  => array(),
		'user_read'  => array(),
	),
	'tracker_forums'  => array(),
	'cat_title_html'  => array(),
	'forum_name_html' => array(),
	'c' => array(),                // also has $data['c']['cat_id']['forums'] key
	'f' => array(),                // also has $data['f']['forum_id']['subforums'] key
);

// Store only these fields from bb_forums in $data['f']
$forum_store_fields = array_flip(array_keys($bf['forum_perm']));
$forum_store_fields += array_flip(array(
  'forum_id',
  'cat_id',
  'forum_name',
  'forum_desc',
  'forum_status',
  'forum_posts',
  'forum_topics',
  'forum_parent',
));

// Categories
$sql = "SELECT * FROM bb_categories ORDER BY cat_order";

foreach($db->fetch_rowset($sql) as $row)
{
	$data['c'][$row['cat_id']] = $row;
	$data['cat_title_html'][$row['cat_id']] = htmlCHR($row['cat_title']);
}

$sql = "
	SELECT f.*
	FROM bb_forums f, bb_categories c
	WHERE f.cat_id = c.cat_id
	ORDER BY c.cat_order, f.forum_order
";

foreach ($db->fetch_rowset($sql) as $row)
{
	$fid = $row['forum_id'];
	$not_auth =& $data['not_auth_forums'];

	// Find not auth forums
	if ($row['auth_view'] != AUTH_ALL)
	{
		$not_auth['guest_view'][] = $fid;
	}
	if ($row['auth_view'] != AUTH_ALL && $row['auth_view'] != AUTH_REG)
	{
		$not_auth['user_view'][] = $fid;
	}
	if ($row['auth_read'] != AUTH_ALL)
	{
		$not_auth['guest_read'][] = $fid;
	}
	if ($row['auth_read'] != AUTH_ALL && $row['auth_read'] != AUTH_REG)
	{
		$not_auth['user_read'][] = $fid;
	}

	// Store forums data
	if ($parent_id = $row['forum_parent'])
	{
		$parent =& $data['f'][$parent_id];

		$parent['subforums'][] = $fid;
		$parent['forum_posts']  += $row['forum_posts'];
		$parent['forum_topics'] += $row['forum_topics'];
	}
	if ($row['allow_reg_tracker'])
	{
		$data['tracker_forums'][] = $fid;
	}

	$data['f'][$fid] = array_intersect_key($row, $forum_store_fields);
	$data['forum_name_html'][$fid] = htmlCHR($row['forum_name']);

	// Forum ids in cat
	$data['c'][$row['cat_id']]['forums'][] = $fid;
}
foreach ($data['not_auth_forums'] as $key => $val)
{
	$data['not_auth_forums'][$key] = join(',', $val);
}
$data['tracker_forums'] = join(',', $data['tracker_forums']);

$this->store('cat_forums', $data);

//
// jumpbox
//
$data = array(
	'guest' => get_forum_select('guest', 'f', null, null, null, 'id="jumpbox" onchange="window.location.href=\'viewforum.php?f=\'+this.value;"'),
	'user'  => get_forum_select('user',  'f', null, null, null, 'id="jumpbox" onchange="window.location.href=\'viewforum.php?f=\'+this.value;"'),
);

$this->store('jumpbox', $data);

file_write($data['guest'], SITE_DIR . 'ajax/html/jumpbox_guest.html', false, true, true);
file_write($data['user'], SITE_DIR . 'ajax/html/jumpbox_user.html', false, true, true);

//
// viewtopic_forum_select
//
$data = array(
	'viewtopic_forum_select' => get_forum_select('admin', 'new_forum_id'),
);

$this->store('viewtopic_forum_select', $data);

//
// latest_news
//
if ($bb_cfg['show_latest_news'] AND $news_forum_id = intval($bb_cfg['latest_news_forum_id']))
{
	$news_count = max($bb_cfg['latest_news_count'], 1);

	$data = $db->fetch_rowset("
		SELECT topic_id, topic_time, topic_title
		FROM bb_topics
		WHERE forum_id = $news_forum_id
		ORDER BY topic_time DESC
		LIMIT $news_count
	");

	$this->store('latest_news', $data);
}

// Store TopDownloaded
if( $bb_cfg['t_top_downloaded'] )
{
	$sql = '
		SELECT
			tr.topic_id,
			tr.forum_id,
			tr.reg_time,
			tr.complete_count,
			t.topic_title,
			f.forum_name,
			u.username,
			u.user_id
		FROM
			bb_bt_torrents tr
		LEFT JOIN
			bb_topics t ON tr.topic_id = t.topic_id
		LEFT JOIN
			bb_forums f ON tr.forum_id = f.forum_id
		LEFT JOIN
			bb_users u ON tr.poster_id = u.user_id
		ORDER BY
			tr.complete_count DESC
		LIMIT
			0, ' . $bb_cfg['t_top_downloaded'];
	$data = $db->fetch_rowset($sql);
	$this->store('top_downloaded', $data);
}

if( $bb_cfg['t_top_leechers'] )
{
	$sql = '
		SELECT
			t.user_id,
			u.username,
			SUM(t.u_down_total) as sum
		FROM
			bb_bt_users t
		LEFT JOIN
			bb_users u ON (t.user_id = u.user_id)
		GROUP BY
			t.user_id
		ORDER BY
			sum DESC
		LIMIT
			0, ' . $bb_cfg['t_top_leechers'];
	$data = $db->fetch_rowset($sql);
	$this->store('top_leechers', $data);
}

if( $bb_cfg['t_top_seeders'] )
{
	$sql = '
		SELECT
			t.user_id,
			u.username,
			SUM(t.u_up_total) as sum
		FROM
			bb_bt_users t
		LEFT JOIN
			bb_users u ON (t.user_id = u.user_id)
		GROUP BY
			t.user_id
		ORDER BY
			sum DESC
		LIMIT
			0, ' . $bb_cfg['t_top_seeders'];
	$data = $db->fetch_rowset($sql);
	$this->store('top_seeders', $data);
}

/**
* Лучшие по вкладу (объему собственных раздач)
*/
if( $bb_cfg['t_top_releasers'] )
{
	$sql = '
		SELECT
			t.poster_id,
			SUM(t.size) AS total_size,
			u.username
		FROM
			bb_bt_torrents t,
			bb_users u
		WHERE
			t.poster_id = u.user_id
		GROUP BY
			t.poster_id
		ORDER BY
			total_size DESC
		LIMIT
			0, ' . $bb_cfg['t_top_releasers'];
	$data = $db->fetch_rowset($sql);
	$this->store('top_releasers', $data);
}

/**
* Лучшие по шаре
*/
if( $bb_cfg['t_top_share'] )
{
	$sql = '
		SELECT
			tr.user_id,
			SUM(t.size) AS total_size,
			u.username
		FROM
			bb_bt_tracker tr,
			bb_bt_torrents t,
			bb_users u
		WHERE
			tr.topic_id = t.topic_id
		AND
			tr.seeder = 1
		AND
			tr.user_id = u.user_id
		GROUP BY
			tr.user_id
		ORDER BY
			total_size DESC
		LIMIT
			0, ' . $bb_cfg['t_top_share'];
	$data = $db->fetch_rowset($sql);
	$this->store('top_share', $data);
}

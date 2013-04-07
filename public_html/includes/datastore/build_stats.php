<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db;

$data = array();

// usercount
$sql = '
	SELECT
		COUNT(*) AS usercount
	FROM
		bb_users
	WHERE
		user_id NOT IN(' . ANONYMOUS . ', ' . BOT_UID . ')';
$row = $db->fetch_row($sql);
$data['usercount'] = $row['usercount'];

// newestuser
$row = $db->fetch_row("SELECT user_id, username FROM bb_users ORDER BY user_id DESC LIMIT 1");
$data['newestuser'] = $row;

// post/topic count
$row = $db->fetch_row("SELECT SUM(forum_topics) AS topiccount, SUM(forum_posts) AS postcount FROM bb_forums");
$data['postcount'] = $row['postcount'];
$data['topiccount'] = $row['topiccount'];

$this->store('stats', $data);

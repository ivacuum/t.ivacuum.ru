<?php

if (!defined('BB_ROOT')) die(basename(__FILE__));

global $db;

$data = array();

// usercount
$sql = '
	SELECT
		COUNT(*) AS usercount
	FROM
		' . USERS_TABLE . '
	WHERE
		user_id NOT IN(' . ANONYMOUS . ', ' . BOT_UID . ')';
$row = $db->fetch_row($sql);
$data['usercount'] = $row['usercount'];

// newestuser
$row = $db->fetch_row("SELECT user_id, username FROM ". USERS_TABLE ." ORDER BY user_id DESC LIMIT 1");
$data['newestuser'] = $row;

// post/topic count
$row = $db->fetch_row("SELECT SUM(forum_topics) AS topiccount, SUM(forum_posts) AS postcount FROM ". FORUMS_TABLE);
$data['postcount'] = $row['postcount'];
$data['topiccount'] = $row['topiccount'];

$this->store('stats', $data);

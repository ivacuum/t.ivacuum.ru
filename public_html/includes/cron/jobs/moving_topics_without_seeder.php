<?php

if (!defined('SITE_DIR'))
{
	exit;
}

require SITE_DIR . 'includes/functions_admin.php';
require SITE_DIR . 'includes/functions_post.php';

if( !defined('DEFAULT_LANG_DIR') )
{
	define('DEFAULT_LANG_DIR', SITE_DIR . 'language/lang_russian/');
}

/**
* Where topics without seeder should be moved
*/
$sql = '
	SELECT
		*
	FROM
		bb_bt_torrents
	WHERE
		seeder_last_seen > 0
	AND
		seeder_last_seen < ' . ( time() - 2592000 ) . '
	AND
		forum_id NOT IN(1, ' . $bb_cfg['archive_forum'] . ')';
$result = $db->sql_query($sql);

while( $row = $db->sql_fetchrow($result) )
{
	topic_move($row['topic_id'], $bb_cfg['archive_forum'], $row['forum_id'], 0, 1);

	$sql = '
		UPDATE
			bb_topics
		SET
			topic_type = 0
		WHERE
			topic_id = ' . $row['topic_id'];
	$db->sql_query($sql);
}

$db->sql_freeresult($result);

/*
$sql = '
	SELECT
		*
	FROM
		bb_bt_torrents
	WHERE
		seeder_last_seen > 0
	AND
		seeder_last_seen < ' . ( time() - 1209600 ) . '
	AND
		forum_id NOT IN(' . $bb_cfg['archive_forum'] . ', ' . $bb_cfg['archive_hide_forum'] . ', ' . $bb_cfg['hide_forums'] . ')';
$result = $db->sql_query($sql);

while( $row = $db->sql_fetchrow($result) )
{
	topic_move($row['topic_id'], $bb_cfg['archive_forum'], $row['forum_id'], 0, 1);

	$sql = '
		UPDATE
			bb_topics
		SET
			topic_type = 0
		WHERE
			topic_id = ' . $row['topic_id'];
	$db->sql_query($sql);
}

$db->sql_freeresult($result);

$sql = '
	SELECT
		*
	FROM
		bb_bt_torrents
	WHERE
		seeder_last_seen > 0
	AND
		seeder_last_seen < ' . ( time() - 1209600 ) . '
	AND
		forum_id IN(' . $bb_cfg['hide_forums'] . ')';
$result = $db->sql_query($sql);

while( $row = $db->sql_fetchrow($result) )
{
	topic_move($row['topic_id'], $bb_cfg['archive_hide_forum'], $row['forum_id'], 0, 1);

	$sql = '
		UPDATE
			bb_topics
		SET
			topic_type = 0
		WHERE
			topic_id = ' . $row['topic_id'];
	$db->sql_query($sql);
}

$db->sql_freeresult($result);
*/

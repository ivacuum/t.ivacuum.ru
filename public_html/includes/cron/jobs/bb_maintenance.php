<?php

if (!defined('SITE_DIR'))
{
	exit;
}

$fix_errors = true;
$debug_mode = false;

$tmp_attach_tbl = 'tmp_attachments';
$db_max_packet  = 800000;
$sql_limit      = 3000;

$check_attachments = false;
$orphan_files = $orphan_db_attach = $orphan_tor = array();
$posts_without_attach = $topics_without_attach = array();

$lock_tables = array(
	$tmp_attach_tbl        .' f',
	'bb_attachments_desc d',
	'bb_attachments_desc',
	'bb_attachments a',
	'bb_attachments',
	'bb_bt_torrents tor',
	'bb_bt_torrents',
	'bb_posts p',
	'bb_posts',
	'bb_privmsgs pm',
	'bb_topics t',
	'bb_topics',
);

$db->query("
	CREATE TEMPORARY TABLE $tmp_attach_tbl (
		physical_filename VARCHAR(255) NOT NULL default '',
		KEY physical_filename (physical_filename(20))
	) ENGINE = MYISAM DEFAULT CHARSET=$dbcharset
");

// Get attach_mod config
$attach_dir = get_attachments_dir();

// Get all names of existed attachments and insert them into $tmp_attach_tbl
if ($dir = @opendir($attach_dir))
{
	$check_attachments = true;
	$files = array();
	$f_len = 0;

	while (false !== ($f = readdir($dir)))
	{
		if ($f == 'index.php' || $f == '.htaccess' || is_dir("$attach_dir/$f") || is_link("$attach_dir/$f"))
		{
			continue;
		}
		$f = $db->escape($f);
		$files[] = "('$f')";
		$f_len += strlen($f) + 5;

		if ($f_len > $db_max_packet)
		{
			$files = join(',', $files);
			$db->query("INSERT INTO $tmp_attach_tbl VALUES $files");
			$files = array();
			$f_len = 0;
		}
	}
	if ($files = join(',', $files))
	{
		$db->query("INSERT INTO $tmp_attach_tbl VALUES $files");
	}
	closedir($dir);
}

// Lock tables
# $lock = $db->lock($lock_tables);

if ($check_attachments)
{
	// Delete bad records
	$db->query("
		DELETE a, d
		FROM      bb_attachments_desc d
		LEFT JOIN bb_attachments a USING(attach_id)
		WHERE (
		     d.physical_filename = ''
		  OR d.real_filename = ''
		  OR d.extension = ''
		  OR d.mimetype = ''
		  OR d.filesize = 0
		  OR d.filetime = 0
		  OR a.post_id = 0
		)
	");

	// Delete attachments that exist in file system but not exist in DB
	$sql = "SELECT f.physical_filename
		FROM $tmp_attach_tbl f
		LEFT JOIN bb_attachments_desc d USING(physical_filename)
		WHERE d.physical_filename IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		if ($filename = basename($row['physical_filename']))
		{
			if ($fix_errors)
			{
				@unlink("$attach_dir/$filename");
				@unlink("$attach_dir/". THUMB_DIR .'/t_'. $filename);
			}
			if ($debug_mode)
			{
				$orphan_files[] = "$attach_dir/$filename";
			}
		}
	}
	// Find DB records for attachments that exist in DB but not exist in file system
	$sql = "SELECT d.attach_id
		FROM bb_attachments_desc d
		LEFT JOIN $tmp_attach_tbl f USING(physical_filename)
		WHERE f.physical_filename IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$orphan_db_attach[] = $row['attach_id'];
	}
	// Attachment exist in DESC_TABLE but not exist in ATTACH_TABLE
	$sql = "SELECT d.attach_id
		FROM bb_attachments_desc d
		LEFT JOIN bb_attachments a USING(attach_id)
		WHERE a.attach_id IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$orphan_db_attach[] = $row['attach_id'];
	}
	// Attachment exist in ATTACH_TABLE but not exist in DESC_TABLE
	$sql = "SELECT a.attach_id
		FROM bb_attachments a
		LEFT JOIN bb_attachments_desc d USING(attach_id)
		WHERE d.attach_id IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$orphan_db_attach[] = $row['attach_id'];
	}
	// Attachments without post
	$sql = "SELECT a.attach_id
		FROM bb_attachments a
		LEFT JOIN bb_posts p USING(post_id)
		WHERE p.post_id IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$orphan_db_attach[] = $row['attach_id'];
	}
	// Delete all orphan attachments
	if ($orphans_sql = join(',', $orphan_db_attach))
	{
		if ($fix_errors)
		{
			$db->query("DELETE FROM bb_attachments_desc WHERE attach_id IN($orphans_sql)");
			$db->query("DELETE FROM bb_attachments WHERE attach_id IN($orphans_sql)");
		}
	}

	// Torrents without attachments
	$sql = "SELECT tor.topic_id
		FROM bb_bt_torrents tor
		LEFT JOIN bb_attachments_desc d USING(attach_id)
		WHERE d.attach_id IS NULL
		LIMIT $sql_limit";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$orphan_tor[] = $row['topic_id'];
	}
	// Delete all orphan torrents
	if ($orphans_sql = join(',', $orphan_tor))
	{
		if ($fix_errors)
		{
			$db->query("DELETE FROM bb_bt_torrents WHERE topic_id IN($orphans_sql)");
		}
	}

	// Check post_attachment markers
	$sql = "SELECT p.post_id
		FROM bb_posts p
		LEFT JOIN bb_attachments a USING(post_id)
		WHERE p.post_attachment = 1
		AND a.post_id IS NULL";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$posts_without_attach[] = $row['post_id'];
	}
	if ($posts_sql = join(',', $posts_without_attach))
	{
		if ($fix_errors)
		{
			$db->query("UPDATE bb_posts SET post_attachment = 0 WHERE post_id IN($posts_sql)");
		}
	}
	// Check topic_attachment markers
	$sql = "SELECT t.topic_id
		FROM bb_posts p, bb_topics t
		WHERE t.topic_id = p.topic_id
			AND t.topic_attachment = 1
		GROUP BY p.topic_id
		HAVING SUM(p.post_attachment) = 0";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		$topics_without_attach[] = $row['topic_id'];
	}
	if ($topics_sql = join(',', $topics_without_attach))
	{
		if ($fix_errors)
		{
			$db->query("UPDATE bb_topics SET topic_attachment = 0 WHERE topic_id IN($topics_sql)");
		}
	}
}
if ($debug_mode)
{
	prn_r($orphan_files, '$orphan_files');
	prn_r($orphan_db_attach, '$orphan_db_attach');
	prn_r($orphan_tor, '$orphan_tor');
	prn_r($posts_without_attach, '$posts_without_attach');
	prn_r($topics_without_attach, '$topics_without_attach');
}

// Unlock tables
# $unlock = $db->unlock();

$db->query("DROP TEMPORARY TABLE $tmp_attach_tbl");

unset($fix_errors, $debug_mode);

// Sync
require_once SITE_DIR . 'includes/functions_admin.php';
sync('topic', 'all');
sync('forum', 'all');
sync('user_posts', 'all');

// Clean "user_newpasswd"
$db->query("
	UPDATE bb_users SET
		user_newpasswd = ''
	WHERE user_lastvisit < ". (TIMENOW - 7*86400) ."
");

// Clean posts cache
// if ($posts_days = intval($bb_cfg['posts_cache_days_keep']))
// {
// 	$db->query("DELETE FROM bb_posts_html WHERE post_html_time < DATE_SUB(NOW(), INTERVAL $posts_days DAY)");
// }
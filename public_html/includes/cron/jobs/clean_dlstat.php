<?php

if (!defined('SITE_DIR'))
{
	exit;
}

/*
define('BUF_DLSTATUS_TABLE', 'tmp_buf_dlstatus');
*/

// Move new dl-status records to main table
/*
$db->query("
	CREATE TEMPORARY TABLE ". BUF_DLSTATUS_TABLE ." (
		user_id      mediumint(9)          NOT NULL default '0',
		topic_id     mediumint(8) unsigned NOT NULL default '0',
		user_status  tinyint(1)            NOT NULL default '0',
		PRIMARY KEY (user_id, topic_id)
	) TYPE=MyISAM
");

$db->query("
	INSERT INTO ". BUF_DLSTATUS_TABLE ."
		(user_id, topic_id, user_status)
	SELECT
		user_id, topic_id, user_status
	FROM
		bb_bt_dlstatus_new
	WHERE
		last_modified_dlstatus < DATE_SUB(NOW(), INTERVAL 1 DAY)
");

$db->query("
	REPLACE INTO bb_bt_dlstatus_main
		(user_id, topic_id, user_status)
	SELECT
		user_id, topic_id, user_status
	FROM ". BUF_DLSTATUS_TABLE ."
");

$db->query("
	DELETE new
	FROM ". BUF_DLSTATUS_TABLE ." buf
	INNER JOIN bb_bt_dlstatus_new new USING(user_id, topic_id)
");

$db->query("DROP TEMPORARY TABLE ". BUF_DLSTATUS_TABLE);
*/

// Delete staled dl-status records
$keeping_dlstat = array(
	DL_STATUS_WILL     => (int) $bb_cfg['dl_will_days_keep'],
	DL_STATUS_DOWN     => (int) $bb_cfg['dl_down_days_keep'],
	DL_STATUS_COMPLETE => (int) $bb_cfg['dl_complete_days_keep'],
	DL_STATUS_CANCEL   => (int) $bb_cfg['dl_cancel_days_keep'],
);

$delete_dlstat_sql = array();

foreach ($keeping_dlstat as $dl_status => $days_to_keep)
{
	if ($days_to_keep)
	{
		$delete_dlstat_sql[] = "
			user_status = $dl_status
			AND
			last_modified_dlstatus < DATE_SUB(NOW(), INTERVAL $days_to_keep DAY)
		";
	}
}

if ($delete_dlstat_sql = join(') OR (', $delete_dlstat_sql))
{
	$db->query("DELETE QUICK FROM bb_bt_dlstatus_main WHERE ($delete_dlstat_sql)");
}

// Delete orphans
$db->query("
	DELETE QUICK dl
	FROM bb_bt_dlstatus_main dl
	LEFT JOIN bb_users u USING(user_id)
	WHERE u.user_id IS NULL
");

$db->query("
	DELETE QUICK dl
	FROM bb_bt_dlstatus_main dl
	LEFT JOIN bb_topics t USING(topic_id)
	WHERE t.topic_id IS NULL
");

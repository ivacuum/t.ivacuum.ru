<?php

if (!defined('SITE_DIR'))
{
	exit;
}

if (empty($bb_cfg['seeder_last_seen_days_keep']) || empty($bb_cfg['seeder_never_seen_days_keep']))
{
	return;
}

$last_seen_time = TIMENOW - 86400*$bb_cfg['seeder_last_seen_days_keep'];
$never_seen_time = TIMENOW - 86400*$bb_cfg['seeder_never_seen_days_keep'];
$limit_sql = 3000;

$topics_sql = $attach_sql = array();

$sql = "SELECT topic_id, attach_id
	FROM bb_bt_torrents
	WHERE reg_time < $never_seen_time
		AND seeder_last_seen < $last_seen_time
	LIMIT $limit_sql";

foreach ($db->fetch_rowset($sql) as $row)
{
	$topics_sql[] = $row['topic_id'];
	$attach_sql[] = $row['attach_id'];
}
$dead_tor_sql = join(',', $topics_sql);
$attach_sql = join(',', $attach_sql);

if ($dead_tor_sql && $attach_sql)
{
/*
	// Update topic type
	$db->query("
		UPDATE bb_topics SET
			topic_dl_type = ". TOPIC_DL_TYPE_NORMAL ."
		WHERE topic_id IN($dead_tor_sql)
	");
*/

	// Update attach
	$db->query("
		UPDATE
			bb_attachments_desc a,
			bb_bt_torrents tor
		SET
			a.tracker_status = 0,
			a.download_count = tor.complete_count
		WHERE
			    a.attach_id = tor.attach_id
			AND tor.attach_id IN($attach_sql)
	");

	$sql = "INSERT INTO bb_bt_torrents_del (topic_id, info_hash)
		SELECT topic_id, info_hash
		FROM bb_bt_torrents
		WHERE topic_id IN(" . $dead_tor_sql . ") ON DUPLICATE KEY UPDATE is_del=1";
	$db->query($sql);

	// Remove torrents
	$db->query("
		DELETE FROM bb_bt_torrents
		WHERE topic_id IN($dead_tor_sql)
	");
}

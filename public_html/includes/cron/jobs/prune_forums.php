<?php

if (!defined('SITE_DIR'))
{
	exit;
}

require_once SITE_DIR . 'includes/functions_admin.php';

if ($bb_cfg['prune_enable'])
{
	$sql = "SELECT forum_id, prune_days FROM bb_forums WHERE prune_days != 0";

	foreach ($db->fetch_rowset($sql) as $row)
	{
		topic_delete('prune', $row['forum_id'], (TIMENOW - 86400*$row['prune_days']));
	}
}


<?php

if (!defined('SITE_DIR'))
{
	exit;
}

if ($bb_cfg['tr_settings_days_keep'])
{
	$db->query("
		DELETE FROM ". BT_USER_SETTINGS_TABLE ."
		WHERE last_modified < ". (TIMENOW - 86400*$bb_cfg['tr_settings_days_keep']) ."
	");
}


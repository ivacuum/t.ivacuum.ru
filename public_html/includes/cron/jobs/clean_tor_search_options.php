<?php

if (!defined('SITE_DIR'))
{
	exit;
}

if ($bb_cfg['tr_settings_days_keep'])
{
	$db->query("
		DELETE FROM bb_bt_user_settings
		WHERE last_modified < ". (TIMENOW - 86400*$bb_cfg['tr_settings_days_keep']) ."
	");
}


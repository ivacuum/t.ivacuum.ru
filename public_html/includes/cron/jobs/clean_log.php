<?php

if (!defined('SITE_DIR'))
{
	exit;
}

$log_days_keep = (int) $bb_cfg['log_days_keep'];

$db->query("
	DELETE FROM ". LOG_TABLE ."
	WHERE log_time < ". (TIMENOW - 86400*$log_days_keep) ."
");


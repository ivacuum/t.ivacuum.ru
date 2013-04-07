<?php

if (!defined('SITE_DIR'))
{
	exit;
}

$search_results_expire = TIMENOW - ($bb_cfg['user_session_duration'] * 2) - 600;

$db->query("
	DELETE FROM bb_search_results
	WHERE search_time < $search_results_expire
");


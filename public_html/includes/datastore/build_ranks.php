<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db;

$ranks = array();

$sql = "SELECT rank_id, rank_title, rank_image FROM bb_ranks";

foreach ($db->fetch_rowset($sql) as $row)
{
	$ranks[$row['rank_id']] = $row;
}

$this->store('ranks', $ranks);

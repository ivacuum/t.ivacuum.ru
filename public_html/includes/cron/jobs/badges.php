<?php

if (!defined('SITE_DIR'))
{
	exit;
}

/**
* Top user
*/
$sql = '
	SELECT
		b.user_id,
		b.username,
		bu.u_up_total
	FROM
		bb_bt_users bu
	LEFT JOIN
		bb_users b ON (b.user_id = bu.user_id)
	WHERE
		bu.u_up_total >= 3298534883328
	AND
		bu.can_leech = 1
	ORDER BY
		bu.u_up_total DESC';

/**
* Top keeper
*/

/**
* Top releaser
*/
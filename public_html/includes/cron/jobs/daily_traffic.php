<?php

if( !defined('BB_ROOT') ) die(basename(__FILE__));

$db->lock(array('bb_bt_users'));

$sql = '
	UPDATE
		bb_bt_users
	SET
		u_up_yday = u_up_today,
		u_up_today = 0,
		u_down_yday = u_down_today,
		u_down_today = 0,
		u_up_bonus_yday = u_up_bonus_today,
		u_up_bonus_today = 0,
		u_up_release_yday = u_up_release_today,
		u_up_release_today = 0,
		timebonus = LEAST(50000, timebonus + LEAST(1000, timebonus_today)),
		timebonus_yday = LEAST(1000, timebonus_today),
		timebonus_today = 0,
		timebonus_spent_yday = timebonus_spent_today,
		timebonus_spent_today = 0';
$db->sql_query($sql);

$db->unlock();

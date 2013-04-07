<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron;

use fw\cron\task;

/**
* Сброс счетчиков ежедневного трафика
*/
class daily_traffic extends task
{
	public function run()
	{
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
		$this->db->query($sql);
		
		return true;
	}
}
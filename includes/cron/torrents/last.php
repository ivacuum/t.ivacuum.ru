<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron\torrents;

use fw\cron\task;

/**
* Новые раздачи
*/
class last extends task
{
	public function run()
	{
		$sql = '
			SELECT
				tr.topic_id,
				tr.forum_id,
				tr.attach_id,
				tr.reg_time,
				tr.seeders,
				tr.leechers,
				tr.speed_up + tr.speed_down AS speed_up,
				t.topic_title,
				f.forum_name,
				f.forum_icon,
				u.username,
				u.user_id
			FROM
				bb_bt_torrents tr,
				bb_topics t,
				bb_forums f,
				bb_users u
			WHERE
				tr.forum_id = f.forum_id
			AND
				tr.topic_id = t.topic_id
			AND
				tr.poster_id = u.user_id
			ORDER BY
				tr.reg_time DESC';
		$result = $this->db->query_limit($sql, [], 20);
		$data = $this->db->fetchall($result);
		$this->db->freeresult($result);
		$this->cache->set('ds_last_added', $data);
		
		return true;
	}
}
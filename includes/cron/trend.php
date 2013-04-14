<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron;

use fw\cron\task;

/**
* Метка статистики
*/
class trend extends task
{
	public function run()
	{
		$sql = '
			SELECT
				SUM(u_up_total) AS total_upload,
				SUM(u_down_total) AS total_download
			FROM
				bb_bt_users';
		$result = $this->db->query($sql);
		$row = $this->db->fetchrow($result);
		$this->db->freeresult($result);
		$download = (int) $row['total_download'];
		$upload = (int) $row['total_upload'];
		$total_dl_ul = $row['total_download'] * 2;

		$sql = '
			SELECT
				COUNT(DISTINCT user_id) AS seeders
			FROM
				bb_bt_tracker
			WHERE
				seeder = 1';
		$result = $this->db->query($sql);
		$active_seeders = (int) $this->db->fetchfield('seeders', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				COUNT(DISTINCT user_id) AS leechers
			FROM
				bb_bt_tracker
			WHERE
				seeder = 0';
		$result = $this->db->query($sql);
		$active_leechers = (int) $this->db->fetchfield('leechers', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				COUNT(*) AS peers
			FROM
				bb_bt_tracker';
		$result = $this->db->query($sql);
		$peers = (int) $this->db->fetchfield('peers', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				SUM(speed_up) AS speed_up,
				SUM(speed_down) AS speed_down
			FROM
				bb_bt_tracker';
		$result = $this->db->query($sql);
		$row = $this->db->fetchrow($result);
		$this->db->freeresult($result);
		$speed = max($row['speed_down'], $row['speed_up']) * 2;

		$sql = '
			SELECT
				COUNT(DISTINCT tor.topic_id) AS torrents,
				SUM(tor.size) AS size
			FROM
				bb_bt_torrents tor
			LEFT JOIN
				bb_bt_tracker tr ON (tr.topic_id = tor.topic_id)';
		$result = $this->db->query($sql);
		$row = $this->db->fetchrow($result);
		$this->db->freeresult($result);
		$active_tor = (int) $row['torrents'];
		$active_tor_size = (int) $row['size'];

		$sql = '
			SELECT
				COUNT(*) AS torrents,
				SUM(size) AS size
			FROM
				bb_bt_torrents';
		$result = $this->db->query($sql);
		$row = $this->db->fetchrow($result);
		$this->db->freeresult($result);
		$all_tor = (int) $row['torrents'];
		$all_tor_size = (int) $row['size'];

		$sql = '
			SELECT
				COUNT(*) AS posts
			FROM
				bb_posts';
		$result = $this->db->query($sql);
		$posts = (int) $this->db->fetchfield('posts', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				COUNT(*) AS users
			FROM
				bb_users
			WHERE
				user_id > 0';
		$result = $this->db->query($sql);
		$users = (int) $this->db->fetchfield('users', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				COUNT(*) AS users_online
			FROM
				bb_sessions s
			LEFT JOIN
				bb_users u ON (u.user_id = s.session_user_id)
			WHERE
				s.session_time > ?';
		$result = $this->db->query($sql, [$this->ctime - 300]);
		$users_online = (int) $this->db->fetchfield('users_online', false, $result);
		$this->db->freeresult($result);

		$sql = '
			SELECT
				COUNT(*) AS visitors
			FROM
				bb_users
			WHERE
				user_session_time >= ?';
		$result = $this->db->query($sql, [strtotime(date('Y-m-d'))]);
		$visitors = (int) $this->db->fetchfield('visitors', false, $result);
		$this->db->freeresult($result);
		
		$sql_ary = [
			'trend_time'          => $this->ctime,
			'trend_posts'         => $posts,
			'trend_users'         => $users,
			'trend_peers'         => $peers,
			'trend_seeders'       => $active_seeders,
			'trend_leechers'      => $active_leechers,
			'trend_users_online'  => $users_online,
			'trend_visitors'      => $visitors,
			'trend_torrents'      => $all_tor,
			'trend_torrents_size' => $all_tor_size,
			'trend_speed'         => $speed,
			'trend_traffic'       => $total_dl_ul,
		];
		
		$sql = 'INSERT INTO bb_stats_trend ' . $this->db->build_array('INSERT', $sql_ary);
		$this->db->query($sql);
		
		$this->cache->set('ds_stats_update', compact('active_leechers', 'active_seeders', 'active_tor', 'active_tor_size', 'all_tor', 'all_tor_size', 'download', 'peers', 'posts', 'speed', 'total_dl_ul', 'upload', 'users_online', 'visitors'));
		
		return true;
	}
}

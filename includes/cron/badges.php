<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron;

use fw\cron\task;

/**
* Присвоение лычек на основе статистики раздач
*/
class badges extends task
{
	protected $groups;
	
	public function run()
	{
		$this->groups = $this->get_groups();
		
		$this->grant_top_users();
		$this->grant_top_releasers();
		$this->grant_top_keepers();
		
		return true;
	}
	
	protected function add_to_group($group_id, $user_id)
	{
		$sql_ary = [
			'group_id'     => $group_id,
			'user_id'      => $user_id,
			'user_pending' => 0,
		];
		
		$sql = 'INSERT INTO bb_user_group ' . $this->db->build_array('INSERT', $sql_ary);
		$this->db->query($sql);
		
		$sql = 'UPDATE bb_users SET user_level = 20 WHERE user_id = ? AND user_level = 0';
		$this->db->query($sql, [$user_id]);

		$this->send_pm($user_id, "Вам присвоена лычка {$this->groups[$group_id]}", "Здравствуйте!\n\nПоздравляем с получением лычки {$this->groups[$group_id]}! Включить ее отображение вы можете в настройках своего профиля.\n\nБольшое спасибо за вклад в развитие трекера!");
	}
	
	protected function delete_from_group($group_id, $user_id)
	{
		$sql = '
			DELETE
			FROM
				bb_user_group
			WHERE
				user_id = ?
			AND
				group_id = ?';
		$this->db->query($sql, [(int) $user_id, (int) $group_id]);
	}
	
	protected function get_groups()
	{
		$sql = '
			SELECT
				group_id,
				group_name
			FROM
				bb_groups
			WHERE
				group_id BETWEEN 37 AND 54';
		$result = $this->db->query($sql);
		
		while ($row = $this->db->fetchrow($result)) {
			$groups[$row['group_id']] = $row['group_name'];
		}
		
		$this->db->freeresult($result);
		
		return $groups;
	}
	
	protected function grant_top_keepers()
	{
		$top_keeper = [
			1 => ['group' => 46, 'threshold' => 536870912000],
			2 => ['group' => 47, 'threshold' => 1099511627776],
			3 => ['group' => 48, 'threshold' => 3298534883328],
		];

		$sql = '
			SELECT
				u.user_id,
				u.username,
				bu.u_up_bonus AS upload
			FROM
				bb_bt_users bu
			LEFT JOIN
				bb_users u ON (u.user_id = bu.user_id)
			WHERE
				bu.u_up_bonus >= 536870912000
			AND
				bu.can_leech = 1
			ORDER BY
				bu.u_up_bonus DESC';
		$result = $this->db->query($sql);
		$top_keepers = $this->db->fetchall($result, 'user_id');
		$this->db->freeresult($result);

		/* Какая лычка у пользователей уже есть */
		$sql = '
			SELECT
				group_id,
				user_id
			FROM
				bb_user_group
			WHERE
				group_id BETWEEN 46 AND 48
			AND
				user_pending = 0
			AND
				user_id > 0';
		$result = $this->db->query($sql);

		while ($row = $this->db->fetchrow($result)) {
			/* Пропускаем пользователей, которые уже не проходят по требованиям */
			if (!isset($top_keepers[$row['user_id']])) {
				continue;
			}

			$top_keepers[$row['user_id']]['group_id'] = $row['group_id'];
		}

		$this->db->freeresult($result);
		
		foreach ($top_keepers as $user_id => $row) {
			foreach (array_reverse($top_keeper) as $badge) {
				/* Проверка требований включения в группу */
				if ($row['upload'] >= $badge['threshold']) {
					if (!empty($row['group_id'])) {
						/* Пользователь уже с нужной лычкой */
						if ($row['group_id'] == $badge['group']) {
							break;
						}

						/* Пользователь будет переведен в другую группу */
						$row['new_group_id'] = $badge['group'];
						$row['delete_group_id'] = $row['group_id'];
						$this->log("{$row['username']} переведен из группы {$this->groups[$row['group_id']]} в группу {$this->groups[$badge['group']]} (" . floor($row['upload'] / 1073741824) . " GB)");
						break;
					}

					/* Пользователь будет добавлен в группу */
					$row['new_group_id'] = $badge['group'];
					$this->log("{$row['username']} добавлен в группу {$this->groups[$badge['group']]} (" . floor($row['upload'] / 1073741824) . " GB)");
					break;
				}
			}
			
			/* Удаление из прежней группы */
			if (!empty($row['delete_group_id'])) {
				$this->delete_from_group($row['delete_group_id'], $user_id);
			}

			/* Добавление в новую группу */
			if (!empty($row['new_group_id'])) {
				$this->add_to_group($row['new_group_id'], $user_id);
			}
		}
	}
	
	protected function grant_top_releasers()
	{
		$top_releaser = [
			'b1' => ['group' => 37, 'releases' => 25, 'size' => 0],
			'b2' => ['group' => 38, 'releases' => 51, 'size' => 0],
			'b3' => ['group' => 39, 'releases' => 151, 'size' => 0],
			's1' => ['group' => 43, 'releases' => 25, 'size' => 107374182400],
			's2' => ['group' => 44, 'releases' => 51, 'size' => 322122547200],
			's3' => ['group' => 45, 'releases' => 151, 'size' => 536870912000],
			'g1' => ['group' => 40, 'releases' => 151, 'size' => 1099511627776],
			'g2' => ['group' => 41, 'releases' => 251, 'size' => 1649267441664],
			'g3' => ['group' => 42, 'releases' => 500, 'size' => 2199023255552],
		];
		
		$group_weights = array_flip([37, 38, 39, 43, 44, 45, 40, 41, 42]);
		
		$sql = '
			SELECT
				u.user_id,
				u.username,
				SUM(t.size) AS size,
				COUNT(t.size) AS releases,
				TRUNCATE((bu.u_up_total + bu.u_up_release + bu.u_up_bonus) / bu.u_down_total, 0) AS ratio
			FROM
				bb_bt_users bu
			LEFT JOIN
				bb_users u ON (u.user_id = bu.user_id)
			LEFT JOIN
				bb_bt_torrents t ON (t.poster_id = bu.user_id)
			WHERE
				bu.can_leech = 1
			GROUP BY
				u.user_id
			HAVING
				ratio >= 3
			AND
				releases >= 25
			ORDER BY
				releases DESC';
		$result = $this->db->query($sql);
		$top_releasers = $this->db->fetchall($result, 'user_id');
		$this->db->freeresult($result);
		
		/* Какая лычка у пользователей уже есть */
		$sql = '
			SELECT
				group_id,
				user_id
			FROM
				bb_user_group
			WHERE
				group_id BETWEEN 37 AND 45
			AND
				user_pending = 0
			AND
				user_id > 0';
		$result = $this->db->query($sql);

		while ($row = $this->db->fetchrow($result)) {
			/* Пропускаем пользователей, которые уже не проходят по требованиям */
			if (!isset($top_releasers[$row['user_id']])) {
				continue;
			}
			
			$top_releasers[$row['user_id']]['group_id'] = $row['group_id'];
		}

		$this->db->freeresult($result);
		
		foreach ($top_releasers as $user_id => $row) {
			foreach (array_reverse($top_releaser) as $badge) {
				/* Проверка требований включения в группу */
				if ($row['releases'] >= $badge['releases'] && $row['size'] >= $badge['size']) {
					if (!empty($row['group_id'])) {
						/* Пользователь уже с нужной лычкой */
						if ($group_weights[$row['group_id']] >= $group_weights[$badge['group']]) {
							break;
						}

						/* Пользователь будет переведен в другую группу */
						$row['new_group_id'] = $badge['group'];
						$row['delete_group_id'] = $row['group_id'];
						$this->log("{$row['username']} переведен из группы {$this->groups[$row['group_id']]} в группу {$this->groups[$badge['group']]} ({$row['releases']}, " . floor($row['size'] / 1073741824) . " GB)");
						break;
					}

					/* Пользователь будет добавлен в группу */
					$row['new_group_id'] = $badge['group'];
					$this->log("{$row['username']} добавлен в группу {$this->groups[$badge['group']]} ({$row['releases']}, " . floor($row['size'] / 1073741824) . " GB)");
					break;
				}
			}
			
			/* Добавление в новую группу */
			if (!empty($row['new_group_id'])) {
				$this->add_to_group($row['new_group_id'], $user_id);
			}
			
			/* Удаление из прежней группы */
			if (!empty($row['delete_group_id'])) {
				$this->delete_from_group($row['delete_group_id'], $user_id);
			}
		}
	}
	
	protected function grant_top_users()
	{
		$top_user = [
			3  => ['group' => 49, 'threshold' => 3298534883328],
			6  => ['group' => 50, 'threshold' => 6597069766656],
			10 => ['group' => 51, 'threshold' => 10995116277760],
			15 => ['group' => 52, 'threshold' => 16492674416640],
			25 => ['group' => 53, 'threshold' => 27487790694400],
			50 => ['group' => 54, 'threshold' => 54975581388800],
		];
		
		$sql = '
			SELECT
				u.user_id,
				u.username,
				bu.u_up_total AS upload,
				TRUNCATE((bu.u_up_total + bu.u_up_release + bu.u_up_bonus) / bu.u_down_total, 0) AS ratio
			FROM
				bb_bt_users bu
			LEFT JOIN
				bb_users u ON (u.user_id = bu.user_id)
			WHERE
				bu.u_up_total >= 3298534883328
			AND
				bu.can_leech = 1
			HAVING
				ratio >= 3
			ORDER BY
				bu.u_up_total DESC';
		$result = $this->db->query($sql);
		$top_users = $this->db->fetchall($result, 'user_id');
		$this->db->freeresult($result);

		/* Какая лычка у пользователей уже есть */
		$sql = '
			SELECT
				group_id,
				user_id
			FROM
				bb_user_group
			WHERE
				group_id BETWEEN 49 AND 54
			AND
				user_pending = 0
			AND
				user_id > 0';
		$result = $this->db->query($sql);

		while ($row = $this->db->fetchrow($result)) {
			/* Пропускаем пользователей, которые уже не проходят по требованиям */
			if (!isset($top_users[$row['user_id']])) {
				continue;
			}
			
			$top_users[$row['user_id']]['group_id'] = $row['group_id'];
		}

		$this->db->freeresult($result);
		
		foreach ($top_users as $user_id => $row) {
			foreach (array_reverse($top_user) as $badge) {
				/* Проверка требований включения в группу */
				if ($row['upload'] >= $badge['threshold']) {
					if (!empty($row['group_id'])) {
						/* Пользователь уже с нужной лычкой */
						if ($row['group_id'] == $badge['group']) {
							break;
						}

						/* Пользователь будет переведен в другую группу */
						$row['new_group_id'] = $badge['group'];
						$row['delete_group_id'] = $row['group_id'];
						$this->log("{$row['username']} переведен из группы {$this->groups[$row['group_id']]} в группу {$this->groups[$badge['group']]} (" . floor($row['upload'] / 1099511627776) . " TB)");
						break;
					}

					/* Пользователь будет добавлен в группу */
					$row['new_group_id'] = $badge['group'];
					$this->log("{$row['username']} добавлен в группу {$this->groups[$badge['group']]} (" . floor($row['upload'] / 1099511627776) . " TB)");
					break;
				}
			}
			
			/* Добавление в новую группу */
			if (!empty($row['new_group_id'])) {
				$this->add_to_group($row['new_group_id'], $user_id);
			}
			
			/* Удаление из прежней группы */
			if (!empty($row['delete_group_id'])) {
				$this->delete_from_group($row['delete_group_id'], $user_id);
			}
		}
	}
	
	protected function send_pm($user_id, $title, $text)
	{
		$sql_ary = [
			'privmsgs_type'        => 5, /* Непрочитанное */
			'privmsgs_subject'     => $title,
			'privmsgs_from_userid' => 2,
			'privmsgs_to_userid'   => $user_id,
			'privmsgs_date'        => time(),
			'privmsgs_ip'          => '127.0.0.1',
		];
		
		$sql = 'INSERT INTO bb_privmsgs ' . $this->db->build_array('INSERT', $sql_ary);
		$this->db->query($sql);
		
		$pm_id = $this->db->insert_id();
		
		$sql_ary = [
			'privmsgs_text_id'    => $pm_id,
			'privmsgs_bbcode_uid' => make_random_string(10),
			'privmsgs_text'       => $text,
		];
		
		$sql = 'INSERT INTO bb_privmsgs_text ' . $this->db->build_array('INSERT', $sql_ary);
		$this->db->query($sql);
		
		$sql_ary = [
			'user_last_privmsg' => time(),
			'user_newest_pm_id' => $pm_id,
		];
		
		$sql = '
			UPDATE
				bb_users
			SET
				user_new_privmsg = user_new_privmsg + 1,
				' . $this->db->build_array('UPDATE', $sql_ary) . '
			WHERE
				user_id = ' . (int) $user_id;
		$this->db->query($sql);
	}
}

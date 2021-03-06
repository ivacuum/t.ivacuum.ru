<?php

if (!defined('SITE_DIR'))
{
	exit;
}

function update_user_level ($user_id)
{
	global $db, $datastore;

	if (is_array($user_id))
	{
		$user_id = join(',', $user_id);
	}
	$user_groups_in = ($user_id !== 'all') ? "AND ug.user_id IN($user_id)" : '';
	$users_in       = ($user_id !== 'all') ? "AND  u.user_id IN($user_id)" : '';

	$tmp_table = 'tmp_levels';

	$db->query("
		CREATE TEMPORARY TABLE $tmp_table (
			user_id MEDIUMINT NOT NULL DEFAULT '0',
			user_level TINYINT NOT NULL DEFAULT '0',
			PRIMARY KEY (user_id)
		) ENGINE = MEMORY
	");

	$db->query("
		REPLACE INTO $tmp_table (user_id, user_level)
			SELECT u.user_id, ". USER ."
			FROM bb_users u
			WHERE user_level NOT IN(". USER .",". ADMIN .")
				$users_in
		UNION
			SELECT DISTINCT ug.user_id, ". GROUP_MEMBER ."
			FROM bb_groups g, bb_user_group ug
			WHERE g.group_single_user = 0
				AND ug.group_id = g.group_id
				AND ug.user_pending = 0
					$user_groups_in
		UNION
			SELECT DISTINCT ug.user_id, ". MOD ."
			FROM bb_auth_access aa, bb_user_group ug
			WHERE aa.forum_perm & ". BF_AUTH_MOD ."
				AND ug.group_id = aa.group_id
				AND ug.user_pending = 0
					$user_groups_in
	");

	$db->query("
		UPDATE bb_users u, $tmp_table lev SET
			u.user_level = lev.user_level
		WHERE lev.user_id = u.user_id
			AND u.user_level NOT IN(". ADMIN .")
				$users_in
	");

	$db->query("DROP TEMPORARY TABLE $tmp_table");

	update_user_permissions($user_id);
	delete_orphan_usergroups();
	$datastore->update('moderators');
}

function delete_group ($group_id)
{
	global $db;

	$group_id = (int) $group_id;

	$db->query("
		DELETE ug, g, aa
		FROM bb_user_group ug
		LEFT JOIN bb_groups g ON(g.group_id = $group_id)
		LEFT JOIN bb_auth_access aa ON(aa.group_id = $group_id)
		WHERE ug.group_id = $group_id
	");

	update_user_level('all');
}

function add_user_into_group ($group_id, $user_id, $user_pending = 0)
{
	$args = $GLOBALS['db']->build_array('INSERT', array(
		'group_id'     => (int) $group_id,
		'user_id'      => (int) $user_id,
		'user_pending' => (int) $user_pending,
	));
	$GLOBALS['db']->query("REPLACE INTO bb_user_group " . $args);

	if (!$user_pending)
	{
		update_user_level($user_id);
	}
}

function delete_user_group ($group_id, $user_id)
{
	$GLOBALS['db']->query("
		DELETE FROM bb_user_group
		WHERE user_id = ". (int) $user_id ."
			AND group_id = ". (int) $group_id ."
	");

	update_user_level($user_id);
}

function create_user_group ($user_id)
{
	global $db;

	$db->query("INSERT INTO bb_groups (group_single_user) VALUES (1)");

	$group_id = (int) $db->sql_nextid();
	$user_id  = (int) $user_id;

	$db->query("INSERT INTO bb_user_group (user_id, group_id) VALUES ($user_id, $group_id)");

	return $group_id;
}

function get_group_data ($group_id)
{
	global $db;

	if ($group_id === 'all')
	{
		$sql = "SELECT g.*, u.username AS moderator_name, aa.group_id AS auth_mod
			FROM bb_groups g
			LEFT JOIN bb_users u ON(g.group_moderator = u.user_id)
			LEFT JOIN bb_auth_access aa ON(aa.group_id = g.group_id AND aa.forum_perm & ". BF_AUTH_MOD .")
			WHERE g.group_single_user = 0
			GROUP BY g.group_id
			ORDER BY g.group_name";
	}
	else
	{
		$sql = "SELECT g.*, u.username AS moderator_name, aa.group_id AS auth_mod
			FROM bb_groups g
			LEFT JOIN bb_users u ON(g.group_moderator = u.user_id)
			LEFT JOIN bb_auth_access aa ON(aa.group_id = g.group_id AND aa.forum_perm & ". BF_AUTH_MOD .")
			WHERE g.group_id = ". (int) $group_id ."
				AND g.group_single_user = 0
			LIMIT 1";
	}
	$method = ($group_id === 'all') ? 'fetch_rowset' : 'fetch_row';
	return $db->$method($sql);
}

function delete_permissions ($group_id = null, $user_id = null, $cat_id = null)
{
	global $db;

	$group_id = get_id_csv($group_id);
	$user_id  = get_id_csv($user_id);
	$cat_id   = get_id_csv($cat_id);

	$forums_join_sql = ($cat_id) ? "
		INNER JOIN bb_forums f ON(a.forum_id = f.forum_id AND f.cat_id IN($cat_id))
	" : '';

	if ($group_id)
	{
		$db->query("DELETE a FROM bb_auth_access a $forums_join_sql WHERE a.group_id IN($group_id)");
	}
	if ($user_id)
	{
		$db->query("DELETE a FROM bb_auth_access_snap a $forums_join_sql WHERE a.user_id IN($user_id)");
	}
}

function store_permissions ($group_id, $auth_ary)
{
	global $db;

	if (empty($auth_ary) || !is_array($auth_ary)) return;

	$values = array();

	foreach ($auth_ary as $forum_id => $permission)
	{
		$values[] = array(
			'group_id'   => (int) $group_id,
			'forum_id'   => (int) $forum_id,
			'forum_perm' => (int) $permission,
		);
	}
	$values = $db->build_array('MULTI_INSERT', $values);

	$db->query("INSERT INTO bb_auth_access ". $values);
}

function update_user_permissions ($user_id = 'all')
{
	global $db;

	if (is_array($user_id))
	{
		$user_id = join(',', $user_id);
	}
	$delete_in = ($user_id !== 'all') ? " WHERE user_id IN($user_id)" : '';
	$users_in  = ($user_id !== 'all') ? "AND ug.user_id IN($user_id)" : '';

	$db->query("DELETE FROM bb_auth_access_snap " . $delete_in);

	$db->query("
		INSERT INTO bb_auth_access_snap
			(user_id, forum_id, forum_perm)
		SELECT
			ug.user_id, aa.forum_id, BIT_OR(aa.forum_perm)
		FROM
			bb_user_group ug,
			bb_groups g,
			bb_auth_access aa
		WHERE
			    ug.user_pending = 0
				$users_in
			AND g.group_id = ug.group_id
			AND aa.group_id = g.group_id
		GROUP BY
			ug.user_id, aa.forum_id
	");
}

function delete_orphan_usergroups ()
{
	global $db;

	// GROUP_SINGLE_USER without AUTH_ACCESS
	$db->query("
		DELETE g
		FROM bb_groups g
		LEFT JOIN bb_auth_access aa USING(group_id)
		WHERE g.group_single_user = 1
			AND aa.group_id IS NULL
	");

	// orphan USER_GROUP (against GROUP table)
	$db->query("
		DELETE ug
		FROM bb_user_group ug
		LEFT JOIN bb_groups g USING(group_id)
		WHERE g.group_id IS NULL
	");
}


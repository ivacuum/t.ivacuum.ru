<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $db;

$data = array(
	'name_users'  => array(),  // only by personal permissions
	'name_groups' => array(),  // only visible to all users
	'mod_users'   => array(),  // only by personal permissions
	'mod_groups'  => array(),  // only visible to all users
	'moderators'  => array(),  // all moderators
	'admins'      => array(),  // all admins
);

// name_users
// mod_users
$sql = "
	SELECT
		aa.forum_id, u.user_id, u.username
	FROM
		bb_auth_access aa,
		bb_user_group ug,
		bb_groups g,
		bb_users u
	WHERE
				aa.forum_perm & ". BF_AUTH_MOD ."
		AND ug.group_id = aa.group_id
		AND ug.user_pending = 0
		AND g.group_id = ug.group_id
		AND g.group_single_user = 1
		AND u.user_id = ug.user_id
	GROUP BY
		aa.forum_id, u.user_id
	ORDER BY
		u.username
";

foreach ($db->fetch_rowset($sql) as $row)
{
	$data['name_users'][$row['user_id']] = $row['username'];
	$data['mod_users'][$row['forum_id']][] = $row['user_id'];
}

// name_groups
// mod_groups
$sql = "
	SELECT
		aa.forum_id, g.group_id, g.group_name
	FROM
		bb_auth_access aa,
		bb_groups g
	WHERE
				aa.forum_perm & ". BF_AUTH_MOD ."
		AND g.group_id = aa.group_id
		AND g.group_single_user = 0
		AND g.group_type != ". GROUP_HIDDEN ."
	GROUP BY
		aa.forum_id, g.group_id
	ORDER BY
		g.group_name
";

foreach ($db->fetch_rowset($sql) as $row)
{
	$data['name_groups'][$row['group_id']] = $row['group_name'];
	$data['mod_groups'][$row['forum_id']][] = $row['group_id'];
}

// moderators
$sql = "
	SELECT
		u.user_id, u.username
	FROM
		bb_auth_access aa,
		bb_user_group ug,
		bb_groups g,
		bb_users u
	WHERE
				aa.forum_perm & ". BF_AUTH_MOD ."
		AND ug.group_id = aa.group_id
		AND ug.user_pending = 0
		AND g.group_id = ug.group_id
		AND u.user_id = ug.user_id
	GROUP BY
		u.user_id
	ORDER BY
		u.username
";

foreach ($db->fetch_rowset($sql) as $row)
{
	$data['moderators'][$row['user_id']] = $row['username'];
}

// admins
$sql = "
	SELECT user_id, username
	FROM bb_users
	WHERE user_level = ". ADMIN ."
	ORDER BY username
";

foreach ($db->fetch_rowset($sql) as $row)
{
	$data['admins'][$row['user_id']] = $row['username'];
}

$this->store('moderators', $data);


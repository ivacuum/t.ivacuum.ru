<?php	
define('IN_PHPBB', true);
define('BB_ROOT', './');
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require('./common.php');

// Start Session Management
$user->session_start();

$do = request_var('do', '');

if ($do == 'attach_rules')
{
	if (!$forum_id = @intval(request_var('f', '')) OR !forum_exists($forum_id))
	{
		bb_die('invalid forum_id');
	}
	require(BB_ROOT .'attach_mod/attachment_mod.'. PHP_EXT);
	// Display the allowed Extension Groups and Upload Size
	$auth = auth(AUTH_ALL, $forum_id, $userdata);
	$_max_filesize = $attach_config['max_filesize'];
	
	if (!$auth['auth_attachments'] || !$auth['auth_view'])
	{
		bb_die('You are not allowed to call this file');
	}

	$sql = 'SELECT group_id, group_name, max_filesize, forum_permissions
		FROM ' . EXTENSION_GROUPS_TABLE . '
		WHERE allow_group = 1
		ORDER BY group_name ASC';

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not query Extension Groups.', '', __LINE__, __FILE__, $sql);
	}

	$allowed_filesize = array();
	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	// Ok, only process those Groups allowed within this forum
	$nothing = true;
	for ($i = 0; $i < $num_rows; $i++)
	{
		$auth_cache = trim($rows[$i]['forum_permissions']);

		$permit = ((is_forum_authed($auth_cache, $forum_id)) || trim($rows[$i]['forum_permissions']) == '');

		if ($permit)
		{
			$nothing = false;
			$group_name = $rows[$i]['group_name'];
			$f_size = intval(trim($rows[$i]['max_filesize']));
			$det_filesize = (!$f_size) ? $_max_filesize : $f_size;
			$size_lang = ($det_filesize >= 1048576) ? $lang['MB'] : (($det_filesize >= 1024) ? $lang['KB'] : $lang['Bytes']);

			if ($det_filesize >= 1048576)
			{
				$det_filesize = round($det_filesize / 1048576 * 100) / 100;
			}
			else if($det_filesize >= 1024)
			{
				$det_filesize = round($det_filesize / 1024 * 100) / 100;
			}

			$max_filesize = ($det_filesize == 0) ? $lang['Unlimited'] : $det_filesize . ' ' . $size_lang;

			$template->assign_block_vars('group_row', array(
				'GROUP_RULE_HEADER' => sprintf($lang['Group_rule_header'], $group_name, $max_filesize))
			);

			$sql = 'SELECT extension
				FROM ' . EXTENSIONS_TABLE . "
				WHERE group_id = " . (int) $rows[$i]['group_id'] . "
				ORDER BY extension ASC";

			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not query Extensions.', '', __LINE__, __FILE__, $sql);
			}

			$e_rows = $db->sql_fetchrowset($result);
			$e_num_rows = $db->sql_numrows($result);
			$db->sql_freeresult($result);

			for ($j = 0; $j < $e_num_rows; $j++)
			{
				$template->assign_block_vars('group_row.extension_row', array(
					'EXTENSION' => $e_rows[$j]['extension'])
				);
			}
		}
	}

	$template->assign_vars(array(
		'PAGE_TITLE' => $lang['Attach_rules_title'],
		'L_RULES_TITLE' => $lang['Attach_rules_title'],
		'L_EMPTY_GROUP_PERMS' => $lang['Note_user_empty_group_permissions'])
	);

	if ($nothing)
	{
		$template->assign_block_vars('switch_nothing', array());
	}

	print_page('attach_rules.tpl', 'simple');
}
elseif ($do == 'info')
{
	$req_mode = (string) request_var('show', 'not_found');

	$html_dir = BB_PATH .'/misc/html/';
	$require = file_exists($html_dir . $req_mode .'.html') ? $html_dir . $req_mode .'.html' : $html_dir . 'not_found.html';

	$in_info = true;

	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		<meta http-equiv="Content-Style-Type" content="text/css" />

		<link rel="stylesheet" href="./templates/default/css/main.css" type="text/css">
	</head>
	<body>

	<style type="text/css">
	#infobox-wrap { width: 760px; }
	#infobox-body {
		background: #FFFFFF; color: #000000; padding: 1em;
		height: 400px; overflow: auto; border: 1px inset #000000;
	}
	</style>

	<br />
				<?php require($require) ?>
	</body>
	</html>
	<?php
}
else
{
	message_die(GENERAL_ERROR, 'Invalid mode <br /> <a href="javascript:history.go(-1)">Go back</a>');
}

?>
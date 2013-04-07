<?php

require('./pagestart.php');

//
// Generate relevant output
//
if( isset($_GET['pane']) && $_GET['pane'] == 'left' )
{
	$dir = @opendir(".");

	$setmodules = 1;
	while( $file = @readdir($dir) )
	{
		if( preg_match("/^admin_.*?\.php$/", $file) )
		{
			include('./' . $file);
		}
	}

	@closedir($dir);

	unset($setmodules);

	$template->assign_vars(array(
		'TPL_ADMIN_NAVIGATE' => true,

		"U_FORUM_INDEX" => append_sid("/"),
		"U_ADMIN_INDEX" => append_sid("index.php?pane=right"),

		"L_FORUM_INDEX" => $lang['Main_index'],
		"L_ADMIN_INDEX" => $lang['Admin_Index'],
		"L_PREVIEW_FORUM" => $lang['Preview_forum'])
	);

	ksort($module);

	while( list($cat, $action_array) = each($module) )
	{
		$cat = ( !empty($lang[$cat]) ) ? $lang[$cat] : preg_replace("/_/", " ", $cat);

		$template->assign_block_vars("catrow", array(
			"ADMIN_CATEGORY" => $cat)
		);

		ksort($action_array);

		$row_count = 0;
		while( list($action, $file)	= each($action_array) )
		{
			$row_class = !($row_count % 2) ? 'row1' : 'row2';

			$action = ( !empty($lang[$action]) ) ? $lang[$action] : preg_replace("/_/", " ", $action);

			$template->assign_block_vars("catrow.modulerow", array(
				"ROW_CLASS" => $row_class,

				"ADMIN_MODULE" => $action,
				"U_ADMIN_MODULE" => append_sid($file))
			);
			$row_count++;
		}
	}
}
else if( isset($_GET['pane']) && $_GET['pane'] == 'right' )
{
	$template->assign_vars(array(
		'TPL_ADMIN_MAIN' => true,

		"L_WELCOME" => $lang['Welcome_phpBB'],
		"L_ADMIN_INTRO" => $lang['Admin_intro'],
		"L_FORUM_STATS" => $lang['Forum_stats'],
		"L_LAST_UPDATE" => $lang['Last_updated'],
		"L_IP_ADDRESS" => $lang['IP_Address'],
		"L_STATISTIC" => $lang['Statistic'],
		"L_VALUE" => $lang['Value'],
		"L_NUMBER_POSTS" => $lang['Number_posts'],
		"L_POSTS_PER_DAY" => $lang['Posts_per_day'],
		"L_NUMBER_TOPICS" => $lang['Number_topics'],
		"L_TOPICS_PER_DAY" => $lang['Topics_per_day'],
		"L_NUMBER_USERS" => $lang['Number_users'],
		"L_USERS_PER_DAY" => $lang['Users_per_day'],
		"L_BOARD_STARTED" => $lang['Board_started'],
		"L_AVATAR_DIR_SIZE" => $lang['Avatar_dir_size'],
		"L_DB_SIZE" => $lang['Database_size'],
		"L_GZIP_COMPRESSION" => $lang['Gzip_compression'])
	);

	//
	// Get forum statistics
	//
	$total_posts = get_db_stat('postcount');
	$total_users = get_db_stat('usercount');
	$total_topics = get_db_stat('topiccount');

	$start_date = create_date($bb_cfg['default_dateformat'], $bb_cfg['board_startdate'], $bb_cfg['board_timezone']);

	$boarddays = ( time() - $bb_cfg['board_startdate'] ) / 86400;

	$posts_per_day = sprintf("%.2f", $total_posts / $boarddays);
	$topics_per_day = sprintf("%.2f", $total_topics / $boarddays);
	$users_per_day = sprintf("%.2f", $total_users / $boarddays);

	$avatar_dir_size = 0;

	if ($avatar_dir = @opendir(SITE_DIR . $bb_cfg['avatar_path']))
	{
		while( $file = @readdir($avatar_dir) )
		{
			if( $file != "." && $file != ".." )
			{
				$avatar_dir_size += @filesize(SITE_DIR . $bb_cfg['avatar_path'] . "/" . $file);
			}
		}
		@closedir($avatar_dir);

		//
		// This bit of code translates the avatar directory size into human readable format
		// Borrowed the code from the PHP.net annoted manual, origanally written by:
		// Jesse (jesse@jess.on.ca)
		//
		if($avatar_dir_size >= 1048576)
		{
			$avatar_dir_size = round($avatar_dir_size / 1048576 * 100) / 100 . " MB";
		}
		else if($avatar_dir_size >= 1024)
		{
			$avatar_dir_size = round($avatar_dir_size / 1024 * 100) / 100 . " KB";
		}
		else
		{
			$avatar_dir_size = $avatar_dir_size . " Bytes";
		}

	}
	else
	{
		// Couldn't open Avatar dir.
		$avatar_dir_size = $lang['Not_available'];
	}

	if(intval($posts_per_day) > $total_posts)
	{
		$posts_per_day = $total_posts;
	}

	if(intval($topics_per_day) > $total_topics)
	{
		$topics_per_day = $total_topics;
	}

	if($users_per_day > $total_users)
	{
		$users_per_day = $total_users;
	}

	//
	// DB size ... MySQL only
	//
	// This code is heavily influenced by a similar routine
	// in phpMyAdmin 2.2.0
	//
	if( preg_match("/^mysql/", SQL_LAYER) )
	{
		$sql = "SELECT VERSION() AS mysql_version";
		if($result = $db->sql_query($sql))
		{
			$row = $db->sql_fetchrow($result);
			$version = $row['mysql_version'];

			if( preg_match("/^(3\.23|4\.|5\.)/", $version) )
			{
				$db_name = ( preg_match("/^(3\.23\.[6-9])|(3\.23\.[1-9][1-9])|(4\.)|(5\.)/", $version) ) ? "`$dbname`" : $dbname;

				$sql = "SHOW TABLE STATUS
					FROM " . $db_name;
				if($result = $db->sql_query($sql))
				{
					$tabledata_ary = $db->sql_fetchrowset($result);

					$dbsize = 0;
					for($i = 0; $i < count($tabledata_ary); $i++)
					{
						if( @$tabledata_ary[$i]['Type'] != "MRG_MyISAM" )
						{
							if( $table_prefix != "" )
							{
								if( strstr($tabledata_ary[$i]['Name'], $table_prefix) )
								{
									$dbsize += $tabledata_ary[$i]['Data_length'] + $tabledata_ary[$i]['Index_length'];
								}
							}
							else
							{
								$dbsize += $tabledata_ary[$i]['Data_length'] + $tabledata_ary[$i]['Index_length'];
							}
						}
					}
				} // Else we couldn't get the table status.
			}
			else
			{
				$dbsize = $lang['Not_available'];
			}
		}
		else
		{
			$dbsize = $lang['Not_available'];
		}
	}
	else if( preg_match("/^mssql/", SQL_LAYER) )
	{
		$sql = "SELECT ((SUM(size) * 8.0) * 1024.0) as dbsize
			FROM sysfiles";
		if( $result = $db->sql_query($sql) )
		{
			$dbsize = ( $row = $db->sql_fetchrow($result) ) ? intval($row['dbsize']) : $lang['Not_available'];
		}
		else
		{
			$dbsize = $lang['Not_available'];
		}
	}
	else
	{
		$dbsize = $lang['Not_available'];
	}

	if ( is_integer($dbsize) )
	{
		if( $dbsize >= 1048576 )
		{
			$dbsize = sprintf("%.2f MB", ( $dbsize / 1048576 ));
		}
		else if( $dbsize >= 1024 )
		{
			$dbsize = sprintf("%.2f KB", ( $dbsize / 1024 ));
		}
		else
		{
			$dbsize = sprintf("%.2f Bytes", $dbsize);
		}
	}

	$template->assign_vars(array(
		"NUMBER_OF_POSTS" => $total_posts,
		"NUMBER_OF_TOPICS" => $total_topics,
		"NUMBER_OF_USERS" => $total_users,
		"START_DATE" => $start_date,
		"POSTS_PER_DAY" => $posts_per_day,
		"TOPICS_PER_DAY" => $topics_per_day,
		"USERS_PER_DAY" => $users_per_day,
		"AVATAR_DIR_SIZE" => $avatar_dir_size,
		"DB_SIZE" => $dbsize,
	));
	//
	// End forum statistics
	//
	if (@$_GET['users_online'])
	{
		$template->assign_vars(array(
			'SHOW_USERS_ONLINE' => true,
		));
		//
		// Get users online information.
		//
		$sql = "SELECT u.user_id, u.username, s.session_time AS user_session_time, u.user_allow_viewonline, s.session_logged_in, s.session_ip, s.session_start
			FROM " . USERS_TABLE . " u, " . SESSIONS_TABLE . " s
			WHERE s.session_logged_in = 1
				AND u.user_id = s.session_user_id
				AND u.user_id <> " . ANONYMOUS . "
				AND s.session_time >= " . ( time() - 300 ) . "
			ORDER BY s.session_ip ASC, s.session_time DESC";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Couldn't obtain regd user/online information.", "", __LINE__, __FILE__, $sql);
		}
		$onlinerow_reg = $db->sql_fetchrowset($result);

		$sql = "SELECT session_logged_in, session_time, session_ip, session_start
			FROM " . SESSIONS_TABLE . "
			WHERE session_logged_in = 0
				AND session_time >= " . ( time() - 300 ) . "
			ORDER BY session_ip ASC, session_time DESC";
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Couldn't obtain guest user/online information.", "", __LINE__, __FILE__, $sql);
		}
		$onlinerow_guest = $db->sql_fetchrowset($result);

		$sql = "SELECT forum_name, forum_id
			FROM " . FORUMS_TABLE;
		if($forums_result = $db->sql_query($sql))
		{
			while($forumsrow = $db->sql_fetchrow($forums_result))
			{
				$forum_data[$forumsrow['forum_id']] = $forumsrow['forum_name'];
			}
		}
		else
		{
			message_die(GENERAL_ERROR, "Couldn't obtain user/online forums information.", "", __LINE__, __FILE__, $sql);
		}

		$reg_userid_ary = array();

		if( count($onlinerow_reg) )
		{
			$registered_users = 0;

			for($i=0, $cnt=count($onlinerow_reg); $i < $cnt; $i++)
			{
				if( !inarray($onlinerow_reg[$i]['user_id'], $reg_userid_ary) )
				{
					$reg_userid_ary[] = $onlinerow_reg[$i]['user_id'];

					$username = $onlinerow_reg[$i]['username'];

					if( $onlinerow_reg[$i]['user_allow_viewonline'] )
					{
						$registered_users++;
						$hidden = FALSE;
					}
					else
					{
						@$hidden_users++;
						$hidden = TRUE;
					}

					$row_class = 'row1';

					$reg_ip = decode_ip($onlinerow_reg[$i]['session_ip']);

					$template->assign_block_vars("reg_user_row", array(
						"ROW_CLASS" => $row_class,
						"USERNAME" => $username,
						"STARTED" => create_date('H:i', $onlinerow_reg[$i]['session_start'], $bb_cfg['board_timezone']),
						"LASTUPDATE" => create_date('H:i', $onlinerow_reg[$i]['user_session_time'], $bb_cfg['board_timezone']),
						"IP_ADDRESS" => $reg_ip,

						"U_WHOIS_IP" => "http://www.dnsstuff.com/tools/whois.ch?ip=$reg_ip",
						"U_USER_PROFILE" => append_sid("admin_users.php?mode=edit&amp;" . POST_USERS_URL . "=" . $onlinerow_reg[$i]['user_id']),
					));
				}
			}

		}
		else
		{
			$template->assign_vars(array(
				"L_NO_REGISTERED_USERS_BROWSING" => $lang['No_users_browsing'])
			);
		}

		//
		// Guest users
		//
		if( count($onlinerow_guest) )
		{
			$guest_users = 0;

			for($i = 0; $i < count($onlinerow_guest); $i++)
			{
				$guest_userip_ary[] = $onlinerow_guest[$i]['session_ip'];
				$guest_users++;

				$row_class = 'row2';

				$guest_ip = decode_ip($onlinerow_guest[$i]['session_ip']);

				$template->assign_block_vars("guest_user_row", array(
					"ROW_CLASS" => $row_class,
					"USERNAME" => $lang['Guest'],
					"STARTED" => create_date('H:i', $onlinerow_guest[$i]['session_start'], $bb_cfg['board_timezone']),
					"LASTUPDATE" => create_date('H:i', $onlinerow_guest[$i]['session_time'], $bb_cfg['board_timezone']),
					"IP_ADDRESS" => $guest_ip,

					"U_WHOIS_IP" => "http://www.dnsstuff.com/tools/whois.ch?ip=$guest_ip",
				));
			}
		}
		else
		{
			$template->assign_vars(array(
				"L_NO_GUESTS_BROWSING" => $lang['No_users_browsing'])
			);
		}
	}
	else
	{
		$template->assign_vars(array(
			'USERS_ONLINE_HREF' => "index.php?pane=right&users_online=1&sid={$userdata['session_id']}",
		));
	}

	// Check for new version
	$current_version = explode('.', '2' . $bb_cfg['version']);
	$minor_revision = (int) $current_version[2];

	$errno = 0;
	$errstr = $version_info = $fsock = '';

	if ($fsock /* = @fsockopen('www.phpbb.com', 80, $errno, $errstr, 10) */ )
	{
		@fputs($fsock, "GET /updatecheck/20x.txt HTTP/1.1\r\n");
		@fputs($fsock, "HOST: www.phpbb.com\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$get_info = false;
		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$version_info .= @fread($fsock, 1024);
			}
			else
			{
				if (@fgets($fsock, 1024) == "\r\n")
				{
					$get_info = true;
				}
			}
		}
		@fclose($fsock);

		$version_info = explode("\n", $version_info);
		$latest_head_revision = (int) $version_info[0];
		$latest_minor_revision = (int) $version_info[2];
		$latest_version = (int) $version_info[0] . '.' . (int) $version_info[1] . '.' . (int) $version_info[2];

		if ($latest_head_revision == 2 && $minor_revision == $latest_minor_revision)
		{
			$version_info = '<p style="color:green">' . $lang['Version_up_to_date'] . '</p>';
		}
		else
		{
			$version_info = '<p style="color:red">' . $lang['Version_not_up_to_date'];
			$version_info .= '<br />' . sprintf($lang['Latest_version_info'], $latest_version) . '<br />' . sprintf($lang['Current_version_info'], '2' . $bb_cfg['version']) . '</p>';
		}
	}
	else
	{
		if ($errstr)
		{
			$version_info = '<p style="color:red">' . sprintf($lang['Connect_socket_error'], $errstr) . '</p>';
		}
		else
		{
			$version_info = '<p>' . $lang['Socket_functions_disabled'] . '</p>';
		}
	}

	$version_info .= '<p>' . $lang['Mailing_list_subscribe_reminder'] . '</p>';


	$template->assign_vars(array(
		'VERSION_INFO'	=> $version_info,
		'L_VERSION_INFORMATION'	=> $lang['Version_information'],
	));

	$template->assign_vars(array(
		'U_CLEAR_DATASTORE'   => "index.php?clear_datastore=1",
		'U_CLEAR_TPL_CACHE'   => "xs_cache.php?clear=",
		'U_UPDATE_NET_NEWS'   => "index.php?update_net_news=1",
		'U_UPDATE_USER_LEVEL' => "index.php?update_user_level=1",
		'U_SYNC_TOPICS'       => "index.php?sync_topics=1",
		'U_SYNC_USER_POSTS'   => "index.php?sync_user_posts=1",
	));
}
else if (isset($_REQUEST['clear_datastore']))
{
	$datastore->clean();
	bb_die('Datastore cleared');
}
else if (isset($_REQUEST['update_net_news']))
{
	require(SITE_DIR . 'includes/cron/jobs/update_net_news.php');
	bb_die('Net news updated');
}
else if (isset($_REQUEST['update_user_level']))
{
	require(SITE_DIR . 'includes/functions_group.php');
	update_user_level('all');
	bb_die('User levels updated');
}
else if (isset($_REQUEST['sync_topics']))
{
	sync('topic', 'all');
	sync('forum', 'all');
	bb_die('Topics data synchronized');
}
else if (isset($_REQUEST['sync_user_posts']))
{
	sync('user_posts', 'all');
	bb_die('User posts count synchronized');
}
else
{
	//
	// Generate frameset
	//
	$template->assign_vars(array(
		'TPL_ADMIN_FRAMESET' => true,
		'S_FRAME_NAV'        => "index.php?pane=left",
		'S_FRAME_MAIN'       => "index.php?pane=right",
	));
	send_no_cache_headers();
	print_page('index.tpl', 'admin', 'no_header');
}

print_page('index.tpl', 'admin');

//
// Functions
//
function inarray($needle, $haystack)
{
	for($i = 0; $i < sizeof($haystack); $i++ )
	{
		if( $haystack[$i] == $needle )
		{
			return true;
		}
	}
	return false;
}

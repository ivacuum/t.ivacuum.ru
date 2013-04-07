<?php

if (!defined('SITE_DIR'))
{
	exit;
}

function get_torrent_info ($attach_id)
{
	global $db;

	$attach_id = intval($attach_id);

	$sql = "
		SELECT
			a.post_id, d.physical_filename, d.extension, d.tracker_status,
			t.topic_first_post_id,
			p.poster_id, p.topic_id, p.forum_id,
			f.allow_reg_tracker
		FROM
			bb_attachments a,
			bb_attachments_desc d,
			". POSTS_TABLE            ." p,
			". TOPICS_TABLE           ." t,
			". FORUMS_TABLE           ." f
		WHERE
			    a.attach_id = $attach_id
			AND d.attach_id = $attach_id
			AND p.post_id = a.post_id
			AND t.topic_id = p.topic_id
			AND f.forum_id = p.forum_id
		LIMIT 1
	";

	if (!$torrent = $db->fetch_row($sql))
	{
		message_die(GENERAL_ERROR, 'Invalid attach_id');
	}

	return $torrent;
}

function torrent_auth_check ($forum_id, $poster_id)
{
	global $userdata, $lang, $attach_config;

	if (IS_ADMIN) return true;

	$is_auth = auth(AUTH_ALL, $forum_id, $userdata);

	if ($poster_id != $userdata['user_id'] && !$is_auth['auth_mod'])
	{
		message_die(GENERAL_MESSAGE, $lang['Not_Moderator'], $lang['Not_Authorised']);
	}
	else if (!$is_auth['auth_view'] || !$is_auth['auth_attachments'] || $attach_config['disable_mod'])
	{
		$message = sprintf($lang['Sorry_auth_read'], $is_auth['auth_read_type']);
		message_die(GENERAL_MESSAGE, $message);
	}
	return $is_auth;
}

function tracker_unregister ($attach_id, $mode = '')
{
	global $db, $lang, $board_config;

	$attach_id = (int) $attach_id;
	$post_id = $topic_id = $forum_id = null;
	$del_info_hash = '';

	// Get torrent info
	if ($torrent = get_torrent_info($attach_id))
	{
		$post_id  = $torrent['post_id'];
		$topic_id = $torrent['topic_id'];
		$forum_id = $torrent['forum_id'];
	}

	if ($mode == 'request')
	{
		if (!$torrent)
		{
			message_die(GENERAL_ERROR, 'Torrent not found');
		}
		if (!$torrent['tracker_status'])
		{
			message_die(GENERAL_ERROR, 'Torrent already unregistered');
		}
		torrent_auth_check($forum_id, $torrent['poster_id']);
	}

	if (!$topic_id)
	{
		$sql = "SELECT topic_id, info_hash
			FROM ". BT_TORRENTS_TABLE ."
			WHERE attach_id = $attach_id";

		if (!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not query torrent information', '', __LINE__, __FILE__, $sql);
		}
		if ($row = $db->sql_fetchrow($result))
		{
			$topic_id = $row['topic_id'];
			$del_info_hash = $row['info_hash'];
		}
	}

	// Unset DL-Type for topic
	if ($board_config['bt_unset_dltype_on_tor_unreg'] && $topic_id)
	{
		$sql = "UPDATE ". TOPICS_TABLE ." SET
				topic_dl_type = ". TOPIC_DL_TYPE_NORMAL ."
			WHERE topic_id = $topic_id
			LIMIT 1";

		if (!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update topics table', '', __LINE__, __FILE__, $sql);
		}
	}

	$sql = "SELECT topic_id, info_hash
		FROM ". BT_TORRENTS_TABLE ."
		WHERE attach_id = $attach_id";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	$topic_id      = $row['topic_id'];
	$del_info_hash = $row['info_hash'];

	if( $topic_id > 0 && $del_info_hash )
	{
		$sql = 'INSERT IGNORE INTO ' . BT_TORRENTS_TABLE . '_del (topic_id, info_hash, is_del) VALUES (' . $topic_id . ', "' . $db->escape($del_info_hash) . '", 1)';
		$db->sql_query($sql);

		// Remove peers from tracker
		$sql = "DELETE FROM ". BT_TRACKER_TABLE ."
			WHERE topic_id = $topic_id";

		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not delete peers', '', __LINE__, __FILE__, $sql);
		}
	}

	// Delete torrent
	$sql = "DELETE FROM ". BT_TORRENTS_TABLE ."
		WHERE attach_id = $attach_id";

	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not delete torrent from torrents table', '', __LINE__, __FILE__, $sql);
	}

	// Update tracker_status
	$sql = "UPDATE bb_attachments_desc SET
			tracker_status = 0
		WHERE attach_id = $attach_id
		LIMIT 1";

	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not update torrent status', '', __LINE__, __FILE__, $sql);
	}

	if ($mode == 'request')
	{
		exit_redirect($lang['Bt_Deleted'], $post_id, $forum_id);
	}
}

function delete_torrent ($attach_id, $mode = '')
{
	global $lang, $userdata;
	global $reg_mode, $topic_id;

	$attach_id = intval($attach_id);
	$reg_mode = $mode;

	if (!$torrent = get_torrent_info($attach_id))
	{
		message_die(GENERAL_ERROR, 'Torrent not found');
	}

	$post_id   = $torrent['post_id'];
	$topic_id  = $torrent['topic_id'];
	$forum_id  = $torrent['forum_id'];
	$poster_id = $torrent['poster_id'];

	if ($torrent['extension'] !== TORRENT_EXT)
	{
		message_die(GENERAL_ERROR, $lang['Not_torrent']);
	}

	torrent_auth_check($forum_id, $torrent['poster_id']);
	tracker_unregister($attach_id);
	delete_attachment(0, $attach_id);

	return;
}

function change_tor_status ($attach_id, $new_tor_status)
{
	global $db, $topic_id;

	$attach_id = (int) $attach_id;
	$new_tor_status = (int) $new_tor_status;

	if (!$torrent = get_torrent_info($attach_id))
	{
		bb_die('Torrent not found');
	}

	$topic_id = $torrent['topic_id'];

	torrent_auth_check($torrent['forum_id'], $torrent['poster_id']);

	$db->query("
		UPDATE ". BT_TORRENTS_TABLE ." SET
			tor_status = $new_tor_status
		WHERE attach_id = $attach_id
		LIMIT 1
	");
}

function tracker_register ($attach_id, $mode = '')
{
	global $db, $template, $attach_config, $board_config, $lang, $return_message;
	global $reg_mode;

	$attach_id = intval($attach_id);
	$reg_mode = $mode;

	if (!$torrent = get_torrent_info($attach_id))
	{
		message_die(GENERAL_ERROR, 'Torrent not found');
	}

	$post_id   = $torrent['post_id'];
	$topic_id  = $torrent['topic_id'];
	$forum_id  = $torrent['forum_id'];
	$poster_id = $torrent['poster_id'];

	if ($torrent['extension'] !== TORRENT_EXT)
	{
		torrent_error_exit($lang['Not_torrent']);
	}

	if (!$torrent['allow_reg_tracker'])
	{
		torrent_error_exit($lang['Reg_not_allowed_in_this_forum']);
	}

	if ($post_id != $torrent['topic_first_post_id'])
	{
		torrent_error_exit($lang['Allowed_only_1st_post_reg']);
	}

	if ($torrent['tracker_status'])
	{
		torrent_error_exit($lang['Already_reg']);
	}

	if ($this_topic_torrents = get_registered_torrents($topic_id, 'topic'))
	{
		torrent_error_exit($lang['Only_1_tor_per_topic']);
	}

	torrent_auth_check($forum_id, $torrent['poster_id']);

	$filename = get_attachments_dir() .'/'. $torrent['physical_filename'];

	if (!is_file($filename))
	{
		torrent_error_exit('File name error');
	}

	if (!file_exists($filename))
	{
		torrent_error_exit('File not exists');
	}

	if (!$tor = bdecode_file($filename))
	{
		torrent_error_exit('This is not a bencoded file');
	}

	if ($board_config['bt_check_announce_url'])
	{
		include_once(SITE_DIR .'includes/torrent_announce_urls.php');

		$ann = (@$tor['announce']) ? $tor['announce'] : '';
		$announce_urls['main_url'] = $board_config['bt_announce_url'];

		if (!$ann || !in_array($ann, $announce_urls))
		{
			$msg = sprintf($lang['Invalid_ann_url'], htmlspecialchars($ann), $announce_urls['main_url']);
			torrent_error_exit($msg);
		}
	}

	$info = (@$tor['info']) ? $tor['info'] : array();

	if (!@$info['name'] || !@$info['piece length'] || !@$info['pieces'] || strlen($info['pieces']) % 20 != 0)
	{
		torrent_error_exit('Invalid torrent file');
	}

	$board_config['bt_disable_dht'] = 1;

	if( $board_config['bt_disable_dht'] )
	{
		$info['private'] = 1;
		$info['tracker'] = isset($bb_cfg['torrent_sign']) ? $bb_cfg['torrent_sign'] : str_replace('internet.', '', $_SERVER['SERVER_NAME']);

		$tor['info'] = $info;

		$fp = fopen($filename, 'w+');
		fwrite($fp, bencode($tor));
		fclose($fp);
	}

	$info_hash     = pack('H*', sha1(bencode($info)));
	// $info_hash_sql = rtrim($db->escape($info_hash), ' ');
	$info_hash_sql = $db->escape($info_hash);

	$sql = "SELECT topic_id
		FROM ". BT_TORRENTS_TABLE ."
		WHERE info_hash = '$info_hash_sql'
		LIMIT 1";

	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not obtain torrent info', '', __LINE__, __FILE__, $sql);
	}
	if ($row = $db->sql_fetchrow($result))
	{
		$msg = sprintf($lang['Bt_Reg_fail_same_hash'], append_sid(TOPIC_URL . $row['topic_id']));
		torrent_error_exit($msg);
	}

	$totallen = 0;

	if (@$info['length'])
	{
		$totallen = (float) $info['length'];
	}
	else if (@$info['files'] && is_array($info['files']))
	{
		foreach ($info['files'] as $fn => $f)
		{
			$totallen += (float) $f['length'];
		}
	}
	else
	{
		torrent_error_exit('Invalid torrent file');
	}

	$reg_time = TIMENOW;
	$size = sprintf('%.0f', (float) $totallen);

	$columns = '      info_hash,  post_id,  poster_id,  topic_id,  forum_id,  attach_id,   size,   reg_time';
	$values = "'$info_hash_sql', $post_id, $poster_id, $topic_id, $forum_id, $attach_id, '$size', $reg_time";

	$sql = "INSERT INTO ". BT_TORRENTS_TABLE ." ($columns) VALUES ($values)";

	if (!$db->sql_query($sql))
	{
		$sql_error = $db->sql_error();

		if ($sql_error['code'] == 1062) // Duplicate entry
		{
			torrent_error_exit($lang['Bt_Reg_fail_same_hash']);
		}
		message_die(GENERAL_ERROR, 'Could not register torrent on tracker', '', __LINE__, __FILE__, $sql);
	}

	// update tracker status for this attachment
	$sql = "UPDATE bb_attachments_desc SET
			tracker_status = 1
		WHERE attach_id = $attach_id
		LIMIT 1";

	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not update torrent status', '', __LINE__, __FILE__, $sql);
	}

	// set DL-Type for topic
	if ($board_config['bt_set_dltype_on_tor_reg'])
	{
		$sql = 'UPDATE '. TOPICS_TABLE .' SET
				topic_dl_type = '. TOPIC_DL_TYPE_DL ."
			WHERE topic_id = $topic_id
			LIMIT 1";

		if (!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not update topics table', '', __LINE__, __FILE__, $sql);
		}
	}

	if ($reg_mode == 'request' || $reg_mode == 'newtopic')
	{
		$mess = sprintf($lang['Bt_Registered'], append_sid("download.php?id=$attach_id"));
		exit_redirect($mess, $post_id, $forum_id);
	}

	return;
}

function send_torrent_with_passkey ($filename)
{
	global $attachment, $auth_pages, $db, $userdata, $board_config, $lang, $bb_cfg;

	if (!$board_config['bt_add_auth_key'] || $attachment['extension'] !== TORRENT_EXT || !$size = @filesize($filename))
	{
		return;
	}

	$post_id = $poster_id = $passkey_val = '';
	$user_id = $userdata['user_id'];
	$attach_id = $attachment['attach_id'];

	if (!$passkey_key = $bb_cfg['passkey_key'])
	{
		message_die(GENERAL_ERROR, 'Could not add passkey (wrong config $bb_cfg[\'passkey_key\'])');
	}

	// Get $post_id & $poster_id
	foreach ($auth_pages as $rid => $row)
	{
		if ($row['attach_id'] == $attach_id)
		{
			$post_id = $row['post_id'];
			$poster_id = $row['user_id_1'];
			break;
		}
	}

	// Redirect guests to login page
	if (IS_GUEST)
	{
		$redirect_url = ($post_id) ? POST_URL . $post_id : '/';
		redirect(LOGIN_URL . $redirect_url);
	}

	if (!$attachment['tracker_status'])
	{
		message_die(GENERAL_ERROR, $lang['Passkey_err_tor_not_reg']);
	}

	if ($userdata['session_logged_in'] && !$userdata['user_allow_passkey'])
	{
		message_die(GENERAL_ERROR, 'Could not add passkey<br /><br />You are not authorized to use passkey');
	}

	if ($bt_userdata = get_bt_userdata($user_id))
	{
		$passkey_val = $bt_userdata['auth_key'];
	}

	if (!$passkey_val && $userdata['session_logged_in'])
	{
		if ($board_config['bt_gen_passkey_on_reg'])
		{
			if (!$passkey_val = generate_passkey($user_id))
			{
				message_die(GENERAL_ERROR, 'Could not insert passkey', '', __LINE__, __FILE__, $sql);
			}
		}
		else
		{
			$mess = sprintf($lang['Passkey_err_empty'], append_sid("profile.php?mode=editprofile#bittorrent"));
			message_die(GENERAL_ERROR, $mess);
		}
	}

	// Ratio limits
	$min_ratio = $board_config['bt_min_ratio_allow_dl_tor'];

	if ($min_ratio && $user_id != $poster_id && ($user_ratio = get_bt_ratio($bt_userdata)) !== null)
	{
		if ($user_ratio < $min_ratio && $post_id)
		{
			$dl = $db->fetch_row("
				SELECT dl.user_status
				FROM ". POSTS_TABLE ." p
				LEFT JOIN ". BT_DLSTATUS_TABLE ." dl ON dl.topic_id = p.topic_id AND dl.user_id = $user_id
				WHERE p.post_id = $post_id
				LIMIT 1
			");

			if (!isset($dl['user_status']) || $dl['user_status'] != DL_STATUS_COMPLETE)
			{
				$mess = sprintf($lang['Bt_Low_ratio_for_dl'], round($user_ratio, 2), "search.php?dlu=$user_id&amp;dlc=1");
				message_die(GENERAL_ERROR, $mess);
			}
		}
	}

	// Seeding torrents limit
	if ($bb_cfg['max_seeding_torrents'] && IS_USER)
	{
		$seeding = $db->fetch_row("
			SELECT COUNT(DISTINCT topic_id) AS torrents, SUM(speed_up) AS sum_up
			FROM ". BT_TRACKER_TABLE ."
			WHERE user_id = $user_id
				AND seeder = 1
		");

		if ($seeding && $seeding['torrents'] > $bb_cfg['max_seeding_torrents'] && $bt_userdata['u_up_total'] < 200*1024*1024*1024)
		{
			if ($seeding['sum_up'] < ($seeding['torrents'] * $bb_cfg['min_up_speed_per_torrent']))
			{
				$msg = array();
				$msg[] = date('m-d H:i:s');
				$msg[] = sprintf('%-30s', html_entity_decode($userdata['username'])." ($user_id)");
				$msg[] = sprintf('%-3s', $seeding['torrents']);
				$msg[] = sprintf('%.2f', @$user_ratio);
				$msg[] = sprintf('%-9s', humn_size($bt_userdata['u_up_total'], '', '', ' '));
				$msg[] = humn_size($seeding['sum_up'], '', '', ' ');
				$msg = join(LOG_SEPR, $msg) . LOG_LF;
				bb_log($msg, 'overseed/current');

				redirect($bb_cfg['too_many_seeding_redirect_url']);
			}
		}
	}

	// Announce URL
	$ann_url = $board_config['bt_announce_url'];

	if (!$tor = bdecode_file($filename))
	{
		message_die(GENERAL_ERROR, 'This is not a bencoded file');
	}

	$passkey_url = (!$userdata['session_logged_in'] || isset($_GET['no_passkey'])) ? '' : "?$passkey_key=$passkey_val&";

	// Replace original announce url with tracker default
	if ($board_config['bt_replace_ann_url'] || !@$tor['announce'])
	{
		$tor['announce'] = strval($ann_url . $passkey_url);
	}

	// Delete all additional urls
	if ($board_config['bt_del_addit_ann_urls'])
	{
  	unset($tor['announce-list']);
	}
#	$tor['announce-list'] = array(array(
#		'url1' . $passkey_url,
#		'url2' . $passkey_url,
#	));

	// Add publisher & topic url
	$publisher = $board_config['bt_add_publisher'];
	$publisher_url = ($post_id) ? make_url(POST_URL . $post_id) : '';

	if ($publisher)
	{
		$tor['publisher'] = strval($publisher);
		unset($tor['publisher.utf-8']);

		if ($publisher_url)
		{
			$tor['publisher-url'] = strval($publisher_url);
			unset($tor['publisher-url.utf-8']);
		}
	}

	// Add comment
	$comment = '';

	$orig_com = (@$tor['comment']) ? $tor['comment'] : '';

	if ($board_config['bt_add_comment'])
	{
		$comment = $board_config['bt_add_comment'];
	}
	else
	{
		$comment = ($publisher_url) ? $publisher_url : '';
	}

	if ($comment = trim($comment))
	{
		$tor['comment'] = strval($comment);
		unset($tor['comment.utf-8']);
	}

	// DHT
	$board_config['bt_disable_dht'] = 1;

	if ($board_config['bt_disable_dht'])
	{
		$tor['private'] = (int) 1;
		unset($tor['nodes']);

		$tor['azureus_properties'] = array('dht_backup_enable' => intval(0));
	}

	// Send torrent
	$output   = bencode($tor);
	$filename = clean_filename(basename($attachment['real_filename']));
	$mimetype = 'application/x-bittorrent;';
	$charset  = (strpos(USER_AGENT, 'pera') && @$lang['CONTENT_ENCODING']) ? "charset={$lang['CONTENT_ENCODING']};" : '';

#	header("Content-length: ". strlen($output));
	header("Content-Type: $mimetype $charset name=\"$filename\"");
	header("Content-Disposition: attachment; filename=\"$filename\"");

	##### LOG #####
	global $log_ip_resp;

	if (isset($log_ip_resp[USER_IP]) || isset($log_ip_resp[CLIENT_IP]))
	{
		$str = date('H:i:s') . LOG_SEPR . str_compact(ob_get_contents()) . LOG_LF;
		$file = 'sessions/'. date('m-d') .'_{'. USER_IP .'}_'. CLIENT_IP .'_resp';
		bb_log($str, $file);
	}
	### LOG END ###

	bb_exit($output);
}

function generate_passkey ($user_id, $force_generate = false)
{
	global $db, $lang, $sql;

	$user_id = (int) $user_id;

	// Check if user can change passkey
	if (!$force_generate)
	{
		$sql = "SELECT user_allow_passkey
			FROM ". USERS_TABLE ."
			WHERE user_id = $user_id
			LIMIT 1";

		if (!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not query userdata for passkey', '', __LINE__, __FILE__, $sql);
		}
		if ($row = $db->sql_fetchrow($result))
		{
			if (!$row['user_allow_passkey'])
			{
				message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
			}
		}
	}

	for ($i=0; $i < 20; $i++)
	{
		$passkey_val = make_rand_str(BT_AUTH_KEY_LENGTH);

		// Insert new row
		$sql = "INSERT IGNORE INTO ". BT_USERS_TABLE ." (user_id, auth_key, key_regtime) VALUES ($user_id, '$passkey_val', " . time() . ")";

		if ($db->sql_query($sql) && $db->sql_affectedrows() == 1)
		{
			return $passkey_val;
		}
		// Update
		$sql = "UPDATE IGNORE ". BT_USERS_TABLE ." SET
				auth_key = '$passkey_val',
				key_regtime = " . time() . "
			WHERE user_id = $user_id
			LIMIT 1";

		if ($db->sql_query($sql) && $db->sql_affectedrows() == 1)
		{
			return $passkey_val;
		}
	}
	return false;
}

function tracker_rm_torrent ($topic_id)
{
	global $db;
	return $db->sql_query("DELETE FROM ". BT_TRACKER_TABLE ." WHERE topic_id = ". (int) $topic_id);
}

function tracker_rm_user ($user_id)
{
	global $db;
	return $db->sql_query("DELETE FROM ". BT_TRACKER_TABLE ." WHERE user_id = ". (int) $user_id);
}

function get_registered_torrents ($id, $mode)
{
	global $db;

	$field = ($mode == 'topic') ? 'topic_id' : 'post_id';

	$sql = "SELECT topic_id
		FROM ". BT_TORRENTS_TABLE ."
		WHERE $field = $id
		LIMIT 1";

	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not query torrent id', '', __LINE__, __FILE__, $sql);
	}

	if( $mode == 'topic' )
	{
		$sql = '
			SELECT
				info_hash
			FROM
				' . BT_TORRENTS_TABLE . '
			WHERE
				topic_id = ' . $id;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if( $row && $row['info_hash'] == str_pad('', 20, chr(0)) )
		{
			$sql = 'DELETE FROM ' . BT_TORRENTS_TABLE . ' WHERE topic_id = ' . $id;
			$db->sql_query($sql);
			return false;
		}
	}

	if ($rowset = @$db->sql_fetchrowset($result))
	{
		return $rowset;
	}
	else
	{
		return false;
	}
}

function exit_redirect ($message, $post_id, $forum_id)
{
	global $template, $lang;

	meta_refresh(3, append_sid("viewtopic.php?". POST_POST_URL ."=". $post_id));
	$exit_message = $message .'<br /><br />'. sprintf($lang['Click_return_topic'], '<a href="'. append_sid("viewtopic.php?". POST_POST_URL ."=". $post_id) .'#'. $post_id .'">', '</a>') .'<br /><br />'. sprintf($lang['Click_return_forum'], '<a href="'. append_sid("viewforum.php?". POST_FORUM_URL ."=$forum_id") .'">', '</a>');
	message_die(GENERAL_MESSAGE, $exit_message);
}

function torrent_error_exit ($message)
{
	global $reg_mode, $return_message, $lang;

	$err_code = GENERAL_ERROR;
	$msg = '';

	if (isset($reg_mode) && ($reg_mode == 'request' || $reg_mode == 'newtopic'))
	{
		if (isset($return_message))
		{
			$msg .= $return_message .'<br /><br /><hr /><br />';
		}
		$msg .= '<b>'. $lang['Bt_Reg_fail'] .'</b><br /><br />';

		$err_code = GENERAL_MESSAGE;
	}

	$msg .= $message;
	message_die($err_code, $msg);
}

// bdecode: based on OpenTracker [http://whitsoftdev.com/opentracker]
function bdecode_file ($filename)
{
	if (!$fp = fopen($filename, 'rb'))
	{
		return null;
	}
	$fc = fread($fp, filesize($filename));
	fclose($fp);

	return bdecode($fc);
}

function bdecode ($str)
{
	$pos = 0;
	return bdecode_r($str, $pos);
}

function bdecode_r ($str, &$pos)
{
	$strlen = strlen($str);

	if (($pos < 0) || ($pos >= $strlen))
	{
		return null;
	}
	else if ($str{$pos} == 'i')
	{
		$pos++;
		$numlen = strspn($str, '-0123456789', $pos);
		$spos = $pos;
		$pos += $numlen;

		if (($pos >= $strlen) || ($str{$pos} != 'e'))
		{
			return null;
		}
		else
		{
			$pos++;
			return floatval(substr($str, $spos, $numlen));
		}
	}
	else if ($str{$pos} == 'd')
	{
		$pos++;
		$ret = array();

		while ($pos < $strlen)
		{
			if ($str{$pos} == 'e')
			{
				$pos++;
				return $ret;
			}
			else
			{
				$key = bdecode_r($str, $pos);

				if ($key === null)
				{
					return null;
				}
				else
				{
					$val = bdecode_r($str, $pos);

					if ($val === null)
					{
						return null;
					}
					else if (!is_array($key))
					{
						$ret[$key] = $val;
					}
				}
			}
		}
		return null;
	}
	else if ($str{$pos} == 'l')
	{
		$pos++;
		$ret = array();

		while ($pos < $strlen)
		{
			if ($str{$pos} == 'e')
			{
				$pos++;
				return $ret;
			}
			else
			{
				$val = bdecode_r($str, $pos);

				if ($val === null)
				{
					return null;
				}
				else
				{
					$ret[] = $val;
				}
			}
		}
		return null;
	}
	else
	{
		$numlen = strspn($str, '0123456789', $pos);
		$spos = $pos;
		$pos += $numlen;

		if (($pos >= $strlen) || ($str{$pos} != ':'))
		{
			return null;
		}
		else
		{
			$vallen = intval(substr($str, $spos, $numlen));
			$pos++;
			$val = substr($str, $pos, $vallen);

			if (strlen($val) != $vallen)
			{
				return null;
			}
			else
			{
				$pos += $vallen;
				return $val;
			}
		}
	}
}


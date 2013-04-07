<?php

/**
* All Attachment Functions needed everywhere
*/

/**
* html_entity_decode replacement (from php manual)
*/
if (!function_exists('html_entity_decode'))
{
	function html_entity_decode($given_html, $quote_style = ENT_QUOTES)
	{
		$trans_table = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style));
		$trans_table['&#39;'] = "'";
		return (strtr($given_html, $trans_table));
	}
}

/**
* A simple dectobase64 function
*/
function base64_pack($number)
{
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	if ($number > 4096)
	{
		return;
	}
	else if ($number < $base)
	{
		return $chars[$number];
	}

	$hexval = '';

	while ($number > 0)
	{
		$remainder = $number%$base;

		if ($remainder < $base)
		{
			$hexval = $chars[$remainder] . $hexval;
		}

		$number = floor($number/$base);
	}

	return $hexval;
}

/**
* base64todec function
*/
function base64_unpack($string)
{
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';
	$base = strlen($chars);

	$length = strlen($string);
	$number = 0;

	for($i = 1; $i <= $length; $i++)
	{
		$pos = $length - $i;
		$operand = strpos($chars, substr($string,$pos,1));
		$exponent = pow($base, $i-1);
		$decValue = $operand * $exponent;
		$number += $decValue;
	}

	return $number;
}

/**
* Per Forum based Extension Group Permissions (Encode Number) -> Theoretically up to 158 Forums saveable. :)
* We are using a base of 64, but splitting it to one-char and two-char numbers. :)
*/
function auth_pack($auth_array)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';
	$one_char = $two_char = false;
	$auth_cache = '';

	for ($i = 0; $i < sizeof($auth_array); $i++)
	{
		$val = base64_pack(intval($auth_array[$i]));
		if (strlen($val) == 1 && !$one_char)
		{
			$auth_cache .= $one_char_encoding;
			$one_char = true;
		}
		else if (strlen($val) == 2 && !$two_char)
		{
			$auth_cache .= $two_char_encoding;
			$two_char = true;
		}

		$auth_cache .= $val;
	}

	return $auth_cache;
}

/**
* Reverse the auth_pack process
*/
function auth_unpack($auth_cache)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	$auth = array();
	$auth_len = 1;

	for ($pos = 0; $pos < strlen($auth_cache); $pos += $auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}

		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = base64_unpack($forum_auth);
		$auth[] = intval($forum_id);
	}
	return $auth;
}

/**
* Used for determining if Forum ID is authed, please use this Function on all Posting Screens
*/
function is_forum_authed($auth_cache, $check_forum_id)
{
	$one_char_encoding = '#';
	$two_char_encoding = '.';

	if (trim($auth_cache) == '')
	{
		return true;
	}

	$auth = array();
	$auth_len = 1;

	for ($pos = 0; $pos < strlen($auth_cache); $pos+=$auth_len)
	{
		$forum_auth = substr($auth_cache, $pos, 1);
		if ($forum_auth == $one_char_encoding)
		{
			$auth_len = 1;
			continue;
		}
		else if ($forum_auth == $two_char_encoding)
		{
			$auth_len = 2;
			$pos--;
			continue;
		}

		$forum_auth = substr($auth_cache, $pos, $auth_len);
		$forum_id = (int) base64_unpack($forum_auth);
		if ($forum_id == $check_forum_id)
		{
			return true;
		}
	}
	return false;
}

/**
* Init FTP Session
*/
function attach_init_ftp($mode = false)
{
	global $lang, $attach_config;

	$server = (trim($attach_config['ftp_server']) == '') ? 'localhost' : trim($attach_config['ftp_server']);

	$ftp_path = ($mode == MODE_THUMBNAIL) ? trim($attach_config['ftp_path']) . '/' . THUMB_DIR : trim($attach_config['ftp_path']);

	$conn_id = @ftp_connect($server);

	if (!$conn_id)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_connect'], $server));
	}

	$login_result = @ftp_login($conn_id, $attach_config['ftp_user'], $attach_config['ftp_pass']);

	if (!$login_result)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_login'], $attach_config['ftp_user']));
	}

	if (!@ftp_pasv($conn_id, intval($attach_config['ftp_pasv_mode'])))
	{
		message_die(GENERAL_ERROR, $lang['Ftp_error_pasv_mode']);
	}

	$result = @ftp_chdir($conn_id, $ftp_path);

	if (!$result)
	{
		message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_path'], $ftp_path));
	}

	return $conn_id;
}

/**
* Deletes an Attachment
*/
function unlink_attach($filename, $mode = false)
{
	global $upload_dir, $attach_config, $lang;

	$filename = basename($filename);

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if ($mode == MODE_THUMBNAIL)
		{
			$filename = $upload_dir . '/' . THUMB_DIR . '/t_' . $filename;
		}
		else
		{
			$filename = $upload_dir . '/' . $filename;
		}

		$deleted = @unlink($filename);

	}
	else
	{
		$conn_id = attach_init_ftp($mode);

		if ($mode == MODE_THUMBNAIL)
		{
			$filename = 't_' . $filename;
		}

		$res = @ftp_delete($conn_id, $filename);
		if (!$res)
		{
			return $deleted;
		}

		@ftp_quit($conn_id);

		$deleted = true;
	}

	return $deleted;
}

/**
* FTP File to Location
*/
function ftp_file($source_file, $dest_file, $mimetype, $disable_error_mode = false)
{
	global $attach_config, $lang, $error, $error_msg;

	$conn_id = attach_init_ftp();

	// Binary or Ascii ?
	$mode = FTP_BINARY;
	if (preg_match("/text/i", $mimetype) || preg_match("/html/i", $mimetype))
	{
		$mode = FTP_ASCII;
	}

	$res = @ftp_put($conn_id, $dest_file, $source_file, $mode);

	if (!$res && !$disable_error_mode)
	{
		$error = true;
		if (!empty($error_msg))
		{
			$error_msg .= '<br />';
		}
		$error_msg = sprintf($lang['Ftp_error_upload'], $attach_config['ftp_path']) . '<br />';
		@ftp_quit($conn_id);
		return false;
	}

	if (!$res)
	{
		return false;
	}

	@ftp_site($conn_id, 'CHMOD 0644 ' . $dest_file);
	@ftp_quit($conn_id);
	return true;
}

/**
* Check if Attachment exist
*/
function attachment_exists($filename)
{
	global $upload_dir, $attach_config;

	$filename = basename($filename);

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if (!@file_exists(@amod_realpath($upload_dir . '/' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp();

		$file_listing = array();

		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd')
				{
					$dirinfo[0] = 1;	// Directory == 1
				}
				$dirinfo[1] = $regs[2]; // Size
				$dirinfo[2] = $regs[3]; // Date
				$dirinfo[3] = $regs[4]; // Filename
				$dirinfo[4] = $regs[5]; // Time
			}

			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);

		return $found;
	}
}

/**
* Check if Thumbnail exist
*/
function thumbnail_exists($filename)
{
	global $upload_dir, $attach_config;

	$filename = basename($filename);

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if (!@file_exists(@amod_realpath($upload_dir . '/' . THUMB_DIR . '/t_' . $filename)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		$found = false;

		$conn_id = attach_init_ftp(MODE_THUMBNAIL);

		$file_listing = array();

		$filename = 't_' . $filename;
		$file_listing = @ftp_rawlist($conn_id, $filename);

		for ($i = 0, $size = sizeof($file_listing); $i < $size; $i++)
		{
			if (ereg("([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)", $file_listing[$i], $regs))
			{
				if ($regs[1] == 'd')
				{
					$dirinfo[0] = 1;	// Directory == 1
				}
				$dirinfo[1] = $regs[2]; // Size
				$dirinfo[2] = $regs[3]; // Date
				$dirinfo[3] = $regs[4]; // Filename
				$dirinfo[4] = $regs[5]; // Time
			}

			if ($dirinfo[0] != 1 && $dirinfo[4] == $filename)
			{
				$found = true;
			}
		}

		@ftp_quit($conn_id);

		return $found;
	}
}

/**
* Physical Filename stored already ?
*/
function physical_filename_already_stored($filename)
{
	global $db;

	if ($filename == '')
	{
		return false;
	}

	$filename = basename($filename);

	$sql = "SELECT attach_id
		FROM bb_attachments_desc
		WHERE physical_filename = '" . attach_mod_sql_escape($filename) . "'
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not get attachment information for filename: ' . htmlspecialchars($filename), '', __LINE__, __FILE__, $sql);
	}
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	return ($num_rows == 0) ? false : true;
}

/**
* get all attachments from a post (could be an post array too)
*/
function get_attachments_from_post($post_id_array)
{
	global $db, $attach_config;

	$attachments = array();

	if (!is_array($post_id_array))
	{
		if (empty($post_id_array))
		{
			return $attachments;
		}

		$post_id = intval($post_id_array);

		$post_id_array = array();
		$post_id_array[] = $post_id;
	}

	$post_id_array = implode(', ', array_map('intval', $post_id_array));

	if ($post_id_array == '')
	{
		return $attachments;
	}

	$display_order = (intval($attach_config['display_order']) == 0) ? 'DESC' : 'ASC';

	$sql = "SELECT a.post_id, d.*
		FROM bb_attachments a, bb_attachments_desc d
		WHERE a.post_id IN ($post_id_array)
			AND a.attach_id = d.attach_id
		ORDER BY d.filetime $display_order";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get Attachment Informations for post number ' . $post_id_array, '', __LINE__, __FILE__, $sql);
	}

	$num_rows = $db->sql_numrows($result);
	$attachments = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return array();
	}

	return $attachments;
}

/**
* Count Filesize of Attachments in Database based on the attachment id
*/
function get_total_attach_filesize($attach_ids)
{
	global $db;

	if (!is_array($attach_ids) || !sizeof($attach_ids))
	{
		return 0;
	}

	$attach_ids = implode(', ', array_map('intval', $attach_ids));

	if (!$attach_ids)
	{
		return 0;
	}

	$sql = "SELECT filesize
		FROM bb_attachments_desc
		WHERE attach_id IN ($attach_ids)";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query Total Filesize', '', __LINE__, __FILE__, $sql);
	}

	$total_filesize = 0;

	while ($row = $db->sql_fetchrow($result))
	{
		$total_filesize += (int) $row['filesize'];
	}
	$db->sql_freeresult($result);

	return $total_filesize;
}

/**
* Get allowed Extensions and their respective Values
*/
function get_extension_informations()
{
	return $GLOBALS['datastore']->get('attach_extensions');
}

//
// Sync Topic
//
function attachment_sync_topic ($topics)
{
	global $db;

	if (is_array($topics))
	{
		$topics = join(',', $topics);
	}
	$posts_without_attach = $topics_without_attach = array();

	// Check orphan post_attachment markers
	$sql = "SELECT p.post_id
		FROM bb_posts p
		LEFT JOIN bb_attachments a USING(post_id)
		WHERE p.topic_id IN($topics)
			AND p.post_attachment = 1
			AND a.post_id IS NULL";

	if ($rowset = $db->fetch_rowset($sql))
	{
		foreach ($rowset as $row)
		{
			$posts_without_attach[] = $row['post_id'];
		}
		if ($posts_sql = join(',', $posts_without_attach))
		{
			$db->query("UPDATE bb_posts SET post_attachment = 0 WHERE post_id IN($posts_sql)");
		}
	}

	// Update missing topic_attachment markers
	$db->query("
		UPDATE bb_topics t, bb_posts p SET
			t.topic_attachment = 1
		WHERE p.topic_id IN($topics)
			AND p.post_attachment = 1
			AND p.topic_id = t.topic_id
	");

	// Fix orphan topic_attachment markers
	$sql = "SELECT t.topic_id
		FROM bb_posts p, bb_topics t
		WHERE t.topic_id = p.topic_id
			AND t.topic_id IN($topics)
			AND t.topic_attachment = 1
		GROUP BY p.topic_id
		HAVING SUM(p.post_attachment) = 0";

	if ($rowset = $db->fetch_rowset($sql))
	{
		foreach ($rowset as $row)
		{
			$topics_without_attach[] = $row['topic_id'];
		}
		if ($topics_sql = join(',', $topics_without_attach))
		{
			$db->query("UPDATE bb_topics SET topic_attachment = 0 WHERE topic_id IN($topics_sql)");
		}
	}
}

/**
* Get Extension
*/
function get_extension($filename)
{
	if (!stristr($filename, '.'))
	{
		return '';
	}
	$extension = strrchr(strtolower($filename), '.');
	$extension[0] = ' ';
	$extension = strtolower(trim($extension));
	if (is_array($extension))
	{
		return '';
	}
	else
	{
		return $extension;
	}
}

/**
* Delete Extension
*/
function delete_extension($filename)
{
	return substr($filename, 0, strrpos(strtolower(trim($filename)), '.'));
}

/**
* Check if a user is within Group
*/
function user_in_group($user_id, $group_id)
{
	global $db;

	$user_id = (int) $user_id;
	$group_id = (int) $group_id;

	if (!$user_id || !$group_id)
	{
		return false;
	}

	$sql = "SELECT u.group_id
		FROM bb_user_group u, bb_groups g
		WHERE g.group_single_user = 0
			AND u.group_id = g.group_id
			AND u.user_id = $user_id
			AND g.group_id = $group_id
		LIMIT 1";

	if (!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Could not get User Group', '', __LINE__, __FILE__, $sql);
	}

	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	if ($num_rows == 0)
	{
		return false;
	}

	return true;
}

/**
* Realpath replacement for attachment mod
*/
function amod_realpath($path)
{
	return (function_exists('realpath')) ? realpath($path) : $path;
}

/**
* _set_var
*
* Set variable, used by {@link get_var the get_var function}
*
* @private
*/
function _set_var(&$result, $var, $type, $multibyte = false)
{
	settype($var, $type);
	$result = $var;

	if ($type == 'string')
	{
		$result = trim(str_replace(array("\r\n", "\r", '\xFF'), array("\n", "\n", ' '), $result));
		// 2.0.x is doing addslashes on all variables
		if ($multibyte)
		{
			$result = preg_replace('#&amp;(\#[0-9]+;)#', '&\1', $result);
		}
	}
}

/**
* get_var
*
* Used to get passed variable
*/
function get_var($var_name, $default, $multibyte = false)
{
	$request_var = (isset($_POST[$var_name])) ? $_POST : $_GET;

	if (!isset($request_var[$var_name]) || (is_array($request_var[$var_name]) && !is_array($default)) || (is_array($default) && !is_array($request_var[$var_name])))
	{
		return (is_array($default)) ? array() : $default;
	}

	$var = $request_var[$var_name];

	if (!is_array($default))
	{
		$type = gettype($default);
	}
	else
	{
		list($key_type, $type) = each($default);
		$type = gettype($type);
		$key_type = gettype($key_type);
	}

	if (is_array($var))
	{
		$_var = $var;
		$var = array();

		foreach ($_var as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $_k => $_v)
				{
					_set_var($k, $k, $key_type);
					_set_var($_k, $_k, $key_type);
					_set_var($var[$k][$_k], $_v, $type, $multibyte);
				}
			}
			else
			{
				_set_var($k, $k, $key_type);
				_set_var($var[$k], $v, $type, $multibyte);
			}
		}
	}
	else
	{
		_set_var($var, $var, $type, $multibyte);
	}

	return $var;
}

/**
* Escaping SQL
*/
function attach_mod_sql_escape($text)
{
	switch (SQL_LAYER)
	{
		case 'postgresql':
			return pg_escape_string($text);
		break;

		case 'mysql':
		case 'mysql4':
			if (function_exists('mysql_real_escape_string'))
			{
				return mysql_real_escape_string($text);
			}
			else
			{
				return str_replace("'", "''", str_replace('\\', '\\\\', $text));
			}
		break;

		default:
			return str_replace("'", "''", str_replace('\\', '\\\\', $text));
		break;
	}
}

/**
* Build sql statement from array for insert/update/select statements
*
* Idea for this from Ikonboard
* Possible query values: INSERT, INSERT_SELECT, MULTI_INSERT, UPDATE, SELECT
*/
function attach_mod_sql_build_array($query, $assoc_ary = false)
{
	if (!is_array($assoc_ary))
	{
		return false;
	}

	$fields = array();
	$values = array();
	if ($query == 'INSERT' || $query == 'INSERT_SELECT')
	{
		foreach ($assoc_ary as $key => $var)
		{
			$fields[] = $key;

			if (is_null($var))
			{
				$values[] = 'NULL';
			}
			else if (is_string($var))
			{
				$values[] = "'" . attach_mod_sql_escape($var) . "'";
			}
			else if (is_array($var) && is_string($var[0]))
			{
				$values[] = $var[0];
			}
			else
			{
				$values[] = (is_bool($var)) ? intval($var) : $var;
			}
		}

		$query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
	}
	else if ($query == 'MULTI_INSERT')
	{
		$ary = array();
		foreach ($assoc_ary as $id => $sql_ary)
		{
			$values = array();
			foreach ($sql_ary as $key => $var)
			{
				if (is_null($var))
				{
					$values[] = 'NULL';
				}
				elseif (is_string($var))
				{
					$values[] = "'" . attach_mod_sql_escape($var) . "'";
				}
				else
				{
					$values[] = (is_bool($var)) ? intval($var) : $var;
				}
			}
			$ary[] = '(' . implode(', ', $values) . ')';
		}

		$query = ' (' . implode(', ', array_keys($assoc_ary[0])) . ') VALUES ' . implode(', ', $ary);
	}
	else if ($query == 'UPDATE' || $query == 'SELECT')
	{
		$values = array();
		foreach ($assoc_ary as $key => $var)
		{
			if (is_null($var))
			{
				$values[] = "$key = NULL";
			}
			elseif (is_string($var))
			{
				$values[] = "$key = '" . attach_mod_sql_escape($var) . "'";
			}
			else
			{
				$values[] = (is_bool($var)) ? "$key = " . intval($var) : "$key = $var";
			}
		}
		$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
	}

	return $query;
}


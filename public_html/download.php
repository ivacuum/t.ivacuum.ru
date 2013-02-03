<?php

define('NO_GZIP', true);
define('BB_ROOT',  './');
require('common.php');
require(SITE_DIR . 'attach_mod/attachment_mod.php');

$datastore->enqueue(array(
	'attach_extensions',
));

//
// Delete the / * to uncomment the block, and edit the values (read the comments) to
// enable additional security to your board (preventing third site linkage)
//
/*
define('ALLOWED_DENIED', 0);
define('DENIED_ALLOWED', 1);

//
// From this line on you are able to edit the stuff
//

// Possible Values:
// ALLOWED_DENIED <- First allow the listed sites, and then deny all others
// DENIED_ALLOWED <- First deny the listed sites, and then allow all others
$allow_deny_order = ALLOWED_DENIED;

//
// Allowed Syntax:
// Full Domain Name -> www.opentools.de
// Partial Domain Names -> opentools.de
//
$sites = array(
	$board_config['server_name'],	// This is your domain
	'opentools.de',
	'phpbb.com',
	'phpbbhacks.com',
	'phpbb.de'
);

// This is the message displayed, if someone links to this site...
$lang['Denied_Message'] = 'You are not authorized to view, download or link to this Site.';

// End of editable area

//
// Parse the order and evaluate the array
//

$site = explode('?', $_SERVER['HTTP_REFERER']);
$url = trim($site[0]);
//$url = $HTTP_HOST;

if ($url != '')
{
	$allowed = ($allow_deny_order == ALLOWED_DENIED) ? FALSE : TRUE;

	for ($i = 0; $i < count($sites); $i++)
	{
		if (strstr($url, $sites[$i]))
		{
			$allowed = ($allow_deny_order == ALLOWED_DENIED) ? TRUE : FALSE;
			break;
		}
	}
}
else
{
	$allowed = TRUE;
}

if ($allowed == FALSE)
{
	message_die(GENERAL_MESSAGE, $lang['Denied_Message']);
}

// Delete the following line, to uncomment this block
*/

$download_id = request_var('id', 0);
$thumbnail = request_var('thumb', 0);

// Send file to browser
function send_file_to_browser($attachment, $upload_dir)
{
	global $_SERVER, $HTTP_USER_AGENT, $lang, $db, $attach_config;

	$filename = ($upload_dir == '') ? $attachment['physical_filename'] : $upload_dir . '/' . $attachment['physical_filename'];

	$gotit = false;

	if (!intval($attach_config['allow_ftp_upload']))
	{
		if (@!file_exists(@amod_realpath($filename)))
		{
			message_die(GENERAL_ERROR, $lang['Error_no_attachment'] . "<br /><br /><b>404 File Not Found:</b> The File <i>" . $filename . "</i> does not exist.");
		}
		else
		{
			$gotit = true;
		}
	}

	//
	// Determine the Browser the User is using, because of some nasty incompatibilities.
	// Most of the methods used in this function are from phpMyAdmin. :)
	//
	if (!empty($_SERVER['HTTP_USER_AGENT']))
	{
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
	}
	else if (!empty($_SERVER['HTTP_USER_AGENT']))
	{
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
	}
	else if (!isset($HTTP_USER_AGENT))
	{
		$HTTP_USER_AGENT = '';
	}

	$browser_version = 0;
	$browser_agent = 'other';

	// Correct the mime type - we force application/octetstream for all files, except images
	// Please do not change this, it is a security precaution
	$user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';

	if( !strstr($attachment['mimetype'], 'image') )
	{
		$attachment['mimetype'] = ( strpos(strtolower($user_agent), 'msie') !== false || strpos(strtolower($user_agent), 'opera') !== false ) ? 'application/octetstream' : 'application/octet-stream';
	}

	//bt
	global $userdata;

	if (!(isset($_GET['original']) && !IS_USER))
	{
		include_once(SITE_DIR . 'includes/functions_torrent.php');
		send_torrent_with_passkey($filename);
	}
	//bt end

	// Now the tricky part... let's dance
//	@ob_end_clean();
//	@ini_set('zlib.output_compression', 'Off');
	header('Pragma: public');
//	header('Content-Transfer-Encoding: none');

//$real_filename = html_entity_decode(basename($attachment['real_filename']));
	$real_filename = clean_filename(basename($attachment['real_filename']));
	$mimetype = "{$attachment['mimetype']};";
	$charset = (@$lang['CONTENT_ENCODING']) ? "charset={$lang['CONTENT_ENCODING']};" : '';

	// Send out the Headers
	header("Content-Type: $mimetype $charset name=\"$real_filename\"");
	header("Content-Disposition: inline; filename=\"$real_filename\"");

	unset($real_filename);
	//
	// Now send the File Contents to the Browser
	//
	if ($gotit)
	{
		$size = @filesize($filename);
		if ($size)
		{
			header("Content-length: $size");
		}
		readfile($filename);
	}
	else if (!$gotit && intval($attach_config['allow_ftp_upload']))
	{
		$conn_id = attach_init_ftp();

		$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';

		$tmp_path = ( !@$ini_val('safe_mode') ) ? '/tmp' : $upload_dir;
		$tmp_filename = @tempnam($tmp_path, 't0000');

		@unlink($tmp_filename);

		$mode = FTP_BINARY;
		if ( (preg_match("/text/i", $attachment['mimetype'])) || (preg_match("/html/i", $attachment['mimetype'])) )
		{
			$mode = FTP_ASCII;
		}

		$result = @ftp_get($conn_id, $tmp_filename, $filename, $mode);

		if (!$result)
		{
			message_die(GENERAL_ERROR, $lang['Error_no_attachment'] . "<br /><br /><b>404 File Not Found:</b> The File <i>" . $filename . "</i> does not exist.");
		}

		@ftp_quit($conn_id);

		$size = @filesize($tmp_filename);
		if ($size)
		{
			header("Content-length: $size");
		}
		readfile($tmp_filename);
		@unlink($tmp_filename);
	}
	else
	{
		message_die(GENERAL_ERROR, $lang['Error_no_attachment'] . "<br /><br /><b>404 File Not Found:</b> The File <i>" . $filename . "</i> does not exist.");
	}

	exit;
}
//
// End Functions
//

//
// Start Session Management
//
$user->session_start();

if (!$download_id)
{
	message_die(GENERAL_ERROR, $lang['No_attachment_selected']);
}

if ($attach_config['disable_mod'] && !IS_ADMIN)
{
	message_die(GENERAL_MESSAGE, $lang['Attachment_feature_disabled']);
}

$sql = 'SELECT *
	FROM ' . ATTACHMENTS_DESC_TABLE . '
	WHERE attach_id = ' . (int) $download_id;

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query attachment informations', '', __LINE__, __FILE__, $sql);
}

if (!($attachment = $db->sql_fetchrow($result)))
{
	message_die(GENERAL_MESSAGE, $lang['Error_no_attachment']);
}

$attachment['physical_filename'] = basename($attachment['physical_filename']);

$db->sql_freeresult($result);

// get forum_id for attachment authorization or private message authorization
$authorised = false;

$sql = 'SELECT *
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE attach_id = ' . (int) $attachment['attach_id'];

if (!($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query attachment informations', '', __LINE__, __FILE__, $sql);
}

$auth_pages = $db->sql_fetchrowset($result);
$num_auth_pages = $db->sql_numrows($result);

for ($i = 0; $i < $num_auth_pages && $authorised == false; $i++)
{
	$auth_pages[$i]['post_id'] = intval($auth_pages[$i]['post_id']);

	if ($auth_pages[$i]['post_id'] != 0)
	{
		$sql = 'SELECT forum_id
			FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . (int) $auth_pages[$i]['post_id'];

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query post information', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrow($result);

		$forum_id = $row['forum_id'];

		$is_auth = array();
		$is_auth = auth(AUTH_ALL, $forum_id, $userdata);

		if ($is_auth['auth_download'])
		{
			$authorised = TRUE;
		}
	}
}


if (!$authorised)
{
	message_die(GENERAL_MESSAGE, $lang['Sorry_auth_view_attach']);
}

$datastore->rm('cat_forums');

//
// Get Information on currently allowed Extensions
//
$rows = get_extension_informations();
$num_rows = count($rows);

for ($i = 0; $i < $num_rows; $i++)
{
	$extension = strtolower(trim($rows[$i]['extension']));
	$allowed_extensions[] = $extension;
	$download_mode[$extension] = $rows[$i]['download_mode'];
}

// disallowed ?
if (!in_array($attachment['extension'], $allowed_extensions) && !IS_ADMIN)
{
	message_die(GENERAL_MESSAGE, sprintf($lang['Extension_disabled_after_posting'], $attachment['extension']));
}

$download_mode = intval($download_mode[$attachment['extension']]);

if ($thumbnail)
{
	$attachment['physical_filename'] = THUMB_DIR . '/t_' . $attachment['physical_filename'];
}

// Update download count
if (!$thumbnail)
{
	$sql = 'UPDATE ' . ATTACHMENTS_DESC_TABLE . '
	SET download_count = download_count + 1
	WHERE attach_id = ' . (int) $attachment['attach_id'];

	if (!$db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Couldn\'t update attachment download count', '', __LINE__, __FILE__, $sql);
	}
}

// Determine the 'presenting'-method
if ($download_mode == PHYSICAL_LINK)
{
	$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://';
	$server_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['server_name']));
	$server_port = ($board_config['server_port'] <> 80) ? ':' . trim($board_config['server_port']) : '';
	$script_name = preg_replace('/^\/?(.*?)\/?$/', '/\1', trim($board_config['script_path']));

	if ($script_name[strlen($script_name)] != '/')
	{
		$script_name .= '/';
	}

	if (intval($attach_config['allow_ftp_upload']))
	{
		if (trim($attach_config['download_path']) == '')
		{
			message_die(GENERAL_ERROR, 'Physical Download not possible with the current Attachment Setting');
		}

		$url = trim($attach_config['download_path']) . '/' . $attachment['physical_filename'];
		$redirect_path = $url;
	}
	else
	{
		$url = $upload_dir . '/' . $attachment['physical_filename'];
//		$url = preg_replace('/^\/?(.*?\/)?$/', '\1', trim($url));
		$redirect_path = $server_protocol . $server_name . $server_port . $script_name . $url;
	}

	// Redirect via an HTML form for PITA webservers
	if (@preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')))
	{
		header('Refresh: 0; URL=' . $redirect_path);
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0; url=' . $redirect_path . '"><title>Redirect</title></head><body><div align="center">If your browser does not support meta redirection please click <a href="' . $redirect_path . '">HERE</a> to be redirected</div></body></html>';
		exit;
	}

	// Behave as per HTTP/1.1 spec for others
	header('Location: ' . $redirect_path);
	exit;
}
else
{
	if (intval($attach_config['allow_ftp_upload']))
	{
		// We do not need a download path, we are not downloading physically
		send_file_to_browser($attachment, '');
		exit;
	}
	else
	{
		send_file_to_browser($attachment, $upload_dir);
		exit;
	}
}

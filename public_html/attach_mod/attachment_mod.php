<?php

if (!defined('SITE_DIR'))
{
	exit;
}

require(SITE_DIR . 'attach_mod/includes/functions_includes.php');
require(SITE_DIR . 'attach_mod/includes/functions_attach.php');
require(SITE_DIR . 'attach_mod/includes/functions_delete.php');
require(SITE_DIR . 'attach_mod/includes/functions_thumbs.php');
require(SITE_DIR . 'attach_mod/includes/functions_filetypes.php');

if (defined('ATTACH_INSTALL'))
{
	return;
}

/**
* wrapper function for determining the correct language directory
*/
function attach_mod_get_lang($language_file)
{
	global $attach_config, $board_config;

	$language = $board_config['default_lang'];

	if (!file_exists(SITE_DIR . 'language/lang_' . $language . '/' . $language_file . '.php'))
	{
		$language = $attach_config['board_lang'];

		if (!file_exists(SITE_DIR . 'language/lang_' . $language . '/' . $language_file . '.php'))
		{
			message_die(GENERAL_MESSAGE, 'Attachment Mod language file does not exist: language/lang_' . $language . '/' . $language_file . '.php');
		}
		else
		{
			return $language;
		}
	}
	else
	{
		return $language;
	}
}

/**
* Include attachment mod language entries
*/
function include_attach_lang()
{
}

/**
* Get attachment mod configuration
*/
function get_config()
{
	global $db, $board_config;

	$attach_config = array();

	$sql = 'SELECT * FROM bb_attachments_config';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query attachment information', '', __LINE__, __FILE__, $sql);
	}

	while ($row = $db->sql_fetchrow($result))
	{
		$attach_config[$row['config_name']] = trim($row['config_value']);
	}

	// We assign the original default board language here, because it gets overwritten later with the users default language
	$attach_config['board_lang'] = trim($board_config['default_lang']);

	return $attach_config;
}

// Get Attachment Config
$cache_dir = SITE_DIR . 'cache';
$cache_file = $cache_dir . '/attach_config.php';
$attach_config = array();

if (file_exists($cache_dir) && is_dir($cache_dir) && is_writable($cache_dir))
{
	if (file_exists($cache_file))
	{
		include($cache_file);
	}
	else
	{
		$attach_config = get_config();
		$fp = @fopen($cache_file, 'wt+');
		if ($fp)
		{
			$lines = array();
			foreach ($attach_config as $k => $v)
			{
				if (is_int($v))
				{
					$lines[] = "'$k'=>$v";
				}
				else if (is_bool($v))
				{
					$lines[] = "'$k'=>" . (($v) ? 'TRUE' : 'FALSE');
				}
				else
				{
					$lines[] = "'$k'=>'" . str_replace("'", "\\'", str_replace('\\', '\\\\', $v)) . "'";
				}
			}
			fwrite($fp, '<?php $attach_config = array(' . implode(',', $lines) . '); ?>');
			fclose($fp);

			@chmod($cache_file, 0777);
		}
	}
}
else
{
	$attach_config = get_config();
}

// Please do not change the include-order, it is valuable for proper execution.
// Functions for displaying Attachment Things
include(SITE_DIR . 'attach_mod/displaying.php');
// Posting Attachments Class (HAVE TO BE BEFORE PM)
include(SITE_DIR . 'attach_mod/posting_attachments.php');

if (!intval($attach_config['allow_ftp_upload']))
{
	$upload_dir = $attach_config['upload_dir'];
}
else
{
	$upload_dir = $attach_config['download_path'];
}


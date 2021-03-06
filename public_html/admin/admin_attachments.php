<?php

// ACP Header - START
if (!empty($setmodules))
{
	$filename = basename(__FILE__);
	$module['Attachments']['Manage'] = $filename . '?mode=manage';
	$module['Attachments']['Special_categories'] = $filename . '?mode=cats';
	$module['Attachments']['Quota_limits'] = $filename . '?mode=quota';
	return;
}
require './pagestart.php';
// ACP Header - END

$error = false;

if (!intval($attach_config['allow_ftp_upload']))
{
	if ( ($attach_config['upload_dir'][0] == '/') || ( ($attach_config['upload_dir'][0] != '/') && ($attach_config['upload_dir'][1] == ':') ) )
	{
		$upload_dir = $attach_config['upload_dir'];
	}
	else
	{
		$upload_dir = '../' . $attach_config['upload_dir'];
	}
}
else
{
	$upload_dir = $attach_config['download_path'];
}

require SITE_DIR . 'attach_mod/includes/functions_selects.php';

// Check if the language got included
if (!isset($lang['Test_settings_successful']))
{
	// include_once is used within the function
	include_attach_lang();
}

// Init Vars
$mode = get_var('mode', '');
$e_mode = get_var('e_mode', '');
$size = get_var('size', '');
$quota_size = get_var('quota_size', '');
$pm_size = get_var('pm_size', '');

$submit = (isset($_POST['submit'])) ? TRUE : FALSE;
$check_upload = (isset($_POST['settings'])) ? TRUE : FALSE;
$check_image_cat = (isset($_POST['cat_settings'])) ? TRUE : FALSE;
$search_imagick = (isset($_POST['search_imagick'])) ? TRUE : FALSE;

// Re-evaluate the Attachment Configuration
$sql = 'SELECT * FROM bb_attachments_config';

if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not find Attachment Config Table', '', __LINE__, __FILE__, $sql);
}

while ($row = $db->sql_fetchrow($result))
{
	$config_name = $row['config_name'];
	$config_value = $row['config_value'];

	$new_attach[$config_name] = get_var($config_name, trim($attach_config[$config_name]));

	if (!$size && !$submit && $config_name == 'max_filesize')
	{
		$size = ($attach_config[$config_name] >= 1048576) ? 'mb' : (($attach_config[$config_name] >= 1024) ? 'kb' : 'b');
	}

	if (!$quota_size && !$submit && $config_name == 'attachment_quota')
	{
		$quota_size = ($attach_config[$config_name] >= 1048576) ? 'mb' : (($attach_config[$config_name] >= 1024) ? 'kb' : 'b');
	}

	if (!$pm_size && !$submit && $config_name == 'max_filesize_pm')
	{
		$pm_size = ($attach_config[$config_name] >= 1048576) ? 'mb' : (($attach_config[$config_name] >= 1024) ? 'kb' : 'b');
	}

	if (!$submit && ($config_name == 'max_filesize' || $config_name == 'attachment_quota' || $config_name == 'max_filesize_pm'))
	{
		if ($new_attach[$config_name] >= 1048576)
		{
			$new_attach[$config_name] = round($new_attach[$config_name] / 1048576 * 100) / 100;
		}
		else if ($new_attach[$config_name] >= 1024)
		{
			$new_attach[$config_name] = round($new_attach[$config_name] / 1024 * 100) / 100;
		}
	}

	if ( $submit && ( $mode == 'manage' || $mode == 'cats') )
	{
		if ($config_name == 'max_filesize')
		{
			$old = $new_attach[$config_name];
			$new_attach[$config_name] = ( $size == 'kb' ) ? round($new_attach[$config_name] * 1024) : ( ($size == 'mb') ? round($new_attach[$config_name] * 1048576) : $new_attach[$config_name] );
		}

		if ($config_name == 'attachment_quota')
		{
			$old = $new_attach[$config_name];
			$new_attach[$config_name] = ( $quota_size == 'kb' ) ? round($new_attach[$config_name] * 1024) : ( ($quota_size == 'mb') ? round($new_attach[$config_name] * 1048576) : $new_attach[$config_name] );
		}

		if ($config_name == 'max_filesize_pm')
		{
			$old = $new_attach[$config_name];
			$new_attach[$config_name] = ( $pm_size == 'kb' ) ? round($new_attach[$config_name] * 1024) : ( ($pm_size == 'mb') ? round($new_attach[$config_name] * 1048576) : $new_attach[$config_name] );
		}

		if ($config_name == 'ftp_server' || $config_name == 'ftp_path' || $config_name == 'download_path')
		{
			$value = trim($new_attach[$config_name]);

			if ($value[strlen($value)-1] == '/')
			{
				$value[strlen($value)-1] = ' ';
			}

			$new_attach[$config_name] = trim($value);

		}

		if ($config_name == 'max_filesize')
		{
			$old_size = $attach_config[$config_name];
			$new_size = $new_attach[$config_name];

			if ($old_size != $new_size)
			{
				// See, if we have a similar value of old_size in Mime Groups. If so, update these values.
				$sql = 'UPDATE bb_extension_groups
					SET max_filesize = ' . (int) $new_size . '
					WHERE max_filesize = ' . (int) $old_size;

				if ( !($result_2 = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not update Extension Group informations', '', __LINE__, __FILE__, $sql);
				}

			}

			$sql = "UPDATE bb_attachments_config
				SET	config_value = '" . attach_mod_sql_escape($new_attach[$config_name]) . "'
				WHERE config_name = '" . attach_mod_sql_escape($config_name) . "'";
		}
		else
		{
			$sql = "UPDATE bb_attachments_config
				SET	config_value = '" . attach_mod_sql_escape($new_attach[$config_name]) . "'
				WHERE config_name = '" . attach_mod_sql_escape($config_name) . "'";
		}

		if( !$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, 'Failed to update attachment configuration for ' . $config_name, '', __LINE__, __FILE__, $sql);
		}

		if ($config_name == 'max_filesize' || $config_name == 'attachment_quota' || $config_name == 'max_filesize_pm')
		{
			$new_attach[$config_name] = $old;
		}
	}
}
$db->sql_freeresult($result);

$cache_dir = SITE_DIR . 'cache';
$cache_file = $cache_dir . '/attach_config.php';

if ((file_exists($cache_dir)) && (is_dir($cache_dir)))
{
	if (file_exists($cache_file))
	{
		@unlink($cache_file);
	}
}

$select_size_mode = size_select('size', $size);
$select_quota_size_mode = size_select('quota_size', $quota_size);
$select_pm_size_mode = size_select('pm_size', $pm_size);

// Search Imagick
if ($search_imagick)
{
	$imagick = '';

	if (eregi('convert', $imagick))
	{
		return true;
	}
	else if ($imagick != 'none')
	{
		if (!eregi('WIN', PHP_OS))
		{
			$retval = @exec('whereis convert');
			$paths = explode(' ', $retval);

			if (is_array($paths))
			{
				for ( $i=0; $i < sizeof($paths); $i++)
				{
					$path = basename($paths[$i]);

					if ($path == 'convert')
					{
						$imagick = $paths[$i];
					}
				}
			}
		}
		else if (eregi('WIN', PHP_OS))
		{
			$path = 'c:/imagemagick/convert.exe';

			if (@file_exists(@amod_realpath($path)))
			{
				$imagick = $path;
			}
		}
	}

	if (@file_exists(@amod_realpath(trim($imagick))))
	{
		$new_attach['img_imagick'] = trim($imagick);
	}
	else
	{
		$new_attach['img_imagick'] = '';
	}
}

// Check Settings
if ($check_upload)
{
	// Some tests...
	$attach_config = array();

	$sql = 'SELECT * FROM bb_attachments_config';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not find Attachment Config Table', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$attach_config[$row[$i]['config_name']] = trim($row[$i]['config_value']);
	}

	if ($attach_config['upload_dir'][0] == '/' || ($attach_config['upload_dir'][0] != '/' && $attach_config['upload_dir'][1] == ':'))
	{
		$upload_dir = $attach_config['upload_dir'];
	}
	else
	{
		$upload_dir = SITE_DIR . $attach_config['upload_dir'];
	}

	$error = false;

	// Does the target directory exist, is it a directory and writeable. (only test if ftp upload is disabled)
	if (intval($attach_config['allow_ftp_upload']) == 0)
	{
		if ( !@file_exists(@amod_realpath($upload_dir)) )
		{
			$error = true;
			$error_msg = sprintf($lang['Directory_does_not_exist'], $attach_config['upload_dir']) . '<br />';
		}

		if (!$error && !is_dir($upload_dir))
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Directory_is_not_a_dir'], $attach_config['upload_dir']) . '<br />';
		}

		if (!$error)
		{
			if ( !($fp = @fopen($upload_dir . '/0_000000.000', 'w')) )
			{
				$error = TRUE;
				$error_msg = sprintf($lang['Directory_not_writeable'], $attach_config['upload_dir']) . '<br />';
			}
			else
			{
				@fclose($fp);
				unlink_attach($upload_dir . '/0_000000.000');
			}
		}
	}
	else
	{
		// Check FTP Settings
		$server = ( empty($attach_config['ftp_server']) ) ? 'localhost' : $attach_config['ftp_server'];

		$conn_id = @ftp_connect($server);

		if (!$conn_id)
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Ftp_error_connect'], $server) . '<br />';
		}

		$login_result = @ftp_login($conn_id, $attach_config['ftp_user'], $attach_config['ftp_pass']);

		if ( (!$login_result) && (!$error) )
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Ftp_error_login'], $attach_config['ftp_user']) . '<br />';
		}

		if (!@ftp_pasv($conn_id, intval($attach_config['ftp_pasv_mode'])))
		{
			$error = TRUE;
			$error_msg = $lang['Ftp_error_pasv_mode'];
		}

		if (!$error)
		{
			// Check Upload
			$tmpfname = @tempnam('/tmp', 't0000');

			@unlink($tmpfname); // unlink for safety on php4.0.3+

			$fp = @fopen($tmpfname, 'w');

			@fwrite($fp, 'test');

			@fclose($fp);

			$result = @ftp_chdir($conn_id, $attach_config['ftp_path']);

			if (!$result)
			{
				$error = TRUE;
				$error_msg = sprintf($lang['Ftp_error_path'], $attach_config['ftp_path']) . '<br />';
			}
			else
			{
				$res = @ftp_put($conn_id, 't0000', $tmpfname, FTP_ASCII);

				if (!$res)
				{
					$error = TRUE;
					$error_msg = sprintf($lang['Ftp_error_upload'], $attach_config['ftp_path']) . '<br />';
				}
				else
				{
					$res = @ftp_delete($conn_id, 't0000');

					if (!$res)
					{
						$error = TRUE;
						$error_msg = sprintf($lang['Ftp_error_delete'], $attach_config['ftp_path']) . '<br />';
					}
				}
			}

			@ftp_quit($conn_id);

			@unlink($tmpfname);
		}
	}

	if (!$error)
	{
		message_die(GENERAL_MESSAGE, $lang['Test_settings_successful'] . '<br /><br />' . sprintf($lang['Click_return_attach_config'], '<a href="' . append_sid("admin_attachments.php?mode=manage") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.php?pane=right") . '">', '</a>'));
	}
}

// Management
if ($submit && $mode == 'manage')
{
	if (!$error)
	{
		message_die(GENERAL_MESSAGE, $lang['Attach_config_updated'] . '<br /><br />' . sprintf($lang['Click_return_attach_config'], '<a href="' . append_sid("admin_attachments.php?mode=manage") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.php?pane=right") . '">', '</a>'));
	}
}

if ($mode == 'manage')
{
	$yes_no_switches = array('disable_mod', 'allow_pm_attach', 'allow_ftp_upload', 'display_order', 'ftp_pasv_mode');

	for ($i = 0; $i < sizeof($yes_no_switches); $i++)
	{
		eval("\$" . $yes_no_switches[$i] . "_yes = ( \$new_attach['" . $yes_no_switches[$i] . "'] != '0' ) ? 'checked=\"checked\"' : '';");
		eval("\$" . $yes_no_switches[$i] . "_no = ( \$new_attach['" . $yes_no_switches[$i] . "'] == '0' ) ? 'checked=\"checked\"' : '';");
	}

	if (!function_exists('ftp_connect'))
	{
		$template->assign_block_vars('switch_no_ftp', array());
	}
	else
	{
		$template->assign_block_vars('switch_ftp', array());
	}

	$template->assign_vars(array(
		'TPL_ATTACH_MANAGE' => true,

		'L_MANAGE_TITLE' => $lang['Attach_settings'],
		'L_MANAGE_EXPLAIN' => $lang['Manage_attachments_explain'],
		'L_ATTACHMENT_SETTINGS' => $lang['Attach_settings'],
		'L_ATTACHMENT_FILESIZE_SETTINGS' => $lang['Attach_filesize_settings'],
		'L_ATTACHMENT_NUMBER_SETTINGS' => $lang['Attach_number_settings'],
		'L_ATTACHMENT_OPTIONS_SETTINGS' => $lang['Attach_options_settings'],
		'L_ATTACHMENT_FTP_SETTINGS' => $lang['ftp_info'],
		'L_NO_FTP_EXTENSIONS' => $lang['No_ftp_extensions_installed'],
		'L_UPLOAD_DIR' => $lang['Upload_directory'],
		'L_UPLOAD_DIR_EXPLAIN' => $lang['Upload_directory_explain'],
		'L_ATTACHMENT_IMG_PATH' => $lang['Attach_img_path'],
		'L_IMG_PATH_EXPLAIN' => $lang['Attach_img_path_explain'],
		'L_ATTACHMENT_TOPIC_ICON' => $lang['Attach_topic_icon'],
		'L_TOPIC_ICON_EXPLAIN' => $lang['Attach_topic_icon_explain'],
		'L_DISPLAY_ORDER' => $lang['Attach_display_order'],
		'L_DISPLAY_ORDER_EXPLAIN' => $lang['Attach_display_order_explain'],
		'L_MAX_FILESIZE' => $lang['Max_filesize_attach'],
		'L_MAX_FILESIZE_EXPLAIN' => $lang['Max_filesize_attach_explain'],
		'L_ATTACH_QUOTA' => $lang['Attach_quota'],
		'L_ATTACH_QUOTA_EXPLAIN' => $lang['Attach_quota_explain'],
		'L_DEFAULT_QUOTA_LIMIT' => $lang['Default_quota_limit'],
		'L_DEFAULT_QUOTA_LIMIT_EXPLAIN' => $lang['Default_quota_limit_explain'],
		'L_MAX_FILESIZE_PM' => $lang['Max_filesize_pm'],
		'L_MAX_FILESIZE_PM_EXPLAIN' => $lang['Max_filesize_pm_explain'],
		'L_MAX_ATTACHMENTS' => $lang['Max_attachments'],
		'L_MAX_ATTACHMENTS_EXPLAIN' => $lang['Max_attachments_explain'],
		'L_MAX_ATTACHMENTS_PM' => $lang['Max_attachments_pm'],
		'L_MAX_ATTACHMENTS_PM_EXPLAIN' => $lang['Max_attachments_pm_explain'],
		'L_DISABLE_MOD' => $lang['Disable_mod'],
		'L_DISABLE_MOD_EXPLAIN' => $lang['Disable_mod_explain'],
		'L_PM_ATTACH' => $lang['PM_Attachments'],
		'L_PM_ATTACH_EXPLAIN' => $lang['PM_Attachments_explain'],
		'L_FTP_UPLOAD' => $lang['Ftp_upload'],
		'L_FTP_UPLOAD_EXPLAIN' => $lang['Ftp_upload_explain'],
		'L_ATTACHMENT_FTP_PATH' => $lang['Attach_ftp_path'],
		'L_ATTACHMENT_FTP_USER' => $lang['ftp_username'],
		'L_ATTACHMENT_FTP_PASS' => $lang['ftp_password'],
		'L_ATTACHMENT_FTP_PATH_EXPLAIN' => $lang['Attach_ftp_path_explain'],
		'L_ATTACHMENT_FTP_SERVER' => $lang['Ftp_server'],
		'L_ATTACHMENT_FTP_SERVER_EXPLAIN' => $lang['Ftp_server_explain'],
		'L_FTP_PASSIVE_MODE' => $lang['Ftp_passive_mode'],
		'L_FTP_PASSIVE_MODE_EXPLAIN' => $lang['Ftp_passive_mode_explain'],
		'L_DOWNLOAD_PATH' => $lang['Ftp_download_path'],
		'L_DOWNLOAD_PATH_EXPLAIN' => $lang['Ftp_download_path_explain'],
		'L_TEST_SETTINGS' => $lang['Test_settings'],

		'S_ATTACH_ACTION' => append_sid('admin_attachments.php?mode=manage'),
		'S_FILESIZE' => $select_size_mode,
		'S_FILESIZE_QUOTA' => $select_quota_size_mode,
		'S_FILESIZE_PM' => $select_pm_size_mode,
		'S_DEFAULT_UPLOAD_LIMIT' => default_quota_limit_select('default_upload_quota', intval(trim($new_attach['default_upload_quota']))),
		'S_DEFAULT_PM_LIMIT' => default_quota_limit_select('default_pm_quota', intval(trim($new_attach['default_pm_quota']))),

		'UPLOAD_DIR' => $new_attach['upload_dir'],
		'ATTACHMENT_IMG_PATH' => $new_attach['upload_img'],
		'TOPIC_ICON' => $new_attach['topic_icon'],
		'MAX_FILESIZE' => $new_attach['max_filesize'],
		'ATTACHMENT_QUOTA' => $new_attach['attachment_quota'],
		'MAX_FILESIZE_PM' => $new_attach['max_filesize_pm'],
		'MAX_ATTACHMENTS' => $new_attach['max_attachments'],
		'MAX_ATTACHMENTS_PM' => $new_attach['max_attachments_pm'],
		'FTP_SERVER' => $new_attach['ftp_server'],
		'FTP_PATH' => $new_attach['ftp_path'],
		'FTP_USER' => $new_attach['ftp_user'],
		'FTP_PASS' => $new_attach['ftp_pass'],
		'DOWNLOAD_PATH' => $new_attach['download_path'],
		'DISABLE_MOD_YES' => $disable_mod_yes,
		'DISABLE_MOD_NO' => $disable_mod_no,
		'PM_ATTACH_YES' => $allow_pm_attach_yes,
		'PM_ATTACH_NO' => $allow_pm_attach_no,
		'FTP_UPLOAD_YES' => $allow_ftp_upload_yes,
		'FTP_UPLOAD_NO' => $allow_ftp_upload_no,
		'FTP_PASV_MODE_YES' => $ftp_pasv_mode_yes,
		'FTP_PASV_MODE_NO' => $ftp_pasv_mode_no,
		'DISPLAY_ORDER_ASC' => $display_order_yes,
		'DISPLAY_ORDER_DESC' => $display_order_no,
	));
}

if ($submit && $mode == 'cats')
{
	if (!$error)
	{
		message_die(GENERAL_MESSAGE, $lang['Attach_config_updated'] . '<br /><br />' . sprintf($lang['Click_return_attach_config'], '<a href="' . append_sid("admin_attachments.php?mode=cats") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.php?pane=right") . '">', '</a>'));
	}
}

if ($mode == 'cats')
{
	$s_assigned_group_images = $lang['None'];
	$s_assigned_group_streams = $lang['None'];
	$s_assigned_group_flash = $lang['None'];

	$sql = 'SELECT group_name, cat_id
		FROM bb_extension_groups
		WHERE cat_id > 0
		ORDER BY cat_id';

	$s_assigned_group_images = array();
	$s_assigned_group_streams = array();
	$s_assigned_group_flash = array();

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get Group Names from bb_extension_groups', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < sizeof($row); $i++)
	{
		if ($row[$i]['cat_id'] == IMAGE_CAT)
		{
			$s_assigned_group_images[] = $row[$i]['group_name'];
		}
		else if ($row[$i]['cat_id'] == STREAM_CAT)
		{
			$s_assigned_group_streams[] = $row[$i]['group_name'];
		}
		else if ($row[$i]['cat_id'] == SWF_CAT)
		{
			$s_assigned_group_flash[] = $row[$i]['group_name'];
		}
	}

	$display_inlined_yes = ( $new_attach['img_display_inlined'] != '0' ) ? 'checked="checked"' : '';
	$display_inlined_no = ( $new_attach['img_display_inlined'] == '0' ) ? 'checked="checked"' : '';

	$create_thumbnail_yes = ( $new_attach['img_create_thumbnail'] != '0' ) ? 'checked="checked"' : '';
	$create_thumbnail_no = ( $new_attach['img_create_thumbnail'] == '0' ) ? 'checked="checked"' : '';

	$use_gd2_yes = ( $new_attach['use_gd2'] != '0' ) ? 'checked="checked"' : '';
	$use_gd2_no = ( $new_attach['use_gd2'] == '0' ) ? 'checked="checked"' : '';

	// Check Thumbnail Support
	if (!is_imagick() && !@extension_loaded('gd'))
	{
		$new_attach['img_create_thumbnail'] = '0';
	}
	else
	{
		$template->assign_block_vars('switch_thumbnail_support', array());
	}

	$template->assign_vars(array(
		'TPL_ATTACH_SPECIAL_CATEGORIES' => true,

		'L_MANAGE_CAT_TITLE' => $lang['Manage_categories'],
		'L_MANAGE_CAT_EXPLAIN' => $lang['Manage_categories_explain'],
		'L_SETTINGS_CAT_IMAGES' => $lang['Settings_cat_images'],
		'L_SETTINGS_CAT_STREAM' => $lang['Settings_cat_streams'],
		'L_SETTINGS_CAT_FLASH' => $lang['Settings_cat_flash'],
		'L_ASSIGNED_GROUP' => $lang['Assigned_group'],

		'L_DISPLAY_INLINED' => $lang['Display_inlined'],
		'L_DISPLAY_INLINED_EXPLAIN' => $lang['Display_inlined_explain'],
		'L_MAX_IMAGE_SIZE' => $lang['Max_image_size'],
		'L_MAX_IMAGE_SIZE_EXPLAIN' => $lang['Max_image_size_explain'],
		'L_IMAGE_LINK_SIZE' => $lang['Image_link_size'],
		'L_IMAGE_LINK_SIZE_EXPLAIN' => $lang['Image_link_size_explain'],
		'L_CREATE_THUMBNAIL' => $lang['Image_create_thumbnail'],
		'L_CREATE_THUMBNAIL_EXPLAIN' => $lang['Image_create_thumbnail_explain'],
		'L_MIN_THUMB_FILESIZE' => $lang['Image_min_thumb_filesize'],
		'L_MIN_THUMB_FILESIZE_EXPLAIN' => $lang['Image_min_thumb_filesize_explain'],
		'L_IMAGICK_PATH' => $lang['Image_imagick_path'],
		'L_IMAGICK_PATH_EXPLAIN' => $lang['Image_imagick_path_explain'],
		'L_SEARCH_IMAGICK' => $lang['Image_search_imagick'],
		'L_BYTES' => $lang['Bytes'],
		'L_TEST_SETTINGS' => $lang['Test_settings'],
		'L_USE_GD2' => $lang['Use_gd2'],
		'L_USE_GD2_EXPLAIN' => $lang['Use_gd2_explain'],

		'IMAGE_MAX_HEIGHT' => $new_attach['img_max_height'],
		'IMAGE_MAX_WIDTH' => $new_attach['img_max_width'],

		'IMAGE_LINK_HEIGHT' => $new_attach['img_link_height'],
		'IMAGE_LINK_WIDTH' => $new_attach['img_link_width'],
		'IMAGE_MIN_THUMB_FILESIZE' => $new_attach['img_min_thumb_filesize'],
		'IMAGE_IMAGICK_PATH' => $new_attach['img_imagick'],

		'DISPLAY_INLINED_YES' => $display_inlined_yes,
		'DISPLAY_INLINED_NO' => $display_inlined_no,

		'CREATE_THUMBNAIL_YES' => $create_thumbnail_yes,
		'CREATE_THUMBNAIL_NO' => $create_thumbnail_no,

		'USE_GD2_YES' => $use_gd2_yes,
		'USE_GD2_NO' => $use_gd2_no,

		'S_ASSIGNED_GROUP_IMAGES' => implode(', ', $s_assigned_group_images),
		'S_ATTACH_ACTION' => append_sid('admin_attachments.php?mode=cats'))
	);
}

// Check Cat Settings
if ($check_image_cat)
{
	// Some tests...
	$attach_config = array();

	$sql = 'SELECT * FROM bb_attachments_config';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not find Attachment Config Table', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		$attach_config[$row[$i]['config_name']] = trim($row[$i]['config_value']);
	}

	if ($attach_config['upload_dir'][0] == '/' || ($attach_config['upload_dir'][0] != '/' && $attach_config['upload_dir'][1] == ':'))
	{
		$upload_dir = $attach_config['upload_dir'];
	}
	else
	{
		$upload_dir = SITE_DIR . $attach_config['upload_dir'];
	}

	$upload_dir = $upload_dir . '/' . THUMB_DIR;

	$error = false;

	// Does the target directory exist, is it a directory and writeable. (only test if ftp upload is disabled)
	if (intval($attach_config['allow_ftp_upload']) == 0 && intval($attach_config['img_create_thumbnail']) == 1)
	{
		if ( !@file_exists(@amod_realpath($upload_dir)) )
		{
			@mkdir($upload_dir, 0755);
			@chmod($upload_dir, 0777);

			if ( !@file_exists(@amod_realpath($upload_dir)) )
			{
				$error = TRUE;
				$error_msg = sprintf($lang['Directory_does_not_exist'], $upload_dir) . '<br />';
			}

		}

		if (!$error && !is_dir($upload_dir))
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Directory_is_not_a_dir'], $upload_dir) . '<br />';
		}

		if (!$error)
		{
			if ( !($fp = @fopen($upload_dir . '/0_000000.000', 'w')) )
			{
				$error = TRUE;
				$error_msg = sprintf($lang['Directory_not_writeable'], $upload_dir) . '<br />';
			}
			else
			{
				@fclose($fp);
				@unlink($upload_dir . '/0_000000.000');
			}
		}
	}
	else if (intval($attach_config['allow_ftp_upload']) && intval($attach_config['img_create_thumbnail']))
	{
		// Check FTP Settings
		$server = ( empty($attach_config['ftp_server']) ) ? 'localhost' : $attach_config['ftp_server'];

		$conn_id = @ftp_connect($server);

		if (!$conn_id)
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Ftp_error_connect'], $server) . '<br />';
		}

		$login_result = @ftp_login($conn_id, $attach_config['ftp_user'], $attach_config['ftp_pass']);

		if (!$login_result && !$error)
		{
			$error = TRUE;
			$error_msg = sprintf($lang['Ftp_error_login'], $attach_config['ftp_user']) . '<br />';
		}

		if (!@ftp_pasv($conn_id, intval($attach_config['ftp_pasv_mode'])))
		{
			$error = TRUE;
			$error_msg = $lang['Ftp_error_pasv_mode'];
		}

		if (!$error)
		{
			// Check Upload
			$tmpfname = @tempnam('/tmp', 't0000');

			@unlink($tmpfname); // unlink for safety on php4.0.3+

			$fp = @fopen($tmpfname, 'w');

			@fwrite($fp, 'test');

			@fclose($fp);

			$result = @ftp_chdir($conn_id, $attach_config['ftp_path'] . '/' . THUMB_DIR);

			if (!$result)
			{
				@ftp_mkdir($conn_id, $attach_config['ftp_path'] . '/' . THUMB_DIR);
			}

			$result = @ftp_chdir($conn_id, $attach_config['ftp_path'] . '/' . THUMB_DIR);

			if (!$result)
			{

				$error = TRUE;
				$error_msg = sprintf($lang['Ftp_error_path'], $attach_config['ftp_path'] . '/' . THUMB_DIR) . '<br />';
			}
			else
			{
				$res = @ftp_put($conn_id, 't0000', $tmpfname, FTP_ASCII);

				if (!$res)
				{
					$error = TRUE;
					$error_msg = sprintf($lang['Ftp_error_upload'], $attach_config['ftp_path'] . '/' . THUMB_DIR) . '<br />';
				}
				else
				{
					$res = @ftp_delete($conn_id, 't0000');

					if (!$res)
					{
						$error = TRUE;
						$error_msg = sprintf($lang['Ftp_error_delete'], $attach_config['ftp_path'] . '/' . THUMB_DIR) . '<br />';
					}
				}
			}

			@ftp_quit($conn_id);

			@unlink($tmpfname);
		}
	}

	if (!$error)
	{
		message_die(GENERAL_MESSAGE, $lang['Test_settings_successful'] . '<br /><br />' . sprintf($lang['Click_return_attach_config'], '<a href="' . append_sid("admin_attachments.php?mode=cats") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.php?pane=right") . '">', '</a>'));
	}
}

// Quota Limit Settings
if ($submit && $mode == 'quota')
{
	// Change Quota Limit
	$quota_change_list = get_var('quota_change_list', array(0));
	$quota_desc_list = get_var('quota_desc_list', array(''));
	$filesize_list = get_var('max_filesize_list', array(0));
	$size_select_list = get_var('size_select_list', array(''));

	$allowed_list = array();

	for ($i = 0; $i < sizeof($quota_change_list); $i++)
	{
		$filesize_list[$i] = ( $size_select_list[$i] == 'kb' ) ? round($filesize_list[$i] * 1024) : ( ($size_select_list[$i] == 'mb') ? round($filesize_list[$i] * 1048576) : $filesize_list[$i] );

		$sql = "UPDATE bb_quota_limits
			SET quota_desc = '" . attach_mod_sql_escape($quota_desc_list[$i]) . "', quota_limit = " . (int) $filesize_list[$i] . "
			WHERE quota_limit_id = " . (int) $quota_change_list[$i];

		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Couldn\'t update Quota Limits', '', __LINE__, __FILE__, $sql);
		}
	}

	// Delete Quota Limits
	$quota_id_list = get_var('quota_id_list', array(0));

	$quota_id_sql = implode(', ', $quota_id_list);

	if ($quota_id_sql != '')
	{
		$sql = 'DELETE
		FROM bb_quota_limits
		WHERE quota_limit_id IN (' . $quota_id_sql . ')';

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not delete Quota Limits', '', __LINE__, __FILE__, $sql);
		}

		// Delete Quotas linked to this setting
		$sql = 'DELETE
		FROM bb_attach_quota
		WHERE quota_limit_id IN (' . $quota_id_sql . ')';

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not delete Quotas', '', __LINE__, __FILE__, $sql);
		}
	}

	// Add Quota Limit ?
	$quota_desc = get_var('quota_description', '');
	$filesize = get_var('add_max_filesize', 0);
	$size_select = get_var('add_size_select', '');
	$add = ( isset($_POST['add_quota_check']) ) ? TRUE : FALSE;

	if ($quota_desc != '' && $add)
	{
		// check Quota Description
		$sql = 'SELECT quota_desc
			FROM bb_quota_limits';

		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not query Quota Limits Table', '', __LINE__, __FILE__, $sql);
		}

		$row = $db->sql_fetchrowset($result);
		$num_rows = $db->sql_numrows($result);
		$db->sql_freeresult($result);

		if ( $num_rows > 0 )
		{
			for ($i = 0; $i < $num_rows; $i++)
			{
				if ($row[$i]['quota_desc'] == $quota_desc)
				{
					$error = TRUE;
					if( isset($error_msg) )
					{
						$error_msg .= '<br />';
					}
					$error_msg .= sprintf($lang['Quota_limit_exist'], $extension_group);
				}
			}
		}

		if (!$error)
		{
			$filesize = ( $size_select == 'kb' ) ? round($filesize * 1024) : ( ($size_select == 'mb') ? round($filesize * 1048576) : $filesize );

			$sql = "INSERT INTO bb_quota_limits (quota_desc, quota_limit)
			VALUES ('" . attach_mod_sql_escape($quota_desc) . "', " . (int) $filesize . ")";

			if ( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not add Quota Limit', '', __LINE__, __FILE__, $sql);
			}
		}

	}

	if (!$error)
	{
		$message = $lang['Attach_config_updated'] . '<br /><br />' . sprintf($lang['Click_return_attach_config'], '<a href="' . append_sid("admin_attachments.php?mode=quota") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.php?pane=right") . '">', '</a>');

		message_die(GENERAL_MESSAGE, $message);
	}

}

if ($mode == 'quota')
{
	$max_add_filesize = $attach_config['max_filesize'];
	$size = ($max_add_filesize >= 1048576) ? 'mb' : ( ($max_add_filesize >= 1024) ? 'kb' : 'b' );

	if ($max_add_filesize >= 1048576)
	{
		$max_add_filesize = round($max_add_filesize / 1048576 * 100) / 100;
	}
	else if ( $max_add_filesize >= 1024)
	{
		$max_add_filesize = round($max_add_filesize / 1024 * 100) / 100;
	}

	$template->assign_vars(array(
		'TPL_ATTACH_QUOTA' => true,

		'L_MANAGE_QUOTAS_TITLE' => $lang['Manage_quotas'],
		'L_MANAGE_QUOTAS_EXPLAIN' => $lang['Manage_quotas_explain'],
		'L_EDIT' => $lang['Edit'],
		'L_VIEW' => $lang['View'],
		'L_DESCRIPTION' => $lang['Description'],
		'L_SIZE' => $lang['Max_filesize_attach'],
		'L_ADD_NEW' => $lang['Add_new'],
		'MAX_FILESIZE' => $max_add_filesize,

		'S_FILESIZE' => size_select('add_size_select', $size),

		'S_ATTACH_ACTION' => append_sid('admin_attachments.php?mode=quota'))
	);

	$sql = "SELECT * FROM bb_quota_limits ORDER BY quota_limit DESC";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get quota limits', '', __LINE__, __FILE__, $sql);
	}

	$rows = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < sizeof($rows); $i++)
	{
		$size_format = ($rows[$i]['quota_limit'] >= 1048576) ? 'mb' : ( ($rows[$i]['quota_limit'] >= 1024) ? 'kb' : 'b' );

		if ( $rows[$i]['quota_limit'] >= 1048576)
		{
			$rows[$i]['quota_limit'] = round($rows[$i]['quota_limit'] / 1048576 * 100) / 100;
		}
		else if($rows[$i]['quota_limit'] >= 1024)
		{
			$rows[$i]['quota_limit'] = round($rows[$i]['quota_limit'] / 1024 * 100) / 100;
		}

		$template->assign_block_vars('limit_row', array(
			'QUOTA_NAME'		=> $rows[$i]['quota_desc'],
			'QUOTA_ID' => $rows[$i]['quota_limit_id'],
			'S_FILESIZE' => size_select('size_select_list[]', $size_format),
			'U_VIEW' => append_sid("admin_attachments.php?mode=$mode&amp;e_mode=view_quota&amp;quota_id=" . $rows[$i]['quota_limit_id']),
			'MAX_FILESIZE' => $rows[$i]['quota_limit'])
		);
	}
}

if ($mode == 'quota' && $e_mode == 'view_quota')
{
	$quota_id = get_var('quota_id', 0);

	if (!$quota_id)
	{
		message_die(GENERAL_MESSAGE, 'Invalid Call');
	}

	$template->assign_block_vars('switch_quota_limit_desc', array());

	$sql = "SELECT * FROM bb_quota_limits WHERE quota_limit_id = " . (int) $quota_id . " LIMIT 1";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get quota limits', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'L_QUOTA_LIMIT_DESC' => $row['quota_desc'],
		'L_ASSIGNED_USERS' => $lang['Assigned_users'],
		'L_ASSIGNED_GROUPS' => $lang['Assigned_groups'],
	));

	$sql = 'SELECT q.user_id, u.username, q.quota_type
		FROM bb_attach_quota q, bb_users u
		WHERE q.quota_limit_id = ' . (int) $quota_id . '
			AND q.user_id <> 0
			AND q.user_id = u.user_id';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get quota limits', '', __LINE__, __FILE__, $sql);
	}

	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		if ($rows[$i]['quota_type'] == QUOTA_UPLOAD_LIMIT)
		{
			$template->assign_block_vars('users_upload_row', array(
				'USER_ID' => $rows[$i]['user_id'],
				'USERNAME' => $rows[$i]['username'])
			);
		}
		else if ($rows[$i]['quota_type'] == QUOTA_PM_LIMIT)
		{
			$template->assign_block_vars('users_pm_row', array(
				'USER_ID' => $rows[$i]['user_id'],
				'USERNAME' => $rows[$i]['username'])
			);
		}
	}

	$sql = 'SELECT q.group_id, g.group_name, q.quota_type
		FROM bb_attach_quota q, bb_groups g
		WHERE q.quota_limit_id = ' . (int) $quota_id . '
			AND q.group_id <> 0
			AND q.group_id = g.group_id';

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get quota limits', '', __LINE__, __FILE__, $sql);
	}

	$rows = $db->sql_fetchrowset($result);
	$num_rows = $db->sql_numrows($result);
	$db->sql_freeresult($result);

	for ($i = 0; $i < $num_rows; $i++)
	{
		if ($rows[$i]['quota_type'] == QUOTA_UPLOAD_LIMIT)
		{
			$template->assign_block_vars('groups_upload_row', array(
				'GROUP_ID' => $rows[$i]['group_id'],
				'GROUPNAME' => $rows[$i]['group_name'])
			);
		}
		else if ($rows[$i]['quota_type'] == QUOTA_PM_LIMIT)
		{
			$template->assign_block_vars('groups_pm_row', array(
				'GROUP_ID' => $rows[$i]['group_id'],
				'GROUPNAME' => $rows[$i]['group_name'])
			);
		}
	}
}

if ($error)
{
	$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
}

print_page('admin_attachments.tpl', 'admin');


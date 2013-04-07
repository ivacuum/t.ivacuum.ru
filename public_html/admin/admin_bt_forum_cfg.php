<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['TorrentPier']['Forum_Config'] = basename(__FILE__);
	return;
}
require('./pagestart.php');
// ACP Header - END

$max_forum_name_len = 30;
$max_forum_rows     = 35;

require(SITE_DIR . 'includes/functions_admin_torrent.php');
require(LANG_DIR .'lang_admin_bt.php');

$submit  = isset($_POST['submit']);
$confirm = isset($_POST['confirm']);

$cfg = array();

// All config names with default values
$default_cfg_str = array(
	'bt_announce_url'              => 'http://yourdomain.com/bt/',
	'bt_add_comment'               => '',
	'bt_add_publisher'             => '',
);

$default_cfg_bool = array(
	'bt_show_peers'                => 1,
	'bt_add_auth_key'              => 1,
	'bt_show_dl_list'              => 0,
	'bt_dl_list_only_1st_page'     => 1,
	'bt_dl_list_only_count'        => 1,
	'bt_gen_passkey_on_reg'        => 1,
	'bt_replace_ann_url'           => 1,
	'bt_show_ip_only_moder'        => 1,
	'bt_show_port_only_moder'      => 1,
	'bt_check_announce_url'        => 0,
	'bt_show_dl_list_buttons'      => 1,
	'bt_show_dl_but_will'          => 1,
	'bt_show_dl_but_down'          => 0,
	'bt_show_dl_but_compl'         => 1,
	'bt_show_dl_but_cancel'        => 1,
	'bt_show_dl_stat_on_index'     => 1,
	'bt_newtopic_auto_reg'         => 1,
	'bt_tor_browse_only_reg'       => 1,
	'bt_search_bool_mode'          => 1,
	'bt_allow_spmode_change'       => 1,
	'bt_del_addit_ann_urls'        => 1,
	'bt_set_dltype_on_tor_reg'     => 1,
	'bt_unset_dltype_on_tor_unreg' => 0,
);

$default_cfg_num = array(
	'bt_show_peers_mode'           => SHOW_PEERS_COUNT,
);

$default_cfg = array_merge($default_cfg_str, $default_cfg_bool, $default_cfg_num);

$db_fields_bool = array(
	'allow_reg_tracker'            => 0,  // Allowed forums for registering torrents on tracker
	'allow_dl_topic'               => 0,  // Allowed forums for dl-topics
	'self_moderated'               => 0,  // Users can move theirs topic to another forum
);

// Get config
$cfg = bb_get_config('bb_config', true, false);

// Submit new config
if ($submit && $confirm)
{
	foreach ($db_fields_bool as $field_name => $field_def_val)
	{
		update_table_bool('bb_forums', 'forum_id', $field_name, $field_def_val);
	}

	update_config_table('bb_config', $default_cfg_str,  $cfg, 'str');
	update_config_table('bb_config', $default_cfg_bool, $cfg, 'bool');
	update_config_table('bb_config', $default_cfg_num,  $cfg, 'num');

	$datastore->update('cat_forums');

	$message = $lang['config_upd'] .'<br /><br />'. sprintf($lang['return_config'], '<a href="'. append_sid("admin_bt_forum_cfg.php") .'">', '</a>') .'<br /><br />'. sprintf($lang['Click_return_admin_index'], '<a href="'. append_sid("index.php?pane=right") .'">', '</a>');
	message_die(GENERAL_MESSAGE, $message);
}

// Set template vars
set_tpl_vars      ($default_cfg_str, $cfg);
set_tpl_vars_lang ($default_cfg_str);

set_tpl_vars_bool ($default_cfg_bool, $cfg);
set_tpl_vars_lang ($default_cfg_bool);

set_tpl_vars      ($default_cfg_num, $cfg);
set_tpl_vars_lang ($default_cfg_num);

set_tpl_vars_lang ($db_fields_bool);

// Get Forums list
$sql = "SELECT f.*
	FROM bb_categories c, bb_forums f
	WHERE f.cat_id = c.cat_id
	ORDER BY c.cat_order, f.forum_order";

if (!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, 'Could not obtain forum names', '', __LINE__, __FILE__, $sql);
}

$rowset = $db->sql_fetchrowset($result);
$forum_rows = min($max_forum_rows, count($rowset));

foreach ($db_fields_bool as $field_name => $field_def_val)
{
	$$field_name = '';
}

foreach ($rowset as $rid => $forum)
{
	foreach ($db_fields_bool as $field_name => $field_def_val)
	{
		$forum_name = $forum['forum_name'];
		$selected   = ($forum[$field_name]) ? ' selected="selected"' : '';

		if( mb_strlen($forum_name, 'utf-8') > $max_forum_name_len )
		{
			$forum_name = mb_substr($forum_name, 0, $max_forum_name_len, 'utf-8') . '..';
		}

		$$field_name .= '<option value="'. $forum['forum_id'] .'" '. $selected .'>&nbsp;' . (($forum['forum_parent']) ? HTML_SF_SPACER : '') . htmlCHR($forum_name) ."</option>\n";
	}
}

foreach ($db_fields_bool as $field_name => $field_def_val)
{
	$$field_name = '<select name="'. $field_name ."[]\" multiple=\"multiple\" size=\"$forum_rows\">". $$field_name .'</select>';
	$template->assign_vars(array('S_'. strtoupper($field_name) => $$field_name));
}

$template->assign_vars(array(
	'L_CONFIGURATION_TITLE'   => $lang['forum_cfg_title'],
	'L_GENERAL_SETTINGS'      => $lang['General_settings'],
	'L_BT_SELECT_FORUMS'      => $lang['bt_select_forums'],
	'L_BT_SELECT_FORUMS_EXPL' => $lang['bt_select_forums_expl'],

	'L_BT_SHOW_PEERS_MODE_COUNT'   => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_COUNT) ? '<u>'. $lang['bt_show_peers_mode_count'] .'</u>' : $lang['bt_show_peers_mode_count'],
	'L_BT_SHOW_PEERS_MODE_NAMES'   => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_NAMES) ? '<u>'. $lang['bt_show_peers_mode_names'] .'</u>' : $lang['bt_show_peers_mode_names'],
	'L_BT_SHOW_PEERS_MODE_FULL'    => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_FULL) ? '<u>'. $lang['bt_show_peers_mode_full'] .'</u>' : $lang['bt_show_peers_mode_full'],

	'BT_SHOW_PEERS_MODE_COUNT_VAL' => SHOW_PEERS_COUNT,
	'BT_SHOW_PEERS_MODE_NAMES_VAL' => SHOW_PEERS_NAMES,
	'BT_SHOW_PEERS_MODE_FULL_VAL'  => SHOW_PEERS_FULL,

	'BT_SHOW_PEERS_MODE_COUNT_SEL' => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_COUNT) ? HTML_CHECKED : '',
	'BT_SHOW_PEERS_MODE_NAMES_SEL' => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_NAMES) ? HTML_CHECKED : '',
	'BT_SHOW_PEERS_MODE_FULL_SEL'  => ($cfg['bt_show_peers_mode'] == SHOW_PEERS_FULL) ? HTML_CHECKED : '',

	'S_HIDDEN_FIELDS' => '',
	'S_CONFIG_ACTION' => append_sid("admin_bt_forum_cfg.php"),
));

print_page('admin_bt_forum_cfg.tpl', 'admin');

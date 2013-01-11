<?php

$lang['return_config'] = '%sReturn to Configuration%s';
$lang['config_upd'] = 'Configuration Updated Successfully';
$lang['set_defaults'] = 'Restore defaults';

//
// Tracker config
//
$lang['tracker_cfg_title'] = 'Tracker';
$lang['forum_cfg_title'] = 'Forum settings';
$lang['tracker_settings'] = 'Tracker settings';

$lang['off'] = 'Disable tracker';
$lang['off_reason'] = 'Disable reason';
$lang['off_reason_expl'] = 'this message will be sent to client when the tracker is disabled';
$lang['autoclean'] = 'Autoclean';
$lang['autoclean_expl'] = 'autoclean peers table - do not disable without reason';
$lang['compact_mode'] = 'Compact mode';
$lang['compact_mode_expl'] = '"Yes" - tracker will only accept clients working in compact mode<br />"No" - compatible mode (chosen by client)';
$lang['browser_redirect_url'] = 'Browser redirect URL';
$lang['browser_redirect_url_expl'] = 'if user tries to open tracker URL in Web browser<br />leave blank to disable';

$lang['ANNOUNCE_INTERVAL_HEAD'] = 'Misc';
$lang['ANNOUNCE_INTERVAL'] = 'Announce interval';
$lang['ANNOUNCE_INTERVAL_EXPL'] = 'peers should wait at least this many seconds between announcements';
$lang['numwant'] = 'Numwant value';
$lang['numwant_expl'] = 'number of peers being sent to client';
$lang['expire_factor'] = 'Peer expire factor';
$lang['expire_factor_expl'] = 'Consider a peer dead if it has not announced in a number of seconds equal to this many times the calculated announce interval at the time of its last announcement (must be greater than 1)';
$lang['IGNORE_GIVEN_IP'] = 'Ignore IP reported by client';
$lang['update_dlstat'] = 'Store users up/down statistics';

$lang['limit_active_tor_head'] = 'Limits';
$lang['limit_active_tor'] = 'Limit active torrents';
$lang['limit_seed_count'] = 'Seeding limit';
$lang['limit_seed_count_expl'] = '(0 - no limit)';
$lang['limit_leech_count'] = 'Leeching limit';
$lang['limit_leech_count_expl'] = '(0 - no limit)';
$lang['leech_expire_factor'] = 'Leech expire factor';
$lang['leech_expire_factor_expl'] = 'Treat a peer as active for this number of minutes even if it sent "stopped" event after starting dl<br />0 - take into account "stopped" event';
$lang['limit_concurrent_ips'] = "Limit concurrent IP's";
$lang['limit_concurrent_ips_expl'] = 'per torrent limit';
$lang['limit_seed_ips'] = 'Seeding IP limit';
$lang['limit_seed_ips_expl'] = "allow seeding from no more than <i>xx</i> IP's<br />0 - no limit";
$lang['limit_leech_ips'] = 'Leeching IP limit';
$lang['limit_leech_ips_expl'] = "allow leeching from no more than <i>xx</i> IP's<br />0 - no limit";

$lang['USE_AUTH_KEY_HEAD'] = 'Authorization';
$lang['USE_AUTH_KEY'] = 'Passkey';
$lang['USE_AUTH_KEY_EXPL'] = 'enable check for passkey';
$lang['AUTH_KEY_NAME'] = 'Passkey name';
$lang['AUTH_KEY_NAME_EXPL'] = 'passkey key name in GET request';
$lang['ALLOW_GUEST_DL'] = 'Allow guest access to tracker';

//
// Forum config
//
$lang['forum_cfg_expl'] = 'Forum config';

$lang['bt_select_forums'] = 'Forum options:';
$lang['bt_select_forums_expl'] = 'hold down <i>Ctrl</i> while selecting multiple forums';

$lang['allow_reg_tracker'] = 'Allowed forums for registering <b>.torrents</b> on tracker';
$lang['allow_dl_topic'] = 'Allow post <b>Download topics</b>';
$lang['show_dl_buttons'] = 'Show buttons for manually changing DL-status';
$lang['self_moderated'] = 'Users can <b>move</b> their topics to another forum';

$lang['bt_announce_url_head'] = 'Announce URL';
$lang['bt_announce_url'] = 'Announce url';
$lang['bt_announce_url_expl'] = 'you can define additional allowed urls in "includes/announce_urls.php"';
$lang['bt_check_announce_url'] = 'Verify announce url';
$lang['bt_check_announce_url_expl'] = 'register on tracker only allowed urls';
$lang['bt_replace_ann_url'] = 'Replace announce url';
$lang['bt_replace_ann_url_expl'] = 'replace original announce url with your default in .torrent files';
$lang['bt_del_addit_ann_urls'] = 'Remove all additional announce urls';
$lang['bt_add_comment'] = 'Torrent comments';
$lang['bt_add_comment_expl'] = 'adds the Comments filed to the .torrent files (leave blank to use the topic URL as a comment)';
$lang['bt_add_publisher'] = 'Torrent\'s publisher';
$lang['bt_add_publisher_expl'] = 'adds the Publisher field and topic URL as the Publisher-url to the .torrent files (leave blank to disable)';

$lang['bt_show_peers_head'] = 'Peers-List';
$lang['bt_show_peers'] = 'Show peers (seeders and leechers)';
$lang['bt_show_peers_expl'] = 'this will show seeders/leechers list above the topic with torrent';
$lang['bt_show_peers_mode'] = 'By default, show peers as:';
$lang['bt_show_peers_mode_count'] = 'Count only';
$lang['bt_show_peers_mode_names'] = 'Names only';
$lang['bt_show_peers_mode_full'] = 'Full details';
$lang['bt_allow_spmode_change'] = 'Allow "Full details" mode';
$lang['bt_allow_spmode_change_expl'] = 'if "no", only default peer display mode will be available';
$lang['bt_show_ip_only_moder'] = 'Peers\' <b>IP</b>s are visible to moderators only';
$lang['bt_show_port_only_moder'] = 'Peers\' <b>Port</b>s are visible to moderators only';

$lang['bt_show_dl_list_head'] = 'DL-List';
$lang['bt_show_dl_list'] = 'Show DL-List in Download topics';
$lang['bt_dl_list_only_1st_page'] = 'Show DL-List only on first page in topics';
$lang['bt_dl_list_only_count'] = 'Show only number of users';
$lang['BT_DL_LIST_EXPIRE'] = 'Expire time of DL-List records';
$lang['BT_DL_LIST_EXPIRE_EXPL'] = 'after this time users will be automatically removed from DL-List';
$lang['bt_show_dl_list_buttons'] = 'Show buttons for manually changing DL-status';
$lang['bt_show_dl_but_will'] = $lang['DL_WILL'];
$lang['bt_show_dl_but_down'] = $lang['DL_DOWN'];
$lang['bt_show_dl_but_compl'] = $lang['DL_COMPLETE'];
$lang['bt_show_dl_but_cancel'] = $lang['DL_CANCEL'];

$lang['bt_add_auth_key_head'] = 'Passkey';
$lang['bt_add_auth_key'] = 'Enable adding passkey to the torrent-files before downloading';
$lang['bt_gen_passkey_on_reg'] = 'Automatically generate passkey';
$lang['bt_gen_passkey_on_reg_expl'] = "generate passkey during first downloading attempt if current user's passkey is empty";

$lang['bt_tor_browse_only_reg_head'] = 'Torrent browser (tracker)';
$lang['bt_tor_browse_only_reg'] = 'Torrent browser (tracker.php) accessible only for logged in users';
$lang['bt_search_bool_mode'] = 'Allow boolean full-text searches';
$lang['bt_search_bool_mode_expl'] = 'use *, +, -,.. in searches';

$lang['bt_show_dl_stat_on_index_head'] = "Miscellaneous";
$lang['bt_show_dl_stat_on_index'] = "Show users UL/DL statistics at the top of the forum's main page";
$lang['bt_newtopic_auto_reg'] = 'Automatically register torrent on tracker for new topics';
$lang['bt_set_dltype_on_tor_reg'] = 'Change topic status to "Download" while registering torrent on tracker';
$lang['bt_set_dltype_on_tor_reg_expl'] = 'will change topic type to "Download" regardless of forum settings';
$lang['bt_unset_dltype_on_tor_unreg'] = 'Change topic status to "Normal" while unregistering torrent from tracker';

//
// Release
//
$lang['Release_exp'] = 'This page displays all forums. For each of them you can set the release type which should be posted in the forum.';
$lang['tpl_none'] = 'Don\'t use templates';
$lang['tpl_video'] = 'Video (basic)';
$lang['tpl_video_home'] = 'Video (home)';
$lang['tpl_video_simple'] = 'Video (simple)';
$lang['tpl_video_lesson'] = 'Video (lesson)';
$lang['tpl_games'] = 'Games';
$lang['tpl_games_ps'] = 'Games PS/PS2';
$lang['tpl_games_psp'] = 'Games PSP';
$lang['tpl_games_xbox'] = 'Games XBOX';
$lang['tpl_progs'] = 'Programs';
$lang['tpl_progs_mac'] = 'Programs Mac OS';
$lang['tpl_music'] = 'Music';
$lang['tpl_books'] = 'Books';
$lang['tpl_audiobooks'] = 'Audiobooks';
$lang['tpl_sport'] = 'Sport';



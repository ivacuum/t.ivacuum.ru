<?php namespace app;

$acm_prefix = 't.ivacuum.ru';

$bb_cfg = $page_cfg = array();

// Increase number after changing js or css
$bb_cfg['js_ver']  = 19;
$bb_cfg['css_ver'] = 42;

$bb_cfg['board_disabled_msg'] = 'форум временно отключен'; // show this msg if board has been disabled via ON/OFF trigger
$bb_cfg['srv_overloaded_msg'] = "Извините, в данный момент сервер перегружен\nПопробуйте повторить запрос через несколько минут";

// Database
$dbhost    = $_ENV['DB_HOST'];
$dbname    = $_ENV['DB_DATABASE'];
$dbuser    = $_ENV['DB_USERNAME'];
$dbpasswd  = $_ENV['DB_PASSWORD'];
$dbsock    = $_ENV['DB_SOCKET'];
$dbcharset = 'utf8';

$dbms = 'mysql';
$dbcollation = '';
$pconnect = false;

$table_prefix  = 'bb_';
$buffer_prefix = 'buf_';

$apc_installed = function_exists('apc_fetch');
$bb_cfg['apc_prefix'] = 't.ivacuum.ru';

$bb_cfg['memcached'] = array(
	'host'         => $_ENV['MEMCACHED_HOST'],
	'port'         => $_ENV['MEMCACHED_PORT'],
	'prefix'       => 't.ivacuum.ru',
	'pconnect'     => false,
	'con_required' => true
);

$bb_cfg['redis'] = [
    'url' => null,
    'host' => $_ENV['REDIS_HOST'],
    'port' => $_ENV['REDIS_PORT'],
    'prefix' => 't.ivacuum.ru',
    'password' => null,
    'database' => 0,
];

$bb_cfg['sphinx'] = array(
	'db_torrents' => 'torrents',
	'db_posts'    => 'posts, posts_delta',
	'enabled'     => true,
	'host'        => 'localhost',
	'port'        => false,
	'socket'      => '/tmp/sphinx.sock'
);

// Tracker Cache
$bb_cfg['tr_cache_type'] = $_ENV['CACHE_FOR_TRACKER']; // Available cache types: none, APC, memcached, sqlite

// Forum Cache
$bb_cfg['bb_cache_type'] = $_ENV['CACHE_FOR_FORUM']; // Available cache types: none, same_as_tracker, APC, memcached, sqlite

// Session Cache
$bb_cfg['session_cache_type'] = $_ENV['CACHE_FOR_SESSION']; // Available cache types: none, sqlite, filecache

// Datastore
$bb_cfg['datastore_type'] = $_ENV['CACHE_FOR_DATASTORE']; // Available cache types: mysql, sqlite, APC, filecache

$bb_cfg['passkey_key'] = 'uk'; // Passkey key name in GET request

mb_internal_encoding('utf-8');

//$bb_cfg['bt_ratio_warning_msg']      = '$bb_cfg[\'bt_ratio_warning_msg\']'; /* Перемещено в "attach_mod\displaying_torrent.php" */

$bb_cfg['bt_min_ratio_allow_dl_tor'] = 0;          // 0 - disable
$bb_cfg['bt_min_ratio_warning']      = 0;          // 0 - disable
$bb_cfg['bt_ratio_warning_url_help'] = 'viewtopic.php?t=317'; // URL help link, for limit end.
$bb_cfg['bt_min_ratio_dl_button']    = 0;          // 0 - disable

$bb_cfg['tr_settings_days_keep']    = 14;          // remove search options after xx days of inactivity

$bb_cfg['show_dl_status_in_search'] = true;
$bb_cfg['show_dl_status_in_forum']  = true;

$bb_cfg['show_tor_info_in_dl_list'] = true;        // http://trac.torrentpier.com/trac/changeset/377
$bb_cfg['allow_dl_list_names_mode'] = true;

// Torrents
$bb_cfg['torrent_sign']   = '';                    // e.g. "[yoursite.com]"
$bb_cfg['tor_help_links'] = '';

// Last added torrents
$bb_cfg['t_last_added_num'] = 21;
// Top downloaded torrents
$bb_cfg['t_top_downloaded'] = 0;
// Top seeders
$bb_cfg['t_top_seeders'] = 15;
// Top leechers
$bb_cfg['t_top_leechers'] = 15;
$bb_cfg['t_top_releasers'] = 15;
$bb_cfg['t_top_share'] = 15;

// Days to keep torrent registered, if:
$bb_cfg['seeder_last_seen_days_keep']  = 90;
$bb_cfg['seeder_never_seen_days_keep'] = 2;

$bb_cfg['archive_forum'] = 11;
$bb_cfg['archive_hide_forum'] = 0;
$bb_cfg['hide_forums'] = ''; //implode(',', array(84, 85, 86, 87, 88, 89, 90));

// Ratio limits
define('TR_RATING_LIMITS', false);                  // ON/OFF
define('MIN_DL_FOR_RATIO', 2147483648);            // in bytes, 0 - disable

// Don't change the order of ratios (from 0 to 1)
// rating < 0.4 -- allow only 1 torrent for leeching
// rating < 0.5 -- only 2
// rating < 0.6 -- only 3
// rating > 0.6 -- depend on your tracker config limits (in "ACP - Tracker Config - Limits")
$rating_limits = array(
	'0.3' => 3,
	'0.5' => 3,
	'0.8' => 3,
);

// Seeding torrents limit
$bb_cfg['max_seeding_torrents']     = 0;        // 0 - unlimited
$bb_cfg['min_up_speed_per_torrent'] = 500;      // bytes
$bb_cfg['too_many_seeding_redirect_url'] = 'viewtopic.php?t=TOPIC_ID';

// DL-Status
$bb_cfg['dl_will_days_keep']     = 30;          // days to keep user's dlstatus records
$bb_cfg['dl_down_days_keep']     = 30;
$bb_cfg['dl_complete_days_keep'] = 90;
$bb_cfg['dl_cancel_days_keep']   = 30;

// Tor-Stats
$bb_cfg['torstat_days_keep']     = 30;          // days to keep user's per-torrent stats

// Tor-Help
$bb_cfg['torhelp_enabled']       = true;        // find dead torrents (without seeder) that user might help seeding

$page_cfg['show_torhelp'] = array(
#	BB_SCRIPT => true
	'index'   => true,
	'tracker' => true,
);

// Path (trailing slash '/' at the end: XX_PATH - without, XX_DIR - with)
define('DIR_SEPR', DIRECTORY_SEPARATOR);

// Language
$bb_cfg['default_lang_dir'] = SITE_DIR .'language/lang_russian/';

// Templates
define('ADMIN_TPL_DIR', SITE_DIR . 'templates/admin/');

$bb_cfg['tpl_name']   = 'default';
$bb_cfg['stylesheet'] = 'main.css';
$bb_cfg['theme_css']  = 'theme_default.css';

$bb_cfg['show_sidebar1_on_every_page'] = false;
$bb_cfg['show_sidebar2_on_every_page'] = false;

$bb_cfg['sidebar1_static_content_path'] = SITE_DIR . 'misc/html/sidebar1.html';
$bb_cfg['sidebar2_static_content_path'] = SITE_DIR . 'misc/html/sidebar2.html';

$page_cfg['show_sidebar1'] = array(
#	BB_SCRIPT => true
	'index'  => true,
);
$page_cfg['show_sidebar2'] = array(
#	BB_SCRIPT => true
	'index' => false,
);

$bb_cfg['topic_tpl']['header']         = SITE_DIR . 'templates/topic_tpl_header.html';
$bb_cfg['topic_tpl']['shared_footer']  = SITE_DIR . 'templates/topic_tpl_shared_footer.html';
$bb_cfg['topic_tpl']['shared_header']  = SITE_DIR . 'templates/topic_tpl_shared_header.html';
$bb_cfg['topic_tpl']['overall_header'] = SITE_DIR . 'templates/topic_tpl_overall_header.html';
$bb_cfg['topic_tpl']['rules_video']    = SITE_DIR . 'templates/topic_tpl_rules_video.html';

// Cookie
$bb_cfg['cookie_domain'] = $_ENV['COOKIE_DOMAIN'];
$bb_cfg['cookie_path']   = $_ENV['COOKIE_PATH'];
$bb_cfg['cookie_secure'] = $_ENV['COOKIE_SECURE'];
$bb_cfg['cookie_prefix'] = $_ENV['COOKIE_PREFIX'];

define('COOKIE_DBG', 'bb_dbg');                    // debug cookie name

// Server
$bb_cfg['server_name'] = $_ENV['SITE_DOMAIN'];//$_SERVER['SERVER_NAME'];  // The domain name from which this board runs
$bb_cfg['server_port'] = '80';//$_SERVER['SERVER_PORT'];  // The port your server is running on
$bb_cfg['script_path'] = '/';                // The path where FORUM is located relative to the domain name
$bb_cfg['sitename'] = 'torrent.ivacuum.ru';

/*
if (isset($s_provider) && $s_provider == 'internet')
{
	$bb_cfg['cookie_domain'] = 't.internet.ivacuum.ru';
	$bb_cfg['server_name']   = 't.internet.ivacuum.ru';
}
*/

// Server load
$bb_cfg['max_srv_load']      = 0;                  // 0 - disable
$bb_cfg['tr_working_second'] = 0;                  // 0 - disable

// Backup
$bb_cfg['db_backup_shell_cmd']     = '';           // '/path/to/db_backup.sh 2>&1'
$bb_cfg['site_backup_shell_cmd']   = '';

// GZip
$bb_cfg['gzip_compress'] = false;                  // compress output
$bb_cfg['gzip_force']    = false;                  // always compress (don't check client compatibility)

// Sessions
$bb_cfg['session_update_intrv']    = 180;          // sec

$bb_cfg['user_session_duration']   = 1800;         // sec
$bb_cfg['admin_session_duration']  = 6*3600;       // sec
$bb_cfg['user_session_gc_ttl']     = 1800;         // number of seconds that a staled session entry may remain in sessions table
$bb_cfg['session_cache_gc_ttl']    = 240;         // sec (default: 1200)
$bb_cfg['max_reg_users_online']    = 0;            // 0 - unlimited
$bb_cfg['max_last_visit_days']     = 14;           // days
$bb_cfg['last_visit_update_intrv'] = 3600;         // sec

// Registration
$bb_cfg['new_user_reg_disabled']   = false;        // Disable new user registrations

// Email
$bb_cfg['emailer_disabled']        = false;

$bb_cfg['topic_notify_enabled']    = true;
$bb_cfg['pm_notify_enabled']       = true;
$bb_cfg['groupcp_send_email']      = true;
$bb_cfg['email_change_disabled']   = false;        // disable changing email by user

$bb_cfg['tech_admin_email']        = $_ENV['ADMIN_EMAIL'];    // email for sending error reports
$bb_cfg['email_default_charset']   = 'utf-8';

// Debug
define('DEBUG',     false);                         // !!! "DEBUG" should be ALWAYS DISABLED on production environment !!!
define('PROFILER',  'false');                        // Profiler extension name, or FALSE to disable (supported: 'dbg')

define('SQL_DEBUG',            false);
define('SQL_LOG_ERRORS',       true);              // all SQL_xxx options enabled only if SQL_DEBUG == TRUE
define('SQL_CALC_QUERY_TIME',  false);              // for stats
define('SQL_LOG_SLOW_QUERIES', false);
define('SQL_SLOW_QUERY_TIME',  10);                // sec

// Special users
$bb_cfg['dbg_users'] = array(
#	user_id => 'name',
	2 => 'admin',
);

$bb_cfg['unlimited_users'] = array(
#	user_id => 'name',
	2 => 'admin',
);

$bb_cfg['super_admins'] = array(
#	user_id => 'name',
	2  => 'admin',
	18 => 'mindblower'
);

// Log options
define('LOG_EXT',      'log');
define('LOG_SEPR',     ' | ');
define('LOG_LF',       "\n");
define('LOG_MAX_SIZE', 1048576); // bytes

// Log request
$log_ip_req = array(
#	'127.0.0.1' => 'user1',  // CLIENT_IP => 'name'
#	'7f000001'  => 'user2',  // USER_IP   => 'name'
);

$log_passkey = array(
#	'passkey' => 'log_filename',
);

// Log response
$log_ip_resp = array(
#	'127.0.0.1' => 'user1',  // CLIENT_IP => 'name'
#	'7f000001'  => 'user2',  // USER_IP   => 'name'
);

// Error reporting
if (DEBUG)
{
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('log_errors',     0);
}
else
{
	error_reporting(E_ALL);                          # E_ALL & ~E_NOTICE
	ini_set('display_errors', 0);
	ini_set('log_errors',     1);
}

ini_set('error_log', SITE_DIR . 'log/php_err.log');

// Disable magic_quotes_runtime
define('STRIP_SLASHES', 0);
// set_magic_quotes_runtime(0);

// Triggers
define('BB_ENABLED',   SITE_DIR . 'triggers/$on');
define('BB_DISABLED',  SITE_DIR . 'triggers/$off');
define('CRON_ALLOWED', SITE_DIR . 'triggers/cron_allowed');
define('CRON_RUNNING', SITE_DIR . 'triggers/cron_running');

// Cron
$bb_cfg['cron_enabled']        = true;
$bb_cfg['cron_check_interval'] = 45;               // sec

// News
$bb_cfg['show_latest_news']     = true;
$bb_cfg['latest_news_count']    = 5;
$bb_cfg['latest_news_forum_id'] = 2;

// Subforums
$bb_cfg['sf_on_first_page_only']     = true;
$bb_cfg['sf_check_view_permissions'] = false;

// Forums
$bb_cfg['allowed_topics_per_page'] = array(50, 100, 150, 200, 250, 300);

// Topics
$bb_cfg['show_quick_reply']   = true;
$bb_cfg['show_rank_text']     = false;
$bb_cfg['show_rank_image']    = true;
$bb_cfg['show_poster_joined'] = true;
$bb_cfg['show_poster_posts']  = true;
$bb_cfg['show_poster_from']   = true;
$bb_cfg['show_poster_flag']   = true;
$bb_cfg['show_bot_nick']      = false;
$bb_cfg['text_buttons']       = true;              // replace EDIT, QUOTE... images with text links
$bb_cfg['parse_ed2k_links']   = false;             // make ed2k links clickable
$bb_cfg['post_date_format']   = 'd-M-Y H:i';
$bb_cfg['ext_link_new_win']   = true;              // open external links in new window

$bb_cfg['topic_moved_days_keep'] = 7;              // remove topic moved links after xx days (or FALSE to disable)

$bb_cfg['allowed_posts_per_page'] = array(15, 30, 50, 100);

// Posts
$bb_cfg['use_posts_cache']       = true;           // if you switch from ON to OFF, you need to TRUNCATE `bb_posts_html` table
$bb_cfg['posts_cache_days_keep'] = 7;

// Search
$bb_cfg['disable_ft_search_in_posts']  = false;    // disable searching in post bodies
$bb_cfg['disable_search_for_guest']    = true;
$bb_cfg['allow_search_in_bool_mode']   = true;
$bb_cfg['max_search_words_per_post']   = 200;
$bb_cfg['search_min_word_len']         = 3;
$bb_cfg['search_max_word_len']         = 35;
$bb_cfg['limit_max_search_results']    = false;
$bb_cfg['search_help_url']             = '';
$bb_cfg['search_match_help_topic_id']  = 1457;

// Posting
$bb_cfg['show_virtual_keyboard'] = false;
$bb_cfg['prevent_multiposting']  = true;           // replace "reply" with "edit last msg" if user (not admin or mod) is last topic poster

// Actions log
$bb_cfg['log_days_keep'] = 90;

// Users
$bb_cfg['user_not_activated_days_keep'] = 7;       // "not activated" == "not finished registration"
$bb_cfg['user_not_active_days_keep']    = 180;     // inactive users but only with no posts

// GroupCP
$bb_cfg['groupcp_members_per_page']     = 300;

// Ads
$bb_cfg['show_ads'] = false;
$bb_cfg['show_ads_users'] = array(
#	user_id => 'name',
	2      => 'admin',
);

// block_type => [block_id => block_desc]
$bb_cfg['ad_blocks'] = array(
	'trans' => array(
		100 => 'сквозная сверху',
	),
	'index' => array(
		200 => 'главная, под новостями',
	),
);

// Misc
define('BOT_UID', -746);

define('LOADAVG',   function_exists('get_loadavg') ? get_loadavg() : 0);
define('MEM_USAGE', function_exists('memory_get_usage'));

$bb_cfg['mem_on_start'] = (MEM_USAGE) ? memory_get_usage() : 0;

$bb_cfg['translate_dates'] = true;                 // in displaying time
$bb_cfg['use_word_censor'] = false;

$bb_cfg['last_visit_date_format'] = 'd-M H:i';
$bb_cfg['last_post_date_format']  = 'd-M-y H:i';

$bb_cfg['allow_change'] = array(
	'language'   => false,
	'dateformat' => false,
);

$banned_user_agents = array(
// Download Master
#	'download',
#	'master',
// Others
#	'wget',
);

$bb_cfg['porno_forums']   = '';                    // (string) 1,2,3..
$bb_cfg['porno_forums_screenshots_topic_id'] = 52267;
$bb_cfg['trash_forum_id'] = 0;                     // (int)    27

$bb_cfg['first_logon_redirect_url']    = 'viewtopic.php?t=262';
$bb_cfg['faq_url']                     = 'faq.php';
$bb_cfg['terms_and_conditions_url']    = 'edit_for_config_php';

$bb_cfg['user_agreement_url']          = 'misc.php?show=user_agreement';
$bb_cfg['copyright_holders_url']       = 'misc.php?show=copyright_holders';
$bb_cfg['advert_url']                  = 'misc.php?show=advert';

$bb_cfg['user_agreement_html_path']    = SITE_DIR . 'misc/html/user_agreement.html';
$bb_cfg['copyright_holders_html_path'] = SITE_DIR . 'misc/html/copyright_holders.html';
$bb_cfg['advert_html_path']            = SITE_DIR . 'misc/html/advert.html';

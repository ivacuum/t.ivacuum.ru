<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2010
*/

if( !defined('IN_PHPBB') )
{
	die(basename(__FILE__));
}

if( !defined('BB_SCRIPT') )
{
	define('BB_SCRIPT', 'undefined');
}

//
// Cron functions
//
function cron_release_deadlock()
{
	if( file_exists(CRON_RUNNING) )
	{
		if( TIMENOW - filemtime(CRON_RUNNING) > 2400 )
		{
			cron_enable_board();
			cron_release_file_lock();
		}
	}
}

function cron_release_file_lock()
{
	$lock_released = @rename(CRON_RUNNING, CRON_ALLOWED);
	cron_touch_lock_file(CRON_ALLOWED);
}

function cron_touch_lock_file($lock_file)
{
	file_write(make_rand_str(20), $lock_file, 0, true, true);
}

function cron_enable_board()
{
	@rename(BB_DISABLED, BB_ENABLED);
}

function cron_disable_board ()
{
	@rename(BB_ENABLED, BB_DISABLED);
}

// Define some basic configuration arrays
unset($stopwords, $synonyms_match, $synonyms_replace);
$userdata = $theme = $images = $lang = $nav_links = $bf = $attach_config = array();
$gen_simple_header = false;
$user = null;

// Obtain and encode user IP
$client_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
$user_ip = encode_ip($client_ip);
define('CLIENT_IP', $client_ip);
define('USER_IP',   $user_ip);

// Cookie params
$c = $bb_cfg['cookie_prefix'];
define('COOKIE_DATA',  $c . 'data');
define('COOKIE_FORUM', $c . 'f');
define('COOKIE_LOAD',  $c . 'isl');
define('COOKIE_MARK',  $c . 'mark_read');
define('COOKIE_TEST',  $c . 'test');
define('COOKIE_TOPIC', $c . 't');
unset($c);

define('DELETED', -1);

// User Levels <- Do not change the values of USER or ADMIN
define('USER',         0);
define('ADMIN',        1);
define('MOD',          2);
define('GROUP_MEMBER', 20);

// User related
define('USER_ACTIVATION_NONE',  0);
define('USER_ACTIVATION_SELF',  1);
define('USER_ACTIVATION_ADMIN', 2);

define('USER_AVATAR_NONE',    0);
define('USER_AVATAR_UPLOAD',  1);
define('USER_AVATAR_REMOTE',  2);
define('USER_AVATAR_GALLERY', 3);

// Group settings
define('GROUP_OPEN',   0);
define('GROUP_CLOSED', 1);
define('GROUP_HIDDEN', 2);

// Forum state
define('FORUM_UNLOCKED', 0);
define('FORUM_LOCKED',   1);

// Topic status
define('TOPIC_UNLOCKED',          0);
define('TOPIC_LOCKED',            1);
define('TOPIC_MOVED',             2);

define('TOPIC_WATCH_NOTIFIED',    1);
define('TOPIC_WATCH_UN_NOTIFIED', 0);

// Topic types
define('POST_NORMAL',          0);
define('POST_STICKY',          1);
define('POST_ANNOUNCE',        2);
define('POST_GLOBAL_ANNOUNCE', 3);

// Search types
define('SEARCH_TYPE_POST',     0);
define('SEARCH_TYPE_TRACKER',  1);

// Error codes
define('GENERAL_MESSAGE',      200);
define('GENERAL_ERROR',        202);
define('CRITICAL_MESSAGE',     203);
define('CRITICAL_ERROR',       204);

define('E_AJAX_GENERAL_ERROR', 1000);
define('E_AJAX_NEED_LOGIN',    1001);

// Private messaging
define('PRIVMSGS_READ_MAIL',      0);
define('PRIVMSGS_NEW_MAIL',       1);
define('PRIVMSGS_SENT_MAIL',      2);
define('PRIVMSGS_SAVED_IN_MAIL',  3);
define('PRIVMSGS_SAVED_OUT_MAIL', 4);
define('PRIVMSGS_UNREAD_MAIL',    5);

// URL PARAMETERS (hardcoding allowed)
define('POST_CAT_URL',    'c');
define('POST_FORUM_URL',  'f');
define('POST_GROUPS_URL', 'g');
define('POST_POST_URL',   'p');
define('POST_TOPIC_URL',  't');
define('POST_USERS_URL',  'u');

// Download Modes
define('INLINE_LINK',   1);
define('PHYSICAL_LINK', 2);

// Categories
define('NONE_CAT',   0);
define('IMAGE_CAT',  1);
define('STREAM_CAT', 2);
define('SWF_CAT',    3);

// Misc
define('MEGABYTE',              1024);
define('ADMIN_MAX_ATTACHMENTS', 50);
define('THUMB_DIR',             'thumbs');
define('MODE_THUMBNAIL',        1);

// Forum Extension Group Permissions
define('GPERM_ALL', 0); // ALL FORUMS

// Quota Types
define('QUOTA_UPLOAD_LIMIT', 1);
define('QUOTA_PM_LIMIT',     2);

// Torrents
define('TOR_STATUS_NORMAL', 0);
define('TOR_STATUS_FROZEN', 1);

// Table names
$b = $buffer_prefix;
$t = $table_prefix;

define('BUF_TOPIC_VIEW_TABLE',       $b . 'topic_view');
define('BUF_LAST_SEEDER_TABLE',      $b . 'last_seeder');

define('ADS_TABLE',                  $t . 'ads');
define('ATTACH_CONFIG_TABLE',        $t . 'attachments_config');
define('ATTACHMENTS_DESC_TABLE',     $t . 'attachments_desc');
define('ATTACHMENTS_THANKS_TABLE',   $t . 'attachments_thanks');
define('ATTACHMENTS_TABLE',          $t . 'attachments');
define('AUTH_ACCESS_SNAP_TABLE',     $t . 'auth_access_snap');
define('AUTH_ACCESS_TABLE',          $t . 'auth_access');
define('BANLIST_TABLE',              $t . 'banlist');
define('BT_DLSTATUS_MAIN_TABLE',     $t . 'bt_dlstatus_main');
define('BT_DLSTATUS_NEW_TABLE',      $t . 'bt_dlstatus_new');
define('BT_DLSTATUS_SNAP_TABLE',     $t . 'bt_dlstatus_snap');
define('BT_DLSTATUS_TABLE',          $t . 'bt_dlstatus_main');   // main + new
define('BT_LAST_TORSTAT_TABLE',      $t . 'bt_last_torstat');
define('BT_LAST_USERSTAT_TABLE',     $t . 'bt_last_userstat');
define('BT_TORHELP_TABLE',           $t . 'bt_torhelp');
define('BT_TORSTAT_TABLE',           $t . 'bt_torstat');
define('BT_TRACKER_SNAP_TABLE',      $t . 'bt_tracker_snap');
define('BT_USER_SETTINGS_TABLE',     $t . 'bt_user_settings');
define('CATEGORIES_TABLE',           $t . 'categories');
define('CONFIG_TABLE',               $t . 'config');
define('CONFIRM_TABLE',              $t . 'confirm');
define('CRON_TABLE',                 $t . 'cron');
define('DATASTORE_TABLE',            $t . 'datastore');
define('DISALLOW_TABLE',             $t . 'disallow');
define('EXTENSION_GROUPS_TABLE',     $t . 'extension_groups');
define('EXTENSIONS_TABLE',           $t . 'extensions');
define('FLAG_TABLE',                 $t . 'flags');
define('FORUMS_TABLE',               $t . 'forums');
define('GROUPS_TABLE',               $t . 'groups');
define('LOG_TABLE',                  $t . 'log');
define('POSTS_SEARCH_TABLE',         $t . 'posts_search');
define('POSTS_TABLE',                $t . 'posts');
define('POSTS_TEXT_TABLE',           $t . 'posts_text');
define('POSTS_HTML_TABLE',           $t . 'posts_html');
define('PRIVMSGS_TABLE',             $t . 'privmsgs');
define('PRIVMSGS_TEXT_TABLE',        $t . 'privmsgs_text');
define('QUOTA_LIMITS_TABLE',         $t . 'quota_limits');
define('QUOTA_TABLE',                $t . 'attach_quota');
define('RANKS_TABLE',                $t . 'ranks');
define('SEARCH_REBUILD_TABLE',       $t . 'search_rebuild');
define('SEARCH_TABLE',               $t . 'search_results');
define('SESSIONS_TABLE',             $t . 'sessions');
define('SMILIES_TABLE',              $t . 'smilies');
define('TOPIC_TPL_TABLE',            $t . 'topic_templates');
define('TOPICS_TABLE',               $t . 'topics');
define('TOPICS_WATCH_TABLE',         $t . 'topics_watch');
define('USER_GROUP_TABLE',           $t . 'user_group');
define('USERS_TABLE',                $t . 'users');
define('VOTE_DESC_TABLE',            $t . 'vote_desc');
define('VOTE_RESULTS_TABLE',         $t . 'vote_results');
define('VOTE_USERS_TABLE',           $t . 'vote_voters');
define('WORDS_TABLE',                $t . 'words');
unset($t, $b);

define('TORRENT_EXT', 'torrent');

define('TOPIC_DL_TYPE_NORMAL', 0);
define('TOPIC_DL_TYPE_DL',     1);

define('SHOW_PEERS_COUNT', 1);
define('SHOW_PEERS_NAMES', 2);
define('SHOW_PEERS_FULL',  3);

define('SEARCH_ID_LENGTH', 12);
define('SID_LENGTH',       20);
define('LOGIN_KEY_LENGTH', 12);

define('PAGE_HEADER', SITE_DIR . 'includes/page_header.php');
define('PAGE_FOOTER', SITE_DIR . 'includes/page_footer.php');

define('CAT_URL',      'index.php?'      . 'c=');
define('DOWNLOAD_URL', 'download.php?'  . 'id=');
define('FORUM_URL',    'viewforum.php?' . 'f=');
define('GROUP_URL',    'groupcp.php?'   . 'g=');
define('LOGIN_URL',    'login.php?'     . 'redirect=');
define('MODCP_URL',    'modcp.php?'     . 'f=');
define('PM_URL',       'privmsg.php?'   . 'mode=post&amp;u=');
define('POST_URL',     'viewtopic.php?' . 'p=');
define('PROFILE_URL',  'profile.php?'   . 'mode=viewprofile&amp;u=');
define('TOPIC_URL',    'viewtopic.php?' . 't=');

define('HTML_SELECT_MAX_LENGTH', 60);
define('HTML_WBR_LENGTH', 12);

define('HTML_CHECKED',  ' checked="checked" ');
define('HTML_DISABLED', ' disabled="disabled" ');
define('HTML_READONLY', ' readonly="readonly" ');
define('HTML_SELECTED', ' selected="selected" ');

define('HTML_SF_SPACER', '&nbsp;|-&nbsp;');

// $GPC
define('KEY_NAME', 0);   // position in $GPC['xxx']
define('DEF_VAL',  1);
define('GPC_TYPE', 2);

define('GET',     1);
define('POST',    2);
define('COOKIE',  3);
define('REQUEST', 4);
define('CHBOX',   5);
define('SELECT',  6);

$constants = array(
	/* Права доступа */
	'AUTH_LIST_ALL' => 0,

	/* Права доступа к форумам */
	'AUTH_REG'   => 1,
	'AUTH_ACL'   => 2,
	'AUTH_ADMIN' => 5,

	/* Значения полей прав доступа */
	'AUTH_ALL'        => 0,
	'AUTH_VIEW'       => 1,
	'AUTH_READ'       => 2,
	'AUTH_MOD'        => 3,
	'AUTH_POST'       => 4,
	'AUTH_REPLY'      => 5,
	'AUTH_EDIT'       => 6,
	'AUTH_DELETE'     => 7,
	'AUTH_STICKY'     => 8,
	'AUTH_ANNOUNCE'   => 9,
	'AUTH_VOTE'       => 10,
	'AUTH_POLLCREATE' => 11,
	'AUTH_ATTACH'     => 12,
	'AUTH_DOWNLOAD'   => 13,

	/* Когда определяем права пользователя, принимать во внимание: */
	'UG_PERM_BOTH'       => 1, /* и права пользователя и права группы */
	'UG_PERM_USER_ONLY'  => 2, /* только права пользователя */
	'UG_PERM_GROUP_ONLY' => 3 /* только права группы */
);

foreach( $constants as $key => $value )
{
	define($key, $value);
}

function bb_exit($output = '')
{
	if( $output )
	{
		echo $output;
	}

	exit;
}

function prn_r($var, $title = '', $print = true)
{
	$r = '<pre>'. (($title) ? "<b>$title</b>\n\n" : '') . htmlspecialchars(print_r($var, true)) .'</pre>';

	if( $print )
	{
		echo $r;
	}

	return $r;
}

function prn()
{
	if( !DBG_USER )
	{
		return;
	}

	foreach( func_get_args() as $var )
	{
		prn_r($var);
	}
}

function vdump($var, $title = '')
{
	echo '<pre>'. (($title) ? "<b>$title</b>\n\n" : '');
	var_dump($var);
	echo '</pre>';
}

function htmlCHR($txt, $replace_space = false)
{
	return ($replace_space) ? str_replace(' ', '&nbsp;', htmlspecialchars($txt, ENT_QUOTES)) : htmlspecialchars($txt, ENT_QUOTES);
}

function make_url($path)
{
	global $bb_cfg;

	$server_protocol = ($bb_cfg['cookie_secure']) ? 'https://' : 'http://';
	$server_port = ($bb_cfg['server_port'] != 80) ? ':'. $bb_cfg['server_port'] : '';
	$path = preg_replace('#^\/?(.*?)\/?$#', '\1', $path);

	return $server_protocol . $bb_cfg['server_name'] . $server_port . $bb_cfg['script_path'] . $path;
}

require(SITE_DIR . 'includes/functions.php');
require(SITE_DIR . 'includes/sessions.php');
require(SITE_DIR . 'includes/template.php');
require(SITE_DIR . 'includes/db/mysql.php');

// if (DBG_USER) require(INC_DIR .'functions_dev.php');

// Make the database connection.
$db = new sql_db(array(
	'dbms'        => $dbms,
	'dbhost'      => $dbhost,
	'dbname'      => $dbname,
	'dbuser'      => $dbuser,
	'dbpasswd'    => $dbpasswd,
	'charset'     => $dbcharset,
	'collation'   => $dbcollation,
	'persist'     => $pconnect,
));
unset($dbpasswd);

// Setup forum wide options
$board_config =& $bb_cfg;

$bb_cfg = array_merge(bb_get_config(CONFIG_TABLE), $bb_cfg);

$bb_cfg['cookie_name']      = $bb_cfg['cookie_prefix'];
$bb_cfg['board_dateformat'] = $bb_cfg['default_dateformat'];
$bb_cfg['board_lang']       = $bb_cfg['default_lang'];

$user = new user_common();
$userdata =& $user->data;

$html = new html_common();
$log_action = new log_action();

// Initialize Datastore
switch( $bb_cfg['datastore_type'] )
{
	case 'memcached':

		$datastore = new datastore_memcached($bb_cfg['memcached']);

	break;
	default:

		$datastore = new datastore_mysql();
}

// !!! Temporarily (??) 'cat_forums' always enqueued
$datastore->enqueue(array(
	'cat_forums')
);

// Cron
if( empty($_POST) && !defined('IN_ADMIN') && !defined('IN_AJAX') && !defined('IN_SERVICE') && !file_exists(CRON_RUNNING) && $bb_cfg['cron_enabled'] )
{
	if( TIMENOW - $bb_cfg['cron_last_check'] > $bb_cfg['cron_check_interval'] )
	{
		// Update cron_last_check
		bb_update_config(array('cron_last_check' => TIMENOW));

		require(SITE_DIR . 'config/cron_cfg.php');

		// bb_log(date('H:i:s - ') . getmypid() .' -x-- DB-LOCK try'. LOG_LF, CRON_LOG_DIR .'cron_check');

		if( $db->get_lock('cron', 1) )
		{
			// bb_log(date('H:i:s - ') . getmypid() .' --x- DB-LOCK OBTAINED !!!!!!!!!!!!!!!!!'. LOG_LF, CRON_LOG_DIR .'cron_check');

			sleep(2);
			require(SITE_DIR . 'includes/cron/cron_init.php');
			$db->release_lock('cron');
		}
	}
}

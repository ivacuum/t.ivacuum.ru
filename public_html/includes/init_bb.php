<?php

if( !defined('SITE_DIR') )
{
	exit;
}

if( !defined('BB_SCRIPT') )
{
	define('BB_SCRIPT', 'undefined');
}

// Exit if board is disabled via ON/OFF trigger
if (!defined('IN_ADMIN') && !defined('IN_AJAX'))
{
	if (file_exists(BB_DISABLED))
	{
		cron_release_deadlock();

		require SITE_DIR . 'templates/board_disabled_exit.php';
	}
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

function cron_disable_board()
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

function send_page($contents)
{
	return $contents;
}

// Start output buffering
if (!defined('IN_AJAX'))
{
	ob_start('send_page');
}

// Config options
define('TPL_LIMIT_LOAD_EXIT', SITE_DIR . 'templates/limit_load_exit.php');

define('COOKIE_EXPIRED', TIMENOW - 31536000);
define('COOKIE_PERSIST', TIMENOW + 31536000);

define('PAGE_HEADER', SITE_DIR . 'includes/page_header.php');
define('PAGE_FOOTER', SITE_DIR . 'includes/page_footer.php');

define('USER_AGENT', strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''));
define('UA_OPERA',   strpos(USER_AGENT, 'pera'));
define('UA_IE',      strpos(USER_AGENT, 'msie'));

define('HTML_WBR_TAG', (UA_OPERA || strpos(USER_AGENT, 'afari')) ? '<wbr></wbr>&#8203;' : '<wbr>');

require SITE_DIR . 'includes/constants.php';

function bb_setcookie($name, $val, $lifetime = COOKIE_PERSIST, $httponly = false)
{
	global $bb_cfg;

	return setcookie($name, $val, $lifetime, $bb_cfg['cookie_path'], $bb_cfg['cookie_domain'], $bb_cfg['cookie_secure'], $httponly);
}

if( !empty($banned_user_agents) )
{
	foreach( $banned_user_agents as $agent )
	{
		if( strstr(USER_AGENT, $agent) )
		{
			$filename = 'Skachivajte fajly brauzerom (скачивайте файлы браузером)';
			$output = '@';

			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename="'. $filename .'"');

			die($output);
		}
	}
}

// Functions
function send_no_cache_headers()
{
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '. gmdate('D, d M Y H:i:s'). ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
}

function bb_exit($output = '')
{
	global $app;

	if ($output)
	{
		echo $output;
	}

	exit;
}

// Exit if server overloaded
if (!(defined('IN_PROFILE') || defined('IN_LOGIN') || defined('IN_ADMIN') || defined('IN_AJAX')))
{
	if( $bb_cfg['max_srv_load'] && empty($_POST['message']) && !empty($_COOKIE[COOKIE_LOAD]) && LOADAVG )
	{
		if( LOADAVG > $bb_cfg['max_srv_load'] && (TIMENOW - $_COOKIE[COOKIE_LOAD]) > $bb_cfg['user_session_duration'] )
		{
			require SITE_DIR . 'templates/limit_load_exit.php';
		}
	}
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

function htmlCHR($txt, $replace_space = false)
{
    $txt ??= '';

	return ($replace_space)
        ? str_replace(' ', '&nbsp;', htmlspecialchars($txt, ENT_QUOTES))
        : htmlspecialchars($txt, ENT_QUOTES);
}

function make_url($path)
{
	global $bb_cfg;

	$server_protocol = ($bb_cfg['cookie_secure']) ? 'https://' : 'http://';
	$server_port = ($bb_cfg['server_port'] != 80) ? ':'. $bb_cfg['server_port'] : '';
	$path = preg_replace('#^\/?(.*?)\/?$#', '\1', $path);

	return $server_protocol . $bb_cfg['server_name'] . $server_port . $bb_cfg['script_path'] . $path;
}

require SITE_DIR . 'includes/functions.php';
require SITE_DIR . 'includes/sessions.php';
require SITE_DIR . 'includes/template.php';
require SITE_DIR . 'includes/db/mysqli.php';

// Make the database connection.
$db = new sql_db(array(
	'dbms'      => $dbms,
	'dbhost'    => $dbhost,
	'dbname'    => $dbname,
	'dbuser'    => $dbuser,
	'dbpasswd'  => $dbpasswd,
	'charset'   => $dbcharset,
	'collation' => $dbcollation,
	'persist'   => $pconnect,
));
unset($dbpasswd);

// Setup forum wide options
$board_config =& $bb_cfg;

$bb_cfg = array_merge(bb_get_config('bb_config'), $bb_cfg);

$bb_cfg['cookie_name']      = $bb_cfg['cookie_prefix'];
$bb_cfg['board_dateformat'] = $bb_cfg['default_dateformat'];
$bb_cfg['board_lang']       = $bb_cfg['default_lang'];

$user = new user_common();
$userdata =& $user->data;

$html = new html_common();
$log_action = new log_action();

// $ads = new ads_common();

// Initialize Datastore
switch( $bb_cfg['datastore_type'] )
{
	case 'memcached':

		$datastore = new datastore_memcached($bb_cfg['memcached']);

	break;
	case 'redis':

		$datastore = new datastore_redis($bb_cfg['redis']);

	break;
	case 'apc':

		$datastore = new datastore_apc($bb_cfg['apc_prefix']);

	break;
	case 'filecache':

		$datastore = new datastore_file(SITE_DIR . 'cache/filecache/datastore/');

	break;
	default:

		$datastore = new datastore_common();
}

// !!! Temporarily (??) 'cat_forums' always enqueued
$datastore->enqueue(array(
	'cat_forums')
);

// Cron
$dl_link_css = array(
	DL_STATUS_RELEASER => 'genmed',
	DL_STATUS_WILL     => 'dlWill',
	DL_STATUS_DOWN     => 'leechmed',
	DL_STATUS_COMPLETE => 'seedmed',
	DL_STATUS_CANCEL   => 'dlCancel',
);

$dl_status_css = array(
	DL_STATUS_RELEASER => 'genmed',
	DL_STATUS_WILL     => 'dlWill',
	DL_STATUS_DOWN     => 'dlDown',
	DL_STATUS_COMPLETE => 'dlComplete',
	DL_STATUS_CANCEL   => 'dlCancel',
);

// Show 'Board is disabled' message if needed.
if( $bb_cfg['board_disable'] && !defined('IN_ADMIN') && !defined('IN_LOGIN') )
{
	message_die(GENERAL_MESSAGE, 'Board_disable', 'Information');
}

/**
* Загрузка констант
*/
function load_constants($prefix)
{
	if (!function_exists('apc_fetch'))
	{
		return false;
	}

	return apc_load_constants("{$prefix}_constants");
}

/**
* Установка констант
*/
function set_constants($prefix, $constants)
{
	if (!function_exists('apc_fetch'))
	{
		foreach ($constants as $key => $value)
		{
			define($key, $value);
		}

		return;
	}

	apc_define_constants("{$prefix}_constants", $constants);
}

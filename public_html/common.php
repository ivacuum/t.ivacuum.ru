<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

define('SITE_DIR', __DIR__ . '/');
require(SITE_DIR . 'includes/profiler.php');
$profiler = new profiler();

define('TIMESTART', microtime(true));
define('TIMENOW', time());

$s_provider  = isset($_SERVER['HTTP_PROVIDER']) ? $_SERVER['HTTP_PROVIDER'] : 'internet';
$static_path = $s_provider == 'local' ? '//0.ivacuum.org' : '//ivacuum.org';

// Get initial config
require(SITE_DIR . 'cfg.php');

// Debug options
define('DBG_USER', (isset($_COOKIE[COOKIE_DBG]) || DEBUG === true));

// Board/Tracker shared constants and functions
define('BT_TORRENTS_TABLE', $table_prefix . 'bt_torrents');
define('BT_TRACKER_TABLE',  $table_prefix . 'bt_tracker');
define('BT_USERS_TABLE',    $table_prefix . 'bt_users');

define('BT_AUTH_KEY_LENGTH', 10);

define('DL_STATUS_RELEASER', -1);
define('DL_STATUS_DOWN',      0);
define('DL_STATUS_COMPLETE',  1);
define('DL_STATUS_CANCEL',    3);
define('DL_STATUS_WILL',      4);

define('ANONYMOUS', -1);

// Cache
define('PEER_HASH_PREFIX',  'peer_');
define('PEERS_LIST_PREFIX', 'peers_list_');

class cache_common
{
	var $used = false;

	/**
	* Returns value of variable
	*/
	function get($name)
	{
		return false;
	}

	/**
	* Store value of variable
	*/
	function set($name, $value, $ttl = 0)
	{
		return false;
	}

	/**
	* Remove variable
	*/
	function rm($name)
	{
		return false;
	}
}

class cache_memcached extends cache_common
{
	var $used = true;

	var $cfg;
	var $memcache;
	var $prefix = '';

	function cache_memcached($cfg)
	{
		if( !$this->is_installed() )
		{
			die('Error: Memcached extension not installed');
		}

		if( $cfg['prefix'] )
		{
			$this->prefix = $cfg['prefix'] . '_';
		}

		$this->cfg = $cfg;
		$this->memcache = new Memcache;
		$this->memcache->pconnect($this->cfg['host'], $this->cfg['port']);
	}

	function get($name)
	{
		return $this->memcache->get($this->prefix . $name);
	}

	function set($name, $value, $ttl = 2592000)
	{
		if( !$this->memcache->replace($this->prefix . $name, $value, false, $ttl) )
		{
			return $this->memcache->set($this->prefix . $name, $value, false, $ttl);
		}

		return true;
	}

	function rm($name)
	{
		return $this->memcache->delete($this->prefix . $name, 0);
	}

	function is_installed()
	{
		return class_exists('Memcache');
	}
}

switch( $bb_cfg['bb_cache_type'] )
{
	case 'memcached':

		$bb_cache = new cache_memcached($bb_cfg['memcached']);

	break;
	default:

		$bb_cache = new cache_common();
}

function bb_log($msg, $file_name)
{
	if( is_array($msg) )
	{
		$msg = join(LOG_LF, $msg);
	}

	$file_name .= (LOG_EXT) ? '.'. LOG_EXT : '';

	return file_write($msg, SITE_DIR . 'log/' . $file_name);
}

function file_write($str, $file, $max_size = LOG_MAX_SIZE, $lock = true, $replace_content = false)
{
	$bytes_written = false;

	if( $max_size && @filesize($file) >= $max_size )
	{
		$old_name = $file; $ext = '';

		if( preg_match('#^(.+)(\.[^\\/]+)$#', $file, $matches) )
		{
			$old_name = $matches[1];
			$ext      = $matches[2];
		}

		$new_name = $old_name .'_[old]_'. date('Y-m-d_H-i-s_') . getmypid() . $ext;

		clearstatcache();

		if( @file_exists($file) && @filesize($file) >= $max_size && !@file_exists($new_name) )
		{
			@rename($file, $new_name);
		}
	}

	if( !$fp = @fopen($file, 'ab') )
	{
		if( $dir_created = bb_mkdir(dirname($file)) )
		{
			$fp = @fopen($file, 'ab');
		}
	}

	if( $fp )
	{
		if( $lock )
		{
			@flock($fp, LOCK_EX);
		}

		if( $replace_content )
		{
			@ftruncate($fp, 0);
			@fseek($fp, 0, SEEK_SET);
		}

		$bytes_written = @fwrite($fp, $str);
		@fclose($fp);
	}

	return $bytes_written;
}

function bb_mkdir($path, $mode = 0777)
{
	$old_um = umask(0);
	$dir = mkdir_rec($path, $mode);
	umask($old_um);

	return $dir;
}

function mkdir_rec($path, $mode)
{
	if( is_dir($path) )
	{
		return ($path !== '.' && $path !== '..') ? is_writable($path) : false;
	}
	else
	{
		return (mkdir_rec(dirname($path), $mode)) ? @mkdir($path, $mode) : false;
	}
}

function verify_id($id, $length)
{
	return (preg_match('#^[a-zA-Z0-9]{'. $length .'}$#', $id) && is_string($id));
}

function clean_filename($fname)
{
	static $s = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');

	return str_replace($s, '_', $fname);
}

function encode_ip($ip)
{
	$d = explode('.', $ip);

	return sprintf('%02x%02x%02x%02x', $d[0], $d[1], $d[2], $d[3]);
}

function decode_ip($ip)
{
	return long2ip("0x{$ip}");
}

function verify_ip($ip)
{
	return preg_match('#^(\d{1,3}\.){3}\d{1,3}$#', $ip);
}

function str_compact($str)
{
	return preg_replace('#\s+#', ' ', trim($str));
}

function make_rand_str($length = 10)
{
	return substr(str_shuffle(preg_replace('#[^0-9a-zA-Z]#', '', crypt(uniqid(mt_rand(), true)))), 0, $length);
}

// bencode: based on OpenTracker [http://whitsoftdev.com/opentracker]
function bencode($var)
{
	if( is_string($var) )
	{
		return strlen($var) .':'. $var;
	}
	elseif( is_int($var) )
	{
		return 'i' . $var . 'e';
	}
	elseif( is_float($var) )
	{
		return 'i' . sprintf('%.0f', $var) . 'e';
	}
	elseif( is_array($var) )
	{
		if( count($var) == 0 )
		{
			return 'de';
		}
		else
		{
			$assoc = false;

			foreach( $var as $key => $val )
			{
				if( !is_int($key) )
				{
					$assoc = true;
					break;
				}
			}

			if( $assoc )
			{
				ksort($var, SORT_REGULAR);
				$ret = 'd';

				foreach( $var as $key => $val )
				{
					$ret .= bencode($key) . bencode($val);
				}
				return $ret . 'e';
			}
			else
			{
				$ret = 'l';

				foreach( $var as $val )
				{
					$ret .= bencode($val);
				}
				return $ret . 'e';
			}
		}
	}
	else
	{
		trigger_error('bencode error: wrong data type', E_USER_ERROR);
	}
}

function array_deep(&$var, $fn, $one_dimensional = false, $array_only = false)
{
	if( is_array($var) )
	{
		foreach( $var as $k => $v )
		{
			if( is_array($v) )
			{
				if( $one_dimensional )
				{
					unset($var[$k]);
				}
				elseif( $array_only )
				{
					$var[$k] = $fn($v);
				}
				else
				{
					array_deep($var[$k], $fn);
				}
			}
			elseif( !$array_only )
			{
				$var[$k] = $fn($v);
			}
		}
	}
	elseif( !$array_only )
	{
		$var = $fn($var);
	}
}

function hide_bb_path($path)
{
	return substr(str_replace(SITE_DIR, '', $path), 1);
}

function get_loadavg()
{
	if( is_callable('sys_getloadavg') )
	{
		$loadavg = join(' ', sys_getloadavg());
	}
	elseif( strpos(PHP_OS, 'Linux') !== false )
	{
		$loadavg = @file_get_contents('/proc/loadavg');
	}

	return !empty($loadavg) ? $loadavg : 0;
}

function ver_compare($version1, $operator, $version2)
{
	return version_compare($version1, $version2, $operator);
}

// Board init
require(SITE_DIR . 'includes/init_bb.php');

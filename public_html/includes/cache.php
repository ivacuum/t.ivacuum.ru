<?php

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

<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

if( !defined('SQL_DEBUG') )
{
	die(basename(__FILE__) .": 'SQL_DEBUG' undefined");
}

define('SQL_LAYER',          'mysql');
define('DEFAULT_QUERY_TYPE', 'buffered');   //  buffered, unbuffered

class db_mysqli
{
	var $cfg            = array();
	var $link           = null;
	var $result         = null;
	var $selected_db    = null;

	var $pconnect       = false;
	var $locked         = false;
	var $locks          = array();

	var $num_queries    = 0;
	var $sql_starttime  = 0;
	var $sql_inittime   = 0;
	var $sql_timetotal  = 0;
	var $cur_query_time = 0;

	var $log_file       = 'sql_queries';
	var $log_counter    = 0;

	var $dbg            = array();
	var $dbg_id         = 0;
	var $dbg_enabled    = false;
	var $cur_query      = null;

	var $do_explain     = false;
	var $explain_hold   = '';
	var $explain_out    = '';

	var $shutdown       = array();

	/**
	* Глобальные переменные класса
	*/
	var $connect_id;
	var $query_result;
	var $total_queries = 0;
	var $transaction = false;
	var $transactions = 0;

	var $user = '';
	var $server = '';
	var $database = '';

	/**
	* Initialize connection
	*/
	function init()
	{
		return 'init called';

		// Connect to server
		// $this->link = $this->connect();

		// Select database
		// $this->selected_db = $this->select_db();

		// Set charset
		/*if ($this->cfg['charset'] && !$this->sql_query("SET NAMES {$this->cfg['charset']}"))
		{
			die("Could not set charset {$this->cfg['charset']}");
		}*/

		$this->num_queries = 0;
		$this->sql_inittime = $this->sql_timetotal;
	}

	/**
	* Установка подключения к БД
	*/
	function connect($sqlserver, $sqluser, $sqlpassword, $database, $port = false, $socket = '')
	{
		$this->server = $sqlserver;
		$this->user = $sqluser;
		$this->database = $database;
		$port = ( $port === false ) ? null : $port;

		$this->connect_id = mysqli_connect($this->server, $this->user, $sqlpassword, $this->database, $port, $socket);

		return ( $this->connect_id && $this->database ) ? $this->connect_id : $this->error();
	}

	/**
	* Закрытие текущего подключения
	*/
	function close()
	{
		if( !$this->connect_id )
		{
			return false;
		}

		if( $this->transaction )
		{
			do
			{
				$this->transaction('commit');
			}
			while( $this->transaction );
		}

		$this->unlock();

		if( !empty($this->locks) )
		{
			foreach( $this->locks as $name => $void )
			{
				$this->release_lock($name);
			}
		}

		$this->exec_shutdown_queries();

		return ( $this->connect_id ) ? mysqli_close($this->connect_id) : false;
	}

	/**
	* Выполнение запроса к БД
	*/
	function query($query = '')
	{
		global $profiler;

		if( is_array($query) )
		{
			$query = $this->build_sql($query);
		}

		if( $query )
		{
			$this->query_result = false;
			$this->total_queries++;
			$start_time = microtime(true);

			if( $this->query_result === false )
			{
				if( ( $this->query_result = mysqli_query($this->connect_id, $query) ) === false )
				{
					$this->error($query);
				}
			}

			$profiler->log_query($query, microtime(true) - $start_time);
		}
		else
		{
			return false;
		}

		return ( $this->query_result ) ? $this->query_result : false;
	}

	/**
	* Base query method
	*/
	function sql_query($query)
	{
		return $this->query($query);
	}

	/**
	* Выборка
	*/
	function fetchrow($query_id = false)
	{
		if( $query_id === false )
		{
			$query_id = $this->query_result;
		}

		return ( $query_id !== false ) ? mysqli_fetch_assoc($query_id) : false;
	}

	/**
	* Fetch current row
	*/
	function sql_fetchrow($result)
	{
		return $this->fetchrow($result);
	}

	/**
	* Заносим полученные данные в цифровой массив
	*
	* @param string $field Поле, по которому создавать массив
	*/
	function fetchall($query_id = false, $field = false)
	{
		if( $query_id === false )
		{
			$query_id = $this->query_result;
		}

		if( $query_id !== false )
		{
			$result = array();

			while( $row = $this->fetchrow($query_id) )
			{
				if( $field !== false )
				{
					$result[$row[$field]] = $row;
				}
				else
				{
					$result[] = $row;
				}
			}

			return $result;
		}

		return false;
	}

	/**
	* Транзакции
	*/
	function transaction($status = 'begin')
	{
		switch( $status )
		{
			case 'begin':

				if( $this->transaction )
				{
					$this->transactions++;
					return true;
				}

				if( !mysqli_autocommit($this->connect_id, false) )
				{
					$this->error();
				}

				$this->transaction = true;

			break;
			case 'commit':

				if( $this->transaction && $this->transactions )
				{
					$this->transactions--;
					return true;
				}

				if( !$this->transaction )
				{
					return false;
				}

				$result = mysqli_commit($this->connect_id);
				mysqli_autocommit($this->connect_id, true);

				if( !$result )
				{
					$this->error();
				}

				$this->transaction = false;
				$this->transactions = 0;

			break;
			case 'rollback':

				mysqli_rollback($this->connect_id);
				mysqli_autocommit($this->connect_id, true);
				$this->transaction = false;
				$this->transactions = 0;

			break;
		}
	}

	/**
	* Освобождение памяти
	*/
	function freeresult($query_id = false)
	{
		if( $query_id === false )
		{
			$query_id = $this->query_result;
		}

		return mysqli_free_result($query_id);
	}

	/**
	* Сверяем тим переменной и её значение,
	* строки также экранируем
	*/
	function check_value($value)
	{
		if( is_null($value) )
		{
			return 'NULL';
		}
		elseif( is_string($value) )
		{
			return "'" . $this->escape_string($value) . "'";
		}
		else
		{
			return ( is_bool($value) ) ? intval($value) : $value;
		}
	}

	/**
	* Экранирование LIKE запроса
	*/
	function like_expression($expression)
	{
		$expression = str_replace(array('_', '%'), array("\_", "\%"), $expression);
		$expression = str_replace(array(chr(0) . "\_", chr(0) . "\%"), array('_', '%'), $expression);

		return 'LIKE \'%' . $this->escape_string($expression) . '%\'';
	}

	/**
	* Затронутые поля
	*/
	function affected_rows()
	{
		return ( $this->connect_id ) ? mysqli_affected_rows($this->connect_id) : false;
	}

	/**
	* Return number of affected rows
	*/
	function sql_affectedrows()
	{
		return $this->affected_rows();
	}

	/**
	* ID последнего добавленного элемента
	*/
	function insert_id()
	{
		return ( $this->connect_id ) ? mysqli_insert_id($this->connect_id) : false;
	}

	function in_set($field, $array, $negate = false, $allow_empty_set = false)
	{
		if( !sizeof($array) )
		{
			if( !$allow_empty_set )
			{
				// Print the backtrace to help identifying the location of the problematic code
				$this->error('No values specified for SQL IN comparison');
			}
			else
			{
				// NOT IN () actually means everything so use a tautology
				if( $negate )
				{
					return '1=1';
				}
				// IN () actually means nothing so use a contradiction
				else
				{
					return '1=0';
				}
			}
		}

		if( !is_array($array) )
		{
			$array = array($array);
		}

		if( sizeof($array) == 1 )
		{
			@reset($array);
			$var = current($array);

			return $field . ($negate ? ' <> ' : ' = ') . $this->check_value($var);
		}
		else
		{
			return $field . ($negate ? ' NOT IN ' : ' IN ') . '(' . implode(', ', array_map(array($this, 'check_value'), $array)) . ')';
		}
	}

	/**
	* SQL ошибки передаём нашему обработчику
	*/
	function error($sql = '')
	{
		global $custom_error_style, $user;

		$code = ( $this->connect_id ) ? mysqli_errno($this->connect_id) : mysqli_connect_errno();
		$custom_error_style = true;
		$message = ( $this->connect_id ) ? mysqli_error($this->connect_id) : mysqli_connect_error();

		$message = '<br /><b style="color: red;">Ошибка SQL</b>:<br /><blockquote>Код ошибки: <b>' . $code . '</b>.<br />Текст ошибки: «<b>' . $message . '</b>».<br />';
		$message .= ( $sql ) ? '<br /><b>SQL запрос:</b> ' . htmlspecialchars($sql) . '<br />' : '';
		$message .= '</blockquote>';

		/**
		* Стандартные настройки сервера не позволяют выводить
		* сообщение об ошибке длиной более 1024 символов
		*/
		if( strlen($message) >= 1024 )
		{
			global $message_long_error;

			$message_long_error = $message;

			trigger_error(false, E_USER_ERROR);
		}

		trigger_error($message, E_USER_ERROR);

		return $result;
	}

	/**
	* Return number of rows
	*/
	function sql_numrows()
	{
		return ( $this->query_result ) ? mysqli_num_rows($this->query_result) : false;
	}

	/**
	* Alias of fetchrow()
	*/
	function fetch_next($result)
	{
		return $this->fetchrow($result);
	}

	/**
	* Fetch row WRAPPER (with error handling)
	*/
	function fetch_row($query)
	{
		return $this->fetchrow($this->query($query));
	}

	/**
	* Fetch all rows
	*/
	function sql_fetchrowset($result)
	{
		return $this->fetchall($result);
	}

	/**
	* Fetch all rows WRAPPER (with error handling)
	*/
	function fetch_rowset($query)
	{
		return $this->fetchall($this->query($query));
	}

	/**
	* Get last inserted id after insert statement
	*/
	function sql_nextid()
	{
		return $this->insert_id();
	}

	/**
	* Free sql result
	*/
	function sql_freeresult($result = false)
	{
		return $this->freeresult($result);
	}

	/**
	* Escape data used in sql query
	*/
	function escape($v, $check_type = false, $dont_escape = false)
	{
		if( $dont_escape )
		{
			return $v;
		}

		if( !$check_type )
		{
			return $this->escape_string($v);
		}

		switch( true )
		{
			case is_string($v): return "'". $this->escape_string($v) ."'";
			case is_int   ($v): return "$v";
			case is_bool  ($v): return ($v) ? '1' : '0';
			case is_float ($v): return "'$v'";
			case is_null  ($v): return 'NULL';
		}

		// if $v has unsuitable type
		trigger_error(__FUNCTION__ .' - wrong params', E_USER_ERROR);
	}

	/**
	* Экранируем символы
	*/
	function escape_string($message)
	{
		return mysqli_real_escape_string($this->connect_id, $message);
	}

	/**
	* Build SQL statement from array (based on same method from phpBB3, idea from Ikonboard)
	*
	* Possible $query_type values: INSERT, INSERT_SELECT, MULTI_INSERT, UPDATE, SELECT
	*/
	function build_array($query_type, $input_ary, $data_already_escaped = false, $check_data_type_in_escape = true)
	{
		$fields = $values = $ary = $query = array();
		$dont_escape = $data_already_escaped;
		$check_type = $check_data_type_in_escape;

		if( empty($input_ary) || !is_array($input_ary) )
		{
			trigger_error(__FUNCTION__ .' - wrong params: $input_ary', E_USER_ERROR);
		}

		if( $query_type == 'INSERT' )
		{
			foreach( $input_ary as $field => $val )
			{
				$fields[] = $field;
				$values[] = $this->check_value($val);
			}

			$fields = join(', ', $fields);
			$values = join(', ', $values);
			$query = "($fields)\nVALUES\n($values)";
		}
		elseif( $query_type == 'INSERT_SELECT' )
		{
			foreach( $input_ary as $field => $val )
			{
				$fields[] = $field;
				$values[] = $this->check_value($val);
			}

			$fields = join(', ', $fields);
			$values = join(', ', $values);
			$query = "($fields)\nSELECT\n$values";
		}
		elseif( $query_type == 'MULTI_INSERT' )
		{
			foreach( $input_ary as $id => $sql_ary )
			{
				foreach( $sql_ary as $field => $val )
				{
					$values[] = $this->check_value($val);
				}

				$ary[] = '('. join(', ', $values) .')';
				$values = array();
			}
			$fields = join(', ', array_keys($input_ary[0]));
			$values = join(",\n", $ary);
			$query = "($fields)\nVALUES\n$values";
		}
		elseif( $query_type == 'SELECT' || $query_type == 'UPDATE' )
		{
			foreach( $input_ary as $field => $val )
			{
				$ary[] = "$field = ". $this->check_value($val);
			}

			$glue = ($query_type == 'SELECT') ? "\nAND " : ",\n";
			$query = join($glue, $ary);
		}

		if( !$query )
		{
			bb_die('<pre><b>'. __FUNCTION__ ."</b>: Wrong params for <b>$query_type</b> query type\n\n\$input_ary:\n\n". htmlCHR(print_r($input_ary, true)) .'</pre>');
		}

		return "\n". $query ."\n";
	}

	function get_empty_sql_array()
	{
		return array(
			'SELECT'         => array(),
			'select_options' => array(),
			'FROM'           => array(),
			'INNER JOIN'     => array(),
			'LEFT JOIN'      => array(),
			'WHERE'          => array(),
			'GROUP BY'       => array(),
			'HAVING'         => array(),
			'ORDER BY'       => array(),
			'LIMIT'          => array(),
		);
	}

	function build_sql($sql_ary)
	{
		$sql = '';
		array_deep($sql_ary, 'array_unique', false, true);

		foreach( $sql_ary as $clause => $ary )
		{
			switch( $clause )
			{
				case 'SELECT':
					$sql .= ($ary) ? ' SELECT '. join(' ', $sql_ary['select_options']) .' '. join(', ', $ary) : '';
				break;
				case 'FROM':
					$sql .= ($ary) ? ' FROM '. join(', ', $ary) : '';
				break;
				case 'INNER JOIN':
					$sql .= ($ary) ? ' INNER JOIN '. join(' INNER JOIN ', $ary) : '';
				break;
				case 'LEFT JOIN':
					$sql .= ($ary) ? ' LEFT JOIN '. join(' LEFT JOIN ', $ary) : '';
				break;
				case 'WHERE':
					$sql .= ($ary) ? ' WHERE '. join(' AND ', $ary) : '';
				break;
				case 'GROUP BY':
					$sql .= ($ary) ? ' GROUP BY '. join(', ', $ary) : '';
				break;
				case 'HAVING':
					$sql .= ($ary) ? ' HAVING '. join(' AND ', $ary) : '';
				break;
				case 'ORDER BY':
					$sql .= ($ary) ? ' ORDER BY '. join(', ', $ary) : '';
				break;
				case 'LIMIT':
					$sql .= ($ary) ? ' LIMIT '. join(', ', $ary) : '';
				break;
			}
		}

		return trim($sql);
	}

	/**
	* Return sql error array
	*/
	function sql_error()
	{
		return $this->error();
	}

	/**
	* Add shutdown query
	*/
	function add_shutdown_query($sql)
	{
		$this->shutdown['__sql'][] = $sql;
	}

	/**
	* Exec shutdown queries
	*/
	function exec_shutdown_queries()
	{
		if( empty($this->shutdown) )
		{
			return;
		}

		// post_html
		if( !empty($this->shutdown['post_html']) )
		{
			$post_html_sql = $this->build_array('MULTI_INSERT', $this->shutdown['post_html']);
			$this->query('REPLACE INTO ' . POSTS_HTML_TABLE . ' ' . $post_html_sql);
		}
		// other
		if( !empty($this->shutdown['__sql']) )
		{
			foreach( $this->shutdown['__sql'] as $sql )
			{
				$this->query($sql);
			}
		}
	}

	/**
	* Return the number of fields from a query
	*/
	function sql_numfields($result)
	{
		return ( $this->query_result ) ? mysqli_num_fields($this->query_result) : false;
	}

	/**
	* Return the name of the field index
	*/
	function sql_fieldname($offset, $result)
	{
		//(is_resource($result)) ? mysql_field_name($result, $offset) : false;
		return false;
	}

	/**
	* Return the type of the field
	*/
	function sql_fieldtype($offset, $result = false)
	{
		// (is_resource($result)) ? mysql_field_type($result, $offset) : false;
		return false;
	}

	/**
	* Lock tables
	*/
	function lock($tables, $lock_type = 'WRITE')
	{
		if( $this->pconnect )
		{
#			return true;
		}

		$tables_sql = array();

		foreach( (array) $tables as $table_name )
		{
			$tables_sql[] = "$table_name $lock_type";
		}
		if( $tables_sql = join(', ', $tables_sql) )
		{
			$this->locked = $this->sql_query("LOCK TABLES $tables_sql");
		}

		return $this->locked;
	}

	/**
	* Unlock tables
	*/
	function unlock()
	{
		if( $this->locked && $this->query('UNLOCK TABLES') )
		{
			$this->locked = false;
		}

		return !$this->locked;
	}

	/**
	* Obtain user level lock
	*/
	function get_lock($name, $timeout = 0)
	{
		$lock_name = $this->get_lock_name($name);
		$timeout   = (int) $timeout;
		$row = $this->fetch_row("SELECT GET_LOCK('$lock_name', $timeout) AS lock_result");

		if( $row['lock_result'] )
		{
			$this->locks[$name] = true;
		}

		return $row['lock_result'];
	}

	/**
	* Obtain user level lock status
	*/
	function release_lock($name)
	{
		$lock_name = $this->get_lock_name($name);
		$row = $this->fetch_row("SELECT RELEASE_LOCK('$lock_name') AS lock_result");

		if( $row['lock_result'] )
		{
			unset($this->locks[$name]);
		}

		return $row['lock_result'];
	}

	/**
	* Release user level lock
	*/
	function is_free_lock($name)
	{
		$lock_name = $this->get_lock_name($name);
		$row = $this->fetch_row("SELECT IS_FREE_LOCK('$lock_name') AS lock_result");
		return $row['lock_result'];
	}

	/**
	* Make per db unique lock name
	*/
	function get_lock_name($name)
	{
		return "{$this->selected_db}_{$name}";
	}
}

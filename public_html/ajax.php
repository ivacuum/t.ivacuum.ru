<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_AJAX', true);
$ajax = new ajax_common();
require 'common.php';

$ajax->init();

// Handle "board disabled via ON/OFF trigger"
if( file_exists(BB_DISABLED) )
{
	$ajax->ajax_die($bb_cfg['board_disabled_msg']);
}

// Load actions required modules
switch( $ajax->action )
{
	case 'chat_message':

		require SITE_DIR . 'includes/functions_post.php';
		require SITE_DIR . 'includes/bbcode.php';

	break;
	case 'view_post':

		require SITE_DIR . 'includes/bbcode.php';

	break;
}

// position in $ajax->valid_actions['xxx']
define('AJAX_AUTH', 0);  //  'guest', 'user', 'mod', 'admin'

$user->session_start();
$ajax->exec();

//
// Ajax
//
class ajax_common
{
	var $request  = array();
	var $response = array();

	var $valid_actions = array(
	//   ACTION NAME             AJAX_AUTH
		'chat_ban'            => array('mod'),
		'chat_delete'         => array('mod'),
		'chat_message'        => array('user'),
		'edit_user_profile'   => array('admin'),
		'get_last_login'      => array('guest'),
		'get_peers_details'   => array('user'),
		'get_torrent_dl_list' => array('user'),
		'set_dl_status'       => array('user'),
		'thanks'              => array('user'),
		'thumb_up'            => array('user'),
		'view_post'           => array('guest'),
		'view_today_visitors' => array('user'),
		'view_top_releasers'  => array('guest'),
		'view_top_share'      => array('guest')
	);

	var $action = null;

	/**
	*  Constructor
	*/
	function ajax_common()
	{
		ob_start(array(&$this, 'ob_handler'));
		header('Content-Type: text/plain');
	}

	/**
	*  Perform action
	*/
	function exec()
	{
		global $lang;

		// Exit if we already have errors
		if( !empty($this->response['error_code']) )
		{
			$this->send();
		}

		// Check that requested action is valid
		$action = $this->action;

		if( !$action || !is_string($action) )
		{
			$this->ajax_die('no action specified');
		}
		elseif( !$action_params =& $this->valid_actions[$action] )
		{
			$this->ajax_die('invalid action: '. $action);
		}

		// Auth check
		switch( $action_params[AJAX_AUTH] )
		{
			// GUEST
			case 'guest':
			break;

			// USER
			case 'user':

				if( IS_GUEST )
				{
					$this->ajax_die($lang['Need_to_login_first']);
				}

			break;

			// MOD
			case 'mod':

				if( !(IS_MOD || IS_ADMIN) )
				{
					$this->ajax_die($lang['Only_for_mod']);
				}

				$this->check_admin_session();

			break;

			// ADMIN
			case 'admin':

				if( !IS_ADMIN )
				{
					$this->ajax_die($lang['Only_for_admin']);
				}

				$this->check_admin_session();

			break;
			default:

				trigger_error("invalid auth type for $action", E_USER_ERROR);
		}

		// Run action
		$this->$action();

		// Send output
		$this->send();
	}

	/**
	*  Exit on error
	*/
	function ajax_die($error_msg, $error_code = E_AJAX_GENERAL_ERROR)
	{
		$this->response['error_code'] = $error_code;
		$this->response['error_msg']  = $error_msg;

		$this->send();
	}

	/**
	*  Initialization
	*/
	function init()
	{
		if( $_SERVER['REMOTE_ADDR'] == '192.168.1.1' )
		{
			$this->request = $_REQUEST;
		}
		else
		{
			$this->request = $_POST;
		}

		$this->action =& $this->request['action'];
	}

	/**
	*  Send data
	*/
	function send()
	{
		$this->response['action'] = $this->action;

		// sending output will be handled by $this->ob_handler()
		bb_exit();
		// exit();
	}

	/**
	*  OB Handler
	*/
	function ob_handler($contents)
	{
		return json_encode($this->response);
	}

	/**
	*  Admin session
	*/
	function check_admin_session()
	{
		global $user;

		if( !$user->data['session_admin'] )
		{
			if( empty($this->request['user_password']) )
			{
				$this->prompt_for_password();
			}
			else
			{
				$login_args = array(
					'login_username' => $user->data['username'],
					'login_password' => $_POST['user_password'],
				);

				if( !$user->login($login_args, true) )
				{
					$this->ajax_die('Wrong password');
				}
			}
		}
	}

	/**
	*  Prompt for password
	*/
	function prompt_for_password()
	{
		$this->response['prompt_password'] = 1;
		$this->send();
	}

	/**
	* Бан от чата
	*/
	function chat_ban()
	{
		global $db;

		$chat_id = isset($this->request['id']) ? (int) $this->request['id'] : 0;

		if( !$chat_id )
		{
			$this->ajax_die('id not found');
		}

		if( !IS_MOD && !IS_ADMIN )
		{
			$this->ajax_die('not a moder');
		}

		$sql = '
			SELECT
				*
			FROM
				bb_chat
			WHERE
				chat_id = ' . $db->check_value($chat_id);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if( !$row )
		{
			$this->ajax_die('id not found');
		}

		$sql = '
			UPDATE
				bb_users
			SET
				user_chat = 0
			WHERE
				user_id = ' . $db->check_value($row['user_id']);
		$db->sql_query($sql);

		$this->response['id'] = $chat_id;
		$this->response['html'] = 'OK';
	}

	/**
	* Цензура
	*/
	function chat_delete()
	{
		global $db, $userdata;

		$chat_id = isset($this->request['id']) ? (int) $this->request['id'] : 0;

		if( !$chat_id )
		{
			$this->ajax_die('id not found');
		}

		if( !IS_MOD && !IS_ADMIN )
		{
			$this->ajax_die('not a moder');
		}

		$sql = '
			UPDATE
				bb_chat
			SET
				chat_censored = 1,
				censor_user_id = ' . $db->check_value($userdata['user_id']) . '
			WHERE
				chat_id = ' . $db->check_value($chat_id);
		$db->sql_query($sql);

		$this->response['id'] = $chat_id;
		$this->response['html'] = 'OK';
	}

	/**
	* Сообщения в чате
	*/
	function chat_message()
	{
		global $bb_cfg, $db, $static_path, $userdata;

		$message = isset($this->request['message']) ? (string) $this->request['message'] : '';

		if( !$userdata['user_chat'] )
		{
			$this->response['html'] = '';
			return;
		}

		if( $message )
		{
			$sql = '
				SELECT
					MAX(chat_time) AS last_post_time
				FROM
					bb_chat
				WHERE
					user_id = ' . $db->check_value($userdata['user_id']);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if( $row['last_post_time'] > 0 && time() - $row['last_post_time'] < 2 )
			{
				$this->ajax_die('flood control');
			}

			$sql_ary = array(
				'user_id'   => $userdata['user_id'],
				'chat_time' => time(),
				'chat_ip'   => $_SERVER['REMOTE_ADDR'],
				'chat_text' => smilies_pass(make_clickable(htmlspecialchars($message))),
			);

			$sql = 'INSERT INTO bb_chat ' . $db->build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}

		$html = '';

		$sql = '
			SELECT
				c.*,
				u.username,
				u.user_level
			FROM
				bb_chat c
			LEFT JOIN
				bb_users u ON (u.user_id = c.user_id)
			WHERE
				u.user_chat = 1
			ORDER BY
				c.chat_time DESC
			LIMIT
				0, 50';
		$result = $db->sql_query($sql);

		while( $row = $db->sql_fetchrow($result) )
		{
			switch( $row['user_level'] )
			{
				case ADMIN: $color = 'colorAdmin'; break;
				case MOD: $color = 'colorMod'; break;
				case GROUP_MEMBER: $color = 'colorGroup'; break;
				default: $color = 'colorNick';
			}

			$html_ban = IS_MOD || IS_ADMIN ? '&nbsp;<a href="#" class="chat-ban" data-id="' . $row['chat_id'] . '" style="display: none;" title="Забанить"><img src="' . $static_path . '/i/_/skull.png" alt=""></a>' : '';
			$html_delete = (IS_MOD || IS_ADMIN) && !$row['chat_censored'] ? '<a href="#" class="chat-delete" data-id="' . $row['chat_id'] . '" style="display: none;" title="Скрыть сообщение"><img src="' . $static_path . '/i/_/cross_script.png" alt=""></a>' : '';
			$html_profile = '<a href="/profile.php?mode=viewprofile&u=' . $row['user_id'] . '"><img src="' . $static_path . '/i/_/card_address.png" alt="" style="display: none;" class="chat-profile"></a>';

			$text = $row['chat_censored'] ? '<i>сообщение скрыто</i>' : $row['chat_text'];
			$html .= sprintf('<div class="chat-comment" data-id="%d"><span class="%s chat-nickbuffer">[%s] <span class="chat-nick">%s</span></span>: <span class="chat-text">%s</span> %s %s</div>', $row['chat_id'], $color, bb_date($row['chat_time'], 'H:i:s'), $row['username'], $text, $html_delete, $html_ban);
		}

		$db->sql_freeresult($result);

		$this->response['html'] = $html;
	}

	/**
	*  Edit user profile
	*/
	function edit_user_profile()
	{
		global $db, $bb_cfg, $lang;

		$user_id = intval($this->request['user_id']);

		if( !$user_id || !$profiledata = get_userdata($user_id) )
		{
			$this->ajax_die('invalid user_id');
		}
		if( !$field = (string) $this->request['field'] )
		{
			$this->ajax_die('invalid profile field');
		}

		$table = 'bb_users';
		$value = (string) $this->request['value'];

		switch( $field )
		{
			case 'user_regdate':
			case 'user_lastvisit':

				$tz = TIMENOW + (3600 * $bb_cfg['board_timezone']);

				if( ( $value = strtotime($value, $tz) ) < $bb_cfg['board_startdate'] || $value > TIMENOW )
				{
					$this->ajax_die('invalid date: '. $this->request['value']);
				}

				$this->response['new_value'] = bb_date($value);

			break;
			case 'ignore_srv_load':

				$value = ($this->request['value']) ? 0 : 1;
				$this->response['new_value'] = ( $profiledata['user_level'] != USER || $value ) ? $lang['NO'] : $lang['YES'];

			break;
			case 'u_up_total':
			case 'u_down_total':
			case 'u_up_release':
			case 'u_up_bonus':

				if( !IS_SUPER_ADMIN )
				{
					$this->ajax_die($lang['Only_for_super_admin']);
				}

				$table = 'bb_bt_users';
				$value = (float) str_replace(',', '.', $this->request['value']);

				foreach( array('КБ' => 1, 'МБ' => 2, 'ГБ' => 3, 'ТБ' => 4) as $s => $m )
				{
					if( strpos($this->request['value'], $s) !== false )
					{
						$value *= pow(1024, $m);
						break;
					}
				}

				$value = sprintf('%.0f', $value);
				$this->response['new_value'] = humn_size($value, null, null, ' ');

				if( !$btu = get_bt_userdata($user_id) )
				{
					require SITE_DIR . 'includes/functions_torrent.php';
					generate_passkey($user_id, true);
					$btu = get_bt_userdata($user_id);
				}

				$btu[$field] = $value;
				$this->response['update_ids']['u_ratio'] = (string) get_bt_ratio($btu);

			break;
			case 'can_leech':

				if( !IS_SUPER_ADMIN )
				{
					$this->ajax_die($lang['Only_for_super_admin']);
				}

				$table = 'bb_bt_users';
				$value = (int) $this->request['value'];

				$this->response['new_value'] = (string) $value;
				$this->response['update_ids']['can_leech'] = (string) $value;

				$value_sql = $db->escape($value, true);
				$sql = '
					UPDATE
						' . $table . '
					SET
						key_regtime = ' . TIMENOW . ',
						' . $field . ' = ' . $value_sql . '
					WHERE
						user_id = ' . $user_id . '
					LIMIT
						1';

				$db->query($sql);
				$this->response['edit_id'] = $this->request['edit_id'];
				$this->send();

			break;
			default:

				$this->ajax_die("invalid profile field: $field");
		}

		$value_sql = $db->escape($value, true);
		$db->query("UPDATE $table SET $field = $value_sql WHERE user_id = $user_id LIMIT 1");
		$this->response['query'] = "UPDATE $table SET $field = $value_sql WHERE user_id = " . $user_id . " LIMIT 1";

		$this->response['edit_id'] = $this->request['edit_id'];
	}

	/**
	* Логин, с которого заходили последний раз на этом IP
	*/
	function get_last_login()
	{
		if( substr($_SERVER['REMOTE_ADDR'], 0, 3) != '10.' )
		{
			$this->response['html'] = '';
			$this->send();
		}

		global $db;

		$sql = '
			SELECT
				username
			FROM
				bb_users
			WHERE
				user_ip = "' . $_SERVER['REMOTE_ADDR'] . '"
			ORDER BY
				user_session_time DESC
			LIMIT
				0, 1';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$this->response['html'] = $row['username'];
	}

	/**
	* Подробная статистика пиров
	* TODO: overflow
	*/
	function get_peers_details()
	{
		global $db, $template, $userdata;

		$topic_id = (int) $this->request['topic_id'];

		/* Подключение шаблонизатора */
		setup_style();

		$sql = '
			SELECT
				t.user_id,
				t.ip,
				t.port,
				t.uploaded,
				t.downloaded,
				t.remain,
				t.seeder,
				t.releaser,
				t.speed_up,
				t.speed_down,
				t.user_agent,
				t.update_time,
				tor.size,
				u.username
			FROM
				bb_bt_tracker t
			LEFT JOIN
				bb_users u ON u.user_id = t.user_id
			LEFT JOIN
				bb_bt_torrents tor ON t.topic_id = tor.topic_id
			WHERE
				t.topic_id = ' . $topic_id . '
			LIMIT
				0, 300';
		$result = $db->sql_query($sql);

		$leechers = 0;
		$seeders = 0;
		$who = '';

		while( $row = $db->sql_fetchrow($result) )
		{
			if( $row['seeder'] )
			{
				$link_class = 'seedmed';
				$seeders++;
				$who = 'seeders';
			}
			else
			{
				$link_class = 'leechmed';
				$leechers++;
				$who = 'leechers';
			}

			$bgr_class = ( !($$who % 2) ) ? 'prow1' : 'prow2';
			$row_bgr   = sprintf(' class="%s" onmouseover="this.className=\'prow3\';" onmouseout="this.className=\'%s\';"', $bgr_class, $bgr_class);
			$username  = sprintf('<a href="%s" class="%s">%s</a>%s', append_sid('profile.php?mode=viewprofile&u=' . $row['user_id']), $link_class, wbr($row['username']), (($row['releaser']) ? '<span class="seed">&nbsp;<b><sup>&reg;</sup></b>&nbsp;</span>' : ''));

			$template->assign_block_vars($who, array(
				'CLIENT'         => $row['user_agent'],
				'COMPLETE'       => ( !$row['seeder'] ) ? floor(($row['size'] - $row['remain']) * 100 / $row['size']) : 0,
				'DOWN_TOTAL'     => ( $row['downloaded'] ) ? humn_size($row['downloaded']) : '&mdash;',
				'INT_DOWN_TOTAL' => $row['downloaded'],
				'INT_SPEED_DOWN' => $row['speed_down'],
				'INT_SPEED_UP'   => $row['speed_up'],
				'INT_UP_TOTAL'   => $row['uploaded'],
				'IP'             => $row['ip'],
				'PORT'           => $row['port'],
				'RATIO'          => ( $row['uploaded'] ) ? str_replace(',', '.', round($row['uploaded'] / $row['size'], 1)) : 0,
				'ROW_BGR'        => $row_bgr,
				'SPEED_DOWN'     => ( $row['speed_down'] ) ? sprintf('%s/с', humn_size($row['speed_down'], 0, 'КБ')) : '&mdash;',
				'SPEED_UP'       => ( $row['speed_up'] ) ? sprintf('%s/с', humn_size($row['speed_up'], 0, 'КБ')) : '&mdash;',
				'UP_TOTAL'       => ( $row['uploaded'] ) ? humn_size($row['uploaded']) : '&mdash;',
				'UPDATE_TIME'    => ( $row['update_time'] ) ? 'последнее обновление: ' . bb_date($row['update_time'], 'd-M-y H:i') : 'клиент остановил торрент',
				'USERNAME'       => ( $row['update_time'] ) ? $username : '<s>' . $username . '</s>')
			);
		}

		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'LEECHERS'           => $leechers,
			'SEEDERS'            => $seeders,
			'SHOW_MODER_OPTIONS' => IS_ADMIN || IS_MOD)
		);

		ob_start();
		$template->set_filename('body', 'ajax/get_peers_details.html');
		$template->pparse('body');
		$template_php = ob_get_clean();

		$this->response['update_ids']['full_details'] = $template_php;
	}

	/**
	* Кто скачал/будет качать раздачу
	* TODO: overflow
	*/
	function get_torrent_dl_list()
	{
		global $db, $dl_status_css, $lang, $template;

		$topic_id = (int) $this->request['topic_id'];

		$div_style_normal   = 'padding: 0px;';
		$div_style_overflow = 'padding: 6px; height: 120px; overflow: auto; border: 1px inset;';

		/* Подключение шаблонизатора */
		setup_style();

		$sql = '
			SELECT
				d.user_status,
				d.user_id,
				DATE_FORMAT(d.last_modified_dlstatus, "%Y-%m-%d") AS last_modified_dlstatus,
				u.username
			FROM
				bb_bt_dlstatus_main d
			LEFT JOIN
				bb_users u ON d.user_id = u.user_id
			WHERE
				d.topic_id = ' . $topic_id . '
			AND
				d.user_status != ' . DL_STATUS_RELEASER . '
			LIMIT
				0, 300';
		$result = $db->sql_query($sql);
		$ary = array();

		while( $row = $db->sql_fetchrow($result) )
		{
			if( !isset($ary[$row['user_status']]) )
			{
				$ary[$row['user_status']] = array(
					'count' => 0,
					'users' => ''
				);
			}

			$ary[$row['user_status']]['users'] .= sprintf('<nobr><a class="%s" href="%s" title="%s">%s</a></nobr>, ', $dl_status_css[$row['user_status']], append_sid('profile.php?mode=viewprofile&u='. $row['user_id']), $row['last_modified_dlstatus'], $row['username']);
			$ary[$row['user_status']]['count']++;
		}

		$db->sql_freeresult($result);

		foreach( $dl_status_css as $i => $desc )
		{
			if( isset($ary[$i]) )
			{
				$template->assign_block_vars('row', array(
					'NAME'      => $lang[$desc .'_2'],
					'USERS'     => '<span class="' . $desc . '">' . substr($ary[$i]['users'], 0, -2) . '</span>',
					'COUNT'     => $ary[$i]['count'],
					'DIV_STYLE' => ( $ary[$i]['count'] > 300 ) ? $div_style_overflow : $div_style_normal)
				);
			}
		}

		if( empty($ary) )
		{
			$template->assign_vars(array(
				'EMPTY_DL_LIST' => true)
			);
		}

		ob_start();
		$template->set_filename('body', 'ajax/get_torrent_dl_list.html');
		$template->pparse('body');
		$template_php = ob_get_clean();

		$this->response['html'] = $template_php;
	}

	/**
	* Управление списком закачек
	*
	* - добавление в будущие закачки
	* - удаление из списка закачек
	*/
	function set_dl_status()
	{
		global $db, $static_path, $userdata;

		$dl_status = $this->request['dl_status'];
		$topic_id  = (int) $this->request['topic_id'];

		if( $dl_status == 'will' )
		{
			/* Будущие закачки */
			$sql_ary = array(
				'user_id'     => (int) $userdata['user_id'],
				'topic_id'    => (int) $topic_id,
				'user_status' => (int) DL_STATUS_WILL
			);

			$sql = 'INSERT INTO bb_bt_dlstatus_main ' . $db->build_array('INSERT', $sql_ary) . ' ON DUPLICATE KEY UPDATE user_status = values(user_status)';
			$db->sql_query($sql);

			$this->response['html'] = '<img src="' . $static_path . '/i/_/tick.png" alt="" style="vertical-align: text-top;"> Раздача добавлена в список будущих закачек';
		}
		elseif( $dl_status == 'delete' )
		{
			/* Удаление из списка закачек */
			$sql = '
				DELETE
				FROM
					bb_bt_dlstatus_main
				WHERE
					user_id = ' . $userdata['user_id'] . '
				AND
					topic_id = ' . $topic_id;
			$db->sql_query($sql);

			$this->response['html'] = '<img src="' . $static_path . '/i/_/tick.png" alt="" style="vertical-align: text-top;"> Раздача удалена из списка закачек';
		}
		else
		{
			$this->ajax_die('there is no such method');
		}
	}

	/**
	* Спасибо
	*/
	function thanks()
	{
		global $db;

		$attach_id = (int) $this->request['attach_id'];
		$mode      = $this->request['mode'];

		if( $mode == 'list' )
		{
			/**
			* Список поблагодаривших
			*/
			$sql = '
				SELECT
					u.user_id,
					u.username
				FROM
					bb_attachments_thanks a,
					bb_users u
				WHERE
					a.user_id = u.user_id
				AND
					a.attach_id = ' . $attach_id;
			$result = $db->sql_query($sql);
			$s = '';

			while( $row = $db->sql_fetchrow($result) )
			{
				$s .= '<a href="profile.php?mode=viewprofile&u=' . $row['user_id'] . '">' . $row['username'] . '</a>, ';
			}

			$db->sql_freeresult($result);

			$this->response['update_ids']['show_thanks'] = substr($s, 0, -2);
		}
		else
		{
			/**
			* Сказать "Спасибо"
			*/
			global $userdata;

			$sql = '
				SELECT
					poster_id
				FROM
					bb_bt_torrents
				WHERE
					attach_id = ' . $attach_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if( $row && $row['poster_id'] != $userdata['user_id'] )
			{
				$sql = 'INSERT IGNORE INTO bb_attachments_thanks (attach_id, user_id) VALUES (' . $attach_id . ', ' . $userdata['user_id'] . ')';
				$db->sql_query($sql);
			}

			$sql = '
				SELECT
					COUNT(*) as total_thanks
				FROM
					bb_attachments_thanks
				WHERE
					attach_id = ' . $attach_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if( !$result )
			{
				$this->ajax_die('torrent not found');
			}

			$sql = '
				UPDATE
					bb_attachments_desc
				SET
					thanks = ' . $row['total_thanks'] . '
				WHERE
					attach_id = ' . $attach_id;
			$db->sql_query($sql);

			$this->response['update_ids']['thanks_count'] = $row['total_thanks'];
		}
	}

	/**
	* Спасибо
	*/
	function thumb_up()
	{
		return $this->thanks();
	}

	/**
	*  View post
	*/
	function view_post()
	{
		global $user, $db, $lang;

		$post_id = (int) $this->request['post_id'];

		$sql = "
			SELECT
				p.*,
				pt.post_subject, pt.post_text, pt.bbcode_uid,
				f.auth_read
			FROM       bb_posts p
			INNER JOIN bb_posts_text pt ON(pt.post_id = p.post_id)
			INNER JOIN bb_forums f  ON(f.forum_id = p.forum_id)
			WHERE
			  p.post_id = $post_id
			LIMIT 1
		";

		if( !$post_data = $db->fetch_row($sql) )
		{
			$this->ajax_die($lang['Topic_post_not_exist']);
		}

		// Auth check
		if( $post_data['auth_read'] == AUTH_REG )
		{
			if( IS_GUEST )
			{
				$this->ajax_die($lang['Need_to_login_first']);
			}
		}
		elseif( $post_data['auth_read'] != AUTH_ALL )
		{
			$is_auth = auth(AUTH_READ, $post_data['forum_id'], $user->data, $post_data);

			if( !$is_auth['auth_read'] )
			{
				$this->ajax_die($lang['Topic_post_not_exist']);
			}
		}

		$this->response['post_id']   = $post_id;
		$this->response['post_html'] = get_parsed_post($post_data);
	}

	/**
	* Список посетителей за день
	*/
	function view_today_visitors()
	{
		global $db;

		$sql = '
			SELECT
				user_id,
				username,
				user_level,
				user_session_time
			FROM
				bb_users
			WHERE
				user_session_time >= ' . strtotime(date('Y-m-d')) . '
			ORDER BY
				username ASC';
		$result = $db->sql_query($sql);
		$online_userlist = '';
		$prev_id = array();
		$users = 0;

		while( $row = $db->sql_fetchrow($result) )
		{
			if( !isset($prev_id[$row['user_id']]) )
			{
				$username = $row['username'];

				if( $row['user_level'] == ADMIN )
				{
					$username = '<b class="colorAdmin">' . $username . '</b>';
				}
				elseif( $row['user_level'] == MOD )
				{
					$username = '<b class="colorMod">' . $username . '</b>';
				}
				elseif( $row['user_level'] == GROUP_MEMBER )
				{
					$username = '<b class="colorGroup">' . $username . '</b>';
				}

				$online_userlist .= ( $online_userlist ) ? ', ' : '';
				$online_userlist .= '<a href="' . append_sid('profile.php?mode=viewprofile&amp;' . POST_USERS_URL . '=' . $row['user_id']) . '">' . $username . '</a>';
				$prev_id[$row['user_id']] = 1;
				$users++;
			}
		}

		$db->sql_freeresult($result);

		$this->response['html'] = ( $users > 0 ) ? '<hr class="dashed" /><p>' . $online_userlist . '.</p>' : '';
	}

	/**
	* Топ релизеров
	*/
	function view_top_releasers()
	{
		global $bb_cfg, $datastore;

		if( !$bb_cfg['t_top_releasers'] )
		{
			$this->ajax_die('method is not available');
		}

		$data = $datastore->get('top_releasers');
		$html = '';

		foreach( $data as $row )
		{
			$html .= '<tr><td align="right" width="48%"><a href="profile.php?mode=viewprofile&u=' . $row['poster_id'] . '"><b>' . ( ( mb_strlen($row['username']) >= 12 ) ? mb_substr($row['username'], 0, 10) . '...' : $row['username'] ) . '</b></a></td><td align="center" width="4%">:</td><td width="48%"><span class="seed">' . humn_size($row['total_size']) . '</span></td></tr>';
		}

		$this->response['html'] = $html;
	}

	/**
	* Топ шар
	*/
	function view_top_share()
	{
		global $bb_cfg, $datastore;

		if( !$bb_cfg['t_top_share'] )
		{
			$this->ajax_die('method is not available');
		}

		$data = $datastore->get('top_share');
		$html = '';

		foreach( $data as $row )
		{
			$html .= '<tr><td align="right" width="48%"><a href="profile.php?mode=viewprofile&u=' . $row['user_id'] . '"><b>' . ( ( mb_strlen($row['username']) >= 12 ) ? mb_substr($row['username'], 0, 10) . '...' : $row['username'] ) . '</b></a></td><td align="center" width="4%">:</td><td width="48%"><span class="seed">' . humn_size($row['total_size']) . '</span></td></tr>';
		}

		$this->response['html'] = $html;
	}
}

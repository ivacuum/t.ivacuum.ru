<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_LOGIN', true);
require 'common.php';

$redirect_url = "/";
$login_error = $login_err_msg = false;

// Requested redirect
if (!empty($_POST['redirect']))
{
	$redirect_url = str_replace('&amp;', '&', htmlspecialchars($_POST['redirect']));
}
else if (preg_match('/^redirect=([a-z0-9\.#\/\?&=\+\-_]+)/si', $_SERVER['QUERY_STRING'], $matches))
{
	$redirect_url = $matches[1];

	if (!strstr($redirect_url, '?') && $first_amp = strpos($redirect_url, '&'))
	{
		$redirect_url[$first_amp] = '?';
	}
}

$redirect_url = str_replace('&admin=1', '', $redirect_url);

if (!$redirect_url || strstr(urldecode($redirect_url), "\n") || strstr(urldecode($redirect_url), "\r") || strstr(urldecode($redirect_url), ';url'))
{
	$redirect_url = "/";
}

// if (!empty($_POST['login']) && !empty($_POST['cookie_test']))
// {
// 	if (empty($_COOKIE[COOKIE_TEST]) || $_COOKIE[COOKIE_TEST] !== $_POST['cookie_test'])
// 	{
// 		$login_error = 'cookie';
// 	}
// }

// Start login
$user->session_start();

$redirect_url = str_replace("&sid={$user->data['session_id']}", '', $redirect_url);
if (isset($_REQUEST['admin']) && !(IS_MOD || IS_ADMIN))
{
	bb_die($lang['Not_admin']);
}

$mod_admin_login = ((IS_MOD || IS_ADMIN) && !$user->data['session_admin']);

if ($login_error)
{
	//!? TODO
}
// login
else if (isset($_POST['login']))
{
	if (!IS_GUEST && !$mod_admin_login)
	{
		redirect("/");
	}

	if ($user->login($_POST, $mod_admin_login))
	{
		if ($bb_cfg['board_disable'] && $user->data['user_level'] != ADMIN)
		{
			redirect("/");
		}

		if ($mod_admin_login)
		{
			redirect($redirect_url);
		}
		else
		{
			$redirect_url = (defined('FIRST_LOGON')) ? $bb_cfg['first_logon_redirect_url'] : $redirect_url;
			redirect($redirect_url);
		}
	}

	$login_err_msg = $lang['Error_login'];
}
// logout
else if (!empty($_GET['logout']))
{
	if (!IS_GUEST)
	{
		$user->session_end();
	}
	redirect("/");
}

// Login page
if (IS_GUEST || $mod_admin_login)
{
	$cookie_test_val = mt_rand();
	bb_setcookie(COOKIE_TEST, $cookie_test_val, COOKIE_SESSION);

	$template->assign_vars(array(
		'USERNAME'         => ($mod_admin_login) ? $user->data['username'] : '',

		'ERR_MSG'          => $login_err_msg,
		'T_ENTER_PASSWORD' => ($mod_admin_login) ? $lang['Admin_reauthenticate'] : $lang['Enter_password'],

		'U_SEND_PASSWORD'  => "profile.php?mode=sendpassword",
		'ADMIN_LOGIN'      => $mod_admin_login,
		'COOKIE_TEST_VAL'  => $cookie_test_val,
		'COOKIES_ERROR'    => ($login_error == 'cookie'),

		'REDIRECT_URL'     => $redirect_url,
	));

	print_page('login.tpl');
}

redirect("/");

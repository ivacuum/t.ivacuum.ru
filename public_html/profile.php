<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_PHPBB', true);
define('IN_PROFILE', true);
require('common.php');

// Start session management
$user->session_start();

$mode = request_var('mode', '');
$sid  = request_var('sid', '');

//
// Set default email variables
//
$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name = ( $script_name != '' ) ? $script_name . '/profile.php' : 'profile.php';
$server_name = trim($board_config['server_name']);
$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

$server_url = $server_protocol . $server_name . $server_port . $script_name;

function gen_rand_string($hash)
{
	$rand_str = make_rand_str(8);

	return ($hash) ? md5($rand_str) : $rand_str;
}

if( $mode )
{
	if ( $mode == 'viewprofile' )
	{
		require(SITE_DIR . 'includes/ucp/usercp_viewprofile.php');
		exit;
	}
	else if ( $mode == 'editprofile' || $mode == 'register' )
	{
		if ( !$userdata['session_logged_in'] && $mode == 'editprofile' )
		{
			login_redirect();
		}

		require(SITE_DIR . 'includes/ucp/usercp_register.php');
		exit;
	}
	else if ( $mode == 'confirm' )
	{
		// Visual Confirmation
		if ( $userdata['session_logged_in'] )
		{
			exit;
		}

		require(SITE_DIR . 'includes/ucp/usercp_confirm.php');
		exit;
	}
	else if ( $mode == 'sendpassword' )
	{
		require(SITE_DIR . 'includes/ucp/usercp_sendpasswd.php');
		exit;
	}
	else if ( $mode == 'activate' )
	{
		require(SITE_DIR . 'includes/ucp/usercp_activate.php');
		exit;
	}
	else if ( $mode == 'email' )
	{
		require(SITE_DIR . 'includes/ucp/usercp_email.php');
		exit;
	}
}

redirect(append_sid('index.php', true));

?>
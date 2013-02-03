<?php

if (!defined('SITE_DIR'))
{
	exit;
}

if ($mode == 'register' && $bb_cfg['new_user_reg_disabled'])
{
	message_die(GENERAL_MESSAGE, $lang['new_user_reg_disabled']);
}

if( isset($_SERVER['HTTP_PROVIDER']) && $_SERVER['HTTP_PROVIDER'] != 'local' && $mode == 'register' )
{
	message_die(GENERAL_MESSAGE, 'Регистрация доступна только из локальной сети Билайн-Калуга.');
}

array_deep($_POST, 'trim');

$unhtml_specialchars_match = array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace = array('>', '<', '"', '&');

$default_user_opt = array(
	'viewemail'        => 0,
	'attachsig'        => 1,
	'hide_porn_forums' => 0,
);

// ---------------------------------------
// Load agreement template since user has not yet
// agreed to registration conditions/coppa
//
function show_coppa()
{
	global $userdata, $template, $lang;

	$template->assign_vars(array(
		'REGISTRATION' => $lang['Registration'],
		"AGREE_OVER_13" => $lang['Agree_over_13'],
		'DO_NOT_AGREE' => $lang['Agree_not'],

		"U_AGREE_OVER13" => append_sid("profile.php?mode=register&amp;agreed=true"),
	));

	print_page('agreement.tpl');
}
//
// ---------------------------------------

$user_actkey = $signature = $username = $cur_password = $confirm_code = $email = '';
$error = FALSE;
$error_msg = '';

$template->assign_vars(array(
	'PAGE_TITLE' => ($mode == 'editprofile') ? $lang['Edit_profile'] : $lang['REGISTER'],
));

if ( $mode == 'register' && !isset($_POST['agreed']) && !isset($_GET['agreed']) )
{
	show_coppa();
}

$coppa = ( empty($_POST['coppa']) && empty($_GET['coppa']) ) ? 0 : TRUE;

//
// Check and initialize some variables if needed
//
if (
	isset($_POST['submit']) ||
	isset($_POST['avatargallery']) ||
	isset($_POST['submitavatar']) ||
	isset($_POST['cancelavatar']) ||
	$mode == 'register' )
{
	include(SITE_DIR . 'includes/functions_validate.php');
	include(SITE_DIR . 'includes/bbcode.php');
	include(SITE_DIR . 'includes/functions_post.php');

	if ( $mode == 'editprofile' )
	{
		$user_id = intval($_POST['user_id']);
		$current_email = trim(htmlspecialchars($_POST['current_email']));
	}

	$strip_var_list = array('email' => 'email', 'icq' => 'icq', 'aim' => 'aim', 'msn' => 'msn', 'yim' => 'yim', 'website' => 'website', 'location' => 'location', 'occupation' => 'occupation', 'interests' => 'interests', 'confirm_code' => 'cfmcd');

	// Strip all tags from data ... may p**s some people off, bah, strip_tags is
	// doing the job but can still break HTML output ... have no choice, have
	// to use htmlspecialchars ... be prepared to be moaned at.
	while( list($var, $param) = @each($strip_var_list) )
	{
		$$var = '';
		if ( !empty($_POST[$param]) )
		{
			$$var = trim(htmlspecialchars($_POST[$param]));
		}
	}

	$username = ( !empty($_POST['username']) ) ? phpbb_clean_username($_POST['username']) : '';

	$trim_var_list = array('cur_password' => 'cur_password', 'new_password' => 'new_password', 'password_confirm' => 'password_confirm', 'signature' => 'signature');

	while( list($var, $param) = @each($trim_var_list) )
	{
		$$var = '';
		if ( !empty($_POST[$param]) )
		{
			$$var = trim($_POST[$param]);
		}
	}

	$signature = str_replace('<br />', "\n", $signature);

	// Run some validation on the optional fields. These are pass-by-ref, so they'll be changed to
	// empty strings if they fail.
	validate_optional_fields($icq, $aim, $msn, $yim, $website, $location, $occupation, $interests, $signature);

	$allowviewonline = ( isset($_POST['hideonline']) ) ? ( ($_POST['hideonline']) ? 0 : TRUE ) : TRUE;
	$notifyreply = ( isset($_POST['notifyreply']) ) ? ( ($_POST['notifyreply']) ? TRUE : 0 ) : 0;
	$notifypm = ( isset($_POST['notifypm']) ) ? ( ($_POST['notifypm']) ? TRUE : 0 ) : TRUE;
	$sid = (isset($_POST['sid'])) ? $_POST['sid'] : 0;

	if ($mode == 'register')
	{
		$viewemail = !empty($_POST['viewemail']) ? 1 : $default_user_opt['viewemail'];
		$attachsig = !empty($_POST['attachsig']) ? 1 : $default_user_opt['attachsig'];
		$hide_porn_forums = $_POST['hide_porn_forums'] = $default_user_opt['hide_porn_forums'];
	}
	else
	{
		$viewemail = !empty($_POST['viewemail']);
		$attachsig = !empty($_POST['attachsig']);
		$hide_porn_forums = !empty($_POST['hide_porn_forums']);
	}

	// language
	$user_lang = $bb_cfg['board_lang'];

	if ($mode == 'editprofile' && !empty($_POST['language']))
	{
		if (preg_match('/^[a-z_]+$/i', $_POST['language']))
		{
			if ($_POST['language'] != $bb_cfg['board_lang'])
			{
				$user_lang = $_POST['language'];
			}
		}
	}

	$user_timezone = ( isset($_POST['timezone']) ) ? str_replace(',', '.', doubleval($_POST['timezone'])) : $bb_cfg['board_timezone'];

// FLAGHACK-start
	$user_flag = (@$_POST['user_flag'] && $_POST['user_flag'] != 'blank.gif') ? $_POST['user_flag'] : '';
// FLAGHACK-end

	// dateformat
	$user_dateformat = '';

	if ($mode == 'editprofile' && !empty($_POST['dateformat']))
	{
		if (preg_match('/^[ a-z\-:]+$/i', $_POST['dateformat']))
		{
			if ($_POST['dateformat'] != $bb_cfg['board_dateformat'])
			{
				$user_dateformat = $_POST['dateformat'];
			}
		}
	}

	$user_avatar_local = ( isset($_POST['avatarselect']) && !empty($_POST['submitavatar']) && $bb_cfg['allow_avatar_local'] ) ? htmlspecialchars($_POST['avatarselect']) : ( ( isset($_POST['avatarlocal'])  ) ? htmlspecialchars($_POST['avatarlocal']) : '' );
	$user_avatar_category = ( isset($_POST['avatarcatname']) && $bb_cfg['allow_avatar_local'] ) ? htmlspecialchars($_POST['avatarcatname']) : '' ;

	$user_avatar_remoteurl = ( !empty($_POST['avatarremoteurl']) ) ? trim(htmlspecialchars($_POST['avatarremoteurl'])) : '';
	$user_avatar_upload = ( !empty($_POST['avatarurl']) ) ? trim($_POST['avatarurl']) : ( ( !empty($_FILES['avatar']) && $_FILES['avatar']['tmp_name'] != "none") ? $_FILES['avatar']['tmp_name'] : '' );
	$user_avatar_name = ( !empty($_FILES['avatar']['name']) ) ? $_FILES['avatar']['name'] : '';
	$user_avatar_size = ( !empty($_FILES['avatar']['size']) ) ? $_FILES['avatar']['size'] : 0;
	$user_avatar_filetype = ( !empty($_FILES['avatar']['type']) ) ? $_FILES['avatar']['type'] : '';

	$user_avatar = ( empty($user_avatar_local) && $mode == 'editprofile' ) ? $userdata['user_avatar'] : '';
	$user_avatar_type = ( empty($user_avatar_local) && $mode == 'editprofile' ) ? $userdata['user_avatar_type'] : '';

	if ( (isset($_POST['avatargallery']) || isset($_POST['submitavatar']) || isset($_POST['cancelavatar'])) && (!isset($_POST['submit'])) )
	{
		$cur_password = htmlspecialchars($cur_password);
		$new_password = htmlspecialchars($new_password);
		$password_confirm = htmlspecialchars($password_confirm);

		$signature = htmlspecialchars($signature);

		if ( !isset($_POST['cancelavatar']))
		{
			$user_avatar = $user_avatar_category . '/' . $user_avatar_local;
			$user_avatar_type = USER_AVATAR_GALLERY;
		}
	}
}

//
// Let's make sure the user isn't logged in while registering,
// and ensure that they were trying to register a second time
// (Prevents double registrations)
//
if ($mode == 'register' && ($userdata['session_logged_in'] || $username == $userdata['username']))
{
	message_die(GENERAL_MESSAGE, $lang['Username_taken'], '', __LINE__, __FILE__);
}

//
// Did the user submit? In this case build a query to update the users profile in the DB
//
if ( isset($_POST['submit']) )
{
	include(SITE_DIR . 'includes/ucp/usercp_avatar.php');

	// session id check
	if ($sid == '' || $sid != $userdata['session_id'])
	{
		$error = true;
		$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Session_Expired'];
	}

	$passwd_sql = '';
	if ( $mode == 'editprofile' )
	{
		if ( $user_id != $userdata['user_id'] )
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Wrong_Profile'];
		}
	}
	else if ( $mode == 'register' )
	{
		if ( empty($username) || empty($new_password) || empty($password_confirm) || empty($email) )
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Fields_empty'];
		}
	}

	if ($bb_cfg['enable_confirm'] && $mode == 'register')
	{
		if (empty($_POST['confirm_id']))
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Confirm_code_wrong'];
		}
		else
		{
			$confirm_id = htmlspecialchars($_POST['confirm_id']);
			if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
			{
				$confirm_id = '';
			}

			$sql = 'SELECT code
				FROM ' . CONFIRM_TABLE . "
				WHERE confirm_id = '$confirm_id'
					AND session_id = '" . $userdata['session_id'] . "'";
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain confirmation code', '', __LINE__, __FILE__, $sql);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				// Only compare one char if the zlib-extension is not loaded
				if (!@extension_loaded('zlib'))
				{
					$row['code'] = substr($row['code'], -1);
				}

				if (strtolower($row['code']) != strtolower($confirm_code))
				{
					$error = TRUE;
					$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Confirm_code_wrong'];
				}
				else
				{
					$sql = 'DELETE FROM ' . CONFIRM_TABLE . "
						WHERE confirm_id = '$confirm_id'
							AND session_id = '" . $userdata['session_id'] . "'";
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not delete confirmation code', '', __LINE__, __FILE__, $sql);
					}
				}
			}
			else
			{
				$error = TRUE;
				$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Confirm_code_wrong'];
			}
			$db->sql_freeresult($result);
		}
	}

	$passwd_sql = '';
	if ( !empty($new_password) && !empty($password_confirm) )
	{
		if ( $new_password != $password_confirm )
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Password_mismatch'];
		}
		else if ( strlen($new_password) > 32 )
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Password_long'];
		}
		else
		{
			if ( $mode == 'editprofile' )
			{
				$sql = "SELECT user_password
					FROM " . USERS_TABLE . "
					WHERE user_id = $user_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not obtain user_password information', '', __LINE__, __FILE__, $sql);
				}

				$row = $db->sql_fetchrow($result);

				if ( $row['user_password'] != md5($cur_password) )
				{
					$error = TRUE;
					$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Current_password_mismatch'];
				}
			}

			if ( !$error )
			{
				$new_password = md5($new_password);
				$passwd_sql = "user_password = '$new_password', ";
			}
		}
	}
	else if ( ( empty($new_password) && !empty($password_confirm) ) || ( !empty($new_password) && empty($password_confirm) ) )
	{
		$error = TRUE;
		$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Password_mismatch'];
	}

	//
	// Do a ban check on this email address
	//
	if ( $email != $userdata['user_email'] || $mode == 'register' )
	{
		$result = validate_email($email);
		if ( $result['error'] )
		{
			$email = $userdata['user_email'];

			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $result['error_msg'];
		}

		if ( $mode == 'editprofile' )
		{
			$sql = "SELECT user_password
				FROM " . USERS_TABLE . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain user_password information', '', __LINE__, __FILE__, $sql);
			}

			$row = $db->sql_fetchrow($result);

			if ( $row['user_password'] != md5($cur_password) )
			{
				$email = $userdata['user_email'];

				$error = TRUE;
				$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Current_password_mismatch'];
			}
		}
	}

	$username_sql = $signature_bbcode_uid = '';
	if ( $bb_cfg['allow_namechange'] || $mode == 'register' )
	{
		if ( empty($username) )
		{
			// Error is already triggered, since one field is empty.
			$error = TRUE;
		}
		else if ( $username != $userdata['username'] || $mode == 'register' )
		{
			if (strtolower($username) != strtolower($userdata['username']) || $mode == 'register')
			{
				$result = validate_username($username);
				if ( $result['error'] )
				{
					$error = TRUE;
					$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $result['error_msg'];
				}
			}

			if (!$error)
			{
				$username_sql = "username = '" . str_replace("\'", "''", $username) . "', ";
			}
		}
	}

	if ( $signature != '' )
	{
		if ( strlen($signature) > $bb_cfg['max_sig_chars'] )
		{
			$error = TRUE;
			$error_msg .= ( ($error_msg) ? '<br />' : '' ) . $lang['Signature_too_long'];
		}

		$signature_bbcode_uid = ( $bb_cfg['allow_bbcode'] ) ? make_bbcode_uid() : '';
		$signature = prepare_message($signature, $bb_cfg['allow_bbcode'], $bb_cfg['allow_smilies'], $signature_bbcode_uid);
	}

	if ( $website != '' )
	{
		rawurlencode($website);
	}

	$avatar_sql = '';

	if ( isset($_POST['avatardel']) && $mode == 'editprofile' )
	{
		$avatar_sql = user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
	}
	else
	if ( ( !empty($user_avatar_upload) || !empty($user_avatar_name) ) && $bb_cfg['allow_avatar_upload'] )
	{
		if ( !empty($user_avatar_upload) )
		{
			$avatar_mode = (empty($user_avatar_name)) ? 'remote' : 'local';
			$avatar_sql = user_avatar_upload($mode, $avatar_mode, $userdata['user_avatar'], $userdata['user_avatar_type'], $error, $error_msg, $user_avatar_upload, $user_avatar_name, $user_avatar_size, $user_avatar_filetype);
		}
		else if ( !empty($user_avatar_name) )
		{
			$l_avatar_size = sprintf($lang['Avatar_filesize'], round($bb_cfg['avatar_filesize'] / 1024));

			$error = true;
			$error_msg .= ( ( !empty($error_msg) ) ? '<br />' : '' ) . $l_avatar_size;
		}
	}
	else if ( $user_avatar_remoteurl != '' && $bb_cfg['allow_avatar_remote'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_url($mode, $error, $error_msg, $user_avatar_remoteurl);
	}
	else if ( $user_avatar_local != '' && $bb_cfg['allow_avatar_local'] )
	{
		user_avatar_delete($userdata['user_avatar_type'], $userdata['user_avatar']);
		$avatar_sql = user_avatar_gallery($mode, $error, $error_msg, $user_avatar_local, $user_avatar_category);
	}

	if ( !$error )
	{
		if ( $avatar_sql == '' )
		{
			$avatar_sql = ( $mode == 'editprofile' ) ? '' : "'', " . USER_AVATAR_NONE;
		}

		$update_user_opt = array(
			'viewemail',
			'attachsig',
			'hide_porn_forums',
		);
		$user_opt = $userdata['user_opt'];

		foreach ($update_user_opt as $opt)
		{
			setbit($user_opt, $bf['user_opt'][$opt], !empty($_POST[$opt]));
		}

		if ( $mode == 'editprofile' )
		{
			if ( $email != $userdata['user_email'] && $bb_cfg['require_activation'] != USER_ACTIVATION_NONE && !IS_ADMIN )
			{
				$user_active = 0;
				$user_actkey = make_rand_str(12);

				if ( $userdata['session_logged_in'] )
				{
					$user->session_end();
				}
			}
			else
			{
				$user_active = 1;
				$user_actkey = '';
			}

			if ($user_lang == $bb_cfg['board_lang'])
			{
				$user_lang = '';
			}

			$sql = "UPDATE " . USERS_TABLE . "
				SET " . $username_sql . $passwd_sql . "
					user_opt = $user_opt,
					user_email = '" . str_replace("\'", "''", $email) ."',
					user_icq = '" . str_replace("\'", "''", $icq) . "',
					user_website = '" . str_replace("\'", "''", $website) . "',
					user_occ = '" . str_replace("\'", "''", $occupation) . "',
					user_from = '" . str_replace("\'", "''", $location) . "',
					user_from_flag = '$user_flag',
					user_interests = '" . str_replace("\'", "''", $interests) . "',
					user_sig = '" . str_replace("\'", "''", $signature) . "',
					user_sig_bbcode_uid = '$signature_bbcode_uid',
					user_aim = '" . str_replace("\'", "''", str_replace(' ', '+', $aim)) . "',
					user_yim = '" . str_replace("\'", "''", $yim) . "',
					user_msnm = '" . str_replace("\'", "''", $msn) . "',

					user_allow_viewonline = $allowviewonline,
					user_notify = $notifyreply,
					user_notify_pm = $notifypm,

					user_timezone = '$user_timezone',
					user_dateformat = '" . str_replace("\'", "''", $user_dateformat) . "',
					user_lang = '" . str_replace("\'", "''", $user_lang) . "',
					user_active = $user_active,
					user_actkey = '" . str_replace("\'", "''", $user_actkey) . "'" . $avatar_sql . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}
			if ($db->sql_affectedrows())
			{
				cache_rm_userdata($userdata);
			}

			if ( !$user_active )
			{
				//
				// The users account has been deactivated, send them an email with a new activation key
				//
				include(SITE_DIR . 'includes/emailer.php');
				$emailer = new emailer($bb_cfg['smtp_delivery']);

 				if ( $bb_cfg['require_activation'] != USER_ACTIVATION_ADMIN )
 				{
				$emailer->from($bb_cfg['board_email']);
				$emailer->replyto($bb_cfg['board_email']);

				$emailer->use_template('user_activate', $user_lang);
				$emailer->email_address($email);
				$emailer->set_subject($lang['Reactivate']);

				$emailer->assign_vars(array(
					'SITENAME' => $bb_cfg['sitename'],
					'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, mb_substr(str_replace("\'", "'", $username), 0, 25)),
					'EMAIL_SIG' => (!empty($bb_cfg['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']) : '',

 						'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 					);
 					$emailer->send();
 					$emailer->reset();
 				}
 				else if ( $bb_cfg['require_activation'] == USER_ACTIVATION_ADMIN )
 				{
 					$sql = 'SELECT user_email, user_lang
 						FROM ' . USERS_TABLE . '
 						WHERE user_level = ' . ADMIN;

 					if ( !($result = $db->sql_query($sql)) )
 					{
 						message_die(GENERAL_ERROR, 'Could not select Administrators', '', __LINE__, __FILE__, $sql);
 					}

 					while ($row = $db->sql_fetchrow($result))
 					{
 						$emailer->from($bb_cfg['board_email']);
 						$emailer->replyto($bb_cfg['board_email']);

 						$emailer->email_address(trim($row['user_email']));
 						$emailer->use_template("admin_activate", $row['user_lang']);
 						$emailer->set_subject($lang['Reactivate']);

 						$emailer->assign_vars(array(
 							'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, mb_substr(str_replace("\'", "'", $username), 0, 25)),
 							'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']),

 							'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 						);
 						$emailer->send();
 						$emailer->reset();
 					}
 					$db->sql_freeresult($result);
 				}

				$message = $lang['Profile_updated_inactive'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.php") . '">', '</a>');
			}
			else
			{
				$message = $lang['Profile_updated'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.php") . '">', '</a>');
			}

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			if ($user_lang == $bb_cfg['board_lang'])
			{
				$user_lang = '';
			}
			$avatar_type = USER_AVATAR_NONE;

			// Insert new user data
			$sql = "INSERT INTO " . USERS_TABLE . "
			          (user_opt,                              username,         user_regdate,                               user_password,                                 user_email,                                 user_icq,                                user_website,                                    user_occ,                                     user_from,      user_from_flag,                           user_interests,                                   user_sig,           user_sig_bbcode_uid,  user_avatar, user_avatar_type,                                              user_aim,                                 user_yim,                   user_msnm,                 user_allow_viewonline, user_notify, user_notify_pm, user_timezone,                               user_dateformat,                                     user_lang,         user_level, user_allow_pm, user_active, user_actkey)
				VALUES ($user_opt, '" . str_replace("\'", "''", $username) . "', " . time() . ", '" . str_replace("\'", "''", $new_password) . "', '" . str_replace("\'", "''", $email) . "', '" . str_replace("\'", "''", $icq) . "', '" . str_replace("\'", "''", $website) . "', '" . str_replace("\'", "''", $occupation) . "', '" . str_replace("\'", "''", $location) . "', '$user_flag', '" . str_replace("\'", "''", $interests) . "', '" . str_replace("\'", "''", $signature) . "', '$signature_bbcode_uid', '',          $avatar_type, '" . str_replace("\'", "''", str_replace(' ', '+', $aim)) . "', '" . str_replace("\'", "''", $yim) . "', '" . str_replace("\'", "''", $msn) . "', $allowviewonline,      $notifyreply, $notifypm,     '$user_timezone', '" . str_replace("\'", "''", $user_dateformat) . "', '" . str_replace("\'", "''", $user_lang) . "', 0,          1, ";

			if ($bb_cfg['require_activation'] == USER_ACTIVATION_SELF || $bb_cfg['require_activation'] == USER_ACTIVATION_ADMIN || $coppa)
			{
				$user_actkey = make_rand_str(12);
				$sql .= "0, '$user_actkey')";
			}
			else
			{
				$sql .= "1, '')";
			}

			$db->query($sql);
			$user_id = $db->sql_nextid();

			include(SITE_DIR . 'includes/functions_torrent.php');
			generate_passkey($user_id, 1);

			if ( $coppa )
			{
				$message = $lang['COPPA'];
				$email_template = 'coppa_welcome_inactive';
			}
			else if ( $bb_cfg['require_activation'] == USER_ACTIVATION_SELF )
			{
				$message = $lang['Account_inactive'];
				$email_template = 'user_welcome_inactive';
			}
			else if ( $bb_cfg['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$message = $lang['Account_inactive_admin'];
				$email_template = 'admin_welcome_inactive';
			}
			else
			{
				$message = $lang['Account_added'];
				$email_template = 'user_welcome';
			}

			include(SITE_DIR . 'includes/emailer.php');
			$emailer = new emailer($bb_cfg['smtp_delivery']);

			$emailer->from($bb_cfg['board_email']);
			$emailer->replyto($bb_cfg['board_email']);

			$emailer->use_template($email_template, $user_lang);
			$emailer->email_address($email);
			$emailer->set_subject(sprintf($lang['Welcome_subject'], $bb_cfg['sitename']));

			if( $coppa )
			{
				$emailer->assign_vars(array(
					'SITENAME' => $bb_cfg['sitename'],
					'WELCOME_MSG' => sprintf($lang['Welcome_subject'], $bb_cfg['sitename']),
					'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, mb_substr(str_replace("\'", "'", $username), 0, 25)),
					'PASSWORD' => $password_confirm,
					'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']),

					'FAX_INFO' => $bb_cfg['coppa_fax'],
					'MAIL_INFO' => $bb_cfg['coppa_mail'],
					'EMAIL_ADDRESS' => $email,
					'ICQ' => $icq,
					'AIM' => $aim,
					'YIM' => $yim,
					'MSN' => $msn,
					'WEB_SITE' => $website,
					'FROM' => $location,
					'OCC' => $occupation,
					'INTERESTS' => $interests,
					'SITENAME' => $bb_cfg['sitename']));
			}
			else
			{
				$emailer->assign_vars(array(
					'SITENAME' => $bb_cfg['sitename'],
					'WELCOME_MSG' => sprintf($lang['Welcome_subject'], $bb_cfg['sitename']),
					'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, mb_substr(str_replace("\'", "'", $username), 0, 25)),
					'PASSWORD' => $password_confirm,
					'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']),

					'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
				);
			}

			$emailer->send();
			$emailer->reset();

			if ( $bb_cfg['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$sql = "SELECT user_email, user_lang
					FROM " . USERS_TABLE . "
					WHERE user_level = " . ADMIN;

				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not select Administrators', '', __LINE__, __FILE__, $sql);
				}

				while ($row = $db->sql_fetchrow($result))
				{
					$emailer->from($bb_cfg['board_email']);
					$emailer->replyto($bb_cfg['board_email']);

					$emailer->email_address(trim($row['user_email']));
					$emailer->use_template("admin_activate", $row['user_lang']);
					$emailer->set_subject($lang['New_account_subject']);

					$emailer->assign_vars(array(
						'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, mb_substr(str_replace("\'", "'", $username), 0, 25)),
						'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $bb_cfg['board_email_sig']),

						'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
					);
					$emailer->send();
					$emailer->reset();
				}
				$db->sql_freeresult($result);
			}

			/* Переадресация на тему "Как качать?" */
			// redirect('viewtopic.php?t=262');

			$message = $message . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.php") . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		} // if mode == register
	}
} // End of submit


if ( $error )
{
	//
	// If an error occured we need to stripslashes on returned data
	//
	$new_password = '';
	$password_confirm = '';

	$signature = ($signature_bbcode_uid != '') ? preg_replace("/:(([a-z0-9]+:)?)$signature_bbcode_uid(=|\])/si", '\\3', $signature) : $signature;
}
else if ( $mode == 'editprofile' && !isset($_POST['avatargallery']) && !isset($_POST['submitavatar']) && !isset($_POST['cancelavatar']) )
{
	$user_id = $userdata['user_id'];
	$username = $userdata['username'];
	$email = $userdata['user_email'];
	$new_password = '';
	$password_confirm = '';

	$icq = $userdata['user_icq'];
	$aim = str_replace('+', ' ', $userdata['user_aim']);
	$msn = $userdata['user_msnm'];
	$yim = $userdata['user_yim'];

	$website = $userdata['user_website'];
	$location = $userdata['user_from'];
	$user_flag = $userdata['user_from_flag'];
	$occupation = $userdata['user_occ'];
	$interests = $userdata['user_interests'];
	$signature_bbcode_uid = $userdata['user_sig_bbcode_uid'];
	$signature = ($signature_bbcode_uid != '') ? preg_replace("/:(([a-z0-9]+:)?)$signature_bbcode_uid(=|\])/si", '\\3', $userdata['user_sig']) : $userdata['user_sig'];

	$viewemail = bf($userdata['user_opt'], 'user_opt', 'viewemail');
	$attachsig = bf($userdata['user_opt'], 'user_opt', 'attachsig');
	$hide_porn_forums = bf($userdata['user_opt'], 'user_opt', 'hide_porn_forums');
	$notifypm = $userdata['user_notify_pm'];
	$notifyreply = $userdata['user_notify'];
	$allowviewonline = $userdata['user_allow_viewonline'];

	$user_avatar = ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar'] : '';
	$user_avatar_type = ( $userdata['user_allowavatar'] ) ? $userdata['user_avatar_type'] : USER_AVATAR_NONE;

	$user_timezone   = $userdata['user_timezone'];
	$user_lang       = ($userdata['user_lang']) ? $userdata['user_lang'] : $bb_cfg['board_lang'];
	$user_dateformat = ($userdata['user_dateformat']) ? $userdata['user_dateformat'] : $bb_cfg['default_dateformat'];
}

//
// Default pages
//
if ( $mode == 'editprofile' )
{
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg = $lang['Wrong_Profile'];
	}
}

if( isset($_POST['avatargallery']) && !$error )
{
	include(SITE_DIR . 'includes/ucp/usercp_avatar.php');

	$avatar_category = ( !empty($_POST['avatarcategory']) ) ? htmlspecialchars($_POST['avatarcategory']) : '';

	$template->set_filenames(array(
		'body' => 'usercp_avatar_gallery.tpl')
	);

	$allowviewonline = !$allowviewonline;

	display_avatar_gallery($mode, $avatar_category, $user_id, $email, $current_email, $coppa, $username, $email, $new_password, $cur_password, $password_confirm, $icq, $aim, $msn, $yim, $website, $location, $user_flag, $occupation, $interests, $signature, $viewemail, $notifypm, $notifyreply, $attachsig, $allowviewonline, 1, $user_lang, $user_timezone, $user_dateformat, $userdata['session_id']);
}
else
{
	include(SITE_DIR . 'includes/functions_selects.php');

	if ( !isset($coppa) )
	{
		$coppa = FALSE;
	}

	$avatar_img = '';
	if ( $user_avatar_type )
	{
		switch( $user_avatar_type )
		{
			case USER_AVATAR_UPLOAD:
				$avatar_img = ( $bb_cfg['allow_avatar_upload'] ) ? '<img src="' . $bb_cfg['avatar_path'] . '/' . $user_avatar . '" alt="avatar" />' : '';
				break;
			case USER_AVATAR_REMOTE:
				$avatar_img = ( $bb_cfg['allow_avatar_remote'] ) ? '<img src="' . $user_avatar . '" alt="avatar" />' : '';
				break;
			case USER_AVATAR_GALLERY:
				$avatar_img = ( $bb_cfg['allow_avatar_local'] ) ? '<img src="' . $bb_cfg['avatar_gallery_path'] . '/' . $user_avatar . '" alt="avatar" />' : '';
				break;
		}
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';
	$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';
	if( $mode == 'editprofile' )
	{
		$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
		//
		// Send the users current email address. If they change it, and account activation is turned on
		// the user account will be disabled and the user will have to reactivate their account.
		//
		$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';
	}

	if ( !empty($user_avatar_local) )
	{
		$s_hidden_fields .= '<input type="hidden" name="avatarlocal" value="' . $user_avatar_local . '" /><input type="hidden" name="avatarcatname" value="' . $user_avatar_category . '" />';
	}

	$bbcode_status = ($bb_cfg['allow_bbcode']) ? $lang['BBCode_is_ON'] : $lang['BBCode_is_OFF'];
	$smilies_status = ($bb_cfg['allow_smilies']) ? $lang['Smilies_are_ON'] : $lang['Smilies_are_OFF'];

	if ( $error )
	{
		$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
	}

	$template->set_filenames(array(
		'body' => 'usercp_register.tpl')
	);

	if ( $mode == 'editprofile' )
	{
		$template->assign_block_vars('switch_edit_profile', array());
		$template->assign_vars(array(
			'EDIT_PROFILE'    => true,
			'SHOW_LANG'       => $bb_cfg['allow_change']['language'],
			'SHOW_DATEFORMAT' => $bb_cfg['allow_change']['dateformat'],
		));
	}


// FLAGHACK-start
	// query to get the list of flags
	$sql = "SELECT *
		FROM " . FLAG_TABLE . "
		ORDER BY flag_name";
	if(!$flags_result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, "Couldn't obtain flags information.", "", __LINE__, __FILE__, $sql);
	}
	$flag_row = $db->sql_fetchrowset($flags_result);
	$num_flags = $db->sql_numrows($flags_result);
	// build the html select statement
	$flag_start_image = 'blank.gif' ;
	$selected = ( isset($user_flag) ) ? '' : HTML_SELECTED;
	$flag_select = "<select name=\"user_flag\" onChange=\"document.images['user_flag'].src = '//ivacuum.org/i/flags/48/' + this.value;\" >";
	$flag_select .= "<option value=\"blank.gif\"$selected>" . $lang['Select_Country'] . "</option>";
	for ($i = 0; $i < $num_flags; $i++)
	{
		$flag_name = $flag_row[$i]['flag_name'];
		$flag_image = $flag_row[$i]['flag_image'];
		$selected = ( isset( $user_flag) ) ? (($user_flag == $flag_image) ? HTML_SELECTED : '' ) : '' ;
		$flag_select .= "\t<option value=\"$flag_image\"$selected>$flag_name</option>";
		if ( isset( $user_flag) && ($user_flag == $flag_image))
		{
			$flag_start_image = $flag_image ;
		}
	}
	$flag_select .= '</select>';
// FLAGHACK-end


	if ( ($mode == 'register') || ($bb_cfg['allow_namechange']) )
	{
		$template->assign_block_vars('switch_namechange_allowed', array());
	}
	else
	{
		$template->assign_block_vars('switch_namechange_disallowed', array());
	}


	// Visual Confirmation
	$confirm_image = '';

	if (!empty($bb_cfg['enable_confirm']) && $mode == 'register')
	{
		$db->query("
			DELETE cfm
			FROM ". CONFIRM_TABLE ." cfm
			LEFT JOIN ". SESSIONS_TABLE ." s USING(session_id)
			WHERE s.session_id IS NULL
		");

		$row = $db->fetch_row("
			SELECT COUNT(session_id) AS attempts
			FROM ". CONFIRM_TABLE ."
			WHERE session_id = '{$userdata['session_id']}'
		");

		if (isset($row['attempts']) && $row['attempts'] > 20)
		{
			message_die(GENERAL_MESSAGE, $lang['Too_many_registers']);
		}

		$confirm_chars = array('1', '2', '3', '4', '5', '6', '7', '8', '9');

		$max_chars = count($confirm_chars) - 1;
		$confirm_code = '';
		for ($i = 0; $i < 6; $i++)
		{
			$confirm_code .= $confirm_chars[mt_rand(0, $max_chars)];
		}

		$confirm_id = make_rand_str(12);

		$db->query("
			INSERT INTO ". CONFIRM_TABLE ." (confirm_id, session_id, code)
			VALUES ('$confirm_id', '{$userdata['session_id']}', '$confirm_code')
		");

		$confirm_image = (extension_loaded('zlib')) ? '
			<img src="'. append_sid("profile.php?mode=confirm&amp;id=$confirm_id") .'" alt="" title="" />
		' : '
			<img src="'. append_sid("profile.php?mode=confirm&amp;id=$confirm_id&amp;c=1") .'" alt="" title="" />
			<img src="'. append_sid("profile.php?mode=confirm&amp;id=$confirm_id&amp;c=2") .'" alt="" title="" />
			<img src="'. append_sid("profile.php?mode=confirm&amp;id=$confirm_id&amp;c=3") .'" alt="" title="" />
			<img src="'. append_sid("profile.php?mode=confirm&amp;id=$confirm_id&amp;c=4") .'" alt="" title="" />
		';
		$s_hidden_fields .= '<input type="hidden" name="confirm_id" value="'. $confirm_id .'" />';

		$template->assign_block_vars('switch_confirm', array());
	}


	//
	// Let's do an overall check for settings/versions which would prevent
	// us from doing file uploads....
	//
	$ini_val = ( phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';
	$form_enctype = ( @$ini_val('file_uploads') == '0' || strtolower(@$ini_val('file_uploads') == 'off') || phpversion() == '4.0.4pl1' || !$bb_cfg['allow_avatar_upload'] || ( phpversion() < '4.0.3' && @$ini_val('open_basedir') != '' ) ) ? '' : 'enctype="multipart/form-data"';

	$template->assign_vars(array(
		'USERNAME' => $username,
		'CUR_PASSWORD' => '',
		'NEW_PASSWORD' => '',
		'PASSWORD_CONFIRM' => '',
		'EMAIL' => $email,
		'CONFIRM_IMG' => $confirm_image,
		'YIM' => $yim,
		'ICQ' => $icq,
		'MSN' => $msn,
		'AIM' => $aim,
		'OCCUPATION' => $occupation,
		'INTERESTS' => $interests,
		'LOCATION' => $location,

		'L_FLAG' => $lang['Country_Flag'],
		'FLAG_SELECT' => $flag_select,
		'FLAG_START' => $flag_start_image,

		'WEBSITE' => $website,
		'SIGNATURE' => str_replace('<br />', "\n", $signature),
		'VIEW_EMAIL_YES' => ( $viewemail ) ? 'checked="checked"' : '',
		'VIEW_EMAIL_NO' => ( !$viewemail ) ? 'checked="checked"' : '',
		'HIDE_USER_YES' => ( !$allowviewonline ) ? 'checked="checked"' : '',
		'HIDE_USER_NO' => ( $allowviewonline ) ? 'checked="checked"' : '',
		'NOTIFY_PM_YES' => ( $notifypm ) ? 'checked="checked"' : '',
		'NOTIFY_PM_NO' => ( !$notifypm ) ? 'checked="checked"' : '',
		'ALWAYS_ADD_SIGNATURE_YES' => ( $attachsig ) ? 'checked="checked"' : '',
		'ALWAYS_ADD_SIGNATURE_NO' => ( !$attachsig ) ? 'checked="checked"' : '',
		'HIDE_PORN_FORUMS_YES' => ( $hide_porn_forums ) ? 'checked="checked"' : '',
		'HIDE_PORN_FORUMS_NO' => ( !$hide_porn_forums ) ? 'checked="checked"' : '',
		'NOTIFY_REPLY_YES' => ( $notifyreply ) ? 'checked="checked"' : '',
		'NOTIFY_REPLY_NO' => ( !$notifyreply ) ? 'checked="checked"' : '',
		'ALLOW_AVATAR' => $bb_cfg['allow_avatar_upload'],
		'AVATAR' => $avatar_img,
		'AVATAR_SIZE' => $bb_cfg['avatar_filesize'],
		'LANGUAGE_SELECT' => language_select($user_lang, 'language'),
		'TIMEZONE_SELECT' => tz_select($user_timezone, 'timezone'),
		'DATE_FORMAT' => htmlCHR($user_dateformat),
		'BBCODE_STATUS' => sprintf($bbcode_status, '<a href="' . append_sid("faq.php?mode=bbcode") . '" target="_phpbbcode">', '</a>'),
		'SMILIES_STATUS' => $smilies_status,

		'L_CURRENT_PASSWORD' => $lang['Current_password'],
		'T_NEW_PASSWORD' => ( $mode == 'register' ) ? $lang['PASSWORD'] : $lang['NEW_PASSWORD'],
		'L_CONFIRM_PASSWORD' => $lang['Confirm_password'],
		'L_CONFIRM_PASSWORD_EXPLAIN' => ( $mode == 'editprofile' ) ? $lang['Confirm_password_explain'] : '',
		'L_PASSWORD_IF_CHANGED' => ( $mode == 'editprofile' ) ? $lang['password_if_changed'] : '',
		'L_PASSWORD_CONFIRM_IF_CHANGED' => ( $mode == 'editprofile' ) ? $lang['password_confirm_if_changed'] : '',
		'L_OCCUPATION' => $lang['Occupation'],
		'L_BOARD_LANGUAGE' => $lang['Board_lang'],
		'L_BOARD_STYLE' => $lang['Board_style'],
		'L_TIMEZONE' => $lang['Timezone'],
		'L_DATE_FORMAT' => $lang['Date_format'],
		'L_DATE_FORMAT_EXPLAIN' => $lang['Date_format_explain'],
		'L_INTERESTS' => $lang['Interests'],
		'L_HIDE_USER' => $lang['Hide_user'],
		'L_ALWAYS_ADD_SIGNATURE' => $lang['Always_add_sig'],

		'L_AVATAR_PANEL' => $lang['Avatar_panel'],
		'L_AVATAR_EXPLAIN' => sprintf($lang['Avatar_explain'], $bb_cfg['avatar_max_width'], $bb_cfg['avatar_max_height'], (round($bb_cfg['avatar_filesize'] / 1024))),
		'L_UPLOAD_AVATAR_FILE' => $lang['Upload_Avatar_file'],
		'L_UPLOAD_AVATAR_URL' => $lang['Upload_Avatar_URL'],
		'L_UPLOAD_AVATAR_URL_EXPLAIN' => $lang['Upload_Avatar_URL_explain'],
		'L_AVATAR_GALLERY' => $lang['Select_from_gallery'],
		'L_SHOW_GALLERY' => $lang['View_avatar_gallery'],
		'L_LINK_REMOTE_AVATAR' => $lang['Link_remote_Avatar'],
		'L_LINK_REMOTE_AVATAR_EXPLAIN' => $lang['Link_remote_Avatar_explain'],
		'L_DELETE_AVATAR' => $lang['Delete_Image'],
		'L_CURRENT_IMAGE' => $lang['Current_Image'],

		'L_SIGNATURE' => $lang['Signature'],
		'L_SIGNATURE_EXPLAIN' => sprintf($lang['Signature_explain'], $bb_cfg['max_sig_chars']),
		'L_NOTIFY_ON_REPLY' => $lang['Always_notify'],
		'L_NOTIFY_ON_REPLY_EXPLAIN' => $lang['Always_notify_explain'],
		'L_NOTIFY_ON_PRIVMSG' => $lang['Notify_on_privmsg'],
		'L_PREFERENCES' => $lang['Preferences'],
		'L_PUBLIC_VIEW_EMAIL' => $lang['Public_view_email'],
		'L_ITEMS_REQUIRED' => $lang['Items_required'],
		'L_REGISTRATION_INFO' => $lang['Registration_info'],
		'L_PROFILE_INFO' => $lang['Profile_info'],
		'L_PROFILE_INFO_NOTICE' => $lang['Profile_info_warn'],
		'L_EMAIL_ADDRESS' => $lang['Email_address'],

		'L_CONFIRM_CODE_IMPAIRED'	=> sprintf($lang['Confirm_code_impaired'], '<a href="mailto:' . $bb_cfg['board_email'] . '">', '</a>'),
		'L_CONFIRM_CODE'			=> $lang['Confirm_code'],
		'L_CONFIRM_CODE_EXPLAIN'	=> $lang['Confirm_code_explain'],

		'S_ALLOW_AVATAR_UPLOAD' => $bb_cfg['allow_avatar_upload'],
		'S_ALLOW_AVATAR_LOCAL' => $bb_cfg['allow_avatar_local'],
		'S_ALLOW_AVATAR_REMOTE' => $bb_cfg['allow_avatar_remote'],
		'S_HIDDEN_FIELDS' => $s_hidden_fields,
		'S_FORM_ENCTYPE' => $form_enctype,
		'S_PROFILE_ACTION' => append_sid("profile.php"),

		'U_RESET_AUTOLOGIN'      => "login.php?logout=1&amp;reset_autologin=1&amp;sid={$userdata['session_id']}",
		'L_AUTOLOGIN'            => $lang['Autologin'],
		'L_RESET_AUTOLOGIN'      => $lang['Reset_autologin'],
		'L_RESET_AUTOLOGIN_EXPL' => $lang['Reset_autologin_expl'],
	));

	//
	// This is another cheat using the block_var capability
	// of the templates to 'fake' an IF...ELSE...ENDIF solution
	// it works well :)
	//
	if ( $mode != 'register' )
	{
		if ( $userdata['user_allowavatar'] && ( $bb_cfg['allow_avatar_upload'] || $bb_cfg['allow_avatar_local'] || $bb_cfg['allow_avatar_remote'] ) )
		{
			$template->assign_block_vars('switch_avatar_block', array() );

			if ( $bb_cfg['allow_avatar_upload'] && file_exists(@phpbb_realpath('./' . $bb_cfg['avatar_path'])) )
			{
				if ( $form_enctype != '' )
				{
					$template->assign_block_vars('switch_avatar_block.switch_avatar_local_upload', array() );
				}
				$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_upload', array() );
			}

			if ( $bb_cfg['allow_avatar_remote'] )
			{
				$template->assign_block_vars('switch_avatar_block.switch_avatar_remote_link', array() );
			}

			if ( $bb_cfg['allow_avatar_local'] && file_exists(@phpbb_realpath('./' . $bb_cfg['avatar_gallery_path'])) )
			{
				$template->assign_block_vars('switch_avatar_block.switch_avatar_local_gallery', array() );
			}
		}
	}
}

//bt
if ($mode == 'editprofile' && $userdata['session_logged_in'])
{
	$template->assign_block_vars('switch_bittorrent', array());

	$sql = 'SELECT auth_key
		FROM '. BT_USERS_TABLE .'
		WHERE user_id = '. $userdata['user_id'];

	if (!$result = $db->sql_query($sql))
	{
		message_die(GENERAL_ERROR, 'Could not query users passkey', '', __LINE__, __FILE__, $sql);
	}

	$row = $db->sql_fetchrow($result);
	$curr_passkey = ($row['auth_key']) ? $row['auth_key'] : '';

	$template->assign_vars(array(
		'L_GEN_PASSKEY'           => $lang['Bt_Gen_Passkey'],
		'L_GEN_PASSKEY_EXPLAIN'   => $lang['Bt_Gen_Passkey_Explain'],
		'L_GEN_PASSKEY_EXPLAIN_2' => $lang['Bt_Gen_Passkey_Explain_2'],
		'S_GEN_PASSKEY'           => "<a href=\"torrent.php?mode=gen_passkey&amp;u=" . $userdata['user_id'] . '&amp;sid=' . $userdata['session_id'] . '">' . $lang['Bt_Gen_Passkey_Url'] . '</a>',
		'L_CURR_PASSKEY'          => $lang['Curr_passkey'],
		'CURR_PASSKEY'            => $curr_passkey,
	));
}
//bt end

require(PAGE_HEADER);

$template->pparse('body');

require(PAGE_FOOTER);


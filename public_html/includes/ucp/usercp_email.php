<?php

if (!defined('SITE_DIR'))
{
	exit;
}

// Is send through board enabled? No, return to index
if (!$board_config['board_email_form'])
{
	redirect(append_sid("/", true));
}

if ( !empty($_GET[POST_USERS_URL]) || !empty($_POST[POST_USERS_URL]) )
{
	$user_id = ( !empty($_GET[POST_USERS_URL]) ) ? intval($_GET[POST_USERS_URL]) : intval($_POST[POST_USERS_URL]);
}
else
{
	message_die(GENERAL_MESSAGE, $lang['No_user_specified']);
}

if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.php?redirect=profile.php&mode=email&" . POST_USERS_URL . "=$user_id", true));
}

$sql = "SELECT username, user_email, user_lang
	FROM bb_users
	WHERE user_id = $user_id";
if ( $row = $db->fetch_row($sql) )
{
	$username = $row['username'];
	$user_email = $row['user_email'];
	$user_lang = $row['user_lang'];

	if ( true || IS_ADMIN )  //  TRUE instead of missing user_opt "prevent_email"
	{
		if ( isset($_POST['submit']) )
		{
			$error = FALSE;

			if ( !empty($_POST['subject']) )
			{
				$subject = trim($_POST['subject']);
			}
			else
			{
				$error = TRUE;
				$error_msg = ( !empty($error_msg) ) ? $error_msg . '<br />' . $lang['Empty_subject_email'] : $lang['Empty_subject_email'];
			}

			if ( !empty($_POST['message']) )
			{
				$message = trim($_POST['message']);
			}
			else
			{
				$error = TRUE;
				$error_msg = ( !empty($error_msg) ) ? $error_msg . '<br />' . $lang['Empty_message_email'] : $lang['Empty_message_email'];
			}

			if ( !$error )
			{
				require(SITE_DIR . 'includes/emailer.php');
				$emailer = new emailer($board_config['smtp_delivery']);

				$emailer->from($userdata['user_email']);
				$emailer->replyto($userdata['user_email']);

				$email_headers = 'X-AntiAbuse: Board servername - ' . $server_name . "\n";
				$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
				$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
				$email_headers .= 'X-AntiAbuse: User IP - ' . CLIENT_IP . "\n";

				$emailer->use_template('profile_send_email', $user_lang);
				$emailer->email_address($user_email);
				$emailer->set_subject($subject);
				$emailer->extra_headers($email_headers);

				$emailer->assign_vars(array(
					'SITENAME' => $board_config['sitename'],
					'BOARD_EMAIL' => $board_config['board_email'],
					'FROM_USERNAME' => $userdata['username'],
					'TO_USERNAME' => $username,
					'MESSAGE' => $message)
				);
				$emailer->send();
				$emailer->reset();

				if ( !empty($_POST['cc_email']) )
				{
					$emailer->from($userdata['user_email']);
					$emailer->replyto($userdata['user_email']);
					$emailer->use_template('profile_send_email');
					$emailer->email_address($userdata['user_email']);
					$emailer->set_subject($subject);

					$emailer->assign_vars(array(
						'SITENAME' => $board_config['sitename'],
						'BOARD_EMAIL' => $board_config['board_email'],
						'FROM_USERNAME' => $userdata['username'],
						'TO_USERNAME' => $username,
						'MESSAGE' => $message)
					);
					$emailer->send();
					$emailer->reset();
				}

				$message = $lang['Email_sent'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("/") . '">', '</a>');
				message_die(GENERAL_MESSAGE, $message);
			}
		}

		if (!empty($error))
		{
			$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
		}

		$template->assign_vars(array(
			'USERNAME' => $username,

			'S_HIDDEN_FIELDS' => '',
			'S_POST_ACTION' => append_sid("profile.php?mode=email&amp;" . POST_USERS_URL . "=$user_id"),

			'L_SEND_EMAIL_MSG' => $lang['Send_email_msg'],
			'L_RECIPIENT' => $lang['Recipient'],
			'L_SUBJECT' => $lang['Subject'],
			'L_MESSAGE_BODY' => $lang['Message_body'],
			'L_MESSAGE_BODY_DESC' => $lang['Email_message_desc'],
			'L_EMPTY_SUBJECT_EMAIL' => $lang['Empty_subject_email'],
			'L_EMPTY_MESSAGE_EMAIL' => $lang['Empty_message_email'],
			'L_CC_EMAIL' => $lang['CC_email'],
			'L_SPELLCHECK' => $lang['Spellcheck'],
			'L_SEND_EMAIL' => $lang['Send_email'],
		));

		print_page('usercp_email.tpl');
	}
	else
	{
		message_die(GENERAL_MESSAGE, $lang['User_prevent_email']);
	}
}
else
{
	message_die(GENERAL_MESSAGE, $lang['User_not_exist']);
}


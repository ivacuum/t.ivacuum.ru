<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['General']['Mass_Email'] = basename(__FILE__);
	return;
}
require './pagestart.php';
// ACP Header - END

@set_time_limit(1200);

$message = '';
$subject = '';

//
// Do the job ...
//
if ( isset($_POST['submit']) )
{
	$subject = trim($_POST['subject']);
	$message = trim($_POST['message']);

	$error = FALSE;
	$error_msg = '';

	if ( empty($subject) )
	{
		$error = true;
		$error_msg .= ( !empty($error_msg) ) ? '<br />' . $lang['Empty_subject'] : $lang['Empty_subject'];
	}

	if ( empty($message) )
	{
		$error = true;
		$error_msg .= ( !empty($error_msg) ) ? '<br />' . $lang['Empty_message'] : $lang['Empty_message'];
	}

	$group_id = intval($_POST[POST_GROUPS_URL]);

	$sql = ( $group_id != -1 ) ? "SELECT u.user_email FROM bb_users u, bb_user_group ug WHERE ug.group_id = $group_id AND ug.user_pending <> 1 AND u.user_id = ug.user_id" : "SELECT user_email FROM bb_users";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select group members', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$bcc_list = array();
		do
		{
			$bcc_list[] = $row['user_email'];
		}
		while ( $row = $db->sql_fetchrow($result) );

		$db->sql_freeresult($result);
	}
	else
	{
		$message = ( $group_id != -1 ) ? $lang['Group_not_exist'] : $lang['No_such_user'];

		$error = true;
		$error_msg .= ( !empty($error_msg) ) ? '<br />' . $message : $message;
	}

	if ( !$error )
	{
		require SITE_DIR . 'includes/emailer.php';

		//
		// Let's do some checking to make sure that mass mail functions
		// are working in win32 versions of php.
		//
		if ( preg_match('/[c-z]:\\\.*/i', getenv('PATH')) && !$board_config['smtp_delivery'])
		{
			$ini_val = ( @phpversion() >= '4.0.0' ) ? 'ini_get' : 'get_cfg_var';

			// We are running on windows, force delivery to use our smtp functions
			// since php's are broken by default
			$board_config['smtp_delivery'] = 1;
			$board_config['smtp_host'] = @$ini_val('SMTP');
		}

		$emailer = new emailer($board_config['smtp_delivery']);

		$emailer->from($board_config['board_email']);
		$emailer->replyto($board_config['board_email']);

		for ($i = 0; $i < count($bcc_list); $i++)
		{
			$emailer->bcc($bcc_list[$i]);
		}

		$email_headers = 'X-AntiAbuse: Board servername - ' . $board_config['server_name'] . "\n";
		$email_headers .= 'X-AntiAbuse: User_id - ' . $userdata['user_id'] . "\n";
		$email_headers .= 'X-AntiAbuse: Username - ' . $userdata['username'] . "\n";
		$email_headers .= 'X-AntiAbuse: User IP - ' . CLIENT_IP . "\n";

		$emailer->use_template('admin_send_email');
		$emailer->email_address($board_config['board_email']);
		$emailer->set_subject($subject);
		$emailer->extra_headers($email_headers);

		$emailer->assign_vars(array(
			'SITENAME' => $board_config['sitename'],
			'BOARD_EMAIL' => $board_config['board_email'],
			'MESSAGE' => $message)
		);
		$emailer->send();
		$emailer->reset();

		message_die(GENERAL_MESSAGE, $lang['Email_sent'] . '<br /><br />' . sprintf($lang['Click_return_admin_index'],  '<a href="' . append_sid("index.php?pane=right") . '">', '</a>'));
	}
}

if ( @$error )
{
	$template->assign_vars(array('ERROR_MESSAGE' => $error_msg));
}

//
// Initial selection
//

$sql = "SELECT group_id, group_name
	FROM bb_groups
	WHERE group_single_user <> 1";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain list of groups', '', __LINE__, __FILE__, $sql);
}

$select_list = '<select name = "' . POST_GROUPS_URL . '"><option value = "-1">' . $lang['All_users'] . '</option>';
if ( $row = $db->sql_fetchrow($result) )
{
	do
	{
		$select_list .= '<option value = "' . $row['group_id'] . '">' . $row['group_name'] . '</option>';
	}
	while ( $row = $db->sql_fetchrow($result) );
}
$select_list .= '</select>';

//
// Generate page
//
require PAGE_HEADER;

$template->assign_vars(array(
	'MESSAGE' => $message,
	'SUBJECT' => $subject,

	'L_EMAIL_EXPLAIN' => $lang['Mass_email_explain'],
	'L_COMPOSE' => $lang['Compose'],
	'L_RECIPIENTS' => $lang['Recipients'],
	'L_EMAIL_SUBJECT' => $lang['Subject'],
	'L_NOTICE' => @$notice,

	'S_USER_ACTION' => append_sid('admin_mass_email.php'),
	'S_GROUP_SELECT' => $select_list)
);

print_page('admin_mass_email.tpl', 'admin');

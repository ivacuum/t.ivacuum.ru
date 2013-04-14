<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['Users']['Flags'] = basename(__FILE__);
	return;
}
require './pagestart.php';
// ACP Header - END

if( isset($_GET['mode']) || isset($_POST['mode']) )
{
	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
}
else
{
	//
	// These could be entered via a form button
	//
	if( isset($_POST['add']) )
	{
		$mode = "add";
	}
	else if( isset($_POST['save']) )
	{
		$mode = "save";
	}
	else
	{
		$mode = "";
	}
}

// if we are are doing a delete make sure we got confirmation
if ( $mode == 'do_delete')
{
	// user bailed out, return to flag admin
	if ( !$_POST['confirm'] )
	{
		$mode = '' ;
	}
}


if( $mode != "" )
{
	if( $mode == "edit" || $mode == "add" )
	{
		//
		// They want to add a new flag, show the form.
		//
		$flag_id = ( isset($_GET['id']) ) ? intval($_GET['id']) : 0;

		$s_hidden_fields = "";

		if( $mode == "edit" )
		{
			if( empty($flag_id) )
			{
				message_die(GENERAL_MESSAGE, $lang['Must_select_flag']);
			}

			$sql = "SELECT * FROM bb_flags
				WHERE flag_id = $flag_id";
			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Couldn't obtain flag data", "", __LINE__, __FILE__, $sql);
			}

			$flag_info = $db->sql_fetchrow($result);
			$s_hidden_fields .= '<input type="hidden" name="id" value="' . $flag_id . '" />';

		}

		$s_hidden_fields .= '<input type="hidden" name="mode" value="save" />';

		$template->assign_vars(array(
			'TPL_FLAGS_EDIT' => true,

			"FLAG" => $flag_info['flag_name'],
			"IMAGE" => ( $flag_info['flag_image'] != "" ) ? $flag_info['flag_image'] : "",
			"IMAGE_DISPLAY" => ( $flag_info['flag_image'] != "" ) ? '<img src="http://ivacuum.org/i/flags/48/' . $flag_info['flag_image'] . '" />' : "",

			"L_FLAGS_TITLE" => $lang['Flags_title'],
			"L_FLAGS_TEXT" => $lang['Flags_explain'],
			"L_FLAG_NAME" => $lang['Flag_name'],
			"L_FLAG_IMAGE" => $lang['Flag_image'],
			"L_FLAG_IMAGE_EXPLAIN" => $lang['Flag_image_explain'],

			"S_FLAG_ACTION" => append_sid("admin_flags.php"),
			"S_HIDDEN_FIELDS" => $s_hidden_fields)
		);

	}
	else if( $mode == "save" )
	{
		//
		// Ok, they sent us our info, let's update it.
		//

		$flag_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : 0;
		$flag_name = ( isset($_POST['title']) ) ? trim($_POST['title']) : "";
		$flag_image = ( (isset($_POST['flag_image'])) ) ? trim($_POST['flag_image']) : "";

		if( $flag_name == "" )
		{
			message_die(GENERAL_MESSAGE, $lang['Must_select_flag']);
		}

		//
		// The flag image has to be a jpg, gif or png
		//
		if($flag_image != "")
		{
			if ( !preg_match("/(\.gif|\.png|\.jpg)$/is", $flag_image))
			{
				$flag_image = "";
			}
		}

		if ($flag_id)
		{
			$sql = "UPDATE 
				SET flag_name = '" . str_replace("\'", "''", $flag_name) . "', flag_image = '" . str_replace("\'", "''", $flag_image) . "'
				WHERE flag_id = $flag_id";

			$message = $lang['Flag_updated'];
		}
		else
		{
			$sql = "INSERT INTO bb_flags (flag_name, flag_image)
				VALUES ('" . str_replace("\'", "''", $flag_name) . "', '" . str_replace("\'", "''", $flag_image) . "')";

			$message = $lang['Flag_added'];
		}

		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't update/insert into flags table", "", __LINE__, __FILE__, $sql);
		}

		$message .= "<br /><br />" . sprintf($lang['Click_return_flagadmin'], "<a href=\"" . append_sid("admin_flags.php") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.php?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);

	}
	else if( $mode == 'delete' )
	{
		if( isset($_POST['id']) || isset($_GET['id']) )
		{
			$flag_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : intval($_GET['id']);
		}
		else
		{
			$flag_id = 0;
		}
		$hidden_fields = '<input type="hidden" name="id" value="' . $flag_id . '" /><input type="hidden" name="mode" value="do_delete" />';

		print_confirmation(array(
			'QUESTION'      => $lang['Confirm_delete_flag'],
			'FORM_ACTION'   => "admin_flags.php",
			'HIDDEN_FIELDS' => $hidden_fields,
		));
	}
	else if( $mode == 'do_delete' )
	{

		//
		// Ok, they want to delete their flag
		//

		if( isset($_POST['id']) || isset($_GET['id']) )
		{
			$flag_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : intval($_GET['id']);
		}
		else
		{
			$flag_id = 0;
		}

		if( $flag_id )
		{
			// get the doomed flag's info
			$sql = "SELECT * FROM bb_flags
				WHERE flag_id = $flag_id" ;
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't get flag data", "", __LINE__, __FILE__, $sql);
			}
			$row = $db->sql_fetchrow($result);
			$flag_image = $row['flag_image'] ;


			// delete the flag
			$sql = "DELETE FROM bb_flags
				WHERE flag_id = $flag_id";

			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Couldn't delete flag data", "", __LINE__, __FILE__, $sql);
			}

			// update the users who where using this flag
			$sql = "UPDATE bb_users
				SET user_from_flag = ''
				WHERE user_from_flag = '$flag_image'";
			if( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, $lang['No_update_flags'], "", __LINE__, __FILE__, $sql);
			}

			$message = $lang['Flag_removed'] . "<br /><br />" . sprintf($lang['Click_return_flagadmin'], "<a href=\"" . append_sid("admin_flags.php") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.php?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);

		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['Must_select_flag']);
		}
	}
	else
	{
		//
		// They didn't feel like giving us any information. Oh, too bad, we'll just display the
		// list then...
		//
		$sql = "SELECT * FROM bb_flags
			ORDER BY flag_name";
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't obtain flags data", "", __LINE__, __FILE__, $sql);
		}

		$flag_rows = $db->sql_fetchrowset($result);
		$flag_count = count($flag_rows);

		$template->assign_vars(array(
			'TPL_FLAGS_LIST' => true,

			"L_FLAGS_TITLE" => $lang['Flags_title'],
			"L_FLAGS_TEXT" => $lang['Flags_explain'],
			"L_FLAG" => $lang['Flag_name'],

			"L_EDIT" => $lang['Edit'],
			"L_ADD_FLAG" => $lang['Add_new_flag'],
			"L_ACTION" => $lang['Action'],

			"S_FLAGS_ACTION" => append_sid("admin_flags.php"))
		);

		for( $i = 0; $i < $flag_count; $i++)
		{
			$flag = $flag_rows[$i]['flag_name'];
			$flag_id = $flag_rows[$i]['flag_id'];

			$row_class = !($i % 2) ? 'row1' : 'row2';

			$template->assign_block_vars("flags", array(
				"ROW_CLASS" => $row_class,

				"FLAG" => $flag,
				"IMAGE_DISPLAY" => ( $flag_rows[$i]['flag_image'] != "" ) ? '<img src="http://ivacuum.org/i/flags/48/' . $flag_rows[$i]['flag_image'] . '" />' : "",

				"U_FLAG_EDIT" => append_sid("admin_flags.php?mode=edit&amp;id=$flag_id"),
				"U_FLAG_DELETE" => append_sid("admin_flags.php?mode=delete&amp;id=$flag_id"))
			);
		}
	}
}
else
{
	//
	// Show the default page
	//
	$sql = "SELECT * FROM bb_flags
		ORDER BY flag_name ASC";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Couldn't obtain flags data", "", __LINE__, __FILE__, $sql);
	}
	$flag_count = $db->sql_numrows($result);

	$flag_rows = $db->sql_fetchrowset($result);

	$template->assign_vars(array(
		'TPL_FLAGS_LIST' => true,

		"L_FLAGS_TITLE" => $lang['Flags_title'],
		"L_FLAGS_TEXT" => $lang['Flags_explain'],
		"L_FLAG" => $lang['Flag_name'],
		"L_FLAG_PIC" => $lang['Flag_pic'],
		"L_EDIT" => $lang['Edit'],
		"L_ADD_FLAG" => $lang['Add_new_flag'],
		"L_ACTION" => $lang['Action'],

		"S_FLAGS_ACTION" => append_sid("admin_flags.php"))
	);

	for($i = 0; $i < $flag_count; $i++)
	{
		$flag = $flag_rows[$i]['flag_name'];
		$flag_id = $flag_rows[$i]['flag_id'];
		$row_class = !($i % 2) ? 'row1' : 'row2';

		$template->assign_block_vars("flags", array(
			"ROW_CLASS" => $row_class,
			"FLAG" => $flag,
			"IMAGE_DISPLAY" => ( $flag_rows[$i]['flag_image'] != "" ) ? '<img src="http://ivacuum.org/i/flags/48/' . $flag_rows[$i]['flag_image'] . '" />' : "",

			"U_FLAG_EDIT" => append_sid("admin_flags.php?mode=edit&amp;id=$flag_id"),
			"U_FLAG_DELETE" => append_sid("admin_flags.php?mode=delete&amp;id=$flag_id"))
		);
	}
}

print_page('admin_flags.tpl', 'admin');

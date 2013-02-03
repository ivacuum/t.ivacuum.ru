<?php

// ACP Header - START
if (!empty($setmodules))
{
	$module['General']['Word_Censor'] = basename(__FILE__);
	return;
}
require('./pagestart.php');
// ACP Header - END

if (!$bb_cfg['use_word_censor'])
{
	bb_die('Word Censor disabled <br /><br /> ($bb_cfg[\'use_word_censor\'] in config.php)');
}

if( isset($_GET['mode']) || isset($_POST['mode']) )
{
	$mode = ($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
	$mode = htmlspecialchars($mode);
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

if( $mode != "" )
{
	if( $mode == "edit" || $mode == "add" )
	{
		$word_id = ( isset($_GET['id']) ) ? intval($_GET['id']) : 0;

		$s_hidden_fields = '';

		if( $mode == "edit" )
		{
			if( $word_id )
			{
				$sql = "SELECT *
					FROM " . WORDS_TABLE . "
					WHERE word_id = $word_id";
				if(!$result = $db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, "Could not query words table", "Error", __LINE__, __FILE__, $sql);
				}

				$word_info = $db->sql_fetchrow($result);
				$s_hidden_fields .= '<input type="hidden" name="id" value="' . $word_id . '" />';
			}
			else
			{
				message_die(GENERAL_MESSAGE, $lang['No_word_selected']);
			}
		}

		$template->assign_vars(array(
			'TPL_ADMIN_WORDS_EDIT' => true,

			"WORD" => $word_info['word'],
			"REPLACEMENT" => $word_info['replacement'],

			"L_WORDS_TITLE" => $lang['Words_title'],
			"L_WORDS_TEXT" => $lang['Words_explain'],
			"L_WORD_CENSOR" => $lang['Edit_word_censor'],
			"L_WORD" => $lang['Word'],
			"L_REPLACEMENT" => $lang['Replacement'],

			"S_WORDS_ACTION" => append_sid("admin_words.php"),
			"S_HIDDEN_FIELDS" => $s_hidden_fields)
		);
	}
	else if( $mode == "save" )
	{
		$word_id = ( isset($_POST['id']) ) ? intval($_POST['id']) : 0;
		$word = ( isset($_POST['word']) ) ? trim($_POST['word']) : "";
		$replacement = ( isset($_POST['replacement']) ) ? trim($_POST['replacement']) : "";

		if($word == "" || $replacement == "")
		{
			message_die(GENERAL_MESSAGE, $lang['Must_enter_word']);
		}

		if( $word_id )
		{
			$sql = "UPDATE " . WORDS_TABLE . "
				SET word = '" . str_replace("\'", "''", $word) . "', replacement = '" . str_replace("\'", "''", $replacement) . "'
				WHERE word_id = $word_id";
			$message = $lang['Word_updated'];
		}
		else
		{
			$sql = "INSERT INTO " . WORDS_TABLE . " (word, replacement)
				VALUES ('" . str_replace("\'", "''", $word) . "', '" . str_replace("\'", "''", $replacement) . "')";
			$message = $lang['Word_added'];
		}

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, "Could not insert data into words table", $lang['Error'], __LINE__, __FILE__, $sql);
		}

		$message .= "<br /><br />" . sprintf($lang['Click_return_wordadmin'], "<a href=\"" . append_sid("admin_words.php") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.php?pane=right") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
	else if( $mode == "delete" )
	{
		if( isset($_POST['id']) ||  isset($_GET['id']) )
		{
			$word_id = ( isset($_POST['id']) ) ? $_POST['id'] : $_GET['id'];
			$word_id = intval($word_id);
		}
		else
		{
			$word_id = 0;
		}

		if( $word_id )
		{
			$sql = "DELETE FROM " . WORDS_TABLE . "
				WHERE word_id = $word_id";

			if(!$result = $db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, "Could not remove data from words table", $lang['Error'], __LINE__, __FILE__, $sql);
			}

			$message = $lang['Word_removed'] . "<br /><br />" . sprintf($lang['Click_return_wordadmin'], "<a href=\"" . append_sid("admin_words.php") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.php?pane=right") . "\">", "</a>");

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			message_die(GENERAL_MESSAGE, $lang['No_word_selected']);
		}
	}
}
else
{
	$sql = "SELECT *
		FROM " . WORDS_TABLE . "
		ORDER BY word";
	if( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, "Could not query words table", $lang['Error'], __LINE__, __FILE__, $sql);
	}

	$word_rows = $db->sql_fetchrowset($result);
	$word_count = count($word_rows);

	$template->assign_vars(array(
		'TPL_ADMIN_WORDS_LIST' => true,

		"L_WORDS_TITLE" => $lang['Words_title'],
		"L_WORDS_TEXT" => $lang['Words_explain'],
		"L_WORD" => $lang['Word'],
		"L_REPLACEMENT" => $lang['Replacement'],
		"L_EDIT" => $lang['Edit'],
		"L_ADD_WORD" => $lang['Add_new_word'],
		"L_ACTION" => $lang['Action'],

		"S_WORDS_ACTION" => append_sid("admin_words.php"),
		"S_HIDDEN_FIELDS" => '')
	);

	for($i = 0; $i < $word_count; $i++)
	{
		$word = $word_rows[$i]['word'];
		$replacement = $word_rows[$i]['replacement'];
		$word_id = $word_rows[$i]['word_id'];

		$row_class = !($i % 2) ? 'row1' : 'row2';

		$template->assign_block_vars("words", array(
			"ROW_CLASS" => $row_class,
			"WORD" => $word,
			"REPLACEMENT" => $replacement,

			"U_WORD_EDIT" => append_sid("admin_words.php?mode=edit&amp;id=$word_id"),
			"U_WORD_DELETE" => append_sid("admin_words.php?mode=delete&amp;id=$word_id"))
		);
	}
}

print_page('admin_words.tpl', 'admin');

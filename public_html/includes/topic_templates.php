<?php

if (!defined('IN_PHPBB')) die(basename(__FILE__));
if (!$post_info) die('$post_info missing');

require(DEFAULT_LANG_DIR .'lang_topic_templates.'. PHP_EXT);

function build_tpl_item ($item, $val)
{
	if (strpos($item, '--BR--') === 0)
	{
		return "\n\n";
	}
	if (!$val)
	{
		return '';
	}

	if (function_exists("tpl_func_$item"))
	{
		return call_user_func("tpl_func_$item", $item, $val);
	}
	else if (isset($GLOBALS['tpl_sprintf'][$item]))
	{
		return sprintf($GLOBALS['tpl_sprintf'][$item], $val);
	}
	else
	{
		return '[b]'. $GLOBALS['lang']['tpl'][$item] .'[/b]: '. $val ."\n";
	}
}

function tpl_build_message ($msg)
{
	$message = '';
	foreach ($msg as $item => $val)
	{
		if (is_array($item))
		{
			$name = array_keys($item);
			$item = $name[0];
		}
		$message .= build_tpl_item($item, $val);
	}
	return $message;
}

function tpl_func_screen_shots ($item, $val)
{
	if (!$val) return '';

	$img = preg_replace('#(?<=\s)(http\S+?(jpg|gif|png))(?=\s)#i', '[img]$1[/img]', " $val ");

//	return "\n[b]". $GLOBALS['lang']['tpl'][$item] ."[/b]: \n". trim($img) ."\n";
	return "\n[spoiler=\"" . $GLOBALS['lang']['tpl'][$item] . "\"]\n" . trim($img) . "\n[/spoiler]\n";
}

// get tpl data
$sql = "SELECT *
	FROM ". TOPIC_TPL_TABLE ."
	WHERE tpl_id = ". (int) $post_info['topic_tpl_id'];

if ($topic_tpl = $db->fetch_row($sql))
{
	$message = $subject = '';
	$tpl_script = basename($topic_tpl['tpl_script']);

	// this include() should return $message and $subject on submit
	require(INC_DIR ."topic_templates/$tpl_script.". PHP_EXT);

	$lang['tpl']['guide'] = array();
	@include(INC_DIR ."topic_templates/{$tpl_script}_guide.". PHP_EXT);

	if (isset($_REQUEST['preview']))
	{
		$_POST['subject'] = $subject;
		$_POST['message'] = $message;
	}
	else
	{
		require(INC_DIR .'topic_templates/tpl_selects.'. PHP_EXT);

		$template->assign_vars(array(
			'PAGE_TITLE'        => $lang['bt_new_release'],
			'FORUM_NAME'        => htmlCHR($post_info['forum_name']),
			'S_ACTION'          => append_sid("posting.$phpEx?mode=newtopic&tpl=1&". POST_FORUM_URL .'='. $post_info['forum_id']),
			'S_CANCEL_ACTION'   => append_sid(FORUM_URL . $post_info['forum_id']),
			'TORRENT_EXT'       => TORRENT_EXT,
			'TORRENT_EXT_LEN'   => strlen(TORRENT_EXT) + 1,
			'U_VIEW_FORUM'      => append_sid(FORUM_URL . $post_info['forum_id']),

			'REGULAR_TOPIC_BUTTON' => true, # (IS_MOD || IS_ADMIN),
			'REGULAR_TOPIC_HREF'   => append_sid("posting.$phpEx?mode=newtopic&". POST_FORUM_URL .'='. $post_info['forum_id']),
			'L_POST_REGULAR_TOPIC' => $lang['Post_regular_topic'],

			'L_BACK'            => $lang['bt_back'],
			'L_ERROR'           => $lang['bt_bad_fields'],
			'L_NEXT'            => $lang['bt_next'],
			'L_RELEASE_WELCOME' => $lang['bt_fill_form'],
			'L_TITLE'           => $lang['tpl']['release_name'],
			'L_TITLE_DESC'      => $lang['tpl']['release_name_desc'],
			'L_ORIGINAL_TITLE'  => $lang['tpl']['original_name'],
			'L_ORIGINAL_TITLE_DESC' => $lang['tpl']['original_name_desc'],
			'L_TITLE_EXP'       => $lang['tpl']['name_exp'],

			'TORRENT_SIGN'      => $bb_cfg['torrent_sign'],
		));

		if( $tpl_script == 'anime' )
		{
			$template->assign_vars(array(
				'S_ACTION' => append_sid("posting.$phpEx?mode=newtopic&". POST_FORUM_URL .'='. $post_info['forum_id']))
			);
		}

		foreach ($lang['tpl'] as $name => $val)
		{
			$template->assign_vars(array(
				'L_'. strtoupper($name) => $val,
			));
		}
		foreach ($lang['tpl']['guide'] as $name => $guide_post_id)
		{
			$template->assign_vars(array(
				strtoupper($name) .'_HREF' => append_sid(POST_URL ."$guide_post_id&amp;single=1#$guide_post_id"),
			));
		}

		$tpl_file = basename($topic_tpl['tpl_template']) .'.tpl';

		print_page("topic_templates/$tpl_file");
	}
}


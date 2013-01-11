<?php

global $bb_cfg, $page_cfg, $template, $images, $lang, $phpEx, $static_path;

$width = $height = array();
$template_name = basename(dirname(__FILE__));

if( !$static_path )
{
	$static_path = '//ivacuum.org';
	$s_provider  = isset($_SERVER['HTTP_PROVIDER']) ? $_SERVER['HTTP_PROVIDER'] : 'internet';

	if( $s_provider == 'local' )
	{
		$static_path = '//0.ivacuum.org';
	}
}

//$_main = BB_ROOT . basename(TEMPLATES_DIR) .'/'. $template_name .'/images/';
//$_lang = $_main . basename('lang_'. $bb_cfg['default_lang']) .'/';
$_main = $static_path . '/i/tracker/';
$_lang = $static_path . '/i/tracker/' . substr($bb_cfg['default_lang'], 0, 2) . '/';

// post_buttons
$images['icon_quote']          = $_lang .'icon_quote.gif';
$images['icon_edit']           = $_lang .'icon_edit.gif';
$images['icon_search']         = $_lang .'icon_search.gif';
$images['icon_profile']        = $_lang .'icon_profile.gif';
$images['icon_pm']             = $_lang .'icon_pm.gif';
$images['icon_email']          = $_lang .'icon_email.gif';
$images['icon_delpost']        = $_main .'icon_delete.gif';
$images['icon_ip']             = $_lang .'icon_ip.gif';
$images['icon_www']            = $_lang .'icon_www.gif';
$images['icon_icq']            = $_lang .'icon_icq_add.gif';
$images['icon_aim']            = $_lang .'icon_aim.gif';
$images['icon_yim']            = $_lang .'icon_yim.gif';
$images['icon_msnm']           = $_lang .'icon_msnm.gif';

// post_icons
$images['icon_minipost']       = $_main .'icon_minipost.gif';
$images['icon_gotopost']       = $_main .'icon_minipost.gif';
$images['icon_minipost_new']   = $_main .'icon_minipost_new.gif';
$images['icon_latest_reply']   = $_main .'icon_latest_reply.gif';
$images['icon_newest_reply']   = $_main .'icon_newest_reply.gif';

// forum_icons
$images['forum']               = $_main .'folder_big.gif';
$images['forum_new']           = $_main .'folder_new_big.gif';
$images['forum_locked']        = $_main .'folder_locked_big.gif';

// topic_icons
$images['folder']              = $_main .'folder.gif';
$images['folder_new']          = $_main .'folder_new.gif';
$images['folder_hot']          = $_main .'folder_hot.gif';
$images['folder_hot_new']      = $_main .'folder_new_hot.gif';
$images['folder_locked']       = $_main .'folder_lock.gif';
$images['folder_locked_new']   = $_main .'folder_lock_new.gif';
$images['folder_sticky']       = $_main .'folder_sticky.gif';
$images['folder_sticky_new']   = $_main .'folder_sticky_new.gif';
$images['folder_announce']     = $_main .'folder_announce.gif';
$images['folder_announce_new'] = $_main .'folder_announce_new.gif';
$images['folder_dl']           = $_main .'folder_dl.gif';
$images['folder_dl_new']       = $_main .'folder_dl_new.gif';
$images['folder_dl_hot']       = $_main .'folder_dl_hot.gif';
$images['folder_dl_hot_new']   = $_main .'folder_dl_hot_new.gif';

// posting_icons
$images['post_new']            = $_lang .'post.gif';
$images['post_locked']         = $_lang .'reply-locked.gif';
$images['reply_new']           = $_lang .'reply.gif';
$images['reply_locked']        = $_lang .'reply-locked.gif';

// pm_icons
$images['pm_inbox']            = $_main .'msg_inbox.gif';
$images['pm_outbox']           = $_main .'msg_outbox.gif';
$images['pm_savebox']          = $_main .'msg_savebox.gif';
$images['pm_sentbox']          = $_main .'msg_sentbox.gif';
$images['pm_readmsg']          = $_main .'folder.gif';
$images['pm_unreadmsg']        = $_main .'folder_new.gif';
$images['pm_replymsg']         = $_lang .'reply.gif';
$images['pm_postmsg']          = $_lang .'msg_newpost.gif';
$images['pm_quotemsg']         = $_lang .'icon_quote.gif';
$images['pm_editmsg']          = $_lang .'icon_edit.gif';
$images['pm_new_msg']          = '';
$images['pm_no_new_msg']       = '';

// topic_mod_icons will be replaced with SELECT later...
$images['topic_watch']         = '';
$images['topic_un_watch']      = '';
$images['topic_mod_lock']      = $_main .'topic_lock.gif';
$images['topic_mod_unlock']    = $_main .'topic_unlock.gif';
$images['topic_mod_split']     = $_main .'topic_split.gif';
$images['topic_mod_move']      = $_main .'topic_move.gif';
$images['topic_mod_delete']    = $_main .'topic_delete.gif';
$images['topic_dl']            = $_main .'topic_dl.gif';
$images['topic_normal']        = $_main .'topic_normal.gif';

$images['voting_graphic'][0]   = $_main .'voting_bar.gif';
$images['voting_graphic'][1]   = $_main .'voting_bar.gif';
$images['voting_graphic'][2]   = $_main .'voting_bar.gif';
$images['voting_graphic'][3]   = $_main .'voting_bar.gif';
$images['voting_graphic'][4]   = $_main .'voting_bar.gif';
$images['progress_bar']	       = $_main .'progress_bar.gif';
$images['progress_bar_full']	 = $_main .'progress_bar_full.gif';

// Vote graphic length defines the maximum length of a vote result graphic, ie. 100% = this length
$bb_cfg['vote_graphic_length']     = 205;
$bb_cfg['privmsg_graphic_length']  = 175;

$bb_cfg['topic_left_column_witdh'] = 150;
// Images auto-resize
$bb_cfg['post_img_width_decr']     = 52;    // decrement for posted images width (px)
$bb_cfg['attach_img_width_decr']   = 130;   // decrement for attach images width (px)

$template->assign_vars(array(
	'IMG' => $_main,

	'TEXT_BUTTONS'        => $bb_cfg['text_buttons'],
	'POST_BTN_SPACER'     => ($bb_cfg['text_buttons']) ? '&nbsp;' : '',
	'TOPIC_ATTACH_ICON'   => '<img src="' . $static_path . '/i/tracker/icon_clip.gif" alt="" />',
	'ATTACHMENT_ICON'     => '<img src="' . $static_path . '/i/tracker/icon_clip.gif" alt="" />',
	'OPEN_MENU_IMG_ALT1'  => '<img src="'. $_main .'menu_open_1.gif" class="menu-alt1" alt="" />',

	'TOPIC_LEFT_COL_SPACER_WITDH' => $bb_cfg['topic_left_column_witdh'] - 8,  // 8px padding
// Images auto-resize
	'POST_IMG_WIDTH_DECR_JS'      => $bb_cfg['topic_left_column_witdh'] + $bb_cfg['post_img_width_decr'],
	'ATTACH_IMG_WIDTH_DECR_JS'    => $bb_cfg['topic_left_column_witdh'] + $bb_cfg['attach_img_width_decr'],
));

// post_buttons
if (!empty($page_cfg['load_tpl_vars']) AND $vars = array_flip($page_cfg['load_tpl_vars']))
{
	if (isset($vars['post_buttons']))
	{
		$template->assign_vars(array(
			'QUOTE_IMG'       => ($bb_cfg['text_buttons']) ? $lang['Reply_with_quote_txtb'] : '<img src="'. $images['icon_quote']   .'" alt="Quote" title="'. $lang['Reply_with_quote'] .'" />',
			'EDIT_POST_IMG'   => ($bb_cfg['text_buttons']) ? $lang['Edit_delete_post_txtb'] : '<img src="'. $images['icon_edit']    .'" alt="Edit" title="'. $lang['Edit_post'] .'" />',
			'DELETE_POST_IMG' => ($bb_cfg['text_buttons']) ? $lang['Delete_post_txtb']      : '<img src="'. $images['icon_delpost'] .'" alt="Delete" title="'. $lang['Delete_post'] .'" />',
			'IP_POST_IMG'     => ($bb_cfg['text_buttons']) ? $lang['View_IP_txtb']          : '<img src="'. $images['icon_ip']      .'" alt="IP" title="'. $lang['View_IP'] .'" />',

			'QUOTE_URL'       => BB_ROOT ."posting.$phpEx?mode=quote&amp;p=",
			'EDIT_POST_URL'   => BB_ROOT ."posting.$phpEx?mode=editpost&amp;p=",
			'DELETE_POST_URL' => BB_ROOT ."posting.$phpEx?mode=delete&amp;p=",
			'IP_POST_URL'     => BB_ROOT ."modcp.$phpEx?mode=ip&amp;p=",

			'PROFILE_IMG'     => ($bb_cfg['text_buttons']) ? $lang['Read_profile_txtb']     : '<img src="'. $images['icon_profile'] .'" alt="Profile" title="'. $lang['Read_profile'] .'" />',
			'PM_IMG'          => ($bb_cfg['text_buttons']) ? $lang['Send_pm_txtb']          : '<img src="'. $images['icon_pm'] .'" alt="PM" title="'. $lang['Send_private_message'] .'" />',
			'EMAIL_IMG'       => ($bb_cfg['text_buttons']) ? $lang['Send_email_txtb']       : '<img src="'. $images['icon_email'] .'" alt="email" title="'. $lang['Send_email'] .'" />',
			'WWW_IMG'         => ($bb_cfg['text_buttons']) ? $lang['Visit_website_txtb']    : '<img src="'. $images['icon_www'] .'" alt="www" title="'. $lang['Visit_website'] .'" />',
			'ICQ_IMG'         => ($bb_cfg['text_buttons']) ? $lang['ICQ_txtb']              : '<img src="'. $images['icon_icq'] .'" alt="ICQ" title="'. $lang['ICQ'] .'" />',
			'AIM_IMG'         => ($bb_cfg['text_buttons']) ? $lang['AIM_txtb']              : '<img src="'. $images['icon_aim'] .'" alt="AIM" title="'. $lang['AIM'] .'" />',
			'MSN_IMG'         => ($bb_cfg['text_buttons']) ? $lang['MSNM_txtb']             : '<img src="'. $images['icon_msnm'] .'" alt="MSN" title="'. $lang['MSNM'] .'" />',
			'YIM_IMG'         => ($bb_cfg['text_buttons']) ? $lang['YIM_txtb']              : '<img src="'. $images['icon_yim'] .'" alt="YIM" title="'. $lang['YIM'] .'" />',

			'AIM_URL'         => 'aim:goim?screenname=',
			'EMAIL_URL'       => BB_ROOT ."profile.$phpEx?mode=email&amp;u=",
			'FORUM_URL'       => BB_ROOT . FORUM_URL,
			'ICQ_URL'         => 'http://wwp.icq.com/scripts/search.dll?to=',
			'MSN_URL'         => BB_ROOT . PROFILE_URL,
			'PM_URL'          => BB_ROOT . PM_URL,
			'PROFILE_URL'     => BB_ROOT . PROFILE_URL,
			'YIM_URL'         => 'http://edit.yahoo.com/config/send_webmesg?.src=pg&amp;.target=',
		));
	}
	if (isset($vars['post_icons']))
	{
		$template->assign_vars(array(
			'MINIPOST_IMG'      => '<img src="'. $images['icon_minipost']     .'" class="icon1" alt="post" />',
			'ICON_GOTOPOST'     => '<img src="'. $images['icon_gotopost']     .'" class="icon1" alt="goto" title="'. $lang['GOTO_PAGE'] .'" />',
			'MINIPOST_IMG_NEW'  => '<img src="'. $images['icon_minipost_new'] .'" class="icon1" alt="new" />',
			'ICON_LATEST_REPLY' => '<img src="'. $images['icon_latest_reply'] .'" class="icon2" alt="latest" title="'. $lang['View_latest_post'] .'" />',
			'ICON_NEWEST_REPLY' => '<img src="'. $images['icon_newest_reply'] .'" class="icon2" alt="newest" title="'. $lang['View_newest_post'] .'" />',
		));
	}
	if (isset($vars['topic_icons']))
	{
		$template->assign_vars(array(
			'MOVED'      => TOPIC_MOVED,
			'ANNOUNCE'   => POST_ANNOUNCE,
			'STICKY'     => POST_STICKY,
			'LOCKED'     => TOPIC_LOCKED,
			'L_MOVED'    => $lang['Topic_Moved'],
			'L_ANNOUNCE' => $lang['Topic_Announcement'],
			'L_DL_TOPIC' => $lang['Topic_DL'],
			'L_POLL'     => $lang['Topic_Poll'],
		));
	}
	if (isset($vars['pm_icons']))
	{
		$template->assign_vars(array(
			'INBOX_IMG'      => '<img src="'. $images['pm_inbox']   .'" class="pm_box_icon" alt="" />',
			'OUTBOX_IMG'     => '<img src="'. $images['pm_outbox']  .'" class="pm_box_icon" alt="" />',
			'SENTBOX_IMG'    => '<img src="'. $images['pm_sentbox'] .'" class="pm_box_icon" alt="" />',
			'SAVEBOX_IMG'    => '<img src="'. $images['pm_savebox'] .'" class="pm_box_icon" alt="" />',
		));
	}
}


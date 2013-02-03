<?php

if (!defined('SITE_DIR'))
{
	exit;
}

global $bb_cfg, $lang, $userdata, $gen_simple_header, $template, $db;
global $datastore, $bb_cache, $session_cache;

$logged_in = !empty($userdata['session_logged_in']);
$is_admin  = (IS_ADMIN === true);
$is_mod    = (IS_MOD === true);

if (!empty($template))
{
	$template->assign_vars(array(
		'SIMPLE_FOOTER'    => !empty($gen_simple_header),

		'TRANSLATION_INFO' => isset($lang['TRANSLATION_INFO']) ? $lang['TRANSLATION_INFO'] : '',
		'SHOW_ADMIN_LINK'  => ($is_admin && !defined('IN_ADMIN')),
		'ADMIN_LINK_HREF'  => "admin/index.php",
		'L_GOTO_ADMINCP'   => $lang['Admin_panel'],
	#!#
		'SHOW_BANNERS'     => (!DEBUG && (!($is_admin || $is_mod) || $userdata['user_id'] == 2)),
	));

	$template->set_filenames(array('page_footer' => 'page_footer.tpl'));
	$template->pparse('page_footer');
}

if (IS_ADMIN) # (DEBUG || @$_GET['dbg']);
{
	$show_dbg_info = true;
} else {
	$show_dbg_info = false;
}

if ($show_dbg_info)
{
#	echo '</textarea></form></title></comment></a></div></span></ilayer></layer></iframe></noframes></style></noscript></table></script></applet></font>';

	$gen_time = utime() - TIMESTART;
	$gen_time_txt = sprintf('%.3f', $gen_time);
	$gzip_text = (UA_GZIP_SUPPORTED) ? 'GZIP' : '<s>GZIP</s>';
	$gzip_text .= ($bb_cfg['gzip_compress']) ? ' ON' : ' OFF';
	$debug_text = (DEBUG) ? 'Debug ON' : 'Debug OFF';

	$stat = '[&nbsp; ';
	$stat .= "Execution time: $gen_time_txt sec ";

	if (!empty($db))
	{
		$sql_time = ($db->sql_timetotal) ? sprintf('%.3f sec (%d%%) in ', $db->sql_timetotal, round($db->sql_timetotal*100/$gen_time)) : '';
		$stat .= "&nbsp;|&nbsp; MySQL: {$sql_time}{$db->num_queries} queries";
	}

	$stat .= "&nbsp;|&nbsp; $gzip_text";

	if (MEM_USAGE)
	{
		$stat .= ' &nbsp;|&nbsp; Mem: ';
		$stat .= humn_size($bb_cfg['mem_on_start'], 2) .' / ';
		$stat .= (PHP_VERSION >= 5.2) ? humn_size(memory_get_peak_usage(), 2) .' / ' : '';
		$stat .= humn_size(memory_get_usage(), 2);
	}

	if (LOADAVG AND $l = explode(' ', LOADAVG))
	{
		for ($i=0; $i < 3; $i++)
		{
			$l[$i] = round($l[$i], 1);
			$l[$i] = ($is_admin && $bb_cfg['max_srv_load'] && $l[$i] > ($bb_cfg['max_srv_load'] + 4)) ? "<span style='color: red'><b>$l[$i]</b></span>" : $l[$i];
		}
		$stat .= " &nbsp;|&nbsp; Load: $l[0] $l[1] $l[2]";
	}

	$stat .= ' &nbsp;]';

	echo '<div style="padding: 6px; font-family: tahoma; font-size: 11px; color: #444444; letter-spacing: 0px; text-align: center;">'. $stat .'</div>';
}

echo '
	</div><!--/body_container-->
';

/*
if (DBG_USER && (SQL_DEBUG || PROFILER))
{
	require(INC_DIR . 'page_footer_dev.php');
}
$search = $replace = array();

function wbr_callback ($matches)
{
	$max_word_length = ($matches[3]) ? (int) $matches[3] : HTML_WBR_LENGTH;
	return wbr($matches[4], $max_word_length);
}
$contents = preg_replace_callback("#(<\!-- WBR(\[(\d+)\])? -->)(.*?)(<\!-- WBR_END -->)#s", 'wbr_callback', $contents);
*/

##### LOG #####
global $log_ip_resp;

if (isset($log_ip_resp[USER_IP]) || isset($log_ip_resp[CLIENT_IP]))
{
	$str = date('H:i:s') . LOG_SEPR . preg_replace("#\s+#", ' ', $contents) . LOG_LF;
	$file = 'sessions/'. date('m-d') .'_{'. USER_IP .'}_'. CLIENT_IP .'_resp';
	bb_log($str, $file);
}
### LOG END ###

if (!empty($GLOBALS['timer_markers']) && DBG_USER)
{
	$GLOBALS['timer']->display();
}

echo '
	</body>
	</html>
';

if (defined('REQUESTED_PAGE') && !defined('DISABLE_CACHING_OUTPUT'))
{
	if (IS_GUEST === true)
	{
		caching_output(true, 'store', REQUESTED_PAGE .'_guest');
	}
}

bb_exit();

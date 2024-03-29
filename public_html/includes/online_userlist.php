<?php

if (!defined('SITE_DIR'))
{
	exit;
}

// Obtain user/online information
$logged_online = $guests_online = 0;
$time_online = TIMENOW - 300;

$ulist = array(
	ADMIN        => array(),
	MOD          => array(),
	GROUP_MEMBER => array(),
	USER         => array(),
);

$users_cnt = array(
	'admin'        => 0,
	'mod'          => 0,
	'group_member' => 0,
	'ignore_load'  => 0,
	'user'         => 0,
	'guest'        => 0,
);

$online = $online_short = array('userlist' => '');

$sql = '
	SELECT
		COUNT(DISTINCT session_ip) AS ips
	FROM
		bb_sessions
	WHERE
		session_time > ' . $time_online . '
	AND
	    session_logged_in = 0';
$result = $db->sql_query($sql);

while ($u = $db->sql_fetchrow($result)) {
    $guests_online = $u['ips'];
    $users_cnt['guest'] = $guests_online;
}

$sql = '
	SELECT
		u.username,
		u.user_id,
		u.user_allow_viewonline,
		u.user_level,
		u.ignore_srv_load,
		s.session_logged_in,
		s.session_ip
	FROM
		bb_sessions s,
		bb_users u
	WHERE
		s.session_time > ' . $time_online . '
	AND
	    s.session_logged_in = 1
	AND
		u.user_id = s.session_user_id
	ORDER BY
		u.username';
$result = $db->sql_query($sql);

while( $u = $db->sql_fetchrow($result) )
{
	if( $u['session_logged_in'] )
	{
		$stat = array();
		$style_color = $class = '';
		$name = $u['username'];
		$level = $u['user_level'];

		if( $level == ADMIN )
		{
			$name = "<b>$name</b>";
			$class = ' class="colorAdmin"';
			$users_cnt['admin']++;
		}
		elseif( $level == MOD )
		{
			$name = "<b>$name</b>";
			$class = ' class="colorMod"';
			$users_cnt['mod']++;
		}
		elseif( $level == GROUP_MEMBER )
		{
			$name = "<b>$name</b>";
			$class = ' class="colorGroup"';
			$users_cnt['group_member']++;
		}
		elseif( $u['ignore_srv_load'] )
		{
			$class = ' class="colorISL"';
			$users_cnt['ignore_load']++;
		}
		else
		{
			$users_cnt['user']++;
		}

		/*
		if ($u['sessions'] > 3)
		{
			$color = ($u['sessions'] > 2) ? '#FF0000' : '#B22222';
			$s = $u['sessions'];
			$stat[] = "s:<span style=\"color: $color\">$s</span>";
		}
		if ($u['ips'] > 2)
		{
			$ip = $u['ips'];
			$stat[] = "ip:<span style=\"color: #0000FF\">$ip</span>";
		}
		if ($u['ses_len'] > 6*3600 && $level == USER)
		{
			$t = round($u['ses_len'] / 3600, 1);
			$stat[] = "t:<span style=\"color: #1E90FF\">$t</span>";
		}
		*/

		$h = PROFILE_URL . $u['user_id'];
		$u  = "<a href=\"$h\"$class$style_color>";
		$u .= ($stat) ? "$name<span class=\"ou_stat\" style=\"color: #707070\" title=\"{$u['session_ip']}\"> [<b>". join(', ', $stat) .'</b>]</span>' : $name;
		$u .= '</a>';
		$ulist[$level][] = $u;
	}
	else
	{
		$guests_online = $u['ips'];
		$users_cnt['guest'] = $guests_online;
	}
}

$db->sql_freeresult($result);

if( $ulist )
{
	$inline = $block = $short = array();

	foreach( $ulist as $level => $users )
	{
		if( empty($users) )
		{
			continue;
		}

		if( sizeof($users) > 200 )
		{
			$style = 'margin: 3px 0; padding: 2px 4px; border: 1px inset; height: 200px; overflow: auto;';
			$block[] = "<div style=\"$style\">\n". join(",\n", $users) ."</div>\n";
			$short[] = '<a href="/?online_full=1#online">'. $lang['Users'] .': '. count($users) .'</a>';
		}
		else
		{
			$inline[] = join(",\n", $users);
			$short[]  = join(",\n", $users);
		}

		$logged_online += count($users);
	}

	$online['userlist'] = join(",\n", $inline) . join("\n", $block);
	$online_short['userlist'] = join(",\n", $short);
}

if( !$online['userlist'] )
{
	$online['userlist'] = $online_short['userlist'] = $lang['None'];
}
elseif( isset($_REQUEST['f']) )
{
	$online['userlist'] = $online_short['userlist'] = $lang['Browsing_forum'] .' '. $online['userlist'];
}

$total_online = $logged_online + $guests_online;

if( $total_online > $bb_cfg['record_online_users'] )
{
	bb_update_config(array(
		'record_online_users' => $total_online,
		'record_online_date'  => TIMENOW,
	));
}

$online['stat'] = $online_short['stat'] = sprintf($lang['Online_users'], $total_online, $logged_online, $guests_online);

$online['cnt'] = $online_short['cnt'] = <<<HTML
[
	<span class="colorAdmin bold">{$users_cnt['admin']}</span> <span class="small">&middot;</span>
	<span class="colorMod bold">{$users_cnt['mod']}</span> <span class="small">&middot;</span>
	<span class="colorGroup bold">{$users_cnt['group_member']}</span> <span class="small">&middot;</span>
	<span class="colorISL">{$users_cnt['ignore_load']}</span> <span class="small">&middot;</span>
	<span>{$users_cnt['user']}</span> <span class="small">&middot;</span>
	<span><i>{$users_cnt['guest']}</i></span>
]
HTML;

$app['cache']->set('online', $online, 60);
$app['cache']->set('online_short', $online_short, 60);

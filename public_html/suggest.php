<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('IN_PHPBB', true);
$t_root_path = __DIR__ . '/';
require($t_root_path . 'common.php');

$user->session_start();

$q         = request_var('term', '');
$q_max_len = 60;

if( $q == 'поиск...' || !$q || mb_strlen($q) < 2 )
{
	bb_exit();
	// exit();
}

if( $tmp = mb_substr($q, 0, $q_max_len) )
{
	$tmp = preg_replace('#(?<=\S)\-#', ' ', $tmp);                    // "1-2-3" -> "1 2 3"
	$tmp = preg_replace('#[^\da-zA-Z\x{7f}-\x{ff}\-_*|]#', ' ', $tmp);    // допустимые символы (кроме " которые отдельно)
	$tmp = str_replace('-', ' -', $tmp);                              // - только в начале слова
	$tmp = str_replace('*', '* ', $tmp);                              // * только в конце слова
	$tmp = preg_replace('#\s*\|\s*#', '|', $tmp);                     // "| " -> "|"
	$tmp = preg_replace('#\|+#', ' | ', $tmp);                        // "||" -> "|"
	$tmp = preg_replace('#(?<=\s)[\-*]+\s#', ' ', $tmp);              // одиночные " - ", " * "
	$tmp = trim($tmp, ' -|');

	$q = str_compact($tmp);
}

if( !$q || mb_strlen($q) < 2 )
{
	bb_exit();
	// exit();
}

$ary = array();
$ary[] = array(
	'icon'  => 'magnifier',
	'label' => $q
);

$excluded_forums_csv = $user->get_excluded_forums(AUTH_READ);

require($t_root_path . 'includes/db/sphinx.php');

$sphinx = new db_sphinx();
$sphinx->connect($bb_cfg['sphinx']['host'], $bb_cfg['sphinx']['port'], $bb_cfg['sphinx']['socket']);

if( false === strpos($q, ' ') && false === strpos($q, '*') )
{
	$query = $q . '*';
}
else
{
	$query = $q;
}

$sql = "
	SELECT
		id
	FROM
		" . $bb_cfg['sphinx']['db_torrents'] . "
	WHERE
		MATCH('" . $query . "')";
if( $excluded_forums_csv )
{
	$sql .= ' AND forum_id NOT IN(' . $excluded_forums_csv . ')';
}
$sql .= ' ORDER BY seeders DESC';
$sql .= ' LIMIT 0, 10';
$sql .= ' OPTION ranker = none';
$result = $sphinx->query($sql);

$tor_list_ary = array();

while( $row = $sphinx->fetchrow($result) )
{
	$tor_list_ary[] = $row['id'];
}

$sphinx->freeresult($result);
$sphinx->close();

if( !sizeof($tor_list_ary) )
{
	bb_exit(json_encode($ary));
	// exit(json_encode($ary));
}

$tor_list_sql = join(',', $tor_list_ary);
$tor_count = sizeof($tor_list_ary);

if( $userdata['user_id'] != 2 )
{
	$sql = 'INSERT INTO ' . SEARCH_QUERIES_TABLE . ' (user_id, search_query, search_time, search_results, search_suggest) VALUES (' . $userdata['user_id'] . ', "' . $db->escape($q) . '", ' . time() . ', ' . $tor_count . ', 1)';
	$db->sql_query($sql);
}

$sql = '
	SELECT
		f.forum_icon,
		t.topic_id,
		t.topic_title
	FROM
		' . BT_TORRENTS_TABLE . ' tor
	LEFT JOIN
		' . TOPICS_TABLE . ' t ON (t.topic_id = tor.topic_id)
	LEFT JOIN
		' . FORUMS_TABLE . ' f ON (f.forum_id = tor.forum_id)
	WHERE
		tor.topic_id IN(' . $tor_list_sql . ')';
$result = $db->sql_query($sql);

while( $row = $db->sql_fetchrow($result) )
{
	$ary[] = array(
		'icon'  => ( $row['forum_icon'] ) ?: 'question_balloon',
		'label' => $row['topic_title'],
		'link'  => '/viewtopic.php?t=' . $row['topic_id']
	);
}

$db->sql_freeresult($result);

print json_encode($ary);

bb_exit();

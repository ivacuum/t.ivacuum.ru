<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

require 'common.php';

$user->session_start();

$q = $app['request']->variable('term', '');
$q_max_len = 60;

if ($q == 'поиск...' || !$q || mb_strlen($q) < 2)
{
	bb_exit();
}

if ($tmp = mb_substr($q, 0, $q_max_len))
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

if (!$q || mb_strlen($q) < 2)
{
	bb_exit();
}

$suggestions[] = [
	'icon'  => $static_path . '/i/_/magnifier.png',
	'label' => $q,
];

$excluded_forums_csv = $user->get_excluded_forums(AUTH_READ);
$excluded_forums_csv = $excluded_forums_csv ? " AND forum_id NOT IN ({$excluded_forums_csv})" : '';

$query = false === strpos($q, ' ') && false === strpos($q, '*') ? $q . '*' : $q;

$sql = 'SELECT id
	FROM torrents
	WHERE MATCH (?) :excluded
	ORDER BY seeders DESC
	LIMIT 0, 10
	OPTION ranker = none';
$app['sphinx']->query($sql, [$query, ':excluded' => $excluded_forums_csv]);

while ($row = $app['sphinx']->fetchrow())
{
	$tor_list_ary[] = $row['id'];
}

$app['sphinx']->freeresult();

if (empty($tor_list_ary))
{
	bb_exit(json_encode($suggestions));
}

$sql_ary = [
	'user_id'        => $userdata['user_id'],
	'search_query'   => $q,
	'search_time'    => time(),
	'search_results' => sizeof($tor_list_ary),
	'search_suggest' => 1,
];

// $sql = 'INSERT INTO bb_search_queries ' . $app['db']->build_array('INSERT', $sql_ary);
// $app['db']->query($sql);

$sql = '
	SELECT
		f.forum_icon,
		t.topic_id,
		t.topic_title
	FROM
		bb_bt_torrents tor
	LEFT JOIN
		bb_topics t ON (t.topic_id = tor.topic_id)
	LEFT JOIN
		bb_forums f ON (f.forum_id = tor.forum_id)
	WHERE
		:topic_id';
$app['db']->query($sql, [':topic_id' => $app['db']->in_set('tor.topic_id', $tor_list_ary)]);

while ($row = $app['db']->fetchrow())
{
	$suggestions[] = [
		'icon'  => $static_path . '/i/_/' . ($row['forum_icon'] ?: 'question_balloon') . '.png',
		'label' => $row['topic_title'],
		'link'  => '/viewtopic.php?t=' . $row['topic_id'],
	];
}

$app['db']->freeresult();

bb_exit(json_encode($suggestions));

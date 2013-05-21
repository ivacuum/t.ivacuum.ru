<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

define('BB_SCRIPT', 'stats');
require 'common.php';

// Session start
$user->session_start();

$data = [];
$feed = $app['request']->variable('feed', 'lenta');

switch ($feed)
{
	case 'bash':

		$data  = $app['cache']->get('rss_bash.im');
		$title = 'Последние цитаты с сайта bash.im';

	break;
	case 'beeline':

		$data  = $app['cache']->get('rss_mobile.beeline.ru');
		$title = 'Последние новости мобильных услуг Билайн';

	break;
	case 'beeline_internet':

		$data  = $app['cache']->get('rss_internet.beeline.ru');
		$title = 'Последние новости интернет услуг Билайн';

	break;
	/*
	case 'horoscope':

		$data  = $app['cache']->get('rss_horoscope');
		$title = 'Ежедневный гороскоп от Hyrax.ru';

	break;
	*/
	case 'ithappens':

		$data  = $app['cache']->get('rss_ithappens.ru');
		$title = 'Последние истории с сайта ithappens.ru';

	break;
	case 'lenta':

		$data  = $app['cache']->get('rss_lenta.ru');
		$title = 'Новости lenta.ru одной строкой';

	break;
	case 'nefart':

		$data  = $app['cache']->get('rss_nefart.ru');
		$title = 'Последние истории с сайта nefart.ru';

	break;
	default:

		$data = [];
		$feed = $title = '';
}

if (!empty($data))
{
	foreach ($data as $entry)
	{
		$entry['time'] = $entry['time'] ? create_date('d-M-y G:i', $entry['time']) : 0;
		$entry['icon'] = isset($entry['icon']) ? $entry['icon'] : '';
		$template->assign_block_vars('feed', array_change_key_case($entry, CASE_UPPER));
	}
}

$template->assign_vars([
	'FEED_NAME'  => $feed,
	'FEED_TITLE' => $title,
]);

print_page('feed.tpl');

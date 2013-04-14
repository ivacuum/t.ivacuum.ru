<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Новости Калуги с сайта gorodka.ru
*/
class gorodka extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://gorodka.ru/informer'))
		{
			return false;
		}
		
		$data = [];
		$n    = 0;

		foreach ($xml->channel->item as $entry)
		{
			if ($n > 4)
			{
				break;
			}
			
			$text = (string) str_replace(["\n", "\r", "\t", '  '], ' ', trim(strip_tags($entry->description)));
			$text = mb_strlen($text) > 200 ? mb_substr($text, 0, 200) . '...' : $text;
			
			$data[] = [
				'link'  => (string) $entry->link,
				'text'  => $text,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
			
			$n++;
		}

		$this->cache->set('rss_gorodka.ru', $data);
		
		return true;
	}
}

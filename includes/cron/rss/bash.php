<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Цитаты bash.im
*/
class bash extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://bash.im/rss/'))
		{
			return false;
		}
		
		$data = [];

		foreach ($xml->channel->item as $entry)
		{
			$data[] = [
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
		}

		$this->cache->set('rss_bash.im', $data);
		
		return true;
	}
}

<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Истории ithappens.ru
*/
class ithappens_ru extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://ithappens.ru/rss/'))
		{
			return false;
		}
		
		$data = array();

		foreach ($xml->channel->item as $entry)
		{
			$data[] = array(
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title
			);
		}

		$this->cache->set('rss_ithappens.ru', $data);
		
		return true;
	}
}

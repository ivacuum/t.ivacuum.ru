<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Истории nefart.ru
*/
class nefart_ru extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://feeds.feedburner.com/nefart/oanc?format=xml'))
		{
			return false;
		}
		
		$data = array();
		$n    = 0;

		foreach ($xml->channel->item as $entry)
		{
			if ($n > 49)
			{
				break;
			}

			$data[] = array(
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title
			);

			$n++;
		}

		$this->cache->set('rss_nefart.ru', $data);
		
		return true;
	}
}

<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Новости интернет услуг Билайн
*/
class internet_beeline extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://internet.beeline.ru/news/rss.wbp?Id=8f330f50-2574-43ff-aa4f-1555b54d87c2'))
		{
			return false;
		}
		
		$data = array();

		foreach ($xml->channel->item as $entry)
		{
			$data[] = array(
				'link'  => (string) $entry->link,
				'text'  => (string) trim($entry->description),
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title
			);
		}

		$this->cache->set('rss_internet.beeline.ru', $data);
		
		return true;
	}
}

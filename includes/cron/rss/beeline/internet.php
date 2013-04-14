<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Новости интернет услуг Билайн
*/
class beeline_internet extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://internet.beeline.ru/news/rss.wbp?Id=8f330f50-2574-43ff-aa4f-1555b54d87c2'))
		{
			return false;
		}
		
		$data = [];

		foreach ($xml->channel->item as $entry)
		{
			$data[] = [
				'link'  => (string) $entry->link,
				'text'  => (string) trim($entry->description),
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
		}

		$this->cache->set('rss_internet.beeline.ru', $data);
		
		return true;
	}
}

<?php namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Истории ithappens.ru
*/
class ithappens extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://ithappens.ru/rss/')) {
			return false;
		}
		
		$data = [];

		foreach ($xml->channel->item as $entry) {
			$data[] = [
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
		}

		$this->cache->set('rss_ithappens.ru', $data);
		
		return true;
	}
}

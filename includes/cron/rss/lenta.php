<?php namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Новости lenta.ru
*/
class lenta extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://lenta.ru/rss/')) {
			return false;
		}
		
		$data = [];

		foreach ($xml->channel->item as $entry) {
			$data[] = [
				'cat'   => (string) $entry->category,
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
		}

		$this->cache->set('rss_lenta.ru', $data);
		
		return true;
	}
}

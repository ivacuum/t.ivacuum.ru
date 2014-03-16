<?php namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Истории nefart.ru
*/
class nefart extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://feeds.feedburner.com/nefart/oanc?format=xml')) {
			return false;
		}
		
		$data = [];
		$n    = 0;

		foreach ($xml->channel->item as $entry) {
			if ($n > 49) {
				break;
			}

			$data[] = [
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];

			$n++;
		}

		$this->cache->set('rss_nefart.ru', $data);
		
		return true;
	}
}

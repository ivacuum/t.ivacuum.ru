<?php namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Новости мобильных услуг Билайн
*/
class beeline_mobile extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://mobile.beeline.ru/rss/russia.wbp')) {
			return false;
		}
		
		$data = [];

		foreach ($xml->channel->item as $entry) {
			$data[] = [
				'link'  => (string) '',
				'text'  => (string) trim($entry->description),
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title,
			];
		}

		$this->cache->set('rss_mobile.beeline.ru', $data);
		
		return true;
	}
}

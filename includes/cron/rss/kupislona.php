<?php namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Объявления kupislona.ru
*/
class kupislona extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('https://kupislona.ru/api/v1/posts/kp40?from=torrent')) {
			return false;
		}
		
		$data = [];

		foreach ($xml->post as $entry) {
			$data[] = [
				'title' => (string) $entry->title,
				'price' => (string) $entry->price,
				'thumb' => (string) $entry->thumbnail,
				'link'  => (string) $entry->permalink,
			];
		}

		$this->cache->set('kupislona_posts', array_slice($data, 0, 5));
		
		return true;
	}
}

<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Новости мобильных услуг Билайн
*/
class mobile_beeline extends rss
{
	public function run()
	{
		if( false === $xml = $this->get_rss_xml_data('http://mobile.beeline.ru/rss/russia.wbp') )
		{
			return false;
		}
		
		$data = array();

		foreach( $xml->channel->item as $entry )
		{
			$data[] = array(
				'link'  => (string) '',
				'text'  => (string) trim($entry->description),
				'time'  => (int) strtotime($entry->pubDate),
				'title' => (string) $entry->title
			);
		}

		$this->cache->set('rss_mobile.beeline.ru', $data);
		
		return true;
	}
}

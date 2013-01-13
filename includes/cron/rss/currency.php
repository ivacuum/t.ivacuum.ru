<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Курс валют
*/
class currency extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://www.cbr.ru/scripts/XML_daily.asp'))
		{
			return false;
		}
		
		$data = array();
		
		foreach ($xml->Valute as $entry)
		{
			$data[(string) $entry->CharCode] = array(
				'name'    => (string) $entry->Name,
				'nominal' => (int) $entry->Nominal,
				'value'   => (string) $entry->Value
			);
		}

		$this->cache->set('rss_currency', $data);
		
		return true;
	}
}

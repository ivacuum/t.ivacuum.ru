<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2011
*/

namespace app\cron\rss;

use engine\cron\tasks\rss;

/**
* Гороскоп
*/
class horoscope extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://www.hyrax.ru/cgi-bin/bn_xml5.cgi'))
		{
			return false;
		}
		
		$data = array();
		$i    = 0;

		foreach ($xml->channel->item as $entry)
		{
			switch ($i)
			{
				case 1:  $icon = 'zodiac_aries'; break;
				case 2:  $icon = 'zodiac_taurus'; break;
				case 3:  $icon = 'zodiac_gemini'; break;
				case 4:  $icon = 'zodiac_cancer'; break;
				case 5:  $icon = 'zodiac_leo'; break;
				case 6:  $icon = 'zodiac_virgo'; break;
				case 7:  $icon = 'zodiac_libra'; break;
				case 8:  $icon = 'zodiac_scorpio'; break;
				case 9:  $icon = 'zodiac_sagittarius'; break;
				case 10: $icon = 'zodiac_capricorn'; break;
				case 11: $icon = 'zodiac_aquarius'; break;
				case 12: $icon = 'zodiac_pisces'; break;

				default: $icon = '';
			}
			
			$data[] = array(
				'icon'  => $icon,
				'link'  => (string) $entry->link,
				'text'  => (string) $entry->description,
				'time'  => 0,
				'title' => (string) $entry->title
			);

			$i++;
		}

		$this->cache->set('rss_horoscope', $data);
		
		return true;
	}
}

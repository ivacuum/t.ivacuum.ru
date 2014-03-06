<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron\rss;

use fw\cron\tasks\rss;

/**
* Прогноз погоды
*/
class forecast extends rss
{
	public function run()
	{
		if (false === $xml = $this->get_rss_xml_data('http://informer.gismeteo.ru/xml/27703.xml')) {
			return false;
		}
		
		$data = [];

		foreach ($xml->REPORT->TOWN->FORECAST as $entry) {
			switch ((int) $entry->PHENOMENA['cloudiness']) {
				case 0:  $cloudiness = 'ясно'; break;
				case 1:  $cloudiness = 'малооблачно'; break;
				case 2:  $cloudiness = 'облачно'; break;
				case 3:  $cloudiness = 'пасмурно'; break;

				default: $cloudiness = '';
			}

			switch ((int) $entry->PHENOMENA['precipitation']) {
				case 4:  $precipitation = 'дождь'; break;
				case 5:  $precipitation = 'ливень'; break;
				case 6:
				case 7:  $precipitation = 'снег'; break;
				case 8:  $precipitation = 'гроза'; break;
				case 9:  $precipitation = ''; break;
				case 10: $precipitation = 'без осадков'; break;

				default: $precipitation = '';
			}

			switch ((int) $entry['tod']) {
				case 0:  $tod = 'ночь'; break;
				case 1:  $tod = 'утро'; break;
				case 2:  $tod = 'день'; break;
				case 3:  $tod = 'вечер'; break;

				default: $tod = '';
			}

			$icon = $this->get_icon($precipitation, $tod, $cloudiness);

			switch ((int) $entry->WIND['direction']) {
				case 0:  $winddeg = '270'; $winddir = 'северный'; break;
				case 1:  $winddeg = '225'; $winddir = 'северо-восточный'; break;
				case 2:  $winddeg = '180'; $winddir = 'восточный'; break;
				case 3:  $winddeg = '135'; $winddir = 'юго-восточный'; break;
				case 4:  $winddeg = '090'; $winddir = 'южный'; break;
				case 5:  $winddeg = '045'; $winddir = 'юго-западный'; break;
				case 6:  $winddeg = '000'; $winddir = 'западный'; break;
				case 7:  $winddeg = '315'; $winddir = 'северо-западный'; break;

				default: $winddeg = '270'; $winddir = '';
			}

			$tavg = intval(((int) $entry->TEMPERATURE['min'] + (int) $entry->TEMPERATURE['max']) / 2);
			$tavg -= $tavg % 4;

			switch ($tavg) {
				case -40: $tcolor = '86bae3'; break;
				case -36: $tcolor = '90bfe4'; break;
				case -32: $tcolor = '9bc5e5'; break;
				case -28: $tcolor = 'a0c7e6'; break;
				case -24: $tcolor = 'b0cfe8'; break;
				case -20: $tcolor = 'bbd5e9'; break;
				case -16: $tcolor = 'c5daea'; break;
				case -12: $tcolor = 'd0dfec'; break;
				case -8:  $tcolor = 'dae5ed'; break;
				case -4:  $tcolor = 'e5eaee'; break;
				case 0:   $tcolor = 'f0eff0'; break;
				case 4:   $tcolor = 'f1f0e9'; break;
				case 8:   $tcolor = 'f3f1e3'; break;
				case 12:  $tcolor = 'f5f2dc'; break;
				case 16:  $tcolor = 'f6f3d6'; break;
				case 20:  $tcolor = 'f8f4d0'; break;
				case 24:  $tcolor = 'f9eec0'; break;
				case 28:  $tcolor = 'f9e8b1'; break;
				case 32:  $tcolor = 'fae3a3'; break;
				case 36:  $tcolor = 'fbde96'; break;
				case 40:  $tcolor = 'fbd988'; break;
				case 44:  $tcolor = 'fcd47b'; break;
				case 48:  $tcolor = 'fdcf6e'; break;

				default:  $tcolor = 'f0eff0';
			}

			switch ((int) $entry['month']) {
				case 1:  $month = 'января'; break;
				case 2:  $month = 'февраля'; break;
				case 3:  $month = 'марта'; break;
				case 4:  $month = 'апреля'; break;
				case 5:  $month = 'мая'; break;
				case 6:  $month = 'июня'; break;
				case 7:  $month = 'июля'; break;
				case 8:  $month = 'августа'; break;
				case 9:  $month = 'сентября'; break;
				case 10: $month = 'октября'; break;
				case 11: $month = 'ноября'; break;
				case 12: $month = 'декабря'; break;

				default: $month = '';
			}

			$data[] = [
				'cloudiness'    => $cloudiness,
				'day'           => (int) $entry['day'],
				'icon'          => $icon,
				'month'         => $month,
				'pmax'          => (int) $entry->PRESSURE['max'],
				'pmin'          => (int) $entry->PRESSURE['min'],
				'precipitation' => $precipitation,
				'tcolor'        => $tcolor,
				'tmax'          => sprintf('%+d', (int) $entry->TEMPERATURE['max']),
				'tmin'          => sprintf('%+d', (int) $entry->TEMPERATURE['min']),
				'tod'           => $tod,
				'weekday'       => (int) $entry['weekday'],
				'wet'           => intval(((int) $entry->RELWET['min'] + (int) $entry->RELWET['max']) / 2),
				'wind'          => intval(((int) $entry->WIND['min'] + (int) $entry->WIND['max']) / 2),
				'winddeg'       => $winddeg,
				'winddir'       => $winddir,
			];
		}

		$this->cache->set('rss_forecast', $data);
		
		return true;
	}
	
	protected function get_icon($precipitation, $tod, $cloudiness)
	{
		if ($precipitation == 'дождь' || $precipitation == 'ливень') {
			return 'weather_rain';
		} elseif ($precipitation == 'гроза') {
			return 'weather_lightning';
		} elseif ($precipitation == 'снег') {
			return 'weather_snow';
		} else {
			if ($tod == 'ночь') {
				if ($cloudiness == 'ясно') {
					return 'weather_moon';
				} else {
					return 'weather_moon_clouds';
				}
			} else {
				if ($cloudiness == 'малооблачно') {
					return 'weather_cloudy';
				} elseif ($cloudiness == 'облачно') {
					return 'weather_cloud';
				} elseif ($cloudiness == 'пасмурно') {
					return 'weather_clouds';
				}
			}
		}
		
		return 'weather';
	}
}

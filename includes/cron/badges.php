<?php
/**
* @package t.ivacuum.ru
* @copyright (c) 2013
*/

namespace app\cron;

use fw\cron\task;

/**
* Присвоение лычек на основе статистики раздач
*/
class badges extends task
{
	public function run()
	{
		return true;
	}
}

<?php
/**
*
* @package zero.ivacuum.ru
* @copyright (c) 2011
*
*/

namespace app\models;

use engine\models\page as base_page;

class page extends base_page
{
	public function page_header()
	{
		parent::page_header();
		
		$this->template->assign(array(
			'S_BASE_JS_MTIME' => filemtime('/srv/www/vhosts/static.ivacuum.ru/js/bootstrap.js'),
			'S_STYLE_MTIME'   => filemtime('/srv/www/vhosts/static.ivacuum.ru/i/_/bootstrap.css'),
		));
	}
}

#!/usr/bin/local/php
<?php
/**
* @package ivacuum.ru
* @copyright (c) 2013
*/

namespace app;

/* Установка недостающих переменных */
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/public_html';
$_SERVER['SERVER_NAME'] = basename(__DIR__);

require('../_/fw/1.2.0/bootstrap.php');

/* Выполнение задач */
$app['cron']->run();

<?php

$app = array_merge($app, [
    /* Настройки подключения к БД */
    'db.options' => array_merge($app['db.options'], [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USERNAME'],
        'pass' => $_ENV['DB_PASSWORD'],
        'sock' => $_ENV['DB_SOCKET'],
    ]),

    /* Замена доменов на их локальные варианты при редиректе */
    'request.options' => array_merge($app['request.options'], [
        'local_redirect.from' => ['ivacuum.ru/', 't.local.ivacuum.ru/', 'local.local.'],
        'local_redirect.to'   => ['local.ivacuum.ru/', 't.ivacuum.ru/', 'local.'],
    ]),

    'urls' => array_merge($app['urls'], [
        'register'     => '//ivacuum.ru/ucp/register/',
        'signin'       => '//ivacuum.ru/ucp/signin/',
        'signout'      => '//ivacuum.ru/ucp/signout/',
        'static'       => '//ivacuum.org',
        'static_local' => '//0.ivacuum.org',
    ]),
]);

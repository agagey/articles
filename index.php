<?php
include('autoload.php');

use controller\Controller;

// Роутинг
$path = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$path = $path ? $path : 'index';

// Вывод контроллером контента
$controller = new Controller();
$content = $controller->{$path}();

echo $controller->makeView($content);

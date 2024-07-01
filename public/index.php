<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
define('APP_PATH', __DIR__ . '/../application/');
require __DIR__ . '/../thinkphp/start.php';

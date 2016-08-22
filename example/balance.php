<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


$apiKey = '*********** API_KEY **************';

require_once realpath(dirname(dirname(__FILE__))) . '/src/Anticaptcha/Service/AntiCaptcha.php';
$service = new \Anticaptcha\Service\AntiCaptcha($apiKey);

$ac = new \Anticaptcha\Anticaptcha($service);

echo $ac->balance();
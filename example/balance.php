<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


$apiKey = '*********** API_KEY **************';

$service = new \AntiCaptcha\Service\Antigate($apiKey);

$ac = new \AntiCaptcha\AntiCaptcha($service);

echo $ac->balance();
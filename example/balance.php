<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


$apiKey = '*********** API_KEY **************';

$service = new \AntiCaptcha\Service\AntiCaptcha($apiKey);

$ac = new \AntiCaptcha\AntiCaptcha($service);

echo "Your Balance is: " . $ac->balance() . "\n";
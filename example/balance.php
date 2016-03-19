<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


$apiKey = '*********** API_KEY **************';

$service = new \Anticaptcha\Service\Antigate($apiKey);

$ac = new \Anticaptcha\Anticaptcha($service);

echo $ac->balance();
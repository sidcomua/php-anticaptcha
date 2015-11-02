<?php

use Anticaptcha;

$apiKey = '*********** API_KEY **************';

$service = new Anticaptcha\Service\Antigate($apiKey);

$ac = new Anticaptcha($service);

echo $ac->balance();
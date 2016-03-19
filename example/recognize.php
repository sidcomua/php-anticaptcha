<?php

use Anticaptcha\Anticaptcha;

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


$image = file_get_contents(realpath(dirname(__FILE__)) . '/images/image.jpg');

$apiKey = '*********** API_KEY **************';

$ac = new Anticaptcha('rucaptcha', ['api_key' => $apiKey, 'debug' => true]);

echo $ac->recognize($image, null, ['phrase' => 0, 'numeric' => 0]);
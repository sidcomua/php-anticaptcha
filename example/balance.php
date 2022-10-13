<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';


use AntiCaptcha\AntiCaptcha;
use AntiCaptcha\Service\AntiCaptcha as AntiCaptchaService;
use AntiCaptcha\Exception\AntiCaptchaException;

$apiKey = '********** API_KEY *************';

$service = new AntiCaptchaService($apiKey);

try {
    $ac = new AntiCaptcha($service, [
        'debug' => true
    ]);

    echo "Your Balance is: " . $ac->balance() . "\n";
} catch (AntiCaptchaException $exception) {
    echo 'Error:' . $exception->getMessage();
}
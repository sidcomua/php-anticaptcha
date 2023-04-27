<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use AntiCaptcha\AntiCaptcha;
use AntiCaptcha\Exception\AntiCaptchaException;

$apiKey = '********** API_KEY *************';

try {
    $antiCaptchaClient = new AntiCaptcha(
        AntiCaptcha::SERVICE_ANTICAPTCHA,
        [
            'api_key' => $apiKey,
            'debug' => true
        ]
    );

    $task = new \AntiCaptcha\Task\RecaptchaV2EnterpriseTask(
        "http://makeawebsitehub.com/recaptcha/test.php",     // <-- target website address
        "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ"           // <-- recaptcha key from target website
    );

    $task->setEnterprisePayload([
        "s" => "SOME_ADDITIONAL_TOKEN"
    ]);

    $task->setProxy(
        "8.8.8.8",
        1234,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116",
        "http",
        "login",
        "password",
        null // also you can add cookie
    );

    $response = $antiCaptchaClient->recognizeTask($task);

    echo $response['gRecaptchaResponse'];
} catch (AntiCaptchaException $exception) {
    echo 'Error:' . $exception->getMessage();
}
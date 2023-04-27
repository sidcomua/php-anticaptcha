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

    $reCaptchaV3Task = new \AntiCaptcha\Task\RecaptchaV3Task(
        "http://makeawebsitehub.com/recaptcha/test.php",  // target website address
        "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ",      // recaptcha key from target website

        // Filters workers with a particular score. It can have one of the following values:
        // 0.3, 0.7, 0.9
        "0.3"
    );

    // Recaptcha's "action" value. Website owners use this parameter to define what users are doing on the page.
    $reCaptchaV3Task->setPageAction("myaction");

    // Set this flag to "true" if you need this V3 solved with Enterprise API. Default value is "false" and
    // Recaptcha is solved with non-enterprise API.
    $reCaptchaV3Task->setIsEnterprise(false);

    $response = $antiCaptchaClient->recognizeTask($reCaptchaV3Task);

    echo $response['gRecaptchaResponse'];
} catch (AntiCaptchaException $exception) {
    echo 'Error:' . $exception->getMessage();
}
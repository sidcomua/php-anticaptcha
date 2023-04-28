<?php

require_once realpath(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

use AntiCaptcha\AntiCaptcha;
use AntiCaptcha\Task\TurnstileTask;
use AntiCaptcha\Exception\AntiCaptchaException;

$apiKey = '********** API_KEY *************';

try {
    $acClient = new AntiCaptcha(AntiCaptcha::SERVICE_ANTICAPTCHA, ['api_key' => $apiKey, 'debug' => true]);

    $task = new TurnstileTask(
        // Address of a target web page. Can be located anywhere on the web site, even in a member area.
        // Our workers don't navigate there but simulate the visit instead.
        "http://makeawebsitehub.com/recaptcha/test.php",
        // Turnstile sitekey
        "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ"
    );

    // If you need setup proxy
    $task->setProxy(
        "8.8.8.8",
        1234,
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116",
        "http",
        "login",
        "password",
        null // also you can add cookie
    );

    // Optional "action" parameter.
    $task->setAction("myaction");

    $response = $acClient->recognizeTask($task);

    // Token string required for interacting with the submit form on the target website.
    echo $response['token'];  // 0.vtJqmZnvobaUzK2i2PyKaSqHELYtBZfRoPwMvLMdA81WL_9G0vCO3y2VQVIeVplG0mxYF7uX.......

    // User-Agent of worker's browser. Use it when you submit the response token.
    echo $response['userAgent'];  // Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:102.0) Gecko/20100101 Firefox/102.0
} catch (AntiCaptchaException $exception) {
    echo 'Error:' . $exception->getMessage();
}
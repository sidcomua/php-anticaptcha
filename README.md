<p align="center">
  <h1 align="center">Anticaptcha PHP SDK</h1>

  <p align="center">
    This library will help you to recognize any captcha via specific services.<br>
    Easy to install. Easy to use.
    <br/>
    <br/>
    <a href="https://packagist.org/packages/reilag/php-anticaptcha">packages.org</a>
    ·
    <a href="http://getcaptchasolution.com/zi0d4paljn">anti-captcha.com</a>
  </p>
</p>

---

[![Total Downloads](https://poser.pugx.org/reilag/php-anticaptcha/downloads)](https://packagist.org/packages/reilag/php-anticaptcha)
[![License](https://poser.pugx.org/reilag/php-anticaptcha/license)](https://packagist.org/packages/reilag/php-anticaptcha)
[![Latest Stable Version](https://poser.pugx.org/reilag/php-anticaptcha/v/stable)](https://packagist.org/packages/reilag/php-anticaptcha)

---


PHP client for Anticaptcha services:

* [anti-captcha.com](http://getcaptchasolution.com/zi0d4paljn) (recommend)


### Install

You can add Anticaptcha as a dependency using the **composer.phar** CLI:
```bash
# Install Composer (if need)
curl -sS https://getcomposer.org/installer | php

# Add dependency
composer require reilag/php-anticaptcha:^2.1.0
```


After installing, you need to require Composer's autoloader:
```php
require 'vendor/autoload.php';
```

You can find some examples at [/example](/example) path.

### Create Client
```php
use AntiCaptcha\AntiCaptcha;

// Your API key
$apiKey = '*********** API_KEY **************';

$antiCaptchaClient = new AntiCaptcha(
    AntiCaptcha::SERVICE_ANTICAPTCHA,
    [
        'api_key' => $apiKey,
        'debug' => true
    ]
);
```



### Recognize captcha from image

```php
use AntiCaptcha\AntiCaptcha;

// Get file content
$image = file_get_contents(realpath(dirname(__FILE__)) . '/images/image.jpg');

$imageText = $antiCaptchaClient->recognizeImage($image, null, ['phrase' => 0, 'numeric' => 0], 'en');

echo $imageText;
```



### Recognize reCaptcha V2 (with Proxy or without)

```php
$reCaptchaV2Task = new \AntiCaptcha\Task\RecaptchaV2Task(
    "http://makeawebsitehub.com/recaptcha/test.php",     // <-- target website address
    "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ"           // <-- recaptcha key from target website
);

// Value of 'data-s' parameter. Applies only to Recaptchas on Google web sites.
$reCaptchaV2Task->setRecaptchaDataSValue("some data s-value")

// Specify whether or not reCaptcha is invisible. This will render an appropriate widget for our workers. 
$reCaptchaV2Task->setIsInvisible(true);

// To use Proxy, use this function
$reCaptchaV2Task->setProxy(
    "8.8.8.8",
    1234,
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116",
    "http",
    "login",
    "password",
    // also you can add cookie
    "cookiename1=cookievalue1; cookiename2=cookievalue2" 
);

$response = $antiCaptchaClient->recognizeTask($reCaptchaV2Task);

echo $response['gRecaptchaResponse'];
```



### Recognize reCaptcha V3 (or V3 Enterprise)

```php
$reCaptchaV3Task = new \AntiCaptcha\Task\RecaptchaV3Task(
    "http://makeawebsitehub.com/recaptcha/test.php",  // target website address
    "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ",      // recaptcha key from target website

    // Filters workers with a particular score. It can have one of the following values:
    // 0.3, 0.7, 0.9
    "0.3"
);

// Recaptcha's "action" value. Website owners use this parameter to define what users are doing on the page.
$reCaptchaV3Task->pageAction("myaction");

// As V3 Enterprise is virtually the same as V3 non-Enterprise, we decided to roll out it’s support within the usual V3 tasks.
// Set this flag to "true" if you need this V3 solved with Enterprise API. Default value is "false" and
// Recaptcha is solved with non-enterprise API.
$reCaptchaV3Task->setIsEnterprise(false);

$response = $antiCaptchaClient->recognizeTask($reCaptchaV3Task);

echo $response['gRecaptchaResponse'];  // Return 3AHJ_VuvYIBNBW5yyv0zRYJ75VkOKvhKj9_xGBJKnQimF72rfoq3Iy-DyGHMwLAo6a3
```



### Recognize reCaptcha V2 Enterprise (with Proxy or without)

```php
$task = new \AntiCaptcha\Task\RecaptchaV2EnterpriseTask(
    "http://makeawebsitehub.com/recaptcha/test.php",     // <-- target website address
    "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ"           // <-- recaptcha key from target website
);

// Additional array parameters enterprisePayload
$task->setEnterprisePayload([
    "s" => "SOME_ADDITIONAL_TOKEN"
]);

// To use Proxy, use this function
$task->setProxy(
    "8.8.8.8",
    1234,
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116",
    "http",
    "login",
    "password",
    // also you can add cookie
    "cookiename1=cookievalue1; cookiename2=cookievalue2" 
);

$response = $antiCaptchaClient->recognizeTask($task);

echo $response['gRecaptchaResponse'];
```



### Get balance
```php
use AntiCaptcha\AntiCaptcha;

$apiKey = '*********** API_KEY **************';

$service = new \AntiCaptcha\Service\AntiCaptcha($apiKey);
$antiCaptchaClient = new \AntiCaptcha\AntiCaptcha($service);

echo "Your Balance is: " . $antiCaptchaClient->balance() . "\n";
```

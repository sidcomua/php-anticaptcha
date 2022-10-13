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
composer require reilag/php-anticaptcha:^1.3.0
```


After installing, you need to require Composer's autoloader:
```php
require 'vendor/autoload.php';
```

You can find some examples at [/example](/example) path.



### Recognize captcha from image

```php
use AntiCaptcha\AntiCaptcha;

// Get file content
$image = file_get_contents(realpath(dirname(__FILE__)) . '/images/image.jpg');

// Your API key
$apiKey = '*********** API_KEY **************';

$antiCaptchaClient = new AntiCaptcha(
    AntiCaptcha::SERVICE_ANTICAPTCHA,
    [
        'api_key' => $apiKey,
        'debug' => true
    ]
);

$imageText = $antiCaptchaClient->recognizeImage($image, null, ['phrase' => 0, 'numeric' => 0], 'en');

echo $imageText;
```



### Recognize reCaptcha V2

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

$reCaptchaV2Task = new \AntiCaptcha\Task\RecaptchaV2Task(
    "http://makeawebsitehub.com/recaptcha/test.php",     // <-- target website address
    "6LfI9IsUAAAAAKuvopU0hfY8pWADfR_mogXokIIZ"           // <-- recaptcha key from target website
);

$reCaptchaV2Task->setProxy(
    "8.8.8.8",
    1234,
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116",
    "http",
    "login",
    "password",
    null // also you can add cookie
);

$response = $antiCaptchaClient->recognizeTask($reCaptchaV2Task);

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

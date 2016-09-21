# Anticaptcha PHP SDK

PHP client for Anticaptcha services:

* [anti-captcha.com](http://anti-captcha.com) (recommend)
* [antigate.com](http://antigate.com)
* [captchabot.com](http://captchabot.com)
* [rucaptcha.com](http://rucaptcha.com)


### Install

You can add Anticaptcha as a dependency using the **composer.phar** CLI:
```
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add dependency
php composer.phar require reilag/php-anticaptcha:^1.0.0
```


After installing, you need to require Composer's autoloader:
```php
require 'vendor/autoload.php';
```

### Recognize captcha
```php
use AntiCaptcha\AntiCaptcha;

// Get file content
$image = file_get_contents(realpath(dirname(__FILE__)) . '/images/image.jpg');

// Your API key
$apiKey = '*********** API_KEY **************';

$antiCaptchaClient = new AntiCaptcha('rucaptcha', ['api_key' => $apiKey, 'debug' => true]);
echo $antiCaptchaClient->recognize($image, null, ['phrase' => 0, 'numeric' => 0]);
```

### Get balance
```php
use AntiCaptcha\AntiCaptcha;

$apiKey = '*********** API_KEY **************';

$service = new \AntiCaptcha\Service\Antigate($apiKey);
$antiCaptchaClient = new \AntiCaptcha\AntiCaptcha($service);

echo $antiCaptchaClient->balance();
```
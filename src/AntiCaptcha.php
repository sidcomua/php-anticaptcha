<?php namespace AntiCaptcha;

use AntiCaptcha\Exception\AntiCaptchaException;
use AntiCaptcha\Exception\InvalidAntiCaptchaServiceException;
use AntiCaptcha\Service\AbstractService;

use GuzzleHttp\Client;

use Psr\Log\AbstractLogger;


/**
 * Class AntiCaptcha
 * @package AntiCaptcha
 */
class AntiCaptcha
{

    /**
     * @var AbstractService $service
     */
    protected $service;

    /**
     * @var $client
     */
    protected $client;

    /**
     * @var AbstractLogger $logger
     * @deprecated
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debugMod = false;

    /**
     * @var array
     */
    protected $options =
        [
            'timeout_ready' => 3, // задержка между опросами статуса капчи
            'timeout_max' => 120, // время ожидания ввода капчи
        ];

    /**
     * Constants list
     */
    const SERVICE_ANTICAPTCHA   = 'anti-captcha';
    const SERVICE_ANTIGATE      = 'antigate';
    const SERVICE_CAPTCHABOT    = 'captchabot';
    const SERVICE_RUCAPTCHA     = 'rucaptcha';

    /**
     * Captcha service list
     *
     * @var array
     */
    protected static $serviceMap =
        [
            self::SERVICE_ANTICAPTCHA => Service\AntiCaptcha::class,
            self::SERVICE_ANTIGATE => Service\Antigate::class,
            self::SERVICE_CAPTCHABOT => Service\Captchabot::class,
            self::SERVICE_RUCAPTCHA => Service\Rucaptcha::class,
        ];

    /**
     * AntiCaptcha constructor.
     * @param null|string|AbstractService $service
     * @param array $options
     *
     * @throws AntiCaptchaException
     * @throws InvalidAntiCaptchaServiceException
     */
    public function __construct($service = null, array $options = [])
    {
        if (is_string($service))
        {
            $serviceName = ucfirst(strtolower($service));
            $serviceNamespace = __NAMESPACE__ . '\\Service\\' . $serviceName;

            if (array_key_exists($service, self::$serviceMap))
            {
                $serviceNamespace = self::$serviceMap[$service];
                $service = new $serviceNamespace;
            }
            else
            {
                throw new InvalidAntiCaptchaServiceException($service);
            }
        }

        if ($service)
        {
            $this->setService($service);

            if (!empty($options['api_key']))
            {
                $this->getService()->setApiKey($options['api_key']);
                unset($options['api_key']);
            }
        }

        if (!empty($options))
        {
            $this->options = array_merge($this->options, $options);
        }

        if (!empty($options['debug']))
        {
            $this->debugMod = true;
            $this->setLogger(new Logger);
        }

        // set Http Client
        $this->setClient(new Client);
    }


    /**
     * Anticaptcah service provider.
     * @param AbstractService $service
     *
     * @return $this
     */
    public function setService(AbstractService $service)
    {
        $this->service = $service;
        return $this;
    }


    /**
     * Method getService description.
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Logger.
     * @param AbstractLogger $logger
     *
     * @return $this
     */
    public function setLogger(AbstractLogger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * HttpClient.
     * @param $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }


    /**
     * Method balance description.
     *
     * @return mixed
     * @throws AntiCaptchaException
     */
    public function balance()
    {
        if( $this->debugMod )
        {
            $this->logger->debug("check ballans ...");
        }

        $url = $this->getService()->getApiUrl() . '/res.php';

        if( $this->debugMod )
        {
            $this->logger->debug('connect to: ' . $url);
        }

        $request = $this->client->request('GET', $url, [
            'query' =>
                [
                    'key' => $this->getService()->getApiKey(),
                    'action' => 'getbalance'
                ]
        ]);

        $body = $request->getBody();

        if( $this->debugMod )
        {
            $this->logger->debug('result: ' . $body);
        }


        if (strpos($body, 'ERROR') !== false)
        {
            throw new AntiCaptchaException($body);
        }

        return $body;
    }


    /**
     * Method recognize description.
     * @param $image
     * @param null $url
     * @param array $params
     *
     * @return string
     * @throws AntiCaptchaException
     */
    public function recognize($image, $url = null, $params = [])
    {
        if (null !== $url)
        {
            $request = $this->client->request('GET', $url);
            $image = $request->getBody();
        }

        if (!empty($params))
        {
            $this->getService()->setParams($params);
        }

        $captcha_id = $this->sendImage($image);

        if (!empty($captcha_id))
        {
            return $this->getResult($captcha_id);
        }
    }


    /**
     * Method sendImage description.
     * @param $image
     *
     * @return null
     * @throws AntiCaptchaException
     */
    protected function sendImage($image)
    {
        $postfields =
            [
                'form_params' =>
                    [
                        'key' => $this->getService()->getApiKey(),
                        'method' => 'base64',
                        'body' => base64_encode($image),
                    ]
            ];

        foreach ($this->getService()->getParams() as $key => $val)
        {
            $postfields['form_params'][$key] = (string)$val;
        }

        $url = $this->getService()->getApiUrl() . '/in.php';

        $result = $this->client->request('POST', $url, $postfields);
        $body = $result->getBody();

        if (stripos($body, 'ERROR') !== false)
        {
            throw new AntiCaptchaException($body);
        }

        if (stripos($body, 'html') !== false)
        {
            throw new AntiCaptchaException('Anticaptcha server returned error!');
        }

        if (stripos($body, 'OK') !== false)
        {
            $ex = explode("|", $body);
            if (trim($ex[0]) == 'OK')
            {
                return !empty($ex[1]) ? $ex[1] : null; // возвращаем captcha_id
            }
        }
    }


    /**
     * Method getResult description.
     * @param $captcha_id
     *
     * @return string
     * @throws AntiCaptchaException
     */
    protected function getResult($captcha_id)
    {
        if( $this->debugMod )
        {
            $this->logger->debug('captcha sent, got captcha ID: ' . $captcha_id);
        }

        // Delay, before first captcha check
        if( $this->debugMod )
        {
            $this->logger->debug('waiting for 10 seconds');
        }
        sleep(10);

        $waitTime = 0;

        while (true)
        {
            $request = $this->client->request('GET', $this->getService()->getApiUrl() . '/res.php', [
                'query' =>
                    [
                        'key' => $this->getService()->getApiKey(),
                        'action' => 'get',
                        'id' => $captcha_id,
                    ]
            ]);

            $body = $request->getBody();

            if (strpos($body, 'ERROR') !== false)
            {
                throw new AntiCaptchaException("Anticaptcha server returned error: $body");
            }

            if ($body == "CAPCHA_NOT_READY")
            {
                if( $this->debugMod )
                {
                    $this->logger->debug('captcha is not ready yet');
                }

                $waitTime += $this->options['timeout_ready'];

                if ($waitTime > $this->options['timeout_max'])
                {
                    if( $this->debugMod )
                    {
                        $this->logger->debug('timelimit (' . $this->options['timeout_max'] . ') hit');
                    }
                    break;
                }

                if( $this->debugMod )
                {
                    $this->logger->debug('waiting for ' . $this->options['timeout_ready'] . ' seconds');
                }
                sleep($this->options['timeout_ready']);
            }
            else
            {
                $ex = explode('|', $body);

                if (trim($ex[0]) == 'OK')
                {
                    $this->logger->debug('result: ' . $body);
                    return trim($ex[1]);
                }
            }
        }
    }



}
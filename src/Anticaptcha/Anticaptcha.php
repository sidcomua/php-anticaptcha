<?php 

namespace Anticaptcha;

use Anticaptcha\Service\AbstractService;
use Anticaptcha\ExceptionAnticaptcha as Exception;
use Anticaptcha\Logger;
use Psr\Log\AbstractLogger;
use GuzzleHttp\Client;


class Anticaptcha
{
    protected $service;
    protected $client;
    protected $logger;
    
    protected $options = [
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
    protected static $serviceMap = [
        self::SERVICE_ANTICAPTCHA   => Service\AntiCaptcha::class,
        self::SERVICE_ANTIGATE      => Service\Antigate::class,
        self::SERVICE_CAPTCHABOT    => Service\Captchabot::class,
        self::SERVICE_RUCAPTCHA     => Service\Rucaptcha::class,
    ];
    
    public function __construct($service = null, array $options = [])
    {
        if (is_string($service)) {
            $serviceName = ucfirst(strtolower($service));
            $serviceNamespace = __NAMESPACE__ . '\\Service\\' . $serviceName;
            
            if (array_key_exists($service, self::$serviceMap)) {
                $serviceNamespace = self::$serviceMap[$service];
                $service = new $serviceNamespace;
            } else {
                 throw new Exception('Anticaptcha service provider ' . $service . ' not found!');
            }
        }        
        
        if ($service) {
            $this->setService($service);      
            
            if (!empty($options['api_key'])) {
                $this->getService()->setApiKey($options['api_key']);
                unset($options['api_key']);
            }
        }
        
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        
        if (!empty($options['debug'])) {
            // set Logger
            $this->setLogger(new Logger);
        } else {
            $this->setLogger(new \Psr\Log\NullLogger);
        }
        
        // set Http Client
        $this->setClient(new Client);
    }
    
    /*
     * Anticaptcah service provider
     */
    public function setService(AbstractService $service)
    {
        $this->service = $service;
        
        return $this;
    }
    
    public function getService()
    {
        return $this->service;
    }
    
    /*
     * Logger
     */
    public function setLogger(AbstractLogger $logger)
    {
        $this->logger = $logger;
        
        return $this;
    }
    
    /*
     * HttpClient
     */    
    public function setClient(Client $client)
    {
        $this->client = $client;
        
        return $this;
    }
    
    public function balance()
    {
        $this->logger->debug("check ballans ...");
        
        $url = $this->getService()->getApiUrl() . '/res.php';
        $this->logger->debug('connect to: ' . $url);
        
        $request = $this->client->request('GET', $url, [
            'query' => [
                'key' => $this->getService()->getApiKey(), 
                'action' => 'getbalance'
            ]
        ]);
        
        $body = $request->getBody();
        
        $this->logger->debug('result: ' . $body);
        
        if (strpos($body, 'ERROR') !== false) {
            throw new Exception($body);
        }
       
        return $body;
    }
    
    public function recognize($image, $url = null, $params = [])
    {        
        // скачиваем картинку
        if (null !== $url) {
            $request = $this->client->request('GET', $url);
            $image = $request->getBody();
        }
        
        if (!empty($params)) {
            $this->getService()->setParams($params);
        }
        
        // отправляем картинку на сервер антикаптчи
        $captcha_id = $this->sendImage($image); 
       
        // получаем результат
        if (!empty($captcha_id)) {
            return $this->getResult($captcha_id);
        }        
    }
    
    protected function sendImage($image)
    {
        $postfields = [
            'form_params' => [
                'key' => $this->getService()->getApiKey(),
                'method' => 'base64',
                'body' => base64_encode($image),
            ]
        ];
    
        foreach ($this->getService()->getParams() as $key => $val) {
            $postfields['form_params'][$key] = (string) $val;
        }
    
        $url = $this->getService()->getApiUrl() . '/in.php';
    
        $result = $this->client->request('POST', $url, $postfields);
        $body = $result->getBody();
    
        if (stripos($body, 'ERROR') !== false) {
            throw new Exception($body);
        }
    
        if (stripos($body, 'html') !== false) {
            throw new Exception('Anticaptcha server returned error!');
        }
    
        if (stripos($body, 'OK') !== false) {
            $ex = explode("|", $body);
            if (trim($ex[0]) == 'OK') {
                return !empty($ex[1]) ? $ex[1] : null; // возвращаем captcha_id
            }
        }
    }
    
    protected function getResult($captcha_id)
    {
        $this->logger->debug('captcha sent, got captcha ID: ' . $captcha_id);
        
        // задержка перед первым опросом результата каптчи
        $this->logger->debug('waiting for 10 seconds');
        sleep(10); 
    
        // максимальное время опроса результата каптчи
        $waittime = 0;  
        
        while(true) {
            $request = $this->client->request('GET', $this->getService()->getApiUrl() . '/res.php', [
                'query' => [
                    'key' => $this->getService()->getApiKey(),
                    'action' => 'get',
                    'id' => $captcha_id,
                ]
            ]);
            
            $body = $request->getBody();
            
            if (strpos($body, 'ERROR') !== false) {
                throw new Exception("Anticaptcha server returned error: $body");
            }
    
            if ($body == "CAPCHA_NOT_READY")  {
                $this->logger->debug('captcha is not ready yet');
    
                $waittime += $this->options['timeout_ready'];
    
                if ($waittime > $this->options['timeout_max']) {
                    $this->logger->debug('timelimit (' . $this->options['timeout_max'] . ') hit');
                    break;
                }
    
                $this->logger->debug('waiting for ' . $this->options['timeout_ready'] . ' seconds');
                sleep($this->options['timeout_ready']);
            } 
            else {
                $ex = explode('|', $body);
    
                if (trim($ex[0]) == 'OK') {
                    $this->logger->debug('result: ' . $body);
                    return trim($ex[1]);
                }
            }
        }
    }
}
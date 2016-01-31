<?php 

namespace Anticaptcha;

use Anticaptcha\Service\AbstractService;
use Anticaptcha\Exception\Anticaptcha as Exception;
use Anticaptcha\Http\Client as HttpClient;


class Anticaptcha
{
    protected $debug = false; // режим отладки
    
    protected $service;
    protected $client;
    
    protected $options = [
        'timeout_ready' => 3, // задержка между опросами статуса капчи
        'timeout_max' => 120, // время ожидания ввода капчи 
    ];
    
    public function __construct($service = null, $options = [])
    {
        if (is_string($service)) {
            $serviceName = ucfirst(strtolower($service));
            $serviceNamespace = __NAMESPACE__ . '\\Service\\' . $serviceName;
            $service = new $serviceNamespace;
        }        
        
        $this->setService($service);      
        
        if (!empty($options['api_key'])) {
            $this->getService()->setApiKey($options['api_key']);
            unset($options['api_key']);
        }
        
        if (!empty($options['debug'])) {
            $this->setDebug($options['debug']);
            unset($options['debug']);
        }
        
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }
    
    public function setService(AbstractService $service)
    {
        $this->service = $service;
        
        return $this;
    }
    
    public function getService()
    {
        return $this->service;
    }
    
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
    
        return $this;
    }
    
    public function getDebug()
    {
        return (bool) $this->debug;
    }
    
    /*
     * HttpClient
     */
    protected function client()
    {
        if (null === $this->client) {
            $this->client = new HttpClient();
        }
        return $this->client;
    }
    
    public function balance()
    {
        $this->log("check ballans ...");
        
        $url = $this->getService()->getApiUrl() . '/res.php';
        $this->log('connect to', $url);
        
        $request = $this->client()->request('GET', $url, [
            'query' => [
                'key' => $this->getService()->getApiKey(), 
                'action' => 'getbalance'
            ]
        ]);
        
        $body = $request->getBody();
        
        $this->log('result:', $body);
        
        if (strpos($body, 'ERROR') !== false) {
            throw new Exception($body);
        }
       
        return $body;
    }
    
    public function recognize($image, $url = null, $params = [])
    {        
        // скачиваем картинку
        if (null !== $url) {
            $request = $this->client()->request('GET', $url);
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
        // формируем тело запроса
        //$poststr = $this->_createSendContent($image);
        
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
    
        if ($this->getDebug()) {
            $postfields['debug'] = true;
        }
    
        foreach ($this->getService()->getParams() as $key => $val) {
            $postfields['form_params'][$key] = (string) $val;
        }
    
        $url = $this->getService()->getApiUrl() . '/in.php';
    
        $result = $this->client()->request('POST', $url, $postfields);
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
        $this->log('captcha sent, got captcha ID:', $captcha_id);
        $this->log('waiting for 10 seconds');
    
        $waittime = 0;
        sleep(10);
    
        while(true) {
            $request = $this->client()->request('GET', $this->getService()->getApiUrl() . '/res.php', [
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
                $this->log('captcha is not ready yet');
    
                $waittime += $this->options['timeout_ready'];
    
                if ($waittime > $this->options['timeout_max']) {
                    $this->log('timelimit (' . $this->options['timeout_max'] . ') hit');
                    break;
                }
    
                $this->log('waiting for ' . $this->options['timeout_ready'] . ' seconds');
                sleep($this->options['timeout_ready']);
            } else {
                $ex = explode('|', $body);
    
                if (trim($ex[0]) == 'OK') {
                    $this->log('result:', $body);
                    return trim($ex[1]);
                }
            }
        }
    }
    
    protected function log()
    {
        if ($this->getDebug()) {
            echo implode(' ', func_get_args()) . "\n";
        }
    }
}
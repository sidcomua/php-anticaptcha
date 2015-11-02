<?php 

namespace Anticaptcha\Service;


class AbstractService
{
    /*
     * @var string
     */
    protected $api_key;    
    
    /*
     * @var string
     */
    protected $api_url;

    protected $params = [
        'phrase'   => 0, // 0 либо 1 - флаг "в капче 2 и более слов"
        'regsense' => 0, // 0 либо 1 - флаг "регистр букв в капче имеет значение"
        'numeric'  => 0, // 0 либо 1 - флаг "капча состоит только из цифр"
        'calc'     => 0, // 0 либо 1 - помечает что цифры на капче должны быть сплюсованы
        'min_len'  => 0, // 0 (без ограничений), любая другая цифра указывает минимальную длину текста капчи
        'max_len'  => 0, // 0 (без ограничений), любая другая цифра указывает максимальную длину текста капчи
    ];
    
    public function __construct($api_key = null, $api_url = null)
    {
        $this->setApiKey($api_key);
        
        if (null !== $api_url) {
            $this->setApiUrl($api_url);
        }
    }
    
    /*
     * @param string $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    
        return $this;
    }
    
    /*
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /*
     * @param string $api_url
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
    
        return $this;
    }
    
    /*
     * @return string
     */
    public function getApiUrl()
    {
        return rtrim($this->api_url, '/');
    }
    
    /*
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
        
        return $this;
    }
    
    /*
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
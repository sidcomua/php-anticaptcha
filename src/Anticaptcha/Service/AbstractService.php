<?php

namespace Anticaptcha\Service;

/**
 * Class AbstractService
 * @package Anticaptcha\Service
 */
class AbstractService
{
    /**
     * @var string
     */
    protected $api_key;

    /**
     * @var string
     */
    protected $api_url;


    /**
     * @var array
     */
    protected $params = [
        'phrase' => 0, // 0 либо 1 - флаг "в капче 2 и более слов"
        'regsense' => 0, // 0 либо 1 - флаг "регистр букв в капче имеет значение"
        'numeric' => 0, // 0 либо 1 - флаг "капча состоит только из цифр"
        'calc' => 0, // 0 либо 1 - помечает что цифры на капче должны быть сплюсованы
        'min_len' => 0, // 0 (без ограничений), любая другая цифра указывает минимальную длину текста капчи
        'max_len' => 0, // 0 (без ограничений), любая другая цифра указывает максимальную длину текста капчи
    ];

    /**
     * AbstractService constructor.
     * @param null $api_key
     * @param null $api_url
     */
    public function __construct($api_key = null, $api_url = null)
    {
        $this->setApiKey($api_key);

        if (null !== $api_url) {
            $this->setApiUrl($api_url);
        }
    }

    /**
     * Method setApiKey description.
     * @param $api_key
     *
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Method getApiKey description.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Method setApiUrl description.
     * @param $api_url
     *
     * @return $this
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;

        return $this;
    }

    /**
     * Method getApiUrl description.
     *
     * @return string
     */
    public function getApiUrl()
    {
        return rtrim($this->api_url, '/');
    }

    /**
     * Method setParams description.
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Method getParams description.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
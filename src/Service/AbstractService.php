<?php

namespace AntiCaptcha\Service;

/**
 * Class AbstractService
 * @package AntiCaptcha\Service
 */
class AbstractService
{
    /** @var string $apiKey */
    protected $apiKey;

    /** @var string $apiUrl */
    protected $apiUrl;


    /**
     * @var array
     */
    protected $params = [
        'phrase' => 0, // 0 or 1 - флаг "в капче 2 и более слов"
        'regsense' => 0, // 0 or 1 - флаг "регистр букв в капче имеет значение"
        'numeric' => 0, // 0 or 1 - флаг "капча состоит только из цифр"
        'calc' => 0, // 0 or 1 - помечает что цифры на капче должны быть сплюсованы
        'min_len' => 0, // 0 (без ограничений), любая другая цифра указывает минимальную длину текста капчи
        'max_len' => 0, // 0 (без ограничений), любая другая цифра указывает максимальную длину текста капчи
    ];

    /**
     * AbstractService constructor.
     * @param null $apiKey
     * @param null $apiUrl
     */
    public function __construct($apiKey = null, $apiUrl = null)
    {
        $this->setApiKey($apiKey);

        if (null !== $apiUrl) {
            $this->setApiUrl($apiUrl);
        }
    }

    /**
     * Method setApiKey description.
     * @param $apiKey
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Method getApiKey description.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Method setApiUrl description.
     * @param $apiUrl
     *
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Method getApiUrl description.
     *
     * @return string
     */
    public function getApiUrl()
    {
        return rtrim($this->apiUrl, '/');
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
<?php

namespace AntiCaptcha\Task;

use GuzzleHttp\Handler\Proxy;

class RecaptchaV2Task extends AbstractTask
{
    /** @var string $websiteUrl */
    protected $websiteUrl;

    /** @var string $websiteKey */
    protected $websiteKey;

    /** @var string $recaptchaDataSValue */
    protected $recaptchaDataSValue;

    /** @var boolean $isInvisible */
    protected $isInvisible;

    /** @var array $proxyParams */
    protected $proxyParams = [];

    /**
     * @param string $websiteUrl
     * @param string$websiteKey
     *
     * @return void
     */
    public function __construct(string $websiteUrl, string $websiteKey)
    {
        $this->websiteUrl = $websiteUrl;
        $this->websiteKey = $websiteKey;
    }


    public function setIsInvisible(bool $isInvisible)
    {
        $this->isInvisible = $isInvisible;
    }

    public function setRecaptchaDataSValue($recaptchaDataSValue)
    {
        $this->recaptchaDataSValue = $recaptchaDataSValue;
    }

    public function setProxy(
        string $proxyAddress,
        string $proxyPort,
        string $userAgent,
        string $proxyType = 'http',
        string $proxyLogin = null,
        string $proxyPassword = null,
        string $cookies = null
    )
    {
        $this->proxyParams['proxyAddress'] = $proxyAddress;
        $this->proxyParams['proxyPort'] = $proxyPort;
        $this->proxyParams['userAgent'] = $userAgent;
        $this->proxyParams['proxyType'] = $proxyType;

        if (!empty($proxyLogin)) {
            $this->proxyParams['proxyLogin'] = $proxyLogin;
        }

        if (!empty($proxyPassword)) {
            $this->proxyParams['proxyPassword'] = $proxyPassword;
        }

        if (!empty($cookies)) {
            $this->proxyParams['cookies'] = $cookies;
        }
    }

    /**
     * @return array
     */
    public function getTaskParams()
    {
        $data = [];

        $data['type'] = empty($this->proxyParams) ? 'RecaptchaV2TaskProxyless' : 'RecaptchaV2Task';
        $data['websiteURL'] = $this->websiteUrl;
        $data['websiteKey'] = $this->websiteKey;

        if (!is_null($this->recaptchaDataSValue)) {
            $data['recaptchaDataSValue'] = $this->recaptchaDataSValue;
        }

        if (!is_null($this->isInvisible)) {
            $data['isInvisible'] = $this->isInvisible;
        }

        if (!empty($this->proxyParams)) {
            $data = array_merge($data, $this->proxyParams);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function otherRequestParams()
    {
        return [];
    }
}

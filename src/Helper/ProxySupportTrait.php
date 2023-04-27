<?php

namespace AntiCaptcha\Helper;

trait ProxySupportTrait
{
    /** @var array $proxyParams */
    protected $proxyParams = [];

    /**
     * @return bool
     */
    public function useProxy()
    {
        return !empty($this->proxyParams);
    }

    /**
     * @param string $proxyAddress
     * @param string $proxyPort
     * @param string $userAgent
     * @param string $proxyType
     * @param ?string $proxyLogin
     * @param ?string $proxyPassword
     * @param ?string $cookies
     *
     * @return void
     */
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
        $this->proxyParams['proxyType'] = $proxyType;
        $this->proxyParams['proxyAddress'] = $proxyAddress;
        $this->proxyParams['proxyPort'] = $proxyPort;
        $this->proxyParams['userAgent'] = $userAgent;

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
}

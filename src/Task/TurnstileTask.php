<?php

namespace AntiCaptcha\Task;

use AntiCaptcha\Helper\ProxySupportTrait;

class TurnstileTask extends AbstractTask
{
    use ProxySupportTrait;

    /** @var string $websiteUrl */
    protected $websiteUrl;

    /** @var string $websiteKey */
    protected $websiteKey;

    /** @var string $action */
    protected $action;


    /**
     * @param string $websiteUrl
     * @param string $websiteKey
     */
    public function __construct(string $websiteUrl, string $websiteKey)
    {
        $this->websiteUrl = $websiteUrl;
        $this->websiteKey = $websiteKey;
    }

    /**
     * Optional "action" parameter
     *
     * @param string $action
     *
     * @return void
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }


    /**
     * @return array
     */
    public function getTaskParams()
    {
        $data = [];

        $data['type'] = $this->useProxy() ? 'TurnstileTask' : 'TurnstileTaskProxyless';
        $data['websiteURL'] = $this->websiteUrl;
        $data['websiteKey'] = $this->websiteKey;

        if (!is_null($this->action)) {
            $data['action'] = $this->action;
        }

        if ($this->useProxy()) {
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

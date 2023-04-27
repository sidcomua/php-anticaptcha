<?php

namespace AntiCaptcha\Task;

use AntiCaptcha\Helper\ProxySupportTrait;

/**
 * Class RecaptchaV2EnterpriseTask.
 */
class RecaptchaV2EnterpriseTask extends AbstractTask
{
    use ProxySupportTrait;

    /** @var string $websiteUrl */
    protected $websiteUrl;

    /** @var string $websiteKey */
    protected $websiteKey;

    /** @var array $enterprisePayload */
    protected $enterprisePayload;

    /** @var string $apiDomain */
    protected $apiDomain;

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

    public function setEnterprisePayload(array $enterprisePayload)
    {
        $this->enterprisePayload = $enterprisePayload;
    }

    public function setApiDomain(string $apiDomain)
    {
        $this->apiDomain = $apiDomain;
    }

    /**
     * @return array
     */
    public function getTaskParams()
    {
        $data = [];

        $data['type'] = $this->useProxy() ? 'RecaptchaV2EnterpriseTask' : 'RecaptchaV2EnterpriseTaskProxyless';
        $data['websiteURL'] = $this->websiteUrl;
        $data['websiteKey'] = $this->websiteKey;

        if (!is_null($this->enterprisePayload)) {
            $data['enterprisePayload'] = $this->enterprisePayload;
        }

        if (!is_null($this->apiDomain)) {
            $data['apiDomain'] = $this->apiDomain;
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

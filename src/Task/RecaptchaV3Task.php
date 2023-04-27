<?php

namespace AntiCaptcha\Task;

use GuzzleHttp\Handler\Proxy;

class RecaptchaV3Task extends AbstractTask
{
    /** @var string $websiteUrl */
    protected $websiteUrl;

    /** @var string $websiteKey */
    protected $websiteKey;

    /** @var string $minScore */
    protected $minScore;

    /** @var string $pageAction */
    protected $pageAction;

    /** @var boolean $isEnterprise */
    protected $isEnterprise;

    /** @var string $apiDomain */
    protected $apiDomain;

    /**
     * @param string $websiteUrl
     * @param string $websiteKey
     * @param string $minScore
     *
     * @throws \Exception
     */
    public function __construct(string $websiteUrl, string $websiteKey, string $minScore = '0.7')
    {
        $this->websiteUrl = $websiteUrl;
        $this->websiteKey = $websiteKey;
        $this->setMinScore($minScore);
    }


    public function setMinScore(string $minScore)
    {
        if (!in_array($minScore, ['0.3', '0.7', '0.9'])) {
            throw new \Exception('Min Score must be one of 0.3, 0.7 or 0.9');
        }
        $this->minScore = $minScore;
    }

    public function setPageAction(string $pageAction)
    {
        $this->pageAction = $pageAction;
    }

    public function setIsEnterprise(bool $isEnterprise)
    {
        $this->isEnterprise = $isEnterprise;
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

        $data['type'] = 'RecaptchaV3TaskProxyless';
        $data['websiteURL'] = $this->websiteUrl;
        $data['websiteKey'] = $this->websiteKey;
        $data['minScore'] = floatval($this->minScore);

        if (!is_null($this->pageAction)) {
            $data['pageAction'] = $this->pageAction;
        }

        if (!is_null($this->isEnterprise)) {
            $data['isEnterprise'] = $this->isEnterprise;
        }

        if (!is_null($this->apiDomain)) {
            $data['apiDomain'] = $this->apiDomain;
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

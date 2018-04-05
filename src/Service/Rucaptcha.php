<?php

namespace AntiCaptcha\Service;

/**
 * Class Rucaptcha
 * @package AntiCaptcha\Service
 */
class Rucaptcha extends AbstractService
{
    /** @var string $apiUrl */
    protected $apiUrl = 'http://rucaptcha.com';

    /**
     * Method getParams description.
     *
     * @return array
     */
    public function getParams()
    {
        return array_merge($this->params, [
            'soft_id' => 1528
        ]);
    }
}

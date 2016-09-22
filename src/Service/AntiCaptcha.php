<?php namespace AntiCaptcha\Service;

/**
 * Class AntiCaptcha
 * @package AntiCaptcha\Service
 *
 * Created by PhpStorm.
 * User: Maksym Tymchyk
 * Email: m.tymchyk@p1k.co.uk
 * Date: 18.08.16
 * Time: 17:45
 * 
 */
class AntiCaptcha extends AbstractService
{

    /**
     * @var string
     */
    protected $api_url = 'http://anti-captcha.com';
    
    /**
     * Method getParams description.
     *
     * @return array
     */
    public function getParams()
    {
        return array_merge(
            $this->params, 
            [
                'soft_id' => 791
            ]
        );
    }
    
}


<?php

namespace AntiCaptcha\Exception;


/**
 * Class InvalidAntiCaptchaServiceException
 * @package AntiCaptcha\Exception
 */
class InvalidAntiCaptchaServiceException extends AntiCaptchaException
{
    /**
     * InvalidAntiCaptchaServiceException constructor.
     *
     * @param string $service
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($service, $code = 0, \Exception $previous = null)
    {
        $message = 'Anticaptcha service provider ' . $service . ' not found!';
        parent::__construct($message, $code, $previous);
    }
}

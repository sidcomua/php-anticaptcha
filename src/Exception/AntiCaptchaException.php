<?php

namespace AntiCaptcha\Exception;

/**
 * Class AntiCaptchaException
 * @package AntiCaptcha\Exception
 */
class AntiCaptchaException extends \Exception
{
    /** @var string $errorCode */
    public $errorCode;

    /**
     * @param string $message
     * @param string $apiErrorCode
     */
    public function __construct($message = '', $apiErrorCode = '')
    {
        $this->errorCode = $apiErrorCode;

        parent::__construct($message, 0);
    }
}

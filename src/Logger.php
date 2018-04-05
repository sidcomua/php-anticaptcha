<?php

namespace AntiCaptcha;

use Psr\Log\AbstractLogger;

/**
 * Class Logger
 * @package AntiCaptcha
 */
class Logger extends AbstractLogger
{
    /**
     * Method log description.
     * @param $level
     * @param $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        echo date("Y-m-d H:i:s") . " " . str_pad($level, 10, " ") . " " . $message . "\n";
    }
}

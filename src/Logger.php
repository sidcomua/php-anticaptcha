<?php

namespace Anticaptcha;


class Logger extends \Psr\Log\AbstractLogger 
{
    public function log($level, $message, array $context = [])
    {
        echo date("Y-m-d H:i:s") . " " . str_pad($level, 10, " ") . " " . $message . "\n";
    }
}
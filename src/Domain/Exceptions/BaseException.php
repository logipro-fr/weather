<?php

namespace Weather\Domain\Exceptions;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BaseException extends Exception
{
    const ZERO_CODE = 0;
    const LOG_FILE = '/log/exceptions.log';
    public function __construct(string $message = "", int $code = self::ZERO_CODE)
    {
        parent::__construct($message, $code);
        $logger = new Logger('logger');
        $logger->pushHandler(new StreamHandler(getcwd() . self::LOG_FILE));

        $logger->error(get_class($this) . "\n" . $message);
    }
}

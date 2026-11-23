<?php

namespace App\Exceptions;

use RuntimeException;

class DemoLimitReached extends RuntimeException
{
    /**
     * @param  int  $retryAfter  seconds, sent as the Retry-After header
     */
    public function __construct(
        public readonly int $retryAfter,
        string $message = 'The demo limit has been reached.',
    ) {
        parent::__construct($message);
    }
}

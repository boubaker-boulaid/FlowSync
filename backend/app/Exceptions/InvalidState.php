<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidState extends RuntimeException
{
    /**
     * @param  string  $status  the resource's current status
     * @param  list<string>  $allowedFrom  the statuses where the action is allowed
     */
    public function __construct(
        public readonly string $status,
        public readonly array $allowedFrom,
        string $message = 'This action is not allowed in the current state.',
    ) {
        parent::__construct($message);
    }
}

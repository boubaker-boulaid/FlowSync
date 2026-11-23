<?php

namespace App\Exceptions;

use RuntimeException;

class DefinitionInvalid extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $errors
     */
    public function __construct(
        public readonly array $errors,
        string $message = 'The workflow definition is invalid.',
    ) {
        parent::__construct($message);
    }
}

<?php

namespace App\Exceptions;

use RuntimeException;

class RevisionConflict extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $details  current_revision, draft_updated_by, draft_updated_at
     */
    public function __construct(
        public readonly array $details,
        string $message = 'This workflow was changed by someone else. Reload or overwrite.',
    ) {
        parent::__construct($message);
    }
}

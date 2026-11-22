<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;

trait HasUppercaseUlids
{
    use HasUlids;

    // overrides newUniqueId() from HasUlids to return uppercase ids 
    public function newUniqueId(): string 
    {
        return (string) Str::ulid();
    }
}

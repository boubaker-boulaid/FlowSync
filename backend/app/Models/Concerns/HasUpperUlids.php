<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;

trait HasUpperUlids
{
    use HasUlids;

    // overrides newUniqueId() from HasUlids to return uppercase ids 
    public function newUniqueId() 
    {
        return (string) Str::ulid();
    }
}

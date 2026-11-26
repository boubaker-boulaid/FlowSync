<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseUlids;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property-read Carbon|null $demo_expires_at
 * @property-read Carbon|null $deleted_at
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory, HasUppercaseUlids, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'demo_expires_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<User, $this, Member>
     */
    public function users(): BelongsToMany
    { 
        return $this->belongsToMany(User::class, 'workspace_user')->using(Member::class);;
    }

    /**
     * @return HasMany<Member, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Concerns\HasUppercaseUlids;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Tells PHPstan that this properties exist on the model
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read Carbon|null $email_verified_at
 * @property-read bool $is_demo
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUppercaseUlids, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_demo' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Workspace, $this, Member>
     */
    public function workspaces(): BelongsToMany
    {
        return $$this->belongsToMany(Workspace::class, 'workspace_user')->using(Member::class);
    }

    /**
     * @return HasMany<Member, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}

<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Concerns\HasUppercaseUlids;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Member extends Pivot
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, HasUppercaseUlids;

    public $incrementing = false;

    protected $table = 'workspace_user';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
        ];
    }

    /**
     * @return BelongsTo<Workspace, $this>
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

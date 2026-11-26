<?php

use App\Enums\Role;
use App\Models\Member;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('rejects a duplicate workspace membership for the same user', function (): void {
    $member = Member::factory()->create();

    Member::factory()->create([
        'workspace_id' => $member->workspace_id,
        'user_id' => $member->user_id,
    ]);
})->throws(UniqueConstraintViolationException::class);

it('allows only one owner per workspace but allows multiple viewers', function (): void {
    $owner = Member::factory()->owner()->create();

    expect(fn () => Member::factory()->owner()->create([
        'workspace_id' => $owner->workspace_id,
    ]))->toThrow(UniqueConstraintViolationException::class);

    $viewerA = Member::factory()->viewer()->create([
        'workspace_id' => $owner->workspace_id,
        'user_id' => User::factory(),
    ]);

    Member::factory()->viewer()->create([
        'workspace_id' => $owner->workspace_id,
        'user_id' => User::factory(),
    ]);

    expect(Member::query()
        ->where('workspace_id', $owner->workspace_id)
        ->where('role', Role::Viewer)
        ->count())->toBe(2);
});

it('rejects a role that is not allowed by the database check', function (): void {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();

    expect(fn () => DB::table('workspace_user')->insert([
        'id' => (string) Str::ulid(),
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'role' => 'superuser',
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class);
});

it('hides soft deleted workspaces from normal queries but keeps them with trashed', function (): void {
    $workspace = Workspace::factory()->create();
    $workspace->delete();

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse();
    expect(Workspace::withTrashed()->whereKey($workspace->id)->exists())->toBeTrue();
});

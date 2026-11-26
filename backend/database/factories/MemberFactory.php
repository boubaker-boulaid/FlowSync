<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Member;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'role' => Role::Viewer,
        ];
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Owner]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Admin]);
    }

    public function builder(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Builder]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Operator]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Viewer]);
    }
}

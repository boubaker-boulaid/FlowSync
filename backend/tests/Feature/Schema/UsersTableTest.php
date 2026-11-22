<?php

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('rejects a second user with the same email', function () {
    User::factory()->create([
        'email' => 'samemail@gmail.com'
    ]);
    User::factory()->create([
        'email' => 'samemail@gmail.com'
    ]);
})->throws(UniqueConstraintViolationException::class);


it('defaults is_demo to false', function () {
    $id = (string) Str::ulid();

    DB::table('users')->insert([
        'id' => $id,
        'name' => 'test',
        'email' => 'test@email.com',
        'password' => 'test112233',
    ]);

    $user = User::find($id);

    expect($user->is_demo)->toBeFalse();
});
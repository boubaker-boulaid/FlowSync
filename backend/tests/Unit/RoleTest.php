<?php

use App\Enums\Permission;
use App\Enums\Role;

dataset('matrix', [
    'owner allows owner permission' => [Role::Owner, Permission::Owner, true],
    'owner allows admin permission' => [Role::Owner, Permission::Admin, true],
    'owner allows operate permission' => [Role::Owner, Permission::Operate, true],
    'owner allows build permission' => [Role::Owner, Permission::Build, true],
    'owner allows view permission' => [Role::Owner, Permission::View, true],

    'admin denies owner permission' => [Role::Admin, Permission::Owner, false],
    'admin allows admin permission' => [Role::Admin, Permission::Admin, true],
    'admin allows operate permission' => [Role::Admin, Permission::Operate, true],
    'admin allows build permission' => [Role::Admin, Permission::Build, true],
    'admin allows view permission' => [Role::Admin, Permission::View, true],

    'builder denies owner permission' => [Role::Builder, Permission::Owner, false],
    'builder denies admin permission' => [Role::Builder, Permission::Admin, false],
    'builder allows operate permission' => [Role::Builder, Permission::Operate, true],
    'builder allows build permission' => [Role::Builder, Permission::Build, true],
    'builder allows view permission' => [Role::Builder, Permission::View, true],

    'operator denies owner permission' => [Role::Operator, Permission::Owner, false],
    'operator denies admin permission' => [Role::Operator, Permission::Admin, false],
    'operator denies build permission' => [Role::Operator, Permission::Build, false],
    'operator allows operate permission' => [Role::Operator, Permission::Operate, true],
    'operator allows view permission' => [Role::Operator, Permission::View, true],

    'viewer denies owner permission' => [Role::Viewer, Permission::Owner, false],
    'viewer denies admin permission' => [Role::Viewer, Permission::Admin, false],
    'viewer denies operate permission' => [Role::Viewer, Permission::Operate, false],
    'viewer denies build permission' => [Role::Viewer, Permission::Build, false],
    'viewer allows view permission' => [Role::Viewer, Permission::View, true],
]);

it('matches the approved permission matrix', function (Role $role, Permission $permission, bool $expected): void {
    expect($role->can($permission))->toBe($expected);
})->with('matrix');

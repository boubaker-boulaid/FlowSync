<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Builder = 'builder';
    case Operator = 'operator';
    case Viewer = 'viewer';

    /**
     * Whether this workspace role grants the given permission (API §3 matrix).
     */
    public function can(Permission $permission): bool
    {
        return match ($this) {
            self::Owner => true,

            self::Admin => in_array($permission, [
                Permission::Admin,
                Permission::View,
                Permission::Operate,
                Permission::Build,
            ], true),

            self::Builder => in_array($permission, [
                Permission::View,
                Permission::Operate,
                Permission::Build,
            ], true),

            self::Operator => in_array($permission, [
                Permission::View,
                Permission::Operate,
            ], true),

            self::Viewer => $permission === Permission::View,
        };
    }
}

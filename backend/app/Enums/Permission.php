<?php

namespace App\Enums;

enum Permission: string
{
    case View = 'view';
    case Operate = 'operate';
    case Build = 'build';
    case Admin = 'admin';
    case Owner = 'owner';
}

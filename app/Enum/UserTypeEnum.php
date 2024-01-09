<?php
namespace App\Enum;


enum UserTypeEnum: string
{
    case MANAGEMENT = 'management';
    case ADMIN      = 'admin';
    case USER       = 'user';
}
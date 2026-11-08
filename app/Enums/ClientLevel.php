<?php

namespace App\Enums;

enum ClientLevel: string
{
    case Starter = 'starter';
    case Growth = 'growth';
    case Scale = 'scale';

    public function label(): string
    {
        return match ($this) {
            self::Starter => 'Starter',
            self::Growth => 'Growth',
            self::Scale => 'Scale',
        };
    }
}

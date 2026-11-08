<?php

namespace App\Enums;

enum ClientStatus: string
{
    case Onboarding = 'onboarding';
    case Active = 'active';
    case Paused = 'paused';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Onboarding => 'Onboarding',
            self::Active => 'Activo',
            self::Paused => 'Pausado',
            self::Closed => 'Cerrado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Onboarding => 'blue',
            self::Active => 'green',
            self::Paused => 'yellow',
            self::Closed => 'red',
        };
    }
}

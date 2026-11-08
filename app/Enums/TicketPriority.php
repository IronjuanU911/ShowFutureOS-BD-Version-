<?php

namespace App\Enums;

enum TicketPriority: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return match ($this) {
            self::High => 'Alta',
            self::Medium => 'Media',
            self::Low => 'Baja',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::High => 'red',
            self::Medium => 'yellow',
            self::Low => 'green',
        };
    }
}

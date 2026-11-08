<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Backlog = 'backlog';
    case InProgress = 'in_progress';
    case Qa = 'qa';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Backlog => 'Backlog',
            self::InProgress => 'En Proceso',
            self::Qa => 'QA',
            self::Delivered => 'Entregado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Backlog => 'slate',
            self::InProgress => 'blue',
            self::Qa => 'amber',
            self::Delivered => 'green',
        };
    }
}

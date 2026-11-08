<?php

namespace App\Enums;

enum TicketType: string
{
    case Creative = 'creative';
    case Ads = 'ads';
    case Copy = 'copy';
    case Commercial = 'commercial';

    public function label(): string
    {
        return match ($this) {
            self::Creative => '🎨 Creativo',
            self::Ads => '📊 Ads',
            self::Copy => '✍️ Copy',
            self::Commercial => '📞 Comercial',
        };
    }
}

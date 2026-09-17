<?php

declare(strict_types=1);

namespace App\Enum;

enum Education: string
{
    case SECONDARY = 'secondary';
    case SPECIAL = 'special';
    case HIGHER = 'higher';

    public function label(): string
    {
        return match ($this) {
            self::SECONDARY => 'Среднее образование',
            self::SPECIAL => 'Специальное образование',
            self::HIGHER => 'Высшее образование',
        };
    }
}

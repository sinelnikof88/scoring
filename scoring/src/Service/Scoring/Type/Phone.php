<?php

declare(strict_types=1);

namespace App\Service\Scoring\Type;

use App\Entity\Client;

final class Phone implements ScoringRuleInterface
{
    private const SCORE_MEGAFON = 10;
    private const SCORE_BEELINE = 5;
    private const SCORE_MTS = 3;
    private const SCORE_OTHER = 1;

    private const OPERATORS = [
        'МегаФон' => ['920', '921', '922', '923', '924', '925', '926', '927', '928', '929',
            '930', '931', '932', '933', '934', '935', '936', '937', '938', '939'],
        'Билайн' => ['905', '906', '909', '960', '961', '962', '963', '964', '965', '966',
            '967', '968', '969'],
        'МТС' => ['910', '911', '912', '913', '914', '915', '916', '917', '918', '919',
            '980', '981', '982', '983', '984', '985', '986', '987', '988', '989'],
    ];

    public function name(): string
    {
        return 'Сотовый оператор';
    }

    public function score(Client $client): int
    {
        return match ($this->detectOperator($client->getPhone())) {
            'МегаФон' => self::SCORE_MEGAFON,
            'Билайн' => self::SCORE_BEELINE,
            'МТС' => self::SCORE_MTS,
            default => self::SCORE_OTHER,
        };
    }

    public function detectOperator(?string $phone): string
    {
        if (null === $phone || '' === $phone) {
            return 'Иной';
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if ('' === $digits) {
            return 'Иной';
        }

        // +7XXXXXXXXXX или 8XXXXXXXXXX → убираем первую цифру
        if (str_starts_with($digits, '7') || str_starts_with($digits, '8')) {
            $digits = substr($digits, 1);
        }

        $code = substr($digits, 0, 3);

        foreach (self::OPERATORS as $operator => $codes) {
            if (in_array($code, $codes, true)) {
                return $operator;
            }
        }

        return 'Иной';
    }
}

<?php

declare(strict_types=1);

namespace App\Service\Scoring\Type;

use App\Entity\Client;

final class ProcessingConsent implements ScoringRuleInterface
{
    private const SCORE_YES = 4;
    private const SCORE_NO = 0;

    public function name(): string
    {
        return 'Согласие на обработку данных';
    }

    public function score(Client $client): int
    {
        return true === $client->getConsentGiven() ? self::SCORE_YES : self::SCORE_NO;
    }
}

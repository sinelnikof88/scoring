<?php

declare(strict_types=1);

namespace App\Service\Scoring\Type;

use App\Entity\Client;
use App\Enum\Education as EducationEnum;

final class Education implements ScoringRuleInterface
{
    private const SCORE_HIGHER = 15;
    private const SCORE_SPECIAL = 10;
    private const SCORE_SECONDARY = 5;

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Образование';
    }

    /**
     * @param Client $client
     *
     * @return int
     */
    public function score(Client $client): int
    {
        return match ($client->getEducation()) {
            EducationEnum::HIGHER => self::SCORE_HIGHER,
            EducationEnum::SPECIAL => self::SCORE_SPECIAL,
            EducationEnum::SECONDARY => self::SCORE_SECONDARY,
            null => 0,
        };
    }
}

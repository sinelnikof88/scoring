<?php

declare(strict_types=1);

namespace App\Service\Scoring\Type;

use App\Entity\Client;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.scoring_rule')]
interface ScoringRuleInterface
{

    /**
     * @param Client $client
     *
     * @return int
     */
    public function score(Client $client): int;


    /**
     * @return string
     */
    public function name(): string;
}

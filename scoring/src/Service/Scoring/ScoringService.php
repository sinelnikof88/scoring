<?php

declare(strict_types=1);

namespace App\Service\Scoring;

use App\Entity\Client;
use App\Service\Scoring\Type\Education;
use App\Service\Scoring\Type\Email;
use App\Service\Scoring\Type\Phone;
use App\Service\Scoring\Type\ProcessingConsent;
use App\Service\Scoring\Type\ScoringRuleInterface;

final class ScoringService
{
    /** @var iterable<ScoringRuleInterface> */
    private iterable $rules;


    public function __construct(
        Phone             $phone,
        Email             $email,
        Education         $education,
        ProcessingConsent $consent,
    )
    {
        $this->rules = [$phone, $email, $education, $consent];
    }

    /**
     * @param ScoringRuleInterface $rule
     *
     * @return void
     */
    public function setRules(ScoringRuleInterface $rule): void
    {
        $this->rules[] = $rule;

    }

    /**
     * @return array{total: int, details: array}
     */
    public function calculate(Client $client): array
    {
        $details = [];
        $total = 0;

        foreach ($this->rules as $rule) {
            $score = $rule->score($client);
            $details[$rule->name()] = $score;
            $total += $score;
        }

        return [
            'total' => $total,
            'details' => $details,
        ];
    }


    /**
     * @param Client $client
     *
     * @return int
     */
    public function calculateTotal(Client $client): int
    {
        return $this->calculate($client)['total'];
    }
}

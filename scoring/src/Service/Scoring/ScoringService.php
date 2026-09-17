<?php

declare(strict_types=1);

namespace App\Service\Scoring;

use App\Entity\Client;
use App\Service\Scoring\Type\ScoringRuleInterface;

final class ScoringService
{
    /** @var ScoringRuleInterface[] */
    private array $rules = [];

    /**
     * @param iterable<ScoringRuleInterface> $rules
     */
    public function __construct(iterable $rules = [])
    {
        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Динамически добавить правило.
     */
    public function addRule(ScoringRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }

    public function calculateTotal(Client $client): int
    {
        return $this->calculate($client)['total'];
    }

    /**
     * @return array{total: int, details: array<string, int>}
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
}

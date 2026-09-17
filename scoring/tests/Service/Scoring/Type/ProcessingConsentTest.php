<?php

declare(strict_types=1);

namespace App\Tests\Service\Scoring\Type;

use App\Entity\Client;
use App\Service\Scoring\Type\ProcessingConsent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProcessingConsentTest extends TestCase
{
    private ProcessingConsent $rule;

    public static function provideCases(): array
    {
        return [
            'Согласие дано = 4' => [true, 4],
            'Согласие не дано = 0' => [false, 0],
            'Не задано = 0' => [null, 0],
        ];
    }

    public function testName(): void
    {
        $this->assertSame('Согласие на обработку данных', $this->rule->name());
    }

    #[DataProvider('provideCases')]
    public function testScore(?bool $consent, int $expected): void
    {
        $client = new Client();

        if (null !== $consent) {
            $client->setConsentGiven($consent);
        }

        $this->assertSame($expected, $this->rule->score($client));
    }

    protected function setUp(): void
    {
        $this->rule = new ProcessingConsent();
    }
}

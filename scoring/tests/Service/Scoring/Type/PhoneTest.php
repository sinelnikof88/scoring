<?php

declare(strict_types=1);

namespace App\Tests\Service\Scoring\Type;

use App\Entity\Client;
use App\Service\Scoring\Type\Phone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    private Phone $rule;

    public static function provideOperatorCases(): array
    {
        return [
            'МегаФон с +7' => ['+79261234567', 'МегаФон'],
            'МегаФон с 8' => ['89261234567', 'МегаФон'],
            'МегаФон с форматированием' => ['8 (926) 123-45-67', 'МегаФон'],
            'МегаФон код 929' => ['+79291234567', 'МегаФон'],
            'Билайн' => ['+79601234567', 'Билайн'],
            'Билайн код 905' => ['+79051234567', 'Билайн'],
            'МТС' => ['+79101234567', 'МТС'],
            'МТС код 985' => ['+79851234567', 'МТС'],
            'Иной оператор' => ['+79991234567', 'Иной'],
            'Пустая строка' => ['', 'Иной'],
            'null' => [null, 'Иной'],
            'Мусор' => ['abc', 'Иной'],
        ];
    }

    public static function provideScoreCases(): array
    {
        return [
            'МегаФон = 10' => ['+79261234567', 10],
            'Билайн = 5' => ['+79601234567', 5],
            'МТС = 3' => ['+79101234567', 3],
            'Иной = 1' => ['+79991234567', 1],
            'Пустая = 1' => ['', 1],
        ];
    }

    public function testName(): void
    {
        $this->assertSame('Сотовый оператор', $this->rule->name());
    }

    #[DataProvider('provideOperatorCases')]
    public function testDetectOperator(?string $phone, string $expectedOperator): void
    {
        $this->assertSame($expectedOperator, $this->rule->detectOperator($phone));
    }

    #[DataProvider('provideScoreCases')]
    public function testScore(string $phone, int $expectedScore): void
    {
        $client = (new Client())->setPhone($phone);
        $this->assertSame($expectedScore, $this->rule->score($client));
    }

    protected function setUp(): void
    {
        $this->rule = new Phone();
    }
}

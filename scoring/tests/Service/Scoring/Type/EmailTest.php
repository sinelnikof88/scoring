<?php

declare(strict_types=1);

namespace App\Tests\Service\Scoring\Type;

use App\Entity\Client;
use App\Service\Scoring\Type\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    private Email $rule;

    public static function provideProviderCases(): array
    {
        return [
            'gmail.com' => ['test@gmail.com', 'gmail'],
            'googlemail.com' => ['test@googlemail.com', 'gmail'],
            'GMAIL в верхнем' => ['test@GMAIL.COM', 'gmail'],
            'yandex.ru' => ['test@yandex.ru', 'yandex'],
            'ya.ru' => ['test@ya.ru', 'yandex'],
            'yandex.com' => ['test@yandex.com', 'yandex'],
            'mail.ru' => ['test@mail.ru', 'mail'],
            'inbox.ru' => ['test@inbox.ru', 'mail'],
            'list.ru' => ['test@list.ru', 'mail'],
            'bk.ru' => ['test@bk.ru', 'mail'],
            'internet.ru' => ['test@internet.ru', 'mail'],
            'Другой домен' => ['test@example.com', 'Иной'],
            'Пустая строка' => ['', 'Иной'],
            'null' => [null, 'Иной'],
            'Без @' => ['no-at-sign', 'Иной'],
        ];
    }

    public static function provideScoreCases(): array
    {
        return [
            'gmail = 10' => ['test@gmail.com', 10],
            'yandex = 8' => ['test@yandex.ru', 8],
            'mail = 6' => ['test@mail.ru', 6],
            'иной = 3' => ['test@example.com', 3],
        ];
    }

    public function testName(): void
    {
        $this->assertSame('Домен э-почты', $this->rule->name());
    }

    #[DataProvider('provideProviderCases')]
    public function testDetectProvider(?string $email, string $expected): void
    {
        $this->assertSame($expected, $this->rule->detectProvider($email));
    }

    #[DataProvider('provideScoreCases')]
    public function testScore(string $email, int $expected): void
    {
        $client = (new Client())->setEmail($email);
        $this->assertSame($expected, $this->rule->score($client));
    }

    protected function setUp(): void
    {
        $this->rule = new Email();
    }
}

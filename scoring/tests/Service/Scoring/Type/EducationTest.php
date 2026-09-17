<?php

declare(strict_types=1);

namespace App\Tests\Service\Scoring\Type;

use App\Entity\Client;
use App\Enum\Education as EducationEnum;
use App\Service\Scoring\Type\Education;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EducationTest extends TestCase
{
    private Education $rule;

    public static function provideCases(): array
    {
        return [
            'Высшее = 15' => [EducationEnum::HIGHER, 15],
            'Специальное = 10' => [EducationEnum::SPECIAL, 10],
            'Среднее = 5' => [EducationEnum::SECONDARY, 5],
            'Не задано = 0' => [null, 0],
        ];
    }

    public function testName(): void
    {
        $this->assertSame('Образование', $this->rule->name());
    }

    #[DataProvider('provideCases')]
    public function testScore(?EducationEnum $education, int $expected): void
    {
        $client = new Client();

        if ($education !== null) {
            $client->setEducation($education);
        }

        $this->assertSame($expected, $this->rule->score($client));
    }

    protected function setUp(): void
    {
        $this->rule = new Education();
    }
}

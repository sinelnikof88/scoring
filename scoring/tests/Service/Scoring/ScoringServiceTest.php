<?php

namespace App\Tests\Service\Scoring;

use App\Entity\Client;
use App\Enum\Education;
use App\Service\Scoring\ScoringService;
use App\Service\Scoring\Type\Education as EducationRule;
use App\Service\Scoring\Type\Email;
use App\Service\Scoring\Type\Phone;
use App\Service\Scoring\Type\ProcessingConsent;
use PHPUnit\Framework\TestCase;

class ScoringServiceTest extends TestCase
{
    private ScoringService $scoringService;

    public function testCalculateForIdealClient(): void
    {
        // Создаем клиента с максимальными данными
        $client = (new Client())
            ->setFirstName('Иван')
            ->setLastName('Иванов')
            ->setPhone('+79261234567') // МегаФон (10)
            ->setEmail('ivan@gmail.com') // Gmail (10)
            ->setEducation(Education::HIGHER) // Высшее (15)
            ->setConsentGiven(true); // Согласие (4)

        $result = $this->scoringService->calculate($client);

        $this->assertSame(39, $result['total']);

        $this->assertSame(10, $result['details']['Сотовый оператор']);
        $this->assertSame(10, $result['details']['Домен э-почты']);
        $this->assertSame(15, $result['details']['Образование']);
        $this->assertSame(4, $result['details']['Согласие на обработку данных']);
    }

    public function testCalculateForMinimalClient(): void
    {
        // Клиент с минимальными данными
        $client = (new Client())
            ->setPhone('+79990000000') // Иной (1)
            ->setEmail('test@example.com') // Иной (3)
            ->setEducation(Education::SECONDARY) // Среднее (5)
            ->setConsentGiven(false); // Нет (0)

        $result = $this->scoringService->calculate($client);


        $this->assertSame(9, $result['total']);
        $this->assertSame(1, $result['details']['Сотовый оператор']);
        $this->assertSame(3, $result['details']['Домен э-почты']);
        $this->assertSame(5, $result['details']['Образование']);
        $this->assertSame(0, $result['details']['Согласие на обработку данных']);
    }

    protected function setUp(): void
    {
        $this->scoringService = new ScoringService();
        $this->scoringService->addRule(new Phone());
        $this->scoringService->addRule(new Email());
        $this->scoringService->addRule(new EducationRule());
        $this->scoringService->addRule(new ProcessingConsent());
    }
}

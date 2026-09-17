<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Client;
use App\Enum\Education;
use App\Service\Scoring\ScoringService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class ClientFixtures extends Fixture
{
    private const COUNT = 30;

    /**
     * Коды операторов для генерации телефонов.
     * Ключ — оператор, значение — массив кодов.
     */
    private const PHONE_CODES = [
        'megafon' => ['920', '921', '926', '929', '936'],
        'beeline' => ['905', '906', '909', '960', '961'],
        'mts'     => ['910', '911', '915', '916', '985'],
        'other'   => ['999', '998', '997', '996', '995'],
    ];

    private const EMAIL_DOMAINS = [
        'gmail'  => ['gmail.com'],
        'yandex' => ['yandex.ru', 'ya.ru'],
        'mail'   => ['mail.ru', 'inbox.ru', 'bk.ru'],
        'other'  => ['example.com', 'test.org', 'corp.ru'],
    ];

    private Generator $faker;

    public function __construct(
        private readonly ScoringService $scoring,
    ) {
        $this->faker = Factory::create('ru_RU');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT; $i++) {
            $client = new Client();

            $client->setFirstName($this->faker->firstName());
            $client->setLastName($this->faker->lastName());
            $client->setPhone($this->generatePhone());
            $client->setEmail($this->generateEmail());
            $client->setEducation($this->randomEducation());
            $client->setConsentGiven($this->faker->boolean(70)); // 70% дают согласие

            // Считаем скоринг и записываем
            $client->setScore($this->scoring->calculateTotal($client));

            $manager->persist($client);
        }

        $manager->flush();
    }

    private function generatePhone(): string
    {
        $operator = $this->faker->randomElement(array_keys(self::PHONE_CODES));
        $code = $this->faker->randomElement(self::PHONE_CODES[$operator]);
        $number = str_pad((string) $this->faker->numberBetween(0, 9999999), 7, '0', STR_PAD_LEFT);

        return sprintf('+7%s%s', $code, $number);
    }

    private function generateEmail(): string
    {
        $provider = $this->faker->randomElement(array_keys(self::EMAIL_DOMAINS));
        $domain = $this->faker->randomElement(self::EMAIL_DOMAINS[$provider]);
        $local = $this->faker->userName() . $this->faker->numberBetween(1, 999);

        return sprintf('%s@%s', $local, $domain);
    }

    private function randomEducation(): Education
    {
        return $this->faker->randomElement([
            Education::HIGHER,
            Education::SPECIAL,
            Education::SECONDARY,
        ]);
    }
}

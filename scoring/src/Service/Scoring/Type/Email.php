<?php

declare(strict_types=1);

namespace App\Service\Scoring\Type;

use App\Entity\Client;

final class Email implements ScoringRuleInterface
{
    private const SCORE_GMAIL = 10;
    private const SCORE_YANDEX = 8;
    private const SCORE_MAIL = 6;
    private const SCORE_OTHER = 3;


    private const PROVIDERS = [
        'gmail' => ['gmail.com', 'googlemail.com'],
        'yandex' => ['yandex.ru', 'ya.ru', 'yandex.com'],
        'mail' => ['mail.ru', 'inbox.ru', 'list.ru', 'bk.ru', 'internet.ru'],
    ];

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Домен э-почты';
    }

    /**
     * @param Client $client
     *
     * @return int
     */
    public function score(Client $client): int
    {
        return match ($this->detectProvider($client->getEmail())) {
            'gmail' => self::SCORE_GMAIL,
            'yandex' => self::SCORE_YANDEX,
            'mail' => self::SCORE_MAIL,
            default => self::SCORE_OTHER,
        };
    }

    public function detectProvider(?string $email): string
    {
        if ($email === null || !str_contains($email, '@')) {
            return 'Иной';
        }

        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));

        foreach (self::PROVIDERS as $provider => $domains) {
            if (in_array($domain, $domains, true)) {
                return $provider;
            }
        }

        return 'Иной';
    }
}

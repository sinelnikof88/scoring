<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function save(Client $client, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($client);

        if ($flush) {
            $em->flush();
        }
    }

    public function delete(Client $client, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($client);

        if ($flush) {
            $em->flush();
        }
    }
}

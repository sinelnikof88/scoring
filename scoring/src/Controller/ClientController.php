<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientRegistrationType;
use App\Repository\ClientRepository;
use App\Service\Scoring\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
    #[Route('/', name: 'client_index')]
    public function index(Request $request, ClientRepository $clients): Response
    {
        $queryBuilder = $clients->createQueryBuilder('c')->orderBy('c.id', 'DESC');

        $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pager->setMaxPerPage(20);
        $page = max(1, $request->query->getInt('page', 1));

        if ($page > $pager->getNbPages() && $pager->getNbPages() > 0) {
            throw $this->createNotFoundException();
        }

        $pager->setCurrentPage(max(1, $request->query->getInt('page', 1)));


        return $this->render('client/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    #[Route('/register', name: 'client_register')]
    public function register(
        Request                $request,
        EntityManagerInterface $em,
        ScoringService         $scoring,
    ): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientRegistrationType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setScore($scoring->calculateTotal($client));

            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Клиент успешно зарегистрирован. Скоринг: ' . $client->getScore());

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/client/{id}', name: 'client_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/client/{id}/edit', name: 'client_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Client                 $client,
        EntityManagerInterface $em,
        ScoringService         $scoring,
    ): Response
    {
        $form = $this->createForm(ClientRegistrationType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setScore($scoring->calculateTotal($client));

            $em->flush();

            $this->addFlash('success', 'Клиент обновлён. Новый скоринг: ' . $client->getScore());

            return $this->redirectToRoute('client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form,
            'client' => $client,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EntryController extends AbstractController
{
    #[Route('/entry', name: 'create_entry', methods: ['POST'])]
    public function createEntry(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $entry = $serializer->deserialize($request->getContent(), Entry::class, 'json');

        $entityManager->persist($entry);
        $entityManager->flush();

        return new Response(
            $serializer->serialize($entry, 'json'),
            Response::HTTP_CREATED,
            ['content-type' => 'application/json']
        );
    }

    #[Route('/entry', name: 'get_entry', methods: ['GET'])]
    public function getEntry(EntryRepository $entryRepository, SerializerInterface $serializer): Response {
        $entries = $entryRepository->findAll();

        if (sizeof($entries) == 0) {
            return new Response(
                $serializer->serialize('There does not exist an entry', 'json'),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }

        $randomEntry = $entries[random_int(0, sizeof($entries) - 1)];

        return new Response(
            $serializer->serialize($randomEntry, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
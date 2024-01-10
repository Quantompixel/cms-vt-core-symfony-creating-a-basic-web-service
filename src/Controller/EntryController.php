<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class EntryController extends AbstractController
{
    #[Route('/entry', name: 'create_entry', methods: ['POST'])]
    public function createEntry(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        try {
            $entry = $serializer->deserialize($request->getContent(), Entry::class, 'json');
        } catch (NotEncodableValueException) {
            return new Response(
                $serializer->serialize('Please enter valid JSON syntax', 'json'),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }

        if ($entry->getName() === null || $entry->getResourceUrl() === null) {
            return new Response(
                $serializer->serialize('All attributes have to be set', 'json'),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }

        $entityManager->persist($entry);
        $entityManager->flush();

        return new Response(
            $serializer->serialize($entry, 'json'),
            Response::HTTP_CREATED,
            ['content-type' => 'application/json']
        );
    }

    #[Route('/entry', name: 'get_entry', methods: ['GET'])]
    public function getEntry(EntryRepository $entryRepository): Response
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        $randomEntry = $entryRepository->findRandom();

        if ($randomEntry === null) {
            return new Response(
                $serializer->serialize('There does not exist an entry', 'json'),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        return new Response(
            $serializer->serialize($randomEntry, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
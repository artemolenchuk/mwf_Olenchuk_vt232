<?php


namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clients')]
class ClientController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $clients = $em->getRepository(Client::class)->findAll();
        $data = array_map(fn($c) => [
            'id' => $c->getId(),
            'full_name' => $c->getFullName(),
            'phone' => $c->getPhone(),
            'driver_license' => $c->getDriverLicense(),
        ], $clients);
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = new Client();
        $client->setFullName($data['full_name'] ?? '');
        $client->setPhone($data['phone'] ?? '');
        $client->setDriverLicense($data['driver_license'] ?? '');

        $em->persist($client);
        $em->flush();
        return $this->json(['id' => $client->getId(), 'full_name' => $client->getFullName()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $em): JsonResponse
    {
        $client = $em->getRepository(Client::class)->find($id);
        if (!$client) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $em->remove($client);
        $em->flush();
        return $this->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}
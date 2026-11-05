<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Client;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rentals')]
class RentalController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $repository = $em->getRepository(Rental::class);
        $criteria = [];

        if ($request->query->has('id')) {
            $criteria['id'] = $request->query->get('id');
        }
        if ($request->query->has('car_id')) {
            $criteria['car'] = $request->query->get('car_id');
        }
        if ($request->query->has('client_id')) {
            $criteria['client'] = $request->query->get('client_id');
        }
        if ($request->query->has('total_price')) {
            $criteria['total_price'] = $request->query->get('total_price');
        }
        if ($request->query->has('status')) {
            $criteria['status'] = $request->query->get('status');
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', $request->query->get('per_page', 10)));
        $offset = ($page - 1) * $itemsPerPage;

        $totalItems = $repository->count($criteria);
        $rentals = $repository->findBy($criteria, null, $itemsPerPage, $offset);

        $data = array_map(fn($r) => [
            'id' => $r->getId(),
            'car_id' => $r->getCar()?->getId(),
            'client_id' => $r->getClient()?->getId(),
            'start_date' => $r->getStartDate()?->format('Y-m-d H:i:s'),
            'end_date' => $r->getEndDate()?->format('Y-m-d H:i:s'),
            'total_price' => $r->getTotalPrice(),
            'status' => $r->getStatus(),
        ], $rentals);

        return $this->json([
            'data' => $data,
            'total' => $totalItems,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'last_page' => (int) ceil($totalItems / $itemsPerPage)
        ], Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $car = $em->getRepository(Car::class)->find($data['car_id'] ?? 0);
        $client = $em->getRepository(Client::class)->find($data['client_id'] ?? 0);

        if (!$car || !$client) return $this->json(['error' => 'Car or Client not found'], Response::HTTP_BAD_REQUEST);

        $rental = new Rental();
        $rental->setCar($car);
        $rental->setClient($client);
        $rental->setStartDate(new \DateTime($data['start_date'] ?? 'now'));
        $rental->setEndDate(new \DateTime($data['end_date'] ?? '+1 day'));
        $rental->setTotalPrice($data['total_price'] ?? '100.00');
        $rental->setStatus($data['status'] ?? 'active');

        $em->persist($rental);
        $em->flush();
        return $this->json(['id' => $rental->getId(), 'total_price' => $rental->getTotalPrice()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $em): JsonResponse
    {
        $rental = $em->getRepository(Rental::class)->find($id);
        if (!$rental) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $em->remove($rental);
        $em->flush();
        return $this->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}
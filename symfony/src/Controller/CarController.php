<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cars')]
class CarController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $repository = $em->getRepository(Car::class);
        $criteria = [];

        if ($request->query->has('id')) {
            $criteria['id'] = $request->query->get('id');
        }
        if ($request->query->has('category_id')) {
            $criteria['category'] = $request->query->get('category_id');
        }
        if ($request->query->has('brand')) {
            $criteria['brand'] = $request->query->get('brand');
        }
        if ($request->query->has('model')) {
            $criteria['model'] = $request->query->get('model');
        }
        if ($request->query->has('license_plate')) {
            $criteria['licensePlate'] = $request->query->get('license_plate');
        }
        if ($request->query->has('daily_rate')) {
            $criteria['dailyRate'] = $request->query->get('daily_rate');
        }
        if ($request->query->has('is_available')) {
            $criteria['isAvailable'] = filter_var($request->query->get('is_available'), FILTER_VALIDATE_BOOLEAN);
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', $request->query->get('per_page', 10)));
        $offset = ($page - 1) * $itemsPerPage;

        $totalItems = $repository->count($criteria);
        $cars = $repository->findBy($criteria, null, $itemsPerPage, $offset);

        $data = array_map(fn($c) => [
            'id' => $c->getId(),
            'category_id' => $c->getCategory()?->getId(),
            'brand' => $c->getBrand(),
            'model' => $c->getModel(),
            'license_plate' => $c->getLicensePlate(),
            'daily_rate' => $c->getDailyRate(),
            'is_available' => $c->isIsAvailable(),
        ], $cars);

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
        $category = $em->getRepository(Category::class)->find($data['category_id'] ?? 0);
        if (!$category) return $this->json(['error' => 'Category not found'], Response::HTTP_BAD_REQUEST);

        $car = new Car();
        $car->setCategory($category);
        $car->setBrand($data['brand'] ?? '');
        $car->setModel($data['model'] ?? '');
        $car->setLicensePlate($data['license_plate'] ?? '');
        $car->setDailyRate($data['daily_rate'] ?? '0.00');
        $car->setIsAvailable($data['is_available'] ?? true);

        $em->persist($car);
        $em->flush();
        return $this->json(['id' => $car->getId(), 'brand' => $car->getBrand()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $em): JsonResponse
    {
        $car = $em->getRepository(Car::class)->find($id);
        if (!$car) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $em->remove($car);
        $em->flush();
        return $this->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}
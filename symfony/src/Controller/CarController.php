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
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $cars = $em->getRepository(Car::class)->findAll();
        $data = array_map(fn($c) => [
            'id' => $c->getId(),
            'category_id' => $c->getCategory()?->getId(),
            'brand' => $c->getBrand(),
            'model' => $c->getModel(),
            'license_plate' => $c->getLicensePlate(),
            'daily_rate' => $c->getDailyRate(),
            'is_available' => $c->isIsAvailable(),
        ], $cars);
        return $this->json($data, Response::HTTP_OK);
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
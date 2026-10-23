<?php


namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $categories = $em->getRepository(Category::class)->findAll();
        $data = array_map(fn($c) => ['id' => $c->getId(), 'name' => $c->getName()], $categories);
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return $this->json(['id' => $category->getId(), 'name' => $category->getName()], Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category = new Category();
        $category->setName($data['name'] ?? '');
        $em->persist($category);
        $em->flush();
        return $this->json(['id' => $category->getId(), 'name' => $category->getName()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $em): JsonResponse
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $em->remove($category);
        $em->flush();
        return $this->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}
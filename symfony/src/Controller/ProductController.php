<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private array $products = [
        ['id' => 1, 'name' => 'Laptop', 'price' => 1000],
        ['id' => 2, 'name' => 'Phone', 'price' => 500],
    ];

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        return new JsonResponse($this->products, Response::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProductItem(int $id): JsonResponse
    {
        foreach ($this->products as $product) {
            if ($product['id'] === $id) {
                return new JsonResponse($product, Response::HTTP_OK);
            }
        }
        return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newProduct = [
            'id' => rand(3, 100),
            'name' => $data['name'] ?? 'Default Product',
            'price' => $data['price'] ?? 0,
        ];
        return new JsonResponse($newProduct, Response::HTTP_CREATED);
    }
}
<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private array $products = [
        ['id' => 1, 'name' => 'Laptop', 'price' => 1000],
        ['id' => 2, 'name' => 'Phone', 'price' => 500],
    ];

    public function getProducts(): JsonResponse
    {
        return response()->json($this->products, Response::HTTP_OK);
    }

    public function getProductItem(int $id): JsonResponse
    {
        foreach ($this->products as $product) {
            if ($product['id'] == $id) {
                return response()->json($product, Response::HTTP_OK);
            }
        }
        return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newProduct = [
            'id' => rand(3, 100),
            'name' => $data['name'] ?? 'Default Product',
            'price' => $data['price'] ?? 0,
        ];
        return response()->json($newProduct, Response::HTTP_CREATED);
    }
}

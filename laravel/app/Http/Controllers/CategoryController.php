<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function index() { return response()->json(Category::with('cars')->get(), Response::HTTP_OK); }

    public function show($id)
    {
        $category = Category::with('cars')->find($id);
        if (!$category) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return response()->json($category, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $category->update($request->all());
        return response()->json($category, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $category->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}

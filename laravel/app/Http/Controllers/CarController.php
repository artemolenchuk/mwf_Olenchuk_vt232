<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::with('category');

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->input('brand') . '%');
        }
        if ($request->filled('model')) {
            $query->where('model', 'like', '%' . $request->input('model') . '%');
        }
        if ($request->filled('license_plate')) {
            $query->where('license_plate', 'like', '%' . $request->input('license_plate') . '%');
        }
        if ($request->filled('daily_rate')) {
            $query->where('daily_rate', $request->input('daily_rate'));
        }
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        $perPage = $request->input('itemsPerPage', $request->input('per_page', 10));

        return response()->json($query->paginate((int)$perPage), Response::HTTP_OK);
    }

    public function show($id)
    {
        $car = Car::with('category')->find($id);
        if (!$car) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return response()->json($car, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $car = Car::create($request->all());
        return response()->json($car, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);
        if (!$car) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $car->update($request->all());
        return response()->json($car, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $car = Car::find($id);
        if (!$car) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $car->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}

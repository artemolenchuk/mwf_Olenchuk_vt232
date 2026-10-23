<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function index() { return response()->json(Car::with('category')->get(), Response::HTTP_OK); }

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

<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RentalController extends Controller
{
    public function index() { return response()->json(Rental::with(['car', 'client', 'payments'])->get(), Response::HTTP_OK); }

    public function show($id)
    {
        $rental = Rental::with(['car', 'client', 'payments'])->find($id);
        if (!$rental) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return response()->json($rental, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $rental = Rental::create($request->all());
        return response()->json($rental, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $rental = Rental::find($id);
        if (!$rental) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $rental->update($request->all());
        return response()->json($rental, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $rental = Rental::find($id);
        if (!$rental) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $rental->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}

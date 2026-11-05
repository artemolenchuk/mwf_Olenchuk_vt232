<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with(['car', 'client', 'payments']);

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->filled('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', $request->input('end_date'));
        }
        if ($request->filled('total_price')) {
            $query->where('total_price', $request->input('total_price'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->input('itemsPerPage', $request->input('per_page', 10));

        return response()->json($query->paginate((int)$perPage), Response::HTTP_OK);
    }

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

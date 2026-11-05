<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('rental');

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->filled('rental_id')) {
            $query->where('rental_id', $request->input('rental_id'));
        }
        if ($request->filled('amount')) {
            $query->where('amount', $request->input('amount'));
        }
        if ($request->filled('payment_date')) {
            $query->whereDate('payment_date', $request->input('payment_date'));
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $perPage = $request->input('itemsPerPage', $request->input('per_page', 10));

        return response()->json($query->paginate((int)$perPage), Response::HTTP_OK);
    }

    public function show($id)
    {
        $payment = Payment::with('rental')->find($id);
        if (!$payment) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return response()->json($payment, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $payment = Payment::create($request->all());
        return response()->json($payment, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $payment->update($request->all());
        return response()->json($payment, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $payment->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}

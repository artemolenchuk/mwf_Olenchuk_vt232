<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function index() { return response()->json(Payment::with('rental')->get(), Response::HTTP_OK); }

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

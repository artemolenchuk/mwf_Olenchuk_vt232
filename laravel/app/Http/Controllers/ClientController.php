<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', '%' . $request->input('full_name') . '%');
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }
        if ($request->filled('driver_license')) {
            $query->where('driver_license', $request->input('driver_license'));
        }

        $perPage = $request->input('itemsPerPage', $request->input('per_page', 10));

        return response()->json($query->paginate((int)$perPage), Response::HTTP_OK);
    }

    public function show($id)
    {
        $client = Client::find($id);
        if (!$client) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return response()->json($client, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $client = Client::create($request->all());
        return response()->json($client, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);
        if (!$client) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $client->update($request->all());
        return response()->json($client, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $client->delete();
        return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}

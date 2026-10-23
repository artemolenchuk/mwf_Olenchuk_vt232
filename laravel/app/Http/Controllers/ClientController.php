<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function index() { return response()->json(Client::all(), Response::HTTP_OK); }

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

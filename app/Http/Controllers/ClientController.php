<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function add(Request $request)
    {
        $client = Client::create([
            'nombre' => $request->name,
            'cif' => $request->cif,
            'direccion' => $request->direccion,
            'grupo' => $request->grupo,
        ]);

        $response = [
            'status' => 'success',
            'message' => 'User is created successfully.',
            'data' => $client,
        ];

        return response()->json($response, 200);
    }

    public function index(){
        return Client::all();
    }
}

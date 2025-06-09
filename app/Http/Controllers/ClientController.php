<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Validator;

class ClientController extends Controller
{
    public function index()
    {
        
        return response()->json(Client::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'string|nullable|max:255',
            'mobile_no' => 'string|nullable',
            'email' => 'email|nullable|unique:clients,email',
            'address' => 'string|nullable',
            'area' => 'string|nullable',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a valid string.',
            'first_name.max' => 'First name can be up to 255 characters only.',

            'last_name.string' => 'Last name must be a valid string.',
            'last_name.max' => 'Last name can be up to 255 characters only.',

            'mobile_no.string' => 'Mobile number must be a valid string.',

            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',

            'address.string' => 'Address must be a valid string.',
            'area.string' => 'Area must be a valid string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        $client = Client::create($data);
        return response()->json(['message' => 'Client created', 'data' => $client], 201);
    }

    public function show(Client $client)
    {
        
        return response()->json($client);
    }

    public function update(Request $request, Client $client)
    {
        
        $data = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|nullable|max:255',
            'mobile_no' => 'string|nullable',
            'email' => 'email|nullable|unique:clients,email,' . $client->id,
            'address' => 'string|nullable',
            'area' => 'string|nullable',
        ]);

        $client->update($data);
        return response()->json(['message' => 'Client updated', 'data' => $client]);
    }

    public function destroy(Client $client)
    {
        
        $client->delete();
        return response()->json(['message' => 'Client deleted']);
    }
}

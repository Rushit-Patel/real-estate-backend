<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index()
    {
        
        return response()->json(PropertyType::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $propertyType = PropertyType::create($data);
        return response()->json(['message' => 'Property type created', 'data' => $propertyType], 201);
    }

    public function show(PropertyType $propertyType)
    {
        
        return response()->json($propertyType);
    }

    public function update(Request $request, PropertyType $propertyType)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
        ]);

        $propertyType->update($data);
        return response()->json(['message' => 'Property type updated', 'data' => $propertyType]);
    }

    public function destroy(PropertyType $propertyType)
    {
        
        $propertyType->delete();
        return response()->json(['message' => 'Property type deleted']);
    }
}

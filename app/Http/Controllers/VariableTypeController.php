<?php
namespace App\Http\Controllers;

use App\Models\VariableType;
use Illuminate\Http\Request;

class VariableTypeController extends Controller
{
    public function index()
    {
        
        return response()->json(VariableType::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $variableType = VariableType::create($data);
        return response()->json(['message' => 'Variable type created', 'data' => $variableType], 201);
    }

    public function show(VariableType $variableType)
    {
        
        return response()->json($variableType);
    }

    public function update(Request $request, VariableType $variableType)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
        ]);

        $variableType->update($data);
        return response()->json(['message' => 'Variable type updated', 'data' => $variableType]);
    }

    public function destroy(VariableType $variableType)
    {
        
        $variableType->delete();
        return response()->json(['message' => 'Variable type deleted']);
    }
}
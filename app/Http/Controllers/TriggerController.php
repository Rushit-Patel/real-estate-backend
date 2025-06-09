<?php
namespace App\Http\Controllers;

use App\Models\Trigger;
use Illuminate\Http\Request;

class TriggerController extends Controller
{
    public function index()
    {
        
        return response()->json(Trigger::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'event_type' => 'required|string',
            'is_active' => 'boolean',
            'description' => 'string|nullable',
            'conditions' => 'array|nullable',
            'actions' => 'required|array',
        ]);

        $trigger = Trigger::create($data);
        return response()->json(['message' => 'Trigger created', 'data' => $trigger], 201);
    }

    public function show(Trigger $trigger)
    {
        
        return response()->json($trigger);
    }

    public function update(Request $request, Trigger $trigger)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
            'event_type' => 'string',
            'is_active' => 'boolean',
            'description' => 'string|nullable',
            'conditions' => 'array|nullable',
            'actions' => 'array',
        ]);

        $trigger->update($data);
        return response()->json(['message' => 'Trigger updated', 'data' => $trigger]);
    }

    public function destroy(Trigger $trigger)
    {
        
        $trigger->delete();
        return response()->json(['message' => 'Trigger deleted']);
    }
}
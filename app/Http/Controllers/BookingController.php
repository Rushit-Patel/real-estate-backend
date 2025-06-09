<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        
        return response()->json(Booking::with(['client', 'project', 'property'])->get());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'property_id' => 'required|exists:properties,id',
            'booking_date' => 'required|date',
            'status' => 'string|in:Confirmed,Cancelled,Pending',
            'notes' => 'string|nullable',
        ]);

        $booking = Booking::create($data);
        return response()->json(['message' => 'Booking created', 'data' => $booking->load(['client', 'project', 'property'])], 201);
    }

    public function show(Booking $booking)
    {
        
        return response()->json($booking->load(['client', 'project', 'property']));
    }

    public function update(Request $request, Booking $booking)
    {
        
        $data = $request->validate([
            'client_id' => 'exists:clients,id',
            'project_id' => 'exists:projects,id',
            'property_id' => 'exists:properties,id',
            'booking_date' => 'date',
            'status' => 'string|in:Confirmed,Cancelled,Pending',
            'notes' => 'string|nullable',
        ]);

        $booking->update($data);
        return response()->json(['message' => 'Booking updated', 'data' => $booking->load(['client', 'project', 'property'])]);
    }

    public function destroy(Booking $booking)
    {
        
        $booking->delete();
        return response()->json(['message' => 'Booking deleted']);
    }

    public function cancel(Request $request, Booking $booking)
    {
        
        $booking->update(['status' => 'Cancelled']);
        return response()->json(['message' => 'Booking cancelled', 'data' => $booking]);
    }
    public function confirm(Request $request, Booking $booking)
    {
        
        $booking->update(['status' => 'Confirmed']);
        return response()->json(['message' => 'Booking Confirmed', 'data' => $booking]);
    }
}
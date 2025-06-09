<?php
namespace App\Http\Controllers;

use App\Models\EnquiryStatus;
use Illuminate\Http\Request;

class EnquiryStatusController extends Controller
{
    public function index()
    {
        
        return response()->json(EnquiryStatus::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'string|nullable',
            'nature' => 'string|in:Normal,Positive,Negative',
        ]);

        $status = EnquiryStatus::create($data);
        return response()->json(['message' => 'Enquiry status created', 'data' => $status], 201);
    }

    public function show(EnquiryStatus $enquiryStatus)
    {
        
        return response()->json($enquiryStatus);
    }

    public function update(Request $request, EnquiryStatus $enquiryStatus)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
            'color' => 'string|nullable',
            'nature' => 'string|in:Normal,Positive,Negative',
        ]);

        $enquiryStatus->update($data);
        return response()->json(['message' => 'Enquiry status updated', 'data' => $enquiryStatus]);
    }

    public function destroy(EnquiryStatus $enquiryStatus)
    {
        
        $enquiryStatus->delete();
        return response()->json(['message' => 'Enquiry status deleted']);
    }
}
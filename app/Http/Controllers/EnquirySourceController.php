<?php
namespace App\Http\Controllers;

use App\Models\EnquirySource;
use Illuminate\Http\Request;

class EnquirySourceController extends Controller
{
    public function index()
    {
        
        return response()->json(EnquirySource::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $source = EnquirySource::create($data);
        return response()->json(['message' => 'Enquiry source created', 'data' => $source], 201);
    }

    public function show(EnquirySource $enquirySource)
    {
        
        return response()->json($enquirySource);
    }

    public function update(Request $request, EnquirySource $enquirySource)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
        ]);

        $enquirySource->update($data);
        return response()->json(['message' => 'Enquiry source updated', 'data' => $enquirySource]);
    }

    public function destroy(EnquirySource $enquirySource)
    {
        
        $enquirySource->delete();
        return response()->json(['message' => 'Enquiry source deleted']);
    }
}
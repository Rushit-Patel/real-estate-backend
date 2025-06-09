<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\Enquiry;
use App\Models\EnquiryStatus;
use Illuminate\Http\Request;

class PublicProjectController extends Controller
{
    public function show($publicId)
    {
        $project = Project::where('public_id', $publicId)->with(['contacts', 'properties', 'files'])->firstOrFail();
        return response()->json($project);
    }

    public function submitEnquiry(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|string',
            'firstName' => 'required|string|max:255',
            'lastName' => 'string|nullable|max:255',
            'mobileNo' => 'string|nullable',
            'recipient_email' => 'email|nullable',
            'message' => 'required|string',
        ]);

        // Find project by public_id
        $project = Project::where('public_id', $data['project_id'])->firstOrFail();

        // Create or find client
        $client = Client::firstOrCreate(
            ['email' => $data['recipient_email']],
            [
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'] ?? '',
                'mobile_no' => $data['mobileNo'],
            ]
        );

        // Create enquiry
        $enquiry = Enquiry::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'enquiry_status_id' => EnquiryStatus::where('name', 'New')->firstOrFail()->id,
            'general_remarks' => $data['message'],
            'source_id' => EnquirySource::where('name', 'Website')->firstOrFail()->id, // Optional: Adjust source
        ]);

        return response()->json(['message' => 'Enquiry submitted successfully', 'enquiry_id' => $enquiry->id], 201);
    }
}
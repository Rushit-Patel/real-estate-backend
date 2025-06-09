<?php
namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\WhatsAppTemplate;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helpers\Helper;
use Validator;
use App\Jobs\ProcessTriggerActions;

class BulkOperationController extends Controller
{
    public function sendBulkWhatsApp(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'enquiry_ids' => 'required|array',
            'enquiry_ids.*' => 'exists:enquiries,id',
            'template_id' => 'required|exists:whatsapp_templates,id',
            'runtime_variable_inputs' => 'array|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();


        $enquiries = Enquiry::with('client')->whereIn('id', $data['enquiry_ids'])->get();
        $template = WhatsAppTemplate::findOrFail($data['template_id']);
        $templateName = $template->name;
        foreach ($enquiries as $enquiry) {

            // Helper::Event_Triggerd([$templateName],$enquiry->id);
            ProcessTriggerActions::dispatch($data['template_id'], $enquiry->id);

            // Placeholder: Implement WhatsApp message sending logic
            // Example: Replace variables in template content with runtime values and client data
            $message = $template->content;
            if ($data['runtime_variable_inputs']) {
                foreach ($data['runtime_variable_inputs'] as $key => $value) {
                    $message = str_replace($key, $value, $message);
                }
            }
            // Example: Replace {{client_name}} with actual client name
            $message = str_replace('{{client_name}}', $enquiry->client->first_name, $message);

            // Integrate with WhatsApp API (e.g., Twilio, Gupshup)
            // Log: LogActivity::create(['message' => 'Sent WhatsApp to ' . $enquiry->client->email]);
        }

        return response()->json(['message' => 'Bulk WhatsApp messages queued']);
    }

    public function sendBulkEmail(Request $request)
    {
        
        $data = $request->validate([
            'enquiry_ids' => 'required|array',
            'enquiry_ids.*' => 'exists:enquiries,id',
            'template_id' => 'required|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $enquiries = Enquiry::with('client')->whereIn('id', $data['enquiry_ids'])->get();
        $template = EmailTemplate::findOrFail($data['template_id']);

        foreach ($enquiries as $enquiry) {
            // $emailBody = $request->body, or $template->body;
            $emailBody = $request->body;
            $emailSubject = $request->subject;
            // Replace variables
            $emailBody = str_replace('{{client_name}}', $enquiry->client->first_name, $emailBody);
            $emailBody = str_replace('{{project_name}}', $enquiry->project->name, $emailBody);

            // Send email
            Mail::raw($emailBody, function ($message) use ($enquiry, $emailSubject) {
                $message->to($enquiry->client->email)
                        ->subject($emailSubject);
            });

            // Log: LogActivity::create(['message' => 'Sent email to ' . $enquiry->client->email]);
        }

        return response()->json(['message' => 'Bulk emails queued']);
    }
}
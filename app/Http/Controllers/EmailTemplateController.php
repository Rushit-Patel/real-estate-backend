<?php
namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        
        return response()->json(EmailTemplate::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'array|nullable',
        ]);

        $emailTemplate = EmailTemplate::create($data);
        return response()->json(['message' => 'Email template created', 'data' => $emailTemplate], 201);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        
        return response()->json($emailTemplate);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
            'subject' => 'string|max:255',
            'body' => 'string',
            'variables' => 'array|nullable',
        ]);

        $emailTemplate->update($data);
        return response()->json(['message' => 'Email template updated', 'data' => $emailTemplate]);
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        
        $emailTemplate->delete();
        return response()->json(['message' => 'Email template deleted']);
    }
}
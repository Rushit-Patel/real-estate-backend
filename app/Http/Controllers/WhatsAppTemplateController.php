<?php
namespace App\Http\Controllers;

use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppTemplateVariable;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class WhatsAppTemplateController extends Controller
{
    public function index()
    {
        
        return response()->json(WhatsAppTemplate::with('variables.variableType','variables_get')->get());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'array|nullable',
            'variables.*.variable_name' => 'string',
            'variables.*.variable_type_id' => 'exists:variable_types,id|nullable',
            'variables.*.static_value' => 'string|nullable',
        ]);

        $template = WhatsAppTemplate::create($request->only(['name', 'content']));

        if ($request->variables) {
            foreach ($request->variables as $variable) {
                WhatsAppTemplateVariable::create([
                    'whatsapp_template_id' => $template->id,
                    'variable_name' => $variable['variable_name'],
                    'variable_type_id' => $variable['variable_type_id'],
                    'static_value' => $variable['static_value'],
                ]);
            }
        }

        return response()->json(['message' => 'WhatsApp template created', 'data' => $template->load('variables')], 201);
    }

    public function show(WhatsAppTemplate $whatsappTemplate)
    {
        
        return response()->json($whatsappTemplate->load('variables'));
    }

    public function update(Request $request, WhatsAppTemplate $whatsappTemplate)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
            'content' => 'string',
            'variables' => 'array|nullable',
            'variables.*.variable_name' => 'string',
            'variables.*.variable_type_id' => 'exists:variable_types,id|nullable',
            'variables.*.static_value' => 'string|nullable',
        ]);

        $whatsappTemplate->update($request->only(['name', 'content']));

        if ($request->variables) {
            $whatsappTemplate->variables()->delete();
            foreach ($request->variables as $variable) {
                WhatsAppTemplateVariable::create([
                    'whatsapp_template_id' => $whatsappTemplate->id,
                    'variable_name' => $variable['variable_name'],
                    'variable_type_id' => $variable['variable_type_id'],
                    'static_value' => $variable['static_value'],
                ]);
            }
        }

        return response()->json(['message' => 'WhatsApp template updated', 'data' => $whatsappTemplate->load('variables')]);
    }

    public function destroy(WhatsAppTemplate $whatsappTemplate)
    {
        
        $whatsappTemplate->delete();
        return response()->json(['message' => 'WhatsApp template deleted']);
    }

    public function getVariables(WhatsAppTemplate $whatsappTemplate)
    {
        
        return response()->json($whatsappTemplate->variables);
    }

    public function updateVariables(Request $request, WhatsAppTemplate $whatsappTemplate)
    {
        
        $request->validate([
            'variables' => 'required|array',
            'variables.*.variable_name' => 'required|string',
            'variables.*.variable_type_id' => 'exists:variable_types,id|nullable',
            'variables.*.static_value' => 'string|nullable',
        ]);

        $whatsappTemplate->variables()->delete();
        foreach ($request->variables as $variable) {
            WhatsAppTemplateVariable::create([
                'whatsapp_template_id' => $whatsappTemplate->id,
                'variable_name' => $variable['variable_name'],
                'variable_type_id' => $variable['variable_type_id'],
                'static_value' => $variable['static_value'],
            ]);
        }

        return response()->json(['message' => 'Variables updated', 'data' => $whatsappTemplate->load('variables')]);
    }

    public function TempleteDataSynce(Request $request){

        $data = Helper::getTemplete();

        if(isset($data['results']['templates']) && is_array($data['results']['templates'])) {
            foreach($data['results']['templates'] as $template) {
                if (!WhatsappTemplate::where('name', $template['name'])->exists()) {
                    $whatsappTemplate = new WhatsappTemplate();
                    $whatsappTemplate->uuid                     = $template['id'] ?? null;
                    $whatsappTemplate->created_at_utc           = $template['created_at_utc'] ?? null;
                    $whatsappTemplate->name                     = $template['name'] ?? null;
                    $whatsappTemplate->language                 = $template['language'] ?? null;
                    $whatsappTemplate->category                 = $template['category'] ?? null;
                    $whatsappTemplate->template_category_label  = $template['template_category_label'] ?? null;
                    $whatsappTemplate->header_format            = $template['header_format'] ?? null;
                    $whatsappTemplate->header                   = $template['header'] ?? null;
                    $whatsappTemplate->header_handle_file_url   = $template['header_handle_file_url'] ?? null;
                    $whatsappTemplate->header_handle_file_name  = $template['header_handle_file_name'] ?? null;
                    $whatsappTemplate->body                     = $template['body'] ?? null;
                    $whatsappTemplate->footer                   = $template['footer'] ?? null;
                    $whatsappTemplate->buttons                  = $template['buttons'] ?? null;
                    $whatsappTemplate->autosubmitted_for        = $template['autosubmitted_for'] ?? null;
                    $whatsappTemplate->display_name             = $template['display_name'] ?? null;
                    $whatsappTemplate->approval_status          = $template['approval_status'] ?? null;
                    $whatsappTemplate->wa_template_id           = $template['wa_template_id'] ?? null;
                    $whatsappTemplate->variable_present         = $template['variable_present'] ?? null;
                    $whatsappTemplate->save();
        
                    if (!empty($template['body'])) {
                        preg_match_all('/\{\{(\d+)\}\}/', $template['body'], $matches);
                        if (!empty($matches[1])) {
                            foreach ($matches[1] as $match) {
                                WhatsAppTemplateVariable::updateOrCreate(
                                    [
                                        'whatsapp_template_id'   => $whatsappTemplate->id,
                                        'variable_name' => '{{' . $match . '}}',
                                    ],
                                    [
                                        'variable_type_id' => '1'
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }

        

        return response()->json([
            'status' => true,
            'message' => 'Whatsapp Template Sync',
        ]);
    }
}
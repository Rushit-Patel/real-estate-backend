<?php
namespace App\Jobs;

use App\Models\Enquiry;
use App\Models\WhatsAppTemplate;
use App\TraitAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ProcessTriggerActions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels ,TraitAPI;

    protected $trigger;
    protected $enquiryId;

    public function __construct($trigger, $enquiryId)
    {
        $this->trigger = $trigger;
        $this->enquiryId = $enquiryId;
    }

    public function handle()
    {

        $GetEnquiry = Enquiry::find($this->enquiryId);

        if (!$GetEnquiry) {
            Log::error("Enquiry not found with id: {$this->enquiryId}");
            return;
        }

        $getClient = $GetEnquiry->client;

        if(isset($this->trigger->actions)){
            foreach($this->trigger->actions as $action){

                $action_type = $action['action_type'];

                if ($action_type == 'send_whatsapp') {

                    $templete_id = $action['config']['template_id'];

                    // $GetWhatsAppTemplate = WhatsAppTemplate::find($action['whatsapp_templates_id']);
                    $GetWhatsAppTemplate = WhatsAppTemplate::find($templete_id);


                $bodyValues = $this->get_values_for_whatsappvariable($templete_id, $this->enquiryId);
                    
                    $payload = [
                        "phoneNumber"  => $getClient['mobile_no'],
                        "countryCode"  => '+91',
                        "callbackData" => "",
                        "type"         => "Template",
                        "template"     => [
                            "name"         => $GetWhatsAppTemplate->name,
                            "languageCode" => "en",
                            "bodyValues"   => $bodyValues
                        ]
                    ];
                    // dd($payload);

                    // Call the API method (can be extracted to a helper or service class)
                    $this->$action_type($payload);
                }
            }
        }else{
            $GetWhatsAppTemplate = WhatsAppTemplate::find($this->trigger);
             $bodyValues = $this->get_values_for_whatsappvariable($this->trigger, $this->enquiryId);
                    
                    $payload = [
                        "phoneNumber"  => $getClient['mobile_no'],
                        "countryCode"  => '+91',
                        "callbackData" => "",
                        "type"         => "Template",
                        "template"     => [
                            "name"         => $GetWhatsAppTemplate->name,
                            "languageCode" => "en",
                            "bodyValues"   => $bodyValues
                        ]
                    ];

                    $this->send_whatsapp($payload);
        }
        
    }


        function get_values_for_whatsappvariable($whatsapp_template_id,$enquiry_id,$runtime_variables = []){
        $whatsapp_template = WhatsAppTemplate::find($whatsapp_template_id);
        $whatsapp_template_variables = $whatsapp_template->variables_get;
        $enquiry = Enquiry::find($enquiry_id);
        $return_values = [];

        foreach ($whatsapp_template_variables as $index=>$variable) {
            $variableType = $variable->variableType;
            if($variableType->name != "Runtime" || $variableType->name != "Static Text"){
                $data = "";
                if($variableType->relation != null){
                    $relation = $variableType->relation;
                    $field_name = $variableType->field_name;
                    $data = $enquiry->$relation;
                    if($variableType->relation_type == "one"){
                        $data = $data->$field_name;
                    }elseif($variableType->relation_type == "many"){
                        $data = $data->pluck($field_name)->implode(", ");
                    }
                }else{
                    $field_name = $variableType->field_name;
                    $data = $enquiry->$field_name;
                }
                $return_values[$index] = $data;
            }elseif($variableType->name != "Static Text"){
                $return_values[$index] = $variable->static_value;
            }elseif($variableType->name != "Runtime"){
                $return_values[$index] = "";
            }else{
                $return_values[$index] = "";
            }
        }

        return $return_values;
    }
}

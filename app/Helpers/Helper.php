<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App\Jobs\ProcessTriggerActions;
use App\Models\Client;
use App\Models\Enquiry;
use App\Models\Trigger;
use App\Models\WhatsAppTemplate;
use App\TraitAPI;

class Helper
{
    use TraitAPI;
    public static function getTemplete()
    {
        $instance = new self();
        $response = $instance->getApi('GET', '/track/organization/templates');
        return $response;
    }

    public static function SendMessages($response)
    {
        $payload = [
            "phoneNumber"  => $response->mobile_no,
            "countryCode"  => '+91',
            "callbackData" => "",
            "type"         => "Template",
            "template"         => [
                "name" => "property_startup_welcome_msg",
                "languageCode" => "en",
                "bodyValues" =>  []
            ]
        ];

        $instance = new self();
        $response = $instance->getApi('POST', '/message',$payload);
        return $response;
    }

    public static function Event_Triggerd($request,$payload)
    {
        $getTriggers = Trigger::where('is_active',1)->whereIn('event_type',$request)->get();
        foreach ($getTriggers as $key => $value) {
            $instance = new self();
            $instance->FireTrigger($value,$payload);
        }
    }

    public function FireTrigger($Trigger,$request){

        ProcessTriggerActions::dispatch($Trigger, $request);
    }


    

}

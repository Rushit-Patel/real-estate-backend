<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplateVariable extends Model
{
     protected $guarded = [];

     protected $table = 'whatsapp_template_variables';

    public function whatsappTemplate()
    {
        return $this->belongsTo(WhatsAppTemplate::class);
    }

    public function variableType()
    {
        return $this->belongsTo(VariableType::class);
    }
}

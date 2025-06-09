<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppTemplate extends Model
{
    use SoftDeletes;
    
    protected $table = 'whatsapp_templates';

    protected $guarded = [];

    public function variables()
    {
        return $this->hasMany(WhatsAppTemplateVariable::class,'whatsapp_template_id');
    }

    public function variables_get()
    {
        return $this->hasMany(WhatsAppTemplateVariable::class, 'whatsapp_template_id');
    }


}

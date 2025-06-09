<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
     protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }
}

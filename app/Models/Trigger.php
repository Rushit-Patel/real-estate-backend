<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trigger extends Model
{
     use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
    ];
}

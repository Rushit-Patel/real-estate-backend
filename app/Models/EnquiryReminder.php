<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryReminder extends Model
{
    use SoftDeletes;

    protected $table = 'enquiry_reminders';
    protected $guarded = [];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}

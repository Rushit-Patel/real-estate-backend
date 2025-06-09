<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->belongsTo(EnquiryStatus::class, 'enquiry_status_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assign_to_id');
    }

    public function source()
    {
        return $this->belongsTo(EnquirySource::class);
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'enquiry_property');
    }
    public function reminders()
    {
        return $this->hasMany(EnquiryReminder::class);
    }
}

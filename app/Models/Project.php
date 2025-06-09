<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(ProjectContact::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
}

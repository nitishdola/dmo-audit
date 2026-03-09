<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'name',
        'hospital_code',
        'district_id'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function treatments()
    {
        return $this->hasMany(PmjayTreatment::class);
    }
}

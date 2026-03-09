<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name',
        'lgd_code'
    ];

    public function hospitals()
    {
        return $this->hasMany(Hospital::class);
    }

    public function dmos()
    {
        return $this->belongsToMany(
            User::class,
            'dmo_districts'
        );
    }
}

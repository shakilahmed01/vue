<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $fillable = ['name', 'address', 'phone','image','flat_id'];
    public function flats()
    {
        return $this->belongsTo(Flat::class, 'flat_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentBill extends Model
{
    protected $fillable = [
        'user_id', 'month', 'flat_rent', 'electric_bill', 'gas_bill', 'water_bill', 'grand_total','due', 'past_due', 'payment', 'note'
    ];
    

    public function users()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }
}

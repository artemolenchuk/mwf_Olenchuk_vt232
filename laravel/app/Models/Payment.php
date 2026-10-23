<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['rental_id', 'amount', 'payment_date', 'payment_method'];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}

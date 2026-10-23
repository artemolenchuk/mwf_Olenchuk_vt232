<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['full_name', 'phone', 'driver_license'];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}

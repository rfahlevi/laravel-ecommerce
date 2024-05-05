<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'first_name', 'last_name', 'phone', 'street_address', 'city', 'state', 'zip_code'];

    /**
     * Get the order that owns the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Setiap alamat dimiliki oleh satu order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


    // Mendapatkan fullname (first name + last name)
    public function getFullNameAttribute() {
        return "{$this->first_name} {$this->last_name}";
    }
}

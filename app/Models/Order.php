<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'grand_total', 'payment_method', 'payment_status', 'status', 'currency', 'shipping_amount', 'shipping_method', 'notes'];

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Setiap order dimiliki oleh satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the items for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // Setiap order memiliki banyak order item
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the address associated with the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // Setiap order memiliki satu alamat
    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}

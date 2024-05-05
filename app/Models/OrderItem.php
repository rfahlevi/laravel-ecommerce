<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    //  $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    //         $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
    //         $table->integer('quantity')->default(1);
    //         $table->decimal('unit_amount', 10, 2)->nullable();
    //         $table->decimal('total_amount', 10, 2)->nullable();

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_amount', 'total_amount'];

    /**
     * Get the order that owns the OrderItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Setiap order item dimiliki oleh satu order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that owns the OrderItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

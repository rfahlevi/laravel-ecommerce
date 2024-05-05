<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'brand_id', 'name', 'slug', 'images', 'description', 'price', 'is_active', 'is_featured', 'in_stock', 'on_sale'];

    protected $casts = [
        'images' => 'array'
    ];

    /**
     * Get the category that owns the Product
     *
     * @return BelongsTo
     */
    // Setiap produk memiliki satu kategori
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Setiap produk dimiliki oleh satu brand
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get all of the orderItems for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // Satu produk memiliki banyak orderItems
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

}

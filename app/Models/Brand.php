<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'image', 'is_active'];

    /**
     * Get all of the products for the Category
     *
     * @return HasMany
     */
    // Setiap Brand memiliki banyak produk
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'reference',
        'description', 
        'price', 
        'quantity',
        'category_id',
        'min_quantity' // Seuil d'alerte
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function isOutOfStock()
    {
        return $this->quantity == 0;
    }
}


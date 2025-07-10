<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'reference',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'in' => 'Entrée',
            'out' => 'Sortie',
            'adjustment' => 'Ajustement',
            default => 'Inconnu'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'in' => 'success',
            'out' => 'danger',
            'adjustment' => 'warning',
            default => 'secondary'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'in' => 'fas fa-arrow-down',
            'out' => 'fas fa-arrow-up',
            'adjustment' => 'fas fa-exchange-alt',
            default => 'fas fa-question'
        };
    }
} 
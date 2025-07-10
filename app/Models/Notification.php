<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'low_stock' => 'fas fa-exclamation-triangle',
            'out_of_stock' => 'fas fa-times-circle',
            'system' => 'fas fa-info-circle',
            default => 'fas fa-bell'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'low_stock' => 'warning',
            'out_of_stock' => 'danger',
            'system' => 'info',
            default => 'secondary'
        };
    }
} 
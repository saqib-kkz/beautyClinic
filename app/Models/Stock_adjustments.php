<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_adjustments extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'previous_quantity',
        'adjusted_quantity',
        'difference',
        'adjustment_type',
        'reason',
    ];

    protected $casts = [
        'previous_quantity' => 'integer',
        'adjusted_quantity' => 'integer',
        'difference' => 'integer',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getAdjustmentTypeDisplayAttribute()
    {
        return match($this->adjustment_type) {
            'manual_increase' => 'Manual Increase',
            'manual_decrease' => 'Manual Decrease',
            'treatment_usage' => 'Treatment Usage',
            'stock_correction' => 'Stock Correction',
            'damaged_goods' => 'Damaged Goods',
            'expired_goods' => 'Expired Goods',
            default => ucfirst(str_replace('_', ' ', $this->adjustment_type)),
        };
    }

    public function getIsIncreaseAttribute()
    {
        return $this->difference > 0;
    }

    public function getIsDecreaseAttribute()
    {
        return $this->difference < 0;
    }

    // Scopes
    public function scopeIncreases($query)
    {
        return $query->where('difference', '>', 0);
    }

    public function scopeDecreases($query)
    {
        return $query->where('difference', '<', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('adjustment_type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

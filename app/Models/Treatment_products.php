<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment_products extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_id',
        'product_id',
        'quantity_used',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function treatment()
    {
        return $this->belongsTo(Treatments::class);
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Accessors
    public function getFormattedQuantityUsedAttribute()
    {
        if ($this->product && $this->product->isVialType()) {
            return number_format($this->quantity_used, 2) . ' ml';
        }
        return number_format($this->quantity_used, 0) . ' ' . ($this->product ? $this->product->unit_type : 'unit') . '(s)';
    }

    // Boot method to auto-calculate totals
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($treatmentProduct) {
            $treatmentProduct->total_price = $treatmentProduct->quantity_used * $treatmentProduct->unit_price;
        });
    }
}

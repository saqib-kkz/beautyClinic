<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'stock_quantity',
        'unit_type',
        'price',
        'price_per_ml',
        'low_stock_threshold',
        'is_active',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_per_ml' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
    ];

    // Relationships
    public function treatments()
    {
        return $this->belongsToMany(Treatments::class, 'treatment_products')
                    ->withPivot('quantity_used', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    public function treatmentProducts()
    {
        return $this->hasMany(Treatment_products::class, 'product_id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(Stock_adjustments::class, 'product_id');
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getFormattedPriceAttribute()
    {
        return 'AED ' . number_format($this->price, 2);
    }

    public function getUnitTypeDisplayAttribute()
    {
        return ucfirst($this->unit_type);
    }

    public function getFormattedStockQuantityAttribute()
    {
        if ($this->isVialType()) {
            return number_format($this->stock_quantity, 2) . ' ml';
        }
        return number_format($this->stock_quantity, 0) . ' ' . $this->unit_type . '(s)';
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->isVialType() && $this->price_per_ml) {
            return $this->price_per_ml;
        }
        return $this->price;
    }

    public function isVialType()
    {
        return strtolower($this->unit_type) === 'vial' ||
               strtolower($this->unit_type) === 'vial (ml)' ||
               str_contains(strtolower($this->unit_type), 'ml');
    }

    public function isUnitBasedType()
    {
        return in_array(strtolower($this->unit_type), [
            'tube', 'injection', 'syringe', 'piece'
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= low_stock_threshold');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    // Methods
    public function adjustStock($quantity, $type, $reason = null, $user_id = null)
    {
        $previousQuantity = $this->stock_quantity;
        $this->stock_quantity = max(0, $this->stock_quantity + $quantity);
        $this->save();

        // Log the adjustment
        Stock_adjustments::create([
            'product_id' => $this->id,
            'user_id' => $user_id ?? auth()->id(),
            'previous_quantity' => $previousQuantity,
            'adjusted_quantity' => $this->stock_quantity,
            'difference' => $quantity,
            'adjustment_type' => $type,
            'reason' => $reason,
        ]);

        return $this;
    }

    public function calculatePrice($quantity)
    {
        if ($this->isVialType() && $this->price_per_ml) {
            return $quantity * $this->price_per_ml;
        }
        return $quantity * $this->price;
    }

    public function formatQuantityDisplay($quantity)
    {
        if ($this->isVialType()) {
            return number_format($quantity, 2) . ' ml';
        }
        return number_format($quantity, 0) . ' ' . $this->unit_type . '(s)';
    }
}

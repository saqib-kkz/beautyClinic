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
        'low_stock_threshold',
        'is_active',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
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
        return $this->hasMany(Treatment_products::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(Stock_adjustments::class);
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getUnitTypeDisplayAttribute()
    {
        return ucfirst($this->unit_type);
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
}

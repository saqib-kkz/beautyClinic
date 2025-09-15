<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function treatments()
    {
        return $this->hasMany(Treatments::class, 'treatment_type_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    // Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public static function findOrCreateByName($name, $price = null)
    {
        $treatmentType = static::where('name', $name)->first();

        if (!$treatmentType) {
            $treatmentType = static::create([
                'name' => $name,
                'price' => $price,
                'usage_count' => 1,
            ]);
        } else {
            // Update price if provided and current price is null
            if ($price !== null && $treatmentType->price === null) {
                $treatmentType->update(['price' => $price]);
            }
            $treatmentType->incrementUsage();
        }

        return $treatmentType;
    }
}

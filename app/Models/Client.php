<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'contact_number',
        'notes',
    ];

    // Relationships
    public function treatments()
    {
        return $this->hasMany(Treatments::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoices::class, Treatments::class);
    }

    // Accessors
    public function getLastTreatmentAttribute()
    {
        return $this->treatments()->latest()->first();
    }

    public function getTotalTreatmentsAttribute()
    {
        return $this->treatments()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%");
    }
}

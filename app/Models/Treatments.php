<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatments extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'treatment_date',
        'therapist_name',
        'treatment_name',
        'treatment_reason',
        'notes',
        'status',
    ];

    protected $casts = [
        'treatment_date' => 'date',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'treatment_products')
                    ->withPivot('quantity_used', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    public function treatmentProducts()
    {
        return $this->hasMany(Treatment_products::class, 'treatment_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoices::class, 'treatment_id');
    }

    // Accessors
    public function getProductsTotalAttribute()
    {
        return $this->treatmentProducts()->sum('total_price');
    }

    public function getHasInvoiceAttribute()
    {
        return $this->invoice()->exists();
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('treatment_date', today());
    }

    public function scopeByTherapist($query, $therapist)
    {
        return $query->where('therapist_name', 'LIKE', "%{$therapist}%");
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('treatment_date', [$startDate, $endDate]);
    }
}

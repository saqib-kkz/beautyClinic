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
        'treatment_type_id',
        'treatment_date',
        'therapist_name',
        'treatment_name',
        'treatment_reason',
        'notes',
        'status',
        'treatment_amount',
        'vat_amount',
        'discount',
        'total_amount_received',
        'payment_type',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'treatment_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount_received' => 'decimal:2',
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

    public function treatmentType()
    {
        return $this->belongsTo(TreatmentType::class);
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

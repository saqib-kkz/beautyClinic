<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'treatment_id',
        'treatment_amount',
        'vat_percentage',
        'vat_amount',
        'total_amount',
        'payment_type',
        'payment_status',
        'invoice_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'treatment_amount' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function treatment()
    {
        return $this->belongsTo(Treatments::class, 'treatment_id');
    }

    public function client()
    {
        return $this->hasOneThrough(Client::class, Treatments::class, 'id', 'id', 'treatment_id', 'client_id');
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return 'AED ' . number_format($this->total_amount, 2);
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsPendingAttribute()
    {
        return $this->payment_status === 'pending';
    }

    public function getPaymentTypeDisplayAttribute()
    {
        return match($this->payment_type) {
            'cash' => 'Cash',
            'card' => 'Card',
            'tabby' => 'Tabby',
            'tamara' => 'Tamara',
            'bank_transfer' => 'Bank Transfer',
            'pending' => 'Pending',
            'free' => 'Free',
            default => ucfirst($this->payment_type),
        };
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeByPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('invoice_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('treatment.client', function($q) use ($search) {
                        $q->where('full_name', 'LIKE', "%{$search}%");
                    });
    }

    // Boot method to generate invoice number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
            
            // Auto-calculate VAT if not set
            if ($invoice->vat_amount == 0) {
                $invoice->vat_amount = ($invoice->treatment_amount * $invoice->vat_percentage) / 100;
                $invoice->total_amount = $invoice->treatment_amount + $invoice->vat_amount;
            }
        });
    }

    // Methods
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $lastInvoice = self::latest('id')->first();
        $nextNumber = $lastInvoice ? ($lastInvoice->id + 1) : 1;
        
        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}

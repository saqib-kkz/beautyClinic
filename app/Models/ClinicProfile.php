<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name',
        'logo_path',
        'address',
        'phone',
        'email',
        'website',
        'description',
        'tax_number',
        'license_number',
    ];

    public static function getClinicInfo()
    {
        return self::first() ?? new self([
            'clinic_name' => 'Swan Aesthetic Clinic',
            'address' => 'Your clinic address here',
            'phone' => '+971 XX XXX XXXX',
            'email' => 'info@swanaesthetic.com',
        ]);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }
        return asset('assets/images/logo/4.png'); // Default logo
    }
}

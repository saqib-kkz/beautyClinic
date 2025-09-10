<?php

namespace App\Http\Controllers;

use App\Models\ClinicProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClinicProfileController extends Controller
{
    public function index()
    {
        $profile = ClinicProfile::getClinicInfo();
        return view('modules.ClinicProfile.index', compact('profile'));
    }

    public function edit()
    {
        $profile = ClinicProfile::getClinicInfo();
        return view('modules.ClinicProfile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clinic_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profile = ClinicProfile::first();
            if (!$profile) {
                $profile = new ClinicProfile();
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($profile->logo_path && file_exists(public_path($profile->logo_path))) {
                    unlink(public_path($profile->logo_path));
                }

                // Store new logo
                $logoFile = $request->file('logo');
                $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = 'uploads/clinic/' . $logoName;
                
                // Create directory if it doesn't exist
                if (!file_exists(public_path('uploads/clinic'))) {
                    mkdir(public_path('uploads/clinic'), 0755, true);
                }
                
                $logoFile->move(public_path('uploads/clinic'), $logoName);
                $profile->logo_path = $logoPath;
            }

            $profile->fill($request->except(['logo']));
            $profile->save();

            return response()->json([
                'success' => true,
                'message' => 'Clinic profile updated successfully!',
                'redirect' => route('clinic-profile.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }
}

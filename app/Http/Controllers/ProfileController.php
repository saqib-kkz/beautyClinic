<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('modules.Profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('modules.Profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // If changing password, verify current password
        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is required when setting a new password',
                    'errors' => ['current_password' => ['Current password is required']]
                ], 422);
            }
            
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['Current password is incorrect']]
                ], 422);
            }
        }

        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_pic')) {
                // Delete old profile picture if exists
                if ($user->profile_pic && $user->profile_pic !== 'blank.png') {
                    $oldPicPath = public_path('assets/images/uploads/users/profile/' . $user->profile_pic);
                    if (file_exists($oldPicPath)) {
                        unlink($oldPicPath);
                    }
                }

                // Store new profile picture
                $picFile = $request->file('profile_pic');
                $picName = 'profile_' . time() . '.' . $picFile->getClientOriginalExtension();
                
                // Create directory if it doesn't exist
                $uploadDir = public_path('assets/images/uploads/users/profile');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $picFile->move($uploadDir, $picName);
                $user->profile_pic = $picName;
            }

            // Update user data
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'redirect' => route('profile.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }
}

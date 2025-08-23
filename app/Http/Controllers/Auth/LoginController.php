<?php

namespace App\Http\Controllers\Auth;

use GrahamCampbell\Binput\Facades\Binput;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use Redirect;
use Session;
use Crypt;
use DB;

class LoginController extends Controller
{
    public function Login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            return view('modules.auth.login');
        }
    }

    public function postLogin(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('get-login')
                ->withErrors($validator)
                ->withInput();
        }

        $inputdata = $request->all();
        $identity = $inputdata['identity'];
        
        // Determine if identity is email or username (we'll use email from our schema)
        $credentials = [
            filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'email' => $identity,
            'password' => $inputdata['password']
        ];

        // Add active user check to credentials
        $credentials['is_active'] = true;

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active (redundant but good for security)
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('get-login')->with('failed', 'Your account is not active. Please contact administrator.');
            }

            // Log login attempt (optional - you can implement logging here)
            $this->logUserLogin($user, $request);

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('dashboard')->with('success', 'Welcome Admin! Login successful.');
            } elseif ($user->isStaff()) {
                return redirect()->route('dashboard')->with('success', 'Welcome! Login successful.');
            }
            
            // Fallback redirect
            return redirect()->route('dashboard')->with('success', 'Login successful.');
            
        } else {
            return redirect()->route('get-login')
                ->with('failed', 'Invalid credentials. Please check your email and password.')
                ->withInput($request->only('identity'));
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout attempt (optional)
        if ($user) {
            $this->logUserLogout($user, $request);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('get-login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Log user login for audit purposes
     */
    private function logUserLogin($user, $request)
    {
        // You can implement login logging here
        // Example: Log to database, file, or external service
        
        \Log::info('User Login', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Log user logout for audit purposes
     */
    private function logUserLogout($user, $request)
    {
        \Log::info('User Logout', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'ip_address' => $request->ip(),
            'timestamp' => now(),
        ]);
    }
}

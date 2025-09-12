<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.role:admin']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $query = User::query();
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            $staff = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
            
            $data = $staff->map(function($user) {
                // Get treatments count by therapist name instead of user_id relationship
                $treatmentsCount = Treatments::where('therapist_name', $user->name)->count();
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => ucfirst($user->role),
                    'is_active' => $user->is_active,
                    'status' => $user->is_active ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-danger">Inactive</span>',
                    'created_at' => $user->created_at->format('M d, Y'),
                    'treatments_count' => $treatmentsCount,
                    'action' => $this->generateActionButtons($user)
                ];
            });
            
            return response()->json([
                'data' => $data,
                'total' => $staff->total(),
                'per_page' => $staff->perPage(),
                'current_page' => $staff->currentPage(),
                'last_page' => $staff->lastPage(),
                'from' => $staff->firstItem(),
                'to' => $staff->lastItem()
            ]);
        }

        return view('modules.Staff.staffList');
    }

    public function create()
    {
        return view('modules.Staff.addStaff');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:staff,manager,admin',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => $request->is_active == 1 ? true : false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff member created successfully',
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(User $staff)
    {
        // Load treatments by therapist name instead of user_id relationship
        $treatments = Treatments::where('therapist_name', $staff->name)
                              ->with(['client', 'treatmentProducts.product'])
                              ->orderBy('treatment_date', 'desc')
                              ->get();
        
        $staff->load(['stockAdjustments']);
        
        return view('modules.Staff.viewStaff', compact('staff', 'treatments'));
    }

    public function edit(User $staff)
    {
        if ($staff->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit admin users'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'role' => $staff->role,
                'is_active' => $staff->is_active
            ]
        ]);
    }

    public function update(Request $request, User $staff)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($staff->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:staff,manager,admin',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->is_active == 1 ? true : false
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $staff->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Staff member updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $staff)
    {
        if ($staff->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users'
            ], 403);
        }

        // Check if staff has any treatments by therapist name
        $treatmentsCount = Treatments::where('therapist_name', $staff->name)->count();
        if ($treatmentsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete staff member with existing treatments'
            ], 422);
        }

        try {
            $staff->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Staff member deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(User $staff)
    {
        if ($staff->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify admin status'
            ], 403);
        }

        try {
            $staff->update(['is_active' => !$staff->is_active]);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff status updated successfully',
                'is_active' => $staff->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating staff status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateActionButtons(User $user)
    {
        $buttons = '<div class="btn-group btn-group-sm" role="group">';
        
        $buttons .= '<a href="' . route('staff.show', $user) . '" class="btn btn-outline-primary" title="View">
                        <i class="bi bi-eye"></i>
                     </a>';
        
        $buttons .= '<button type="button" class="btn btn-outline-warning" onclick="editStaff(' . $user->id . ')" title="Edit">
                        <i class="bi bi-pencil"></i>
                     </button>';
        
        $statusClass = $user->is_active ? 'btn-outline-warning' : 'btn-outline-success';
        $statusIcon = $user->is_active ? 'pause' : 'play';
        $statusTitle = $user->is_active ? 'Deactivate' : 'Activate';
        
        $buttons .= '<button type="button" class="btn ' . $statusClass . '" onclick="toggleStaffStatus(' . $user->id . ')" title="' . $statusTitle . '">
                        <i class="bi bi-' . $statusIcon . '"></i>
                     </button>';
        
        // Check if staff has treatments by therapist name
        $treatmentsCount = Treatments::where('therapist_name', $user->name)->count();
        if ($treatmentsCount == 0) {
            $buttons .= '<button type="button" class="btn btn-outline-danger" onclick="deleteStaff(' . $user->id . ')" title="Delete">
                            <i class="bi bi-trash"></i>
                         </button>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}
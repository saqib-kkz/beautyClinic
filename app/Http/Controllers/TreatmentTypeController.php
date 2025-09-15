<?php

namespace App\Http\Controllers;

use App\Models\TreatmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TreatmentTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $query = TreatmentType::query();
            
            if ($search) {
                $query->search($search);
            }
            
            $treatmentTypes = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
            
            $data = $treatmentTypes->map(function($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'description' => $type->description ?: 'No description',
                    'price' => $type->price ? 'AED ' . number_format($type->price, 2) : 'Not set',
                    'usage_count' => $type->usage_count,
                    'is_active' => $type->is_active,
                    'status' => $type->is_active ?
                        '<span class="badge bg-success">Active</span>' :
                        '<span class="badge bg-danger">Inactive</span>',
                    'created_at' => $type->created_at->format('M d, Y'),
                    'action' => $this->getActionButtons($type)
                ];
            });
            
            return response()->json([
                'data' => $data,
                'current_page' => $treatmentTypes->currentPage(),
                'per_page' => $treatmentTypes->perPage(),
                'total' => $treatmentTypes->total(),
                'last_page' => $treatmentTypes->lastPage(),
            ]);
        }

        return view('modules.TreatmentTypes.treatmentTypesList');
    }

    private function getActionButtons($type)
    {
        return '
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-outline-warning edit-treatment-type" data-id="'.$type->id.'" title="Edit">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-outline-'.($type->is_active ? 'danger' : 'success').' toggle-status" data-id="'.$type->id.'" title="'.($type->is_active ? 'Deactivate' : 'Activate').'">
                    <i class="bi bi-'.($type->is_active ? 'x-circle' : 'check-circle').'"></i>
                </button>
            </div>
        ';
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:treatment_types,name',
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0|max:99999.99',
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
            $treatmentType = TreatmentType::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'is_active' => $request->is_active ?? true,
                'usage_count' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment type created successfully',
                'data' => $treatmentType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating treatment type: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(TreatmentType $treatmentType)
    {
        return response()->json([
            'success' => true,
            'data' => $treatmentType
        ]);
    }

    public function update(Request $request, TreatmentType $treatmentType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:treatment_types,name,' . $treatmentType->id,
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0|max:99999.99',
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
            // Store old name for comparison
            $oldName = $treatmentType->name;

            $treatmentType->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'is_active' => $request->is_active ?? true
            ]);

            // Update treatment records if name changed
            if ($oldName !== $request->name) {
                $treatmentType->treatments()->update([
                    'treatment_name' => $request->name
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Treatment type updated successfully',
                'data' => $treatmentType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating treatment type: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(TreatmentType $treatmentType)
    {
        try {
            $treatmentType->update([
                'is_active' => !$treatmentType->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment type status updated successfully',
                'is_active' => $treatmentType->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(TreatmentType $treatmentType)
    {
        try {
            // Check if treatment type is being used
            if ($treatmentType->treatments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete treatment type as it is being used in treatments'
                ], 422);
            }

            $treatmentType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Treatment type deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting treatment type: ' . $e->getMessage()
            ], 500);
        }
    }
}

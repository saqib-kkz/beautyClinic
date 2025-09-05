<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::all();
            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $html = '<a href="#" ><i class="bi bi-pencil-square"></i></a> ';
                    // $html .= '<button data-rowid="' . $row->id . '" class="btn btn-xs btn-danger btn-delete">Del</button>';
                    return $html;
                })->toJson();
        }

        return view('modules.Clients.clientsList');
    }
    function show() {
        // dd("function is working");

        return view('modules.Clients.clientsList');
    }


    // AJAX fetch with pagination
    public function fetch() {
        // Debug: Log request details
        \Log::info('Fetch method called');
        \Log::info('User authenticated: ' . (auth()->check() ? 'YES' : 'NO'));
        if (auth()->check()) {
            \Log::info('User role: ' . auth()->user()->role);
        }
        \Log::info('Is AJAX: ' . (request()->ajax() ? 'YES' : 'NO'));
        
        if (request()->ajax()) {
            $perPage = request()->get('per_page', 10);
            $search = request()->get('search', '');
            $sortBy = request()->get('sort_by', 'id');
            $sortOrder = request()->get('sort_order', 'desc');
            
            $query = Client::query();
            
            // Apply search filter
            if (!empty($search)) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                      ->orWhere('contact_number', 'LIKE', "%{$search}%")
                      ->orWhere('notes', 'LIKE', "%{$search}%");
            }
            
            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);
            
            // Get paginated results
            $Client = $query->paginate($perPage);
            
            // Debug: Log the count of Client
            \Log::info('Client count: ' . $Client->count());
            
            $data = $Client->map(function ($Client) {
                return [
                    'id' => $Client->id,
                    'full_name' => $Client->full_name,
                    'contact_number' => $Client->contact_number,
                    'notes' => $Client->notes,
                    'action' => '<a href="#" class="btn btn-sm btn-primary edit-client" data-id="' . $Client->id . '"><i class="bi bi-pencil-square"></i></a>'
                ];
            });

            // Debug: Log the data being returned
            \Log::info('Data being returned:', $data->toArray());

            return response()->json([
                'data' => $data,
                'pagination' => [
                    'current_page' => $Client->currentPage(),
                    'last_page' => $Client->lastPage(),
                    'per_page' => $Client->perPage(),
                    'total' => $Client->total(),
                    'from' => $Client->firstItem(),
                    'to' => $Client->lastItem(),
                    'has_more_pages' => $Client->hasMorePages(),
                    'next_page_url' => $Client->nextPageUrl(),
                    'prev_page_url' => $Client->previousPageUrl(),
                ]
            ]);
        }
        else {
            abort(403, 'Unauthorized Access');
        }
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            try {
                $Client = Client::findOrFail($id);
                return response()->json(['response' => "success", 'post' => $Client]);
            } catch (\Exception $e) {
                return response()->json(['response' => "error", 'message' => 'Client not found'], 404);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    public function update(Request $request, $id)
    {
        if (request()->ajax()) {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'notes' => 'nullable|string',
            ]);

            try {
                $Client = Client::findOrFail($id);
                $Client->update($request->all());
                
                return response()->json([
                    'response' => "success", 
                    'message' => 'Client updated successfully',
                    'Client' => $Client
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'response' => "error", 
                    'message' => 'Error updating Client: ' . $e->getMessage()
                ], 500);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }



    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        try {
            $Client = Client::create($request->all());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully',
                    'Client' => $Client
                ]);
            }
            
            return redirect()->route('clients.index')->with('success', 'Client created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating Client: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error creating Client: ' . $e->getMessage()]);
        }
    }

    // public function adjustStock(Request $request, $Client)
    // {
    //     $request->validate([
    //         'adjustment' => 'required|integer',
    //         'reason' => 'required|string|max:255',
    //     ]);

    //     $Client = Client::findOrFail($Client);
    //     $Client->stock_quantity += $request->adjustment;
    //     $Client->save();

    //     return response()->json(['success' => true, 'message' => 'Stock adjusted successfully']);
    // }
}

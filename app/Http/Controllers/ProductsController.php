<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Products::all();
            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $html = '<a href="#" ><i class="bi bi-pencil-square"></i></a> ';
                    // $html .= '<button data-rowid="' . $row->id . '" class="btn btn-xs btn-danger btn-delete">Del</button>';
                    return $html;
                })->toJson();
        }

        return view('modules.Products.productsList');
    }
    function show() {
        // dd("function is working");

        return view('modules.Products.productsList');
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
            
            $query = Products::query();
            
            // Apply search filter
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            }
            
            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);
            
            // Get paginated results
            $products = $query->paginate($perPage);
            
            // Debug: Log the count of products
            \Log::info('Products count: ' . $products->count());
            
            $data = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 'AED ' . number_format($product->price, 2),
                    'quantity' => $product->stock_quantity,
                    'category' => $product->unit_type ?? 'N/A',
                    'subCategory' => 'N/A',
                    'status' => $product->is_active ? 'Active' : 'Inactive',
                    'action' => '<a href="#" class="btn btn-sm btn-primary edit-product" data-id="' . $product->id . '"><i class="bi bi-pencil-square"></i></a>'
                ];
            });

            // Debug: Log the data being returned
            \Log::info('Data being returned:', $data->toArray());

            return response()->json([
                'data' => $data,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more_pages' => $products->hasMorePages(),
                    'next_page_url' => $products->nextPageUrl(),
                    'prev_page_url' => $products->previousPageUrl(),
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
                $product = Products::findOrFail($id);
                return response()->json(['response' => "success", 'post' => $product]);
            } catch (\Exception $e) {
                return response()->json(['response' => "error", 'message' => 'Product not found'], 404);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    public function update(Request $request, $id)
    {
        if (request()->ajax()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'price_per_ml' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|numeric|min:0',
                'unit_type' => 'nullable|string|max:50',
                'low_stock_threshold' => 'nullable|numeric|min:0',
                'is_active' => 'required|boolean',
            ]);

            try {
                $product = Products::findOrFail($id);
                $product->update($request->all());
                
                return response()->json([
                    'response' => "success", 
                    'message' => 'Product updated successfully',
                    'product' => $product
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'response' => "error", 
                    'message' => 'Error updating product: ' . $e->getMessage()
                ], 500);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_per_ml' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'unit_type' => 'nullable|string|max:50',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        try {
            $product = Products::create($request->all());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'product' => $product
                ]);
            }
            
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating product: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error creating product: ' . $e->getMessage()]);
        }
    }

    public function adjustStock(Request $request, $product)
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $product = Products::findOrFail($product);
        $product->stock_quantity += $request->adjustment;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Stock adjusted successfully']);
    }
}

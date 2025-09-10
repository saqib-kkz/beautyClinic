<?php

namespace App\Http\Controllers;

use App\Models\Treatments;
use App\Models\Client;
use App\Models\Products;
use App\Models\Treatment_products;
use App\Models\User;
use App\Models\TreatmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TreatmentsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            $sortBy = $request->get('sort_by', 'treatment_date');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $query = Treatments::with(['client', 'user', 'treatmentProducts.product']);
            
            // Filter by therapist name if provided
            if ($request->has('therapist') && !empty($request->therapist)) {
                $query->where('therapist_name', $request->therapist);
            }
            
            if ($search) {
                $query->whereHas('client', function($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%");
                })->orWhere('treatment_name', 'LIKE', "%{$search}%")
                  ->orWhere('therapist_name', 'LIKE', "%{$search}%");
            }
            
            $treatments = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
            
            return response()->json([
                'data' => $treatments->items(),
                'total' => $treatments->total(),
                'per_page' => $treatments->perPage(),
                'current_page' => $treatments->currentPage(),
                'last_page' => $treatments->lastPage(),
            ]);
        }

        return view('modules.Treatments.treatmentsList');
    }

    public function create()
    {
        $clients = Client::orderBy('full_name')->get();
        $products = Products::active()->inStock()->orderBy('name')->get();
        $staff = User::active()->whereIn('role', ['staff', 'manager'])->orderBy('name')->get();
        
        return view('modules.Treatments.addTreatment', compact('clients', 'products', 'staff'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'treatment_date' => 'required|date',
            'therapist_name' => 'required|string|max:255',
            'treatment_name' => 'required|string|max:255',
            'treatment_type_id' => 'nullable|integer',
            'treatment_reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'treatment_amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,card,tabby,tamara,bank_transfer',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_used' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Process products (frontend already handles merging)
        $processedProducts = [];
        foreach ($request->products as $productData) {
            $productId = $productData['product_id'];
            $quantity = $productData['quantity_used'];
            $processedProducts[$productId] = $quantity;
        }

        // Check stock availability for products
        foreach ($processedProducts as $productId => $totalQuantity) {
            $product = Products::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product not found: ID {$productId}"
                ], 422);
            }
            
            if ($product->stock_quantity < $totalQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}, Required: {$totalQuantity}"
                ], 422);
            }
        }

        DB::beginTransaction();
        
        try {
            // Handle treatment type - create new or use existing
            $treatmentType = null;
            if ($request->treatment_type_id && $request->treatment_type_id != '0') {
                // Use existing treatment type
                $treatmentType = TreatmentType::find($request->treatment_type_id);
                if ($treatmentType) {
                    $treatmentType->incrementUsage();
                }
            } else {
                // Create new treatment type or find existing by name
                $treatmentType = TreatmentType::findOrCreateByName($request->treatment_name);
            }
            
            // Calculate amounts
            $treatmentAmount = (float) $request->treatment_amount;
            $discount = (float) $request->discount;
            
            // Calculate products subtotal
            $productsSubtotal = 0;
            foreach ($processedProducts as $productId => $totalQuantity) {
                $product = Products::find($productId);
                $productsSubtotal += ($product->price * $totalQuantity);
            }
            
            // Calculate final amounts
            $subtotal = $productsSubtotal + $treatmentAmount;
            $afterDiscount = $subtotal - $discount;
            $vatAmount = $afterDiscount * 0.05;
            $totalAmountReceived = $afterDiscount + $vatAmount;
            
            // Create treatment record
            $treatment = Treatments::create([
                'client_id' => $request->client_id,
                'user_id' => auth()->id(),
                'treatment_type_id' => $treatmentType ? $treatmentType->id : null,
                'treatment_date' => $request->treatment_date,
                'therapist_name' => $request->therapist_name,
                'treatment_name' => $request->treatment_name,
                'treatment_reason' => $request->treatment_reason,
                'notes' => $request->notes,
                'treatment_amount' => $treatmentAmount,
                'vat_amount' => $vatAmount,
                'discount' => $discount,
                'total_amount_received' => $totalAmountReceived,
                'payment_type' => $request->payment_type,
                'status' => 'completed',
            ]);

            // Process products
            foreach ($processedProducts as $productId => $totalQuantity) {
                $product = Products::find($productId);
                
                // Create treatment_product record with combined quantity
                Treatment_products::create([
                    'treatment_id' => $treatment->id,
                    'product_id' => $product->id,
                    'quantity_used' => $totalQuantity,
                    'unit_price' => $product->price,
                ]);

                // Deduct stock with logging
                $product->adjustStock(
                    -$totalQuantity, 
                    'treatment_usage',
                    "Used in treatment for {$treatment->client->full_name} - {$treatment->treatment_name}",
                    auth()->id()
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Treatment logged successfully',
                'treatment_id' => $treatment->id,
                'redirect' => route('treatments.show', $treatment)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Treatments $treatment)
    {
        $treatment->load(['client', 'user', 'treatmentProducts.product']);
        
        return view('modules.Treatments.viewTreatment', compact('treatment'));
    }

    public function edit(Treatments $treatment)
    {
        $clients = Client::orderBy('full_name')->get();
        $products = Products::active()->orderBy('name')->get();
        $staff = User::active()->whereIn('role', ['staff', 'manager'])->orderBy('name')->get();
        $treatment->load(['client', 'treatmentProducts.product']);
        
        return view('modules.Treatments.editTreatment', compact('treatment', 'clients', 'products', 'staff'));
    }

    public function update(Request $request, Treatments $treatment)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'treatment_date' => 'required|date',
            'therapist_name' => 'required|string|max:255',
            'treatment_name' => 'required|string|max:255',
            'treatment_type_id' => 'nullable|integer',
            'treatment_reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'treatment_amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,card,tabby,tamara,bank_transfer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle treatment type - create new or use existing
            $treatmentType = null;
            if ($request->treatment_type_id && $request->treatment_type_id != '0') {
                // Use existing treatment type
                $treatmentType = TreatmentType::find($request->treatment_type_id);
            } else {
                // Create new treatment type or find existing by name
                $treatmentType = TreatmentType::findOrCreateByName($request->treatment_name);
            }
            
            // Calculate amounts
            $treatmentAmount = (float) $request->treatment_amount;
            $discount = (float) $request->discount;
            
            // Calculate products subtotal from existing treatment products
            $productsSubtotal = $treatment->treatmentProducts->sum('total_price');
            
            // Calculate final amounts
            $subtotal = $productsSubtotal + $treatmentAmount;
            $afterDiscount = $subtotal - $discount;
            $vatAmount = $afterDiscount * 0.05;
            $totalAmountReceived = $afterDiscount + $vatAmount;
            
            $treatment->update([
                'client_id' => $request->client_id,
                'treatment_type_id' => $treatmentType ? $treatmentType->id : null,
                'treatment_date' => $request->treatment_date,
                'therapist_name' => $request->therapist_name,
                'treatment_name' => $request->treatment_name,
                'treatment_reason' => $request->treatment_reason,
                'notes' => $request->notes,
                'treatment_amount' => $treatmentAmount,
                'vat_amount' => $vatAmount,
                'discount' => $discount,
                'total_amount_received' => $totalAmountReceived,
                'payment_type' => $request->payment_type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Treatment updated successfully',
                'redirect' => route('treatments.show', $treatment)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Treatments $treatment)
    {
        // Note: This should restore stock quantities if implemented
        // For now, soft delete or prevent deletion of completed treatments
        
        if ($treatment->invoice()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete treatment with existing invoice'
            ], 422);
        }

        try {
            $treatment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Treatment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting treatment: ' . $e->getMessage()
            ], 500);
        }
    }

    // API method to get available products with stock info
    public function getProducts(Request $request)
    {
        $search = $request->get('search', '');
        
        $products = Products::active()
            ->inStock()
            ->when($search, function($query, $search) {
                return $query->search($search);
            })
            ->select('id', 'name', 'stock_quantity', 'unit_type', 'price')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // API method to get client treatments history
    public function getClientTreatments(Request $request, Client $client)
    {
        $treatments = $client->treatments()
            ->with(['treatmentProducts.product'])
            ->orderBy('treatment_date', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $treatments
        ]);
    }

    // API method to get treatment data for printing
    public function getTreatmentForPrint(Request $request, Treatments $treatment)
    {
        $treatment->load(['client', 'user', 'treatmentProducts.product']);
        
        $grandTotal = $treatment->treatmentProducts->sum('total_price');
        $vatAmount = $grandTotal * 0.05;
        $finalTotal = $grandTotal * 1.05;
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $treatment->id,
                'treatment_name' => $treatment->treatment_name,
                'treatment_reason' => $treatment->treatment_reason,
                'treatment_date' => $treatment->treatment_date->format('F d, Y'),
                'therapist_name' => $treatment->therapist_name,
                'notes' => $treatment->notes,
                'client' => [
                    'full_name' => $treatment->client->full_name,
                    'contact_number' => $treatment->client->contact_number
                ],
                'products' => $treatment->treatmentProducts->map(function($tp) {
                    return [
                        'name' => $tp->product->name,
                        'quantity_used' => $tp->quantity_used,
                        'unit_type' => $tp->product->unit_type,
                        'unit_price' => $tp->unit_price,
                        'total_price' => $tp->total_price
                    ];
                }),
                'totals' => [
                    'subtotal' => $grandTotal,
                    'vat_amount' => $vatAmount,
                    'final_total' => $finalTotal
                ]
            ]
        ]);
    }

    // API method to get treatment type suggestions
    public function getTreatmentTypeSuggestions(Request $request)
    {
        $search = $request->get('search', '');
        
        $treatmentTypes = TreatmentType::active()
            ->when($search, function($query, $search) {
                return $query->search($search);
            })
            ->popular()
            ->select('id', 'name', 'usage_count')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $treatmentTypes
        ]);
    }
}

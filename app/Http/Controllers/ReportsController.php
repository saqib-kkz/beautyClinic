<?php

namespace App\Http\Controllers;

use App\Models\Treatments;
use App\Models\Invoices;
use App\Models\Client;
use App\Models\User;
use App\Models\Products;
use App\Models\Treatment_products;
use App\Models\Stock_adjustments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('modules.Reports.index');
    }

    public function invoiceReport(Request $request)
    {
        if ($request->ajax()) {
            $query = Treatments::with(['client', 'user', 'treatmentType'])
                ->selectRaw('
                    treatments.*,
                    clients.full_name as client_name,
                    clients.contact_number as client_phone,
                    users.name as staff_name
                ')
                ->join('clients', 'treatments.client_id', '=', 'clients.id')
                ->join('users', 'treatments.user_id', '=', 'users.id');

            // Apply filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('treatments.treatment_date', [$request->start_date, $request->end_date]);
            }

            if ($request->filled('client_id')) {
                $query->where('treatments.client_id', $request->client_id);
            }

            if ($request->filled('staff_id')) {
                $query->where('treatments.user_id', $request->staff_id);
            }

            if ($request->filled('payment_type')) {
                $query->where('treatments.payment_type', $request->payment_type);
            }

            if ($request->filled('status')) {
                $query->where('treatments.status', $request->status);
            }

            // Get data
            $treatments = $query->orderBy('treatments.treatment_date', 'desc')->get();

            // Calculate totals
            $totalRevenue = $treatments->sum('total_amount_received');
            $totalTreatmentAmount = $treatments->sum('treatment_amount');
            $totalVat = $treatments->sum('vat_amount');
            $totalDiscount = $treatments->sum('discount');
            $treatmentCount = $treatments->count();

            // Group by payment type
            $paymentTypeBreakdown = $treatments->groupBy('payment_type')->map(function ($group, $type) {
                return [
                    'type' => ucfirst($type),
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount_received')
                ];
            })->values();

            // Group by staff
            $staffBreakdown = $treatments->groupBy('staff_name')->map(function ($group, $staff) {
                return [
                    'staff' => $staff,
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount_received')
                ];
            })->values();

            // Daily breakdown
            $dailyBreakdown = $treatments->groupBy(function($treatment) {
                return Carbon::parse($treatment->treatment_date)->format('Y-m-d');
            })->map(function ($group, $date) {
                return [
                    'date' => Carbon::parse($date)->format('M d, Y'),
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount_received')
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $treatments->map(function($treatment) {
                    return [
                        'id' => $treatment->id,
                        'treatment_date' => Carbon::parse($treatment->treatment_date)->format('M d, Y'),
                        'client_name' => $treatment->client_name,
                        'client_phone' => $treatment->client_phone,
                        'treatment_name' => $treatment->treatment_name,
                        'staff_name' => $treatment->staff_name,
                        'therapist_name' => $treatment->therapist_name,
                        'treatment_amount' => $treatment->treatment_amount,
                        'discount' => $treatment->discount,
                        'vat_amount' => $treatment->vat_amount,
                        'total_amount_received' => $treatment->total_amount_received,
                        'payment_type' => ucfirst($treatment->payment_type),
                        'status' => ucfirst($treatment->status),
                    ];
                }),
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_treatment_amount' => $totalTreatmentAmount,
                    'total_vat' => $totalVat,
                    'total_discount' => $totalDiscount,
                    'treatment_count' => $treatmentCount,
                    'average_treatment_value' => $treatmentCount > 0 ? $totalRevenue / $treatmentCount : 0
                ],
                'breakdowns' => [
                    'payment_types' => $paymentTypeBreakdown,
                    'staff' => $staffBreakdown,
                    'daily' => $dailyBreakdown
                ]
            ]);
        }

        // Get filter options
        $clients = Client::orderBy('full_name')->get(['id', 'full_name']);
        $staff = User::active()->whereIn('role', ['staff', 'manager', 'admin'])->orderBy('name')->get(['id', 'name']);

        return view('modules.Reports.invoiceReport', compact('clients', 'staff'));
    }

    public function exportInvoiceReport(Request $request)
    {
        $query = Treatments::with(['client', 'user', 'treatmentType'])
            ->selectRaw('
                treatments.*,
                clients.full_name as client_name,
                clients.contact_number as client_phone,
                users.name as staff_name
            ')
            ->join('clients', 'treatments.client_id', '=', 'clients.id')
            ->join('users', 'treatments.user_id', '=', 'users.id');

        // Apply same filters as AJAX request
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('treatments.treatment_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('client_id')) {
            $query->where('treatments.client_id', $request->client_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('treatments.user_id', $request->staff_id);
        }

        if ($request->filled('payment_type')) {
            $query->where('treatments.payment_type', $request->payment_type);
        }

        if ($request->filled('status')) {
            $query->where('treatments.status', $request->status);
        }

        $treatments = $query->orderBy('treatments.treatment_date', 'desc')->get();

        // Generate CSV
        $filename = 'invoice_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($treatments) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Date', 'Client Name', 'Phone', 'Treatment', 'Staff', 'Therapist',
                'Treatment Amount', 'Discount', 'VAT', 'Total Amount', 'Payment Type', 'Status'
            ]);

            // CSV Data
            foreach ($treatments as $treatment) {
                fputcsv($file, [
                    Carbon::parse($treatment->treatment_date)->format('Y-m-d'),
                    $treatment->client_name,
                    $treatment->client_phone,
                    $treatment->treatment_name,
                    $treatment->staff_name,
                    $treatment->therapist_name,
                    $treatment->treatment_amount,
                    $treatment->discount,
                    $treatment->vat_amount,
                    $treatment->total_amount_received,
                    $treatment->payment_type,
                    $treatment->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function productReport(Request $request)
    {
        if ($request->ajax()) {
            $query = Products::query();

            // Apply filters
            if ($request->filled('product_name')) {
                $query->where('name', 'LIKE', '%' . $request->product_name . '%');
            }

            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'low_stock':
                        $query->lowStock();
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', 0);
                        break;
                    case 'in_stock':
                        $query->inStock();
                        break;
                    case 'overstocked':
                        $query->whereRaw('stock_quantity > (low_stock_threshold * 3)');
                        break;
                }
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active === 'active');
            }

            if ($request->filled('min_quantity')) {
                $query->where('stock_quantity', '>=', $request->min_quantity);
            }

            if ($request->filled('max_quantity')) {
                $query->where('stock_quantity', '<=', $request->max_quantity);
            }

            // Get products with usage statistics
            $products = $query->withCount(['treatmentProducts as total_used' => function($q) {
                $q->select(DB::raw('SUM(quantity_used)'));
            }])
            ->with(['treatmentProducts' => function($q) {
                $q->select('product_id', 'quantity_used', 'created_at')
                  ->latest()
                  ->limit(5);
            }])
            ->orderBy('name')
            ->get();

            // Add usage statistics for each product
            $products->each(function($product) {
                // Calculate usage in last 30 days
                $product->usage_last_30_days = Treatment_products::where('product_id', $product->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->sum('quantity_used');

                // Calculate average usage per treatment
                $totalTreatments = Treatment_products::where('product_id', $product->id)->count();
                $product->avg_usage_per_treatment = $totalTreatments > 0 ?
                    round($product->total_used / $totalTreatments, 2) : 0;

                // Calculate estimated days until stock out
                $product->estimated_days_until_stockout = $product->usage_last_30_days > 0 ?
                    round($product->stock_quantity / ($product->usage_last_30_days / 30)) : null;

                // Calculate stock value
                $product->stock_value = $product->stock_quantity * $product->price;
            });

            // Calculate summary statistics
            $totalProducts = $products->count();
            $activeProducts = $products->where('is_active', true)->count();
            $lowStockProducts = $products->filter(function($product) {
                return $product->is_low_stock;
            })->count();
            $outOfStockProducts = $products->where('stock_quantity', 0)->count();
            $totalStockValue = $products->sum('stock_value');
            $totalUsageLast30Days = $products->sum('usage_last_30_days');

            // Category breakdown (by unit type)
            $categoryBreakdown = $products->groupBy('unit_type')->map(function($group, $type) {
                return [
                    'type' => ucfirst($type),
                    'count' => $group->count(),
                    'total_stock' => $group->sum('stock_quantity'),
                    'total_value' => $group->sum('stock_value'),
                    'low_stock_count' => $group->filter(function($product) {
                        return $product->is_low_stock;
                    })->count()
                ];
            })->values();

            // Top used products
            $topUsedProducts = $products->sortByDesc('total_used')->take(10)->map(function($product) {
                return [
                    'name' => $product->name,
                    'total_used' => $product->total_used,
                    'current_stock' => $product->stock_quantity,
                    'usage_last_30_days' => $product->usage_last_30_days
                ];
            })->values();

            // Critical stock alerts
            $criticalStockAlerts = $products->filter(function($product) {
                return $product->stock_quantity <= $product->low_stock_threshold;
            })->map(function($product) {
                return [
                    'name' => $product->name,
                    'current_stock' => $product->stock_quantity,
                    'threshold' => $product->low_stock_threshold,
                    'estimated_days_until_stockout' => $product->estimated_days_until_stockout,
                    'priority' => $product->stock_quantity == 0 ? 'critical' : 'warning'
                ];
            })->sortBy('current_stock')->values();

            return response()->json([
                'success' => true,
                'data' => $products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'stock_quantity' => $product->stock_quantity,
                        'unit_type' => ucfirst($product->unit_type),
                        'price' => $product->price,
                        'formatted_price' => $product->formatted_price,
                        'low_stock_threshold' => $product->low_stock_threshold,
                        'is_active' => $product->is_active,
                        'is_low_stock' => $product->is_low_stock,
                        'total_used' => $product->total_used ?? 0,
                        'usage_last_30_days' => $product->usage_last_30_days,
                        'avg_usage_per_treatment' => $product->avg_usage_per_treatment,
                        'estimated_days_until_stockout' => $product->estimated_days_until_stockout,
                        'stock_value' => $product->stock_value,
                        'status' => $product->stock_quantity == 0 ? 'Out of Stock' :
                                   ($product->is_low_stock ? 'Low Stock' : 'In Stock'),
                        'status_class' => $product->stock_quantity == 0 ? 'danger' :
                                         ($product->is_low_stock ? 'warning' : 'success')
                    ];
                }),
                'summary' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                    'total_stock_value' => $totalStockValue,
                    'total_usage_last_30_days' => $totalUsageLast30Days
                ],
                'breakdowns' => [
                    'categories' => $categoryBreakdown,
                    'top_used' => $topUsedProducts,
                    'critical_alerts' => $criticalStockAlerts
                ]
            ]);
        }

        return view('modules.Reports.productReport');
    }

    public function exportProductReport(Request $request)
    {
        $query = Products::query();

        // Apply same filters as AJAX request
        if ($request->filled('product_name')) {
            $query->where('name', 'LIKE', '%' . $request->product_name . '%');
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'overstocked':
                    $query->whereRaw('stock_quantity > (low_stock_threshold * 3)');
                    break;
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'active');
        }

        if ($request->filled('min_quantity')) {
            $query->where('stock_quantity', '>=', $request->min_quantity);
        }

        if ($request->filled('max_quantity')) {
            $query->where('stock_quantity', '<=', $request->max_quantity);
        }

        $products = $query->withCount(['treatmentProducts as total_used' => function($q) {
            $q->select(DB::raw('SUM(quantity_used)'));
        }])->orderBy('name')->get();

        // Generate CSV
        $filename = 'product_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Product Name', 'Current Stock', 'Unit Type', 'Price (AED)',
                'Low Stock Threshold', 'Status', 'Total Used', 'Stock Value', 'Is Active'
            ]);

            // CSV Data
            foreach ($products as $product) {
                $status = $product->stock_quantity == 0 ? 'Out of Stock' :
                         ($product->stock_quantity <= $product->low_stock_threshold ? 'Low Stock' : 'In Stock');

                fputcsv($file, [
                    $product->name,
                    $product->stock_quantity,
                    ucfirst($product->unit_type),
                    $product->price,
                    $product->low_stock_threshold,
                    $status,
                    $product->total_used ?? 0,
                    $product->stock_quantity * $product->price,
                    $product->is_active ? 'Yes' : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
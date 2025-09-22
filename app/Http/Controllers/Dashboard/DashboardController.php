<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Treatments;
use App\Models\Client;
use App\Models\Products;
use App\Models\TreatmentType;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        // Key Performance Indicators
        $kpis = $this->getKPIs();

        // Recent activities
        $recentTreatments = $this->getRecentTreatments();

        // Stock alerts
        $stockAlerts = $this->getStockAlerts();

        // Popular treatments
        $popularTreatments = $this->getPopularTreatments();

        // Monthly revenue chart data
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Today's statistics
        $todayStats = $this->getTodayStats();

        return view('modules.dashboard.index', compact(
            'kpis',
            'recentTreatments',
            'stockAlerts',
            'popularTreatments',
            'monthlyRevenue',
            'todayStats'
        ));
    }

    private function getKPIs()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // This month's revenue
        $thisMonthRevenue = Treatments::whereBetween('treatment_date', [$thisMonth, Carbon::now()])
            ->sum('total_amount_received');

        // Last month's revenue for comparison
        $lastMonthRevenue = Treatments::whereBetween('treatment_date', [$lastMonth, $lastMonthEnd])
            ->sum('total_amount_received');

        // Enhanced percentage change calculation
        if ($lastMonthRevenue > 0) {
            // Standard growth formula: ((Current - Previous) / Previous) * 100
            $revenueChange = round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        } elseif ($thisMonthRevenue > 0) {
            // When starting from 0, show as significant positive growth
            $revenueChange = 999; // Indicates major positive change
        } else {
            // Both months are 0
            $revenueChange = 0;
        }

        return [
            'total_clients' => Client::count(),
            'active_clients' => Client::whereHas('treatments', function($q) use ($thisMonth) {
                $q->where('treatment_date', '>=', $thisMonth);
            })->count(),
            'total_treatments' => Treatments::count(),
            'treatments_this_month' => Treatments::where('treatment_date', '>=', $thisMonth)->count(),
            'total_revenue' => Treatments::sum('total_amount_received'),
            'revenue_this_month' => $thisMonthRevenue,
            'revenue_change' => round($revenueChange, 1),
            'low_stock_products' => Products::lowStock()->count(),
            'total_products' => Products::active()->count(),
        ];
    }

    private function getRecentTreatments()
    {
        return Treatments::with(['client', 'user'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function($treatment) {
                return [
                    'id' => $treatment->id,
                    'client_name' => $treatment->client->full_name,
                    'treatment_name' => $treatment->treatment_name,
                    'treatment_date' => $treatment->treatment_date->format('M d, Y'),
                    'total_amount' => $treatment->total_amount_received,
                    'staff_name' => $treatment->user->name,
                    'created_at' => $treatment->created_at->diffForHumans()
                ];
            });
    }

    private function getStockAlerts()
    {
        return Products::lowStock()
            ->active()
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'current_stock' => $product->stock_quantity,
                    'threshold' => $product->low_stock_threshold,
                    'unit_type' => $product->unit_type,
                    'is_critical' => $product->stock_quantity == 0
                ];
            });
    }

    private function getPopularTreatments()
    {
        return TreatmentType::withCount('treatments')
            ->having('treatments_count', '>', 0)
            ->orderBy('treatments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($type) {
                return [
                    'name' => $type->name,
                    'count' => $type->treatments_count,
                    'price' => $type->price
                ];
            });
    }

    private function getMonthlyRevenue()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Treatments::whereYear('treatment_date', $date->year)
                ->whereMonth('treatment_date', $date->month)
                ->sum('total_amount_received');

            $months->push([
                'month' => $date->format('M Y'),
                'revenue' => floatval($revenue)
            ]);
        }

        return $months;
    }

    private function getTodayStats()
    {
        $today = Carbon::today();

        return [
            'treatments_today' => Treatments::whereDate('treatment_date', $today)->count(),
            'revenue_today' => Treatments::whereDate('treatment_date', $today)->sum('total_amount_received'),
            'new_clients_today' => Client::whereDate('created_at', $today)->count(),
        ];
    }
}

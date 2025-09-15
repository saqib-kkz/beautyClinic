@extends('layouts.main')

@section('page_style')
    <style>
        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .stat-card.revenue {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.clients {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        }
        .stat-card.treatments {
            background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
        }
        .stat-card.products {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .stat-change {
            font-size: 0.8rem;
            margin-top: 10px;
        }
        .stat-change.positive {
            color: #d4edda;
        }
        .stat-change.negative {
            color: #f8d7da;
        }
        .quick-action-btn {
            border-radius: 10px;
            padding: 15px;
            text-decoration: none;
            color: white;
            display: block;
            text-align: center;
            transition: transform 0.2s ease-in-out;
            margin-bottom: 10px;
        }
        .quick-action-btn:hover {
            transform: scale(1.05);
            color: white;
        }
        .quick-action-btn.primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        .quick-action-btn.success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
        .quick-action-btn.warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }
        .quick-action-btn.info {
            background: linear-gradient(45deg, #17a2b8, #117a8b);
        }
        .chart-container {
            height: 300px;
            padding: 20px;
        }
        .activity-item {
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        .activity-item:hover {
            background: #e9ecef;
        }
        .alert-item {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #ffc107;
        }
        .alert-item.critical {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }
        .alert-item.warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }
        .popular-treatment {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .today-highlight {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #495057;
        }
    </style>
@endsection

@section('title') Dashboard @endsection

@section('page')
<section class="section dashboard">
    <div class="row">
        <!-- Welcome Section -->
        <div class="col-12">
            <div class="today-highlight">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2><i class="bi bi-heart-pulse me-2"></i>Welcome to Swan Aesthetic Clinic</h2>
                        <p class="mb-0">Good {{ date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}! Here's your clinic overview for today.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h4>{{ date('l, F j, Y') }}</h4>
                        <p class="mb-0">{{ $todayStats['treatments_today'] }} treatments scheduled</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="col-md-3">
            <div class="stat-card revenue">
                <div class="stat-number">AED {{ number_format($kpis['revenue_this_month'], 0) }}</div>
                <div class="stat-label">Revenue This Month</div>
                @if($kpis['revenue_change'] != 0)
                <div class="stat-change {{ $kpis['revenue_change'] > 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-{{ $kpis['revenue_change'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs($kpis['revenue_change']) }}% vs last month
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card clients">
                <div class="stat-number">{{ $kpis['active_clients'] }}</div>
                <div class="stat-label">Active Clients This Month</div>
                <div class="stat-change">
                    <i class="bi bi-people"></i>
                    {{ $kpis['total_clients'] }} total clients
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card treatments">
                <div class="stat-number">{{ $kpis['treatments_this_month'] }}</div>
                <div class="stat-label">Treatments This Month</div>
                <div class="stat-change">
                    <i class="bi bi-graph-up"></i>
                    {{ $kpis['total_treatments'] }} total treatments
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card products">
                <div class="stat-number">{{ $kpis['low_stock_products'] }}</div>
                <div class="stat-label">Low Stock Alerts</div>
                <div class="stat-change">
                    <i class="bi bi-boxes"></i>
                    {{ $kpis['total_products'] }} total products
                </div>
            </div>
        </div>

        <!-- Today's Quick Stats -->
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-calendar-day me-2"></i>Today's Overview</h6>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h3 class="text-primary">{{ $todayStats['treatments_today'] }}</h3>
                            <p class="text-muted mb-0">Treatments Today</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="text-success">AED {{ number_format($todayStats['revenue_today'], 0) }}</h3>
                            <p class="text-muted mb-0">Revenue Today</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="text-info">{{ $todayStats['new_clients_today'] }}</h3>
                            <p class="text-muted mb-0">New Clients Today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-graph-up me-2"></i>Revenue Trend (Last 6 Months)</h6>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                    <a href="{{ route('treatments.create') }}" class="quick-action-btn primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Invoice
                    </a>
                    <a href="{{ route('clients.index') }}" class="quick-action-btn success">
                        <i class="bi bi-people me-2"></i>Manage Clients
                    </a>
                    <a href="{{ route('products.index') }}" class="quick-action-btn warning">
                        <i class="bi bi-boxes me-2"></i>View Products
                    </a>
                    <a href="{{ route('reports.index') }}" class="quick-action-btn info">
                        <i class="bi bi-bar-chart me-2"></i>View Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-clock-history me-2"></i>Recent Treatments</h6>
                    @if($recentTreatments->count() > 0)
                        @foreach($recentTreatments as $treatment)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $treatment['client_name'] }}</strong>
                                    <br><small class="text-muted">{{ $treatment['treatment_name'] }}</small>
                                    <br><small class="text-muted">by {{ $treatment['staff_name'] }} • {{ $treatment['created_at'] }}</small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">AED {{ number_format($treatment['total_amount'], 0) }}</strong>
                                    <br><small class="text-muted">{{ $treatment['treatment_date'] }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-3">No recent treatments found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-exclamation-triangle me-2"></i>Stock Alerts</h6>
                    @if($stockAlerts->count() > 0)
                        @foreach($stockAlerts as $alert)
                        <div class="alert-item {{ $alert['is_critical'] ? 'critical' : 'warning' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $alert['name'] }}</strong>
                                    <br><small>{{ $alert['current_stock'] }} {{ $alert['unit_type'] }}(s) remaining</small>
                                </div>
                                <div>
                                    @if($alert['is_critical'])
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning">Low Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-boxes me-1"></i>Manage Inventory
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3 text-success">
                            <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">All products are well stocked!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Popular Treatments -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-star me-2"></i>Popular Treatments</h6>
                    @if($popularTreatments->count() > 0)
                        @foreach($popularTreatments as $treatment)
                        <div class="popular-treatment">
                            <div class="flex-grow-1">
                                <strong>{{ $treatment['name'] }}</strong>
                                <br><small class="text-muted">{{ $treatment['count'] }} treatments</small>
                            </div>
                            <div>
                                @if($treatment['price'])
                                    <span class="text-success fw-bold">AED {{ number_format($treatment['price'], 0) }}</span>
                                @else
                                    <span class="text-muted">Price not set</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-3">No treatment data available yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h6 class="section-title"><i class="bi bi-info-circle me-2"></i>System Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Total Revenue:</strong></p>
                            <p class="text-success">AED {{ number_format($kpis['total_revenue'], 0) }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Last Login:</strong></p>
                            <p class="text-muted">{{ Auth::user()->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>User Role:</strong></p>
                            <p class="text-info">{{ ucfirst(Auth::user()->role) }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Active Since:</strong></p>
                            <p class="text-muted">{{ Auth::user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page_script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const monthlyData = @json($monthlyRevenue);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Revenue (AED)',
                    data: monthlyData.map(item => item.revenue),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'AED ' + value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 6,
                        backgroundColor: '#28a745',
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }
                }
            }
        });

        // Add hover effects to cards
        $('.dashboard-card').hover(
            function() {
                $(this).css('box-shadow', '0 8px 16px rgba(0, 0, 0, 0.15)');
            },
            function() {
                $(this).css('box-shadow', '0 4px 6px rgba(0, 0, 0, 0.1)');
            }
        );

        // Auto-refresh page every 5 minutes to keep data current
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutes
    });
</script>
@endsection
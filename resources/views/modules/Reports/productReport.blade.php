@extends('layouts.main')

@section('page_style')
    <style>
        .filter-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 15px;
        }
        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .breakdown-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #495057;
            color: white;
            border: none;
            font-size: 0.9rem;
        }
        .table td {
            border-color: #dee2e6;
            font-size: 0.9rem;
        }
        .badge-stock {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        .btn-export {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
        }
        .btn-export:hover {
            background: linear-gradient(45deg, #218838, #1e7e6c);
            color: white;
        }
        .stock-level-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        .stock-level-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        .critical-alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }
        .warning-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }
    </style>
@endsection

@section('title')
    Reports
@endsection

@section('sub-title')
    Product Report
@endsection

@section('page')
    <section class="section">
        <!-- Filters -->
        <div class="filter-card">
            <form id="reportForm">
                <h6 class="mb-3"><i class="bi bi-funnel"></i> Product Filters</h6>
                <div class="row">
                    <div class="col-md-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                               placeholder="Search product name...">
                    </div>
                    <div class="col-md-3">
                        <label for="stock_status" class="form-label">Stock Status</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="">All Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="overstocked">Overstocked</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="is_active" class="form-label">Product Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="">All Products</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="min_quantity" class="form-label">Min Quantity</label>
                        <input type="number" class="form-control" id="min_quantity" name="min_quantity"
                               placeholder="0" min="0">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="max_quantity" class="form-label">Max Quantity</label>
                        <input type="number" class="form-control" id="max_quantity" name="max_quantity"
                               placeholder="1000" min="0">
                    </div>
                    <div class="col-md-9 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Generate Report
                        </button>
                        <button type="button" class="btn btn-export" id="exportBtn">
                            <i class="bi bi-download"></i> Export CSV
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" id="resetBtn">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Section -->
        <div id="summarySection" style="display: none;">
            <div class="summary-card">
                <div class="row" id="summaryContent">
                    <!-- Summary items will be populated here -->
                </div>
            </div>
        </div>

        <!-- Breakdown Section -->
        <div id="breakdownSection" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <div class="card breakdown-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-boxes"></i> Product Categories</h6>
                        </div>
                        <div class="card-body">
                            <div id="categoryBreakdown">
                                <!-- Category breakdown will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card breakdown-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-graph-up"></i> Top Used Products</h6>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div id="topUsedBreakdown">
                                <!-- Top used breakdown will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card breakdown-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stock Alerts</h6>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div id="stockAlerts">
                                <!-- Stock alerts will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div id="dataSection" style="display: none;">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-table"></i> Product Inventory Details</h6>
                    <span id="recordCount" class="badge bg-primary"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Unit Type</th>
                                    <th>Price</th>
                                    <th>Threshold</th>
                                    <th>Total Used</th>
                                    <th>Usage (30d)</th>
                                    <th>Stock Value</th>
                                    <th>Days Left</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Table data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingSection" style="display: none;">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Generating product report...</p>
                </div>
            </div>
        </div>

        <!-- No Data State -->
        <div id="noDataSection" style="display: none;">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No Products Found</h5>
                    <p class="text-muted">No products match your current filter criteria.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script>
        $(document).ready(function() {
            // Form submission
            $('#reportForm').on('submit', function(e) {
                e.preventDefault();
                generateReport();
            });

            // Export button
            $('#exportBtn').on('click', function() {
                exportReport();
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                resetForm();
            });

            // Auto-generate report on page load
            generateReport();
        });

        function generateReport() {
            showLoading();

            const formData = $('#reportForm').serialize();

            $.ajax({
                url: '{{ route("reports.product") }}',
                method: 'GET',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        displayReport(response);
                    } else {
                        showNoData();
                    }
                },
                error: function(xhr) {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to generate report. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        function displayReport(response) {
            hideLoading();

            if (response.data.length === 0) {
                showNoData();
                return;
            }

            // Display summary
            displaySummary(response.summary);

            // Display breakdowns
            displayBreakdowns(response.breakdowns);

            // Display data table
            displayDataTable(response.data);

            // Show sections
            $('#summarySection, #breakdownSection, #dataSection').show();
            $('#noDataSection').hide();
        }

        function displaySummary(summary) {
            const summaryHtml = `
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.total_products}</div>
                    <div class="summary-label">Total Products</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.active_products}</div>
                    <div class="summary-label">Active Products</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.low_stock_products}</div>
                    <div class="summary-label">Low Stock</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.out_of_stock_products}</div>
                    <div class="summary-label">Out of Stock</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">AED ${parseFloat(summary.total_stock_value).toFixed(2)}</div>
                    <div class="summary-label">Stock Value</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.total_usage_last_30_days}</div>
                    <div class="summary-label">30-Day Usage</div>
                </div>
            `;
            $('#summaryContent').html(summaryHtml);
        }

        function displayBreakdowns(breakdowns) {
            // Categories
            let categoryHtml = '';
            breakdowns.categories.forEach(function(item) {
                categoryHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>${item.type}</strong> (${item.count} products)
                            <br><small class="text-muted">Stock: ${item.total_stock} | Low: ${item.low_stock_count}</small>
                        </div>
                        <div class="text-end">
                            <strong>AED ${parseFloat(item.total_value).toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            });
            $('#categoryBreakdown').html(categoryHtml);

            // Top Used Products
            let topUsedHtml = '';
            breakdowns.top_used.forEach(function(item) {
                topUsedHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span>${item.name}</span>
                            <br><small class="text-muted">Stock: ${item.current_stock} | 30d: ${item.usage_last_30_days}</small>
                        </div>
                        <strong>${item.total_used}</strong>
                    </div>
                `;
            });
            $('#topUsedBreakdown').html(topUsedHtml);

            // Stock Alerts
            let alertsHtml = '';
            if (breakdowns.critical_alerts.length === 0) {
                alertsHtml = '<div class="text-muted text-center py-3">No critical stock alerts</div>';
            } else {
                breakdowns.critical_alerts.forEach(function(item) {
                    const alertClass = item.priority === 'critical' ? 'critical-alert' : 'warning-alert';
                    const icon = item.priority === 'critical' ? 'bi-exclamation-triangle-fill' : 'bi-exclamation-triangle';
                    const daysText = item.estimated_days_until_stockout ? `${item.estimated_days_until_stockout} days left` : 'Stock depleted';

                    alertsHtml += `
                        <div class="${alertClass}">
                            <div class="d-flex align-items-center">
                                <i class="bi ${icon} me-2"></i>
                                <div class="flex-grow-1">
                                    <strong>${item.name}</strong>
                                    <br><small>Stock: ${item.current_stock}/${item.threshold} | ${daysText}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            $('#stockAlerts').html(alertsHtml);
        }

        function displayDataTable(data) {
            let tableHtml = '';

            data.forEach(function(row) {
                const statusBadgeClass = getStatusBadgeClass(row.status_class);
                const daysLeft = row.estimated_days_until_stockout ? `${row.estimated_days_until_stockout} days` : '-';

                // Calculate stock level percentage for visual bar
                const stockPercentage = row.low_stock_threshold > 0 ?
                    Math.min(100, (row.stock_quantity / row.low_stock_threshold) * 100) : 100;
                const stockBarColor = stockPercentage <= 100 ? (stockPercentage <= 50 ? '#dc3545' : '#ffc107') : '#28a745';

                tableHtml += `
                    <tr>
                        <td>
                            <div>${row.name}</div>
                            ${!row.is_active ? '<small class="text-muted">(Inactive)</small>' : ''}
                        </td>
                        <td>
                            <div>${row.stock_quantity} ${row.unit_type}</div>
                            <div class="stock-level-bar">
                                <div class="stock-level-fill" style="width: ${Math.min(100, stockPercentage)}%; background-color: ${stockBarColor};"></div>
                            </div>
                        </td>
                        <td>${row.unit_type}</td>
                        <td>${row.formatted_price}</td>
                        <td>${row.low_stock_threshold}</td>
                        <td>${row.total_used}</td>
                        <td>${row.usage_last_30_days}</td>
                        <td><strong>AED ${parseFloat(row.stock_value).toFixed(2)}</strong></td>
                        <td>${daysLeft}</td>
                        <td><span class="badge bg-${statusBadgeClass} badge-stock">${row.status}</span></td>
                    </tr>
                `;
            });

            $('#reportTableBody').html(tableHtml);
            $('#recordCount').text(`${data.length} products`);
        }

        function getStatusBadgeClass(statusClass) {
            const classes = {
                'success': 'success',
                'warning': 'warning',
                'danger': 'danger'
            };
            return classes[statusClass] || 'secondary';
        }

        function exportReport() {
            const formData = $('#reportForm').serialize();
            window.location.href = '{{ route("reports.product.export") }}?' + formData;
        }

        function resetForm() {
            $('#reportForm')[0].reset();
            generateReport();
        }

        function showLoading() {
            $('#summarySection, #breakdownSection, #dataSection, #noDataSection').hide();
            $('#loadingSection').show();
        }

        function hideLoading() {
            $('#loadingSection').hide();
        }

        function showNoData() {
            $('#summarySection, #breakdownSection, #dataSection, #loadingSection').hide();
            $('#noDataSection').show();
        }
    </script>
@endsection
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .badge-payment {
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
    </style>
@endsection

@section('title')
    Reports
@endsection

@section('sub-title')
    Invoice Report
@endsection

@section('page')
    <section class="section">
        <!-- Filters -->
        <div class="filter-card">
            <form id="reportForm">
                <h6 class="mb-3"><i class="bi bi-funnel"></i> Report Filters</h6>
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ date('Y-m-t') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select class="form-select" id="client_id" name="client_id">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="staff_id" class="form-label">Staff</label>
                        <select class="form-select" id="staff_id" name="staff_id">
                            <option value="">All Staff</option>
                            @foreach($staff as $staffMember)
                                <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="payment_type" class="form-label">Payment Type</label>
                        <select class="form-select" id="payment_type" name="payment_type">
                            <option value="">All Types</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="tabby">Tabby</option>
                            <option value="tamara">Tamara</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
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
                            <h6 class="mb-0"><i class="bi bi-credit-card"></i> Payment Types</h6>
                        </div>
                        <div class="card-body">
                            <div id="paymentTypeBreakdown">
                                <!-- Payment type breakdown will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card breakdown-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-people"></i> Staff Performance</h6>
                        </div>
                        <div class="card-body">
                            <div id="staffBreakdown">
                                <!-- Staff breakdown will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card breakdown-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-calendar"></i> Daily Summary</h6>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div id="dailyBreakdown">
                                <!-- Daily breakdown will be populated here -->
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
                    <h6 class="mb-0"><i class="bi bi-table"></i> Detailed Report</h6>
                    <span id="recordCount" class="badge bg-primary"></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Treatment</th>
                                    <th>Staff</th>
                                    <th>Therapist</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>VAT</th>
                                    <th>Total</th>
                                    <th>Payment</th>
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
                    <p class="mt-3">Generating report...</p>
                </div>
            </div>
        </div>

        <!-- No Data State -->
        <div id="noDataSection" style="display: none;">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No Data Found</h5>
                    <p class="text-muted">No invoices match your current filter criteria.</p>
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
                url: '{{ route("reports.invoice") }}',
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
                    <div class="summary-value">AED ${parseFloat(summary.total_revenue).toFixed(2)}</div>
                    <div class="summary-label">Total Revenue</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">${summary.treatment_count}</div>
                    <div class="summary-label">Treatments</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">AED ${parseFloat(summary.average_treatment_value).toFixed(2)}</div>
                    <div class="summary-label">Avg. Treatment</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">AED ${parseFloat(summary.total_vat).toFixed(2)}</div>
                    <div class="summary-label">Total VAT</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">AED ${parseFloat(summary.total_discount).toFixed(2)}</div>
                    <div class="summary-label">Total Discounts</div>
                </div>
                <div class="col-md-2 summary-item">
                    <div class="summary-value">AED ${parseFloat(summary.total_treatment_amount).toFixed(2)}</div>
                    <div class="summary-label">Gross Amount</div>
                </div>
            `;
            $('#summaryContent').html(summaryHtml);
        }

        function displayBreakdowns(breakdowns) {
            // Payment Types
            let paymentHtml = '';
            breakdowns.payment_types.forEach(function(item) {
                paymentHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>${item.type} (${item.count})</span>
                        <strong>AED ${parseFloat(item.total).toFixed(2)}</strong>
                    </div>
                `;
            });
            $('#paymentTypeBreakdown').html(paymentHtml);

            // Staff Performance
            let staffHtml = '';
            breakdowns.staff.forEach(function(item) {
                staffHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>${item.staff} (${item.count})</span>
                        <strong>AED ${parseFloat(item.total).toFixed(2)}</strong>
                    </div>
                `;
            });
            $('#staffBreakdown').html(staffHtml);

            // Daily Breakdown
            let dailyHtml = '';
            breakdowns.daily.slice(0, 10).forEach(function(item) {
                dailyHtml += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>${item.date} (${item.count})</span>
                        <strong>AED ${parseFloat(item.total).toFixed(2)}</strong>
                    </div>
                `;
            });
            $('#dailyBreakdown').html(dailyHtml);
        }

        function displayDataTable(data) {
            let tableHtml = '';

            data.forEach(function(row) {
                const paymentBadgeClass = getPaymentBadgeClass(row.payment_type);
                const statusBadgeClass = getStatusBadgeClass(row.status);

                tableHtml += `
                    <tr>
                        <td>${row.treatment_date}</td>
                        <td>
                            <div>${row.client_name}</div>
                            <small class="text-muted">${row.client_phone}</small>
                        </td>
                        <td>${row.treatment_name}</td>
                        <td>${row.staff_name}</td>
                        <td>${row.therapist_name}</td>
                        <td>AED ${parseFloat(row.treatment_amount).toFixed(2)}</td>
                        <td>AED ${parseFloat(row.discount).toFixed(2)}</td>
                        <td>AED ${parseFloat(row.vat_amount).toFixed(2)}</td>
                        <td><strong>AED ${parseFloat(row.total_amount_received).toFixed(2)}</strong></td>
                        <td><span class="badge ${paymentBadgeClass} badge-payment">${row.payment_type}</span></td>
                        <td><span class="badge ${statusBadgeClass}">${row.status}</span></td>
                    </tr>
                `;
            });

            $('#reportTableBody').html(tableHtml);
            $('#recordCount').text(`${data.length} records`);
        }

        function getPaymentBadgeClass(paymentType) {
            const classes = {
                'Cash': 'bg-success',
                'Card': 'bg-primary',
                'Tabby': 'bg-warning',
                'Tamara': 'bg-info',
                'Bank_transfer': 'bg-secondary'
            };
            return classes[paymentType] || 'bg-secondary';
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'Completed': 'bg-success',
                'Pending': 'bg-warning',
                'Cancelled': 'bg-danger'
            };
            return classes[status] || 'bg-secondary';
        }

        function exportReport() {
            const formData = $('#reportForm').serialize();
            window.location.href = '{{ route("reports.invoice.export") }}?' + formData;
        }

        function resetForm() {
            $('#reportForm')[0].reset();
            $('#start_date').val('{{ date('Y-m-01') }}');
            $('#end_date').val('{{ date('Y-m-t') }}');
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
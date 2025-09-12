@extends('layouts.main')

@section('page_style')
    <link href="{{ getadminasset('vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <style>
        .modal-lg {
            max-width: 900px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .btn:disabled {
            cursor: not-allowed;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .sort-icon {
            font-size: 0.8em;
            margin-left: 5px;
        }
        .treatment-status {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.85em;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .products-used {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
@endsection

@section('title')
    Treatments
@endsection

@section('sub-title')
    All Treatments
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">All Treatments</h5>
                            <a href="{{ route('treatments.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add New Treatment
                            </a>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search treatments...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="perPageSelect" class="form-select">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary" type="button" id="refreshBtn" title="Refresh">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Treatments Table -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="treatmentsTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-column="treatment_date">
                                            Date <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="client_name">
                                            Client <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="treatment_name">
                                            Treatment <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="therapist_name">
                                            Therapist <span class="sort-icon">⇅</span>
                                        </th>
                                        <th>Products Used</th>
                                        <th class="sortable" data-column="status">
                                            Status <span class="sort-icon">⇅</span>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="treatmentsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Treatments pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
    <script>
        let currentPage = 1;
        let currentSort = { column: 'treatment_date', order: 'desc' };
        let currentSearch = '';
        let currentPerPage = 10;
        let isLoading = false;
        
        // Clinic data for invoices
        const clinicData = @json($clinic);

        $(document).ready(function() {
            loadTreatments();

            // Search functionality
            $('#searchInput').on('input', debounce(function() {
                currentSearch = $(this).val();
                currentPage = 1;
                loadTreatments();
            }, 500));
            
            // Search button
            $('#searchBtn').on('click', function() {
                currentSearch = $('#searchInput').val();
                currentPage = 1;
                loadTreatments();
            });
            
            // Search on Enter key
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });
            
            // Refresh button
            $('#refreshBtn').on('click', function() {
                currentSearch = '';
                $('#searchInput').val('');
                currentPage = 1;
                loadTreatments();
            });

            // Per page change
            $('#perPageSelect').on('change', function() {
                currentPerPage = parseInt($(this).val());
                currentPage = 1;
                loadTreatments();
            });

            // Sorting
            $('.sortable').on('click', function() {
                const column = $(this).data('column');
                
                if (currentSort.column === column) {
                    currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.order = 'asc';
                }
                
                currentPage = 1;
                loadTreatments();
                
                // Update sort icons
                $('.sort-icon').html('⇅');
                $(this).find('.sort-icon').html(currentSort.order === 'asc' ? '↑' : '↓');
            });
        });

        function loadTreatments() {
            if (isLoading) return;
            
            isLoading = true;
            $('#treatmentsTable').addClass('loading');
            
            // Show loader in table body
            $('#treatmentsTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '{{ route("treatments.index") }}',
                method: 'GET',
                data: {
                    page: currentPage,
                    per_page: currentPerPage,
                    search: currentSearch,
                    sort_by: currentSort.column,
                    sort_order: currentSort.order
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    renderTreatmentsTable(response.data);
                    renderPagination(response);
                },
                error: function(xhr) {
                    console.error('Error loading treatments:', xhr);
                    showErrorMessage('Error loading treatments. Please try again.');
                },
                complete: function() {
                    isLoading = false;
                    $('#treatmentsTable').removeClass('loading');
                }
            });
        }

        function renderTreatmentsTable(treatments) {
            const tbody = $('#treatmentsTableBody');
            tbody.empty();

            if (treatments.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No treatments found
                        </td>
                    </tr>
                `);
                return;
            }

            treatments.forEach(treatment => {
                const productsUsed = treatment.treatment_products ? 
                    treatment.treatment_products.map(tp => `${tp.product.name} (${tp.quantity_used})`).join(', ') 
                    : 'No products';
                    
                const statusClass = treatment.status === 'completed' ? 'status-completed' : 'status-pending';
                const treatmentDate = new Date(treatment.treatment_date).toLocaleDateString();

                tbody.append(`
                    <tr>
                        <td>${treatmentDate}</td>
                        <td>
                            <strong>${treatment.client.full_name}</strong><br>
                            <small class="text-muted">${treatment.client.contact_number || 'N/A'}</small>
                        </td>
                        <td>
                            <strong>${treatment.treatment_name}</strong>
                            ${treatment.treatment_reason ? `<br><small class="text-muted">${treatment.treatment_reason}</small>` : ''}
                        </td>
                        <td>${treatment.therapist_name}</td>
                        <td><small class="products-used">${productsUsed}</small></td>
                        <td><span class="treatment-status ${statusClass}">${treatment.status}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="/treatments/${treatment.id}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/treatments/${treatment.id}/edit" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-outline-info" title="Print Treatment Receipt" onclick="printTreatmentReceipt(${JSON.stringify(treatment).replace(/"/g, '&quot;')})">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }

        function renderPagination(response) {
            const pagination = $('#pagination');
            pagination.empty();

            if (response.last_page <= 1) return;

            // Previous button
            if (response.current_page > 1) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${response.current_page - 1})">&laquo;</a>
                    </li>
                `);
            }

            // Page numbers
            const startPage = Math.max(1, response.current_page - 2);
            const endPage = Math.min(response.last_page, response.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                const active = i === response.current_page ? 'active' : '';
                pagination.append(`
                    <li class="page-item ${active}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `);
            }

            // Next button
            if (response.current_page < response.last_page) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${response.current_page + 1})">&raquo;</a>
                    </li>
                `);
            }
        }

        function changePage(page) {
            currentPage = page;
            loadTreatments();
        }

        function printTreatmentReceipt(treatmentObj) {
            console.log('Print button clicked for treatment:', treatmentObj);
            
            // Use the treatment data directly from the list
            const treatment = typeof treatmentObj === 'string' ? JSON.parse(treatmentObj) : treatmentObj;
                        
            // Calculate totals
            let productsSubtotal = 0;
            if (treatment.treatment_products && treatment.treatment_products.length > 0) {
                productsSubtotal = treatment.treatment_products.reduce((sum, tp) => sum + parseFloat(tp.unit_price) * parseInt(tp.quantity_used), 0);
            }
            const treatmentAmount = parseFloat(treatment.treatment_amount) || 0;
            const discount = parseFloat(treatment.discount) || 0;
            const subtotal = productsSubtotal + treatmentAmount;
            const afterDiscount = subtotal - discount;
            const vatAmount = afterDiscount * 0.05;
            const finalTotal = afterDiscount + vatAmount;
            
            const treatmentDate = new Date(treatment.treatment_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
            
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Treatment Details - ${treatment.treatment_name}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                        .info-section { margin-bottom: 20px; }
                        .info-label { font-weight: bold; margin-top: 10px; }
                        .products-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        .products-table th, .products-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .products-table th { background-color: #f2f2f2; }
                        .total-row { font-weight: bold; background-color: #f9f9f9; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <img src="${clinicData.logo_url || '{{ asset('assets/images/logo/4.png') }}'}" alt="${clinicData.clinic_name}" style="height: 60px; margin-bottom: 10px;">
                        <h1>${clinicData.clinic_name} - Treatment Receipt</h1>
                        <p>Invoice No: SAC-${String(treatment.id).padStart(5, '0')}</p>
                        ${clinicData.address ? `<p style="font-size: 12px; margin: 5px 0;">${clinicData.address}</p>` : ''}
                        ${(clinicData.phone || clinicData.email) ? `
                        <p style="font-size: 12px; margin: 5px 0;">
                            ${clinicData.phone ? `Phone: ${clinicData.phone}` : ''}
                            ${clinicData.phone && clinicData.email ? ' | ' : ''}
                            ${clinicData.email ? `Email: ${clinicData.email}` : ''}
                        </p>` : ''}
                    </div>

                    <div class="info-section">
                        <div class="info-label">Client:</div>
                        <div>${treatment.client.full_name}</div>
                        
                        <div class="info-label">Treatment:</div>
                        <div>${treatment.treatment_name}</div>
                        
                        <div class="info-label">Therapist:</div>
                        <div>${treatment.therapist_name}</div>
                        
                        ${treatment.treatment_reason ? `
                        <div class="info-label">Reason:</div>
                        <div>${treatment.treatment_reason}</div>
                        ` : ''}
                    </div>

                    ${treatment.treatment_products && treatment.treatment_products.length > 0 ? `
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${treatment.treatment_products.map(tp => `
                            <tr>
                                <td>${tp.product.name}</td>
                                <td>${tp.quantity_used} ${tp.product.unit_type}</td>
                                <td>AED ${parseFloat(tp.unit_price).toFixed(2)}</td>
                                <td>AED ${(parseFloat(tp.unit_price) * parseInt(tp.quantity_used)).toFixed(2)}</td>
                            </tr>
                            `).join('')}
                            <tr class="total-row">
                                <td colspan="3">Products Subtotal:</td>
                                <td>AED ${productsSubtotal.toFixed(2)}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">Treatment Amount:</td>
                                <td>AED ${treatmentAmount.toFixed(2)}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">Subtotal:</td>
                                <td>AED ${subtotal.toFixed(2)}</td>
                            </tr>
                            <tr class="total-row" style="color: #dc3545;">
                                <td colspan="3">Discount:</td>
                                <td>- AED ${discount.toFixed(2)}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">VAT (5%):</td>
                                <td>AED ${vatAmount.toFixed(2)}</td>
                            </tr>
                            <tr class="total-row" style="background-color: #28a745; color: white;">
                                <td colspan="3">Total Amount:</td>
                                <td>AED ${finalTotal.toFixed(2)}</td>
                            </tr>
                        </tbody>
                    </table>
                    ` : ''}

                    ${treatment.notes ? `
                    <div class="info-section">
                        <div class="info-label">Notes:</div>
                        <div>${treatment.notes}</div>
                    </div>
                    ` : ''}

                    <div class="info-section">
                        <div class="info-label">Payment Method:</div>
                        <div>${treatment.payment_type ? treatment.payment_type.charAt(0).toUpperCase() + treatment.payment_type.slice(1).replace('_', ' ') : 'Not specified'}</div>
                    </div>

                    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
                        <div>Treatment Date: ${treatmentDate}</div>
                        <div>Generated on ${new Date().toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'})}</div>
                    </div>
                </body>
                </html>
            `;

                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(printContent);
                        printWindow.document.close();
                        printWindow.focus();
                        
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        }
    </script>
@endsection
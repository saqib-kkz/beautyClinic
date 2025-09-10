@extends('layouts.main')

@section('page_style')
    <style>
        .info-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
            margin-bottom: 15px;
        }
        .treatment-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .products-table th {
            background-color: #f8f9fa;
        }
        .total-section {
            background-color: #e7f3ff;
            border-radius: 8px;
            padding: 20px;
        }
        .btn-group-custom .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('title')
    Treatments
@endsection

@section('sub-title')
    Treatment Details
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <!-- Treatment Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Treatment Information</h5>
                            <span class="treatment-status status-{{ $treatment->status }}">
                                {{ ucfirst($treatment->status) }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Client Information</div>
                                    <div class="info-value">
                                        <strong>{{ $treatment->client->full_name }}</strong><br>
                                        @if($treatment->client->contact_number)
                                            <i class="bi bi-telephone"></i> {{ $treatment->client->contact_number }}
                                        @endif
                                    </div>

                                    <div class="info-label">Treatment Date</div>
                                    <div class="info-value">
                                        <i class="bi bi-calendar"></i> {{ $treatment->treatment_date->format('M d, Y') }}
                                    </div>

                                    <div class="info-label">Therapist</div>
                                    <div class="info-value">
                                        <i class="bi bi-person"></i> {{ $treatment->therapist_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Treatment Name</div>
                                    <div class="info-value">{{ $treatment->treatment_name }}</div>

                                    @if($treatment->treatment_reason)
                                        <div class="info-label">Treatment Reason</div>
                                        <div class="info-value">{{ $treatment->treatment_reason }}</div>
                                    @endif

                                    <div class="info-label">Created By</div>
                                    <div class="info-value">
                                        {{ $treatment->user->name ?? 'System' }} 
                                        <small class="text-muted">on {{ $treatment->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($treatment->notes)
                            <div class="info-card">
                                <div class="info-label">Notes</div>
                                <div class="info-value">{{ $treatment->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Products Used -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Products Used</h5>

                        @if($treatment->treatmentProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped products-table">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit Type</th>
                                            <th>Quantity Used</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $grandTotal = 0; @endphp
                                        @foreach($treatment->treatmentProducts as $tp)
                                            @php $grandTotal += $tp->total_price; @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $tp->product->name }}</strong>
                                                    @if($tp->product->description)
                                                        <br><small class="text-muted">{{ $tp->product->description }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ ucfirst($tp->product->unit_type) }}</td>
                                                <td>{{ $tp->quantity_used }}</td>
                                                <td>AED {{ number_format($tp->unit_price, 2) }}</td>
                                                <td><strong>AED {{ number_format($tp->total_price, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <td colspan="4" class="text-end"><strong>Products Subtotal:</strong></td>
                                            <td><strong>AED {{ number_format($grandTotal, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td colspan="4" class="text-end"><strong>Treatment Amount:</strong></td>
                                            <td><strong>AED {{ number_format($treatment->treatment_amount, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td><strong>AED {{ number_format($grandTotal + $treatment->treatment_amount, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                            <td><strong>- AED {{ number_format($treatment->discount, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td colspan="4" class="text-end"><strong>VAT (5%):</strong></td>
                                            <td><strong>AED {{ number_format($treatment->vat_amount, 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-success">
                                            <td colspan="4" class="text-end"><strong>Total Amount Received:</strong></td>
                                            <td><strong>AED {{ number_format($treatment->total_amount_received, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p>No products were used in this treatment</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Payment Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Payment Type</div>
                                    <div class="info-value">
                                        @if($treatment->payment_type)
                                            <span class="badge {{ $treatment->payment_type == 'cash' ? 'bg-success' : ($treatment->payment_type == 'card' ? 'bg-primary' : 'bg-info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $treatment->payment_type)) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </div>

                                    <div class="info-label">Treatment Amount</div>
                                    <div class="info-value">AED {{ number_format($treatment->treatment_amount, 2) }}</div>

                                    <div class="info-label">Discount Applied</div>
                                    <div class="info-value">AED {{ number_format($treatment->discount, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">VAT Amount (5%)</div>
                                    <div class="info-value">AED {{ number_format($treatment->vat_amount, 2) }}</div>

                                    <div class="info-label">Total Amount Received</div>
                                    <div class="info-value">
                                        <strong class="text-success" style="font-size: 1.2em;">
                                            AED {{ number_format($treatment->total_amount_received, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actions</h5>

                        <div class="btn-group-custom d-grid gap-2">
                            <a href="{{ route('treatments.edit', $treatment) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Treatment
                            </a>


                            <button class="btn btn-info" onclick="printTreatment()">
                                <i class="bi bi-printer"></i> Print Treatment Receipt
                            </button>

                            <hr>

                            <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Treatments
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Treatment Summary -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Treatment Summary</h5>

                        <div class="total-section">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <h6>Products Used</h6>
                                    <h4>{{ $treatment->treatmentProducts->count() }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <h6>Total Quantity</h6>
                                    <h4>{{ $treatment->treatmentProducts->sum('quantity_used') }}</h4>
                                </div>
                                <div class="col-12">
                                    <h6>Total Amount Received</h6>
                                    <h4 class="text-success">
                                        AED {{ number_format($treatment->total_amount_received, 2) }}
                                        <small class="text-muted">(inc. VAT)</small>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client History -->
                @if($treatment->client->treatments->count() > 1)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Client Treatment History</h5>
                            
                            <div class="list-group list-group-flush">
                                @foreach($treatment->client->treatments()->where('id', '!=', $treatment->id)->latest()->take(5)->get() as $pastTreatment)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $pastTreatment->treatment_name }}</h6>
                                            <small>{{ $pastTreatment->treatment_date->format('M d') }}</small>
                                        </div>
                                        <p class="mb-1">{{ $pastTreatment->therapist_name }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Total treatments: {{ $treatment->client->totalTreatments }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script>
        function printTreatment() {
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Treatment Details - {{ $treatment->treatment_name }}</title>
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
                        <img src="{{ $clinic->logo_url }}" alt="{{ $clinic->clinic_name }}" style="height: 60px; margin-bottom: 10px;">
                        <h1>{{ $clinic->clinic_name }} - Treatment Receipt</h1>
                        <p>Invoice No: SAC-{{ str_pad($treatment->id, 5, '0', STR_PAD_LEFT) }}</p>
                        @if($clinic->address)
                        <p style="font-size: 12px; margin: 5px 0;">{{ $clinic->address }}</p>
                        @endif
                        @if($clinic->phone || $clinic->email)
                        <p style="font-size: 12px; margin: 5px 0;">
                            @if($clinic->phone)Phone: {{ $clinic->phone }}@endif
                            @if($clinic->phone && $clinic->email) | @endif
                            @if($clinic->email)Email: {{ $clinic->email }}@endif
                        </p>
                        @endif
                    </div>

                    <div class="info-section">
                        <div class="info-label">Client:</div>
                        <div>{{ $treatment->client->full_name }}</div>
                        
                        <div class="info-label">Treatment:</div>
                        <div>{{ $treatment->treatment_name }}</div>
                        
                        <div class="info-label">Therapist:</div>
                        <div>{{ $treatment->therapist_name }}</div>
                        
                        @if($treatment->treatment_reason)
                        <div class="info-label">Reason:</div>
                        <div>{{ $treatment->treatment_reason }}</div>
                        @endif
                    </div>

                    @if($treatment->treatmentProducts->count() > 0)
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
                            @foreach($treatment->treatmentProducts as $tp)
                            <tr>
                                <td>{{ $tp->product->name }}</td>
                                <td>{{ $tp->quantity_used }} {{ $tp->product->unit_type }}</td>
                                <td>AED {{ number_format($tp->unit_price, 2) }}</td>
                                <td>AED {{ number_format($tp->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3">Products Subtotal:</td>
                                <td>AED {{ number_format($treatment->treatmentProducts->sum('total_price'), 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">Treatment Amount:</td>
                                <td>AED {{ number_format($treatment->treatment_amount, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">Subtotal:</td>
                                <td>AED {{ number_format($treatment->treatmentProducts->sum('total_price') + $treatment->treatment_amount, 2) }}</td>
                            </tr>
                            <tr class="total-row" style="color: #dc3545;">
                                <td colspan="3">Discount:</td>
                                <td>- AED {{ number_format($treatment->discount, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="3">VAT (5%):</td>
                                <td>AED {{ number_format($treatment->vat_amount, 2) }}</td>
                            </tr>
                            <tr class="total-row" style="background-color: #28a745; color: white;">
                                <td colspan="3">Total Amount:</td>
                                <td>AED {{ number_format($treatment->total_amount_received, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endif

                    @if($treatment->notes)
                    <div class="info-section">
                        <div class="info-label">Notes:</div>
                        <div>{{ $treatment->notes }}</div>
                    </div>
                    @endif

                    <div class="info-section">
                        <div class="info-label">Payment Method:</div>
                        <div>{{ ucfirst(str_replace('_', ' ', $treatment->payment_type)) }}</div>
                    </div>

                    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
                        <div>Treatment Date: {{ $treatment->treatment_date->format('F d, Y') }}</div>
                        <div>Generated on {{ now()->format('F d, Y H:i') }}</div>
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
    </script>
@endsection
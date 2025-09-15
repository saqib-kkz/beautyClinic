@extends('layouts.main')

@section('page_style')
    <style>
        .report-card {
            transition: transform 0.2s ease-in-out;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .report-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .report-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .report-description {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
@endsection

@section('title')
    Reports
@endsection

@section('sub-title')
    Business Reports & Analytics
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Available Reports</h5>
                        <p class="text-muted">Generate comprehensive reports to analyze your business performance</p>

                        <div class="row mt-4">
                            <!-- Invoice Report -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-primary">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <h6 class="report-title">Invoice Report</h6>
                                        <p class="report-description">
                                            Detailed invoice analytics with filtering by date range, client, staff, and payment type.
                                            Includes revenue summaries and breakdowns.
                                        </p>
                                        <a href="{{ route('reports.invoice') }}" class="btn btn-primary">
                                            <i class="bi bi-bar-chart"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Report -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-success">
                                            <i class="bi bi-box"></i>
                                        </div>
                                        <h6 class="report-title">Product Report</h6>
                                        <p class="report-description">
                                            Comprehensive inventory analysis with stock levels, usage patterns, and alerts for low stock items.
                                        </p>
                                        <a href="{{ route('reports.product') }}" class="btn btn-success">
                                            <i class="bi bi-bar-chart"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Report (Future) -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100" style="opacity: 0.6;">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-info">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h6 class="report-title">Client Report</h6>
                                        <p class="report-description">
                                            Client analysis including visit frequency, treatment preferences, and spending patterns.
                                        </p>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="bi bi-clock"></i> Coming Soon
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Treatment Analysis (Future) -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100" style="opacity: 0.6;">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-secondary">
                                            <i class="bi bi-heart-pulse"></i>
                                        </div>
                                        <h6 class="report-title">Treatment Analysis</h6>
                                        <p class="report-description">
                                            Popular treatments, seasonal trends, and treatment effectiveness metrics.
                                        </p>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="bi bi-clock"></i> Coming Soon
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Staff Performance Report (Future) -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100" style="opacity: 0.6;">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-warning">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <h6 class="report-title">Staff Performance</h6>
                                        <p class="report-description">
                                            Analyze staff performance, treatment counts, revenue generation, and client satisfaction.
                                        </p>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="bi bi-clock"></i> Coming Soon
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Summary (Future) -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card report-card h-100" style="opacity: 0.6;">
                                    <div class="card-body text-center">
                                        <div class="report-icon text-danger">
                                            <i class="bi bi-graph-up"></i>
                                        </div>
                                        <h6 class="report-title">Financial Summary</h6>
                                        <p class="report-description">
                                            Comprehensive financial overview with profit margins, expenses, and growth trends.
                                        </p>
                                        <button class="btn btn-outline-secondary" disabled>
                                            <i class="bi bi-clock"></i> Coming Soon
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script>
        $(document).ready(function() {
            // Add hover effects and animations
            $('.report-card').on('mouseenter', function() {
                $(this).find('.report-icon').addClass('animate__animated animate__pulse');
            }).on('mouseleave', function() {
                $(this).find('.report-icon').removeClass('animate__animated animate__pulse');
            });
        });
    </script>
@endsection
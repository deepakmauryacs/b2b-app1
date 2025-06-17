@extends('admin.layouts.app')
@section('title', 'Vendors | Deal24hours')
@section('content')
<style>
    .pagination {
        justify-content: center;
        margin-top: 1rem;
    }
    .pagination .page-item .page-link {
        color: #6c757d;
        background: transparent;
        border: none;
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.25rem;
    }
    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .pagination .page-item.active .page-link {
        background-color: #ff6c2f !important;
        color: #fff !important;
        border: none;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background: transparent;
    }
    .vendor-info {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }
</style>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Vendors</h4>
            </div>
            <div>
                <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Vendor Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="name" class="form-control" placeholder="Vendor Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="text" id="email" class="form-control" placeholder="Vendor Email">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-check2-circle"></i>
                                </span>
                                <select id="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">GST No</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" id="gst_no" class="form-control" placeholder="GST Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="search" class="btn btn-primary">
                                <i class="bi bi-search"></i> SEARCH
                            </button>
                        
                            <button type="button" id="reset" class="btn btn-outline-danger">
                                <i class="bi bi-arrow-clockwise"></i> RESET
                            </button>

                            <button id="export-vendors" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                        </div>
                    </form>
                </div>

                <div id="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-striped table-centered" id="vendors-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Store Info</th>
                                <th>Status</th>
                                <th>Products</th>
                                <th>Verified</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="vendors-table-body">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <div id="pagination-links" class="mt-3">
                    <!-- Pagination links will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    // Load initial data
    loadVendors();

    // Filter button
    $('#search').on('click', function () {
        loadVendors();
    });

    $('#reset').on('click', function () {
        $('#filter-form').trigger('reset');
        loadVendors();
    });

    // Handle profile verification toggle
    $(document).on('change', '.profile-verified-toggle', function() {
        var $toggle = $(this);
        var userId = $toggle.data('id');
        var isVerified = $toggle.is(':checked') ? '1' : '2';
        
        $.ajax({
            url: "{{ route('admin.vendors.update-profile-verification') }}",
            method: 'POST',
            data: {
                id: userId,
                is_profile_verified: isVerified,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    // Revert the toggle if there was an error
                    $toggle.prop('checked', !$toggle.is(':checked'));
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
                // Revert the toggle if there was an error
                $toggle.prop('checked', !$toggle.is(':checked'));
            }
        });
    });

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        loadVendors(url);
    });

    function loadVendors(url = "{{ route('admin.vendors.data') }}") {
        $('#loading-spinner').show();
        $('#vendors-table-body').html('');
        
        var data = {
            name: $('#name').val(),
            email: $('#email').val(),
            status: $('#status').val(),
            gst_no: $('#gst_no').val(),
            per_page: $('#per_page').val() || 10
        };

        $.ajax({
            url: url,
            data: data,
            success: function(response) {
                $('#loading-spinner').hide();
                
                // Populate table body
                var html = '';
                $.each(response.data, function(index, vendor) {
                    html += '<tr>';
                    html += '<td>' + (response.from + index) + '</td>';
                    html += '<td>' + vendor.name + '</td>';
                    html += '<td>' + vendor.email + '</td>';
                    html += '<td>' + vendor.phone + '</td>';
                    html += '<td>' + vendor.store_info + '</td>';
                    html += '<td>' + vendor.status + '</td>';
                    html += '<td>' + vendor.products_info + '</td>';
                    html += '<td>' + vendor.is_profile_verified + '</td>';
                    html += '<td>' + vendor.created_at + '</td>';
                    html += '<td>' + vendor.action + '</td>';
                    html += '</tr>';
                });
                $('#vendors-table-body').html(html);
                
                // Update pagination links
                $('#pagination-links').html(response.links);
            },
            error: function() {
                $('#loading-spinner').hide();
                toastr.error('Failed to load vendors. Please try again.');
            }
        });
    }
});
</script>

<script>
$(document).ready(function() {
    $('#export-vendors').click(function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Exporting...');

        // Show progress container
        $('.progress-container').show();
        updateProgress(0, 'Preparing export...');

        // Start the export process
        startExport($btn);
    });

    function startExport($btn) {
        // Step 1: Initiate export on server
        $.ajax({
            url: "{{ route('admin.vendors.export.start') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                name: $('#name').val(),
                email: $('#email').val(),
                status: $('#status').val(),
                gst_no: $('#gst_no').val()
            },
            success: function(response) {
                if (response.status === 'started') {
                    const exportId = response.export_id;
                    pollProgress(exportId, $btn);
                } else {
                    toastr.error('Failed to start export');
                    resetButton($btn);
                }
            },
            error: function() {
                toastr.error('Error initiating export');
                resetButton($btn);
            }
        });
    }

    function pollProgress(exportId, $btn) {
        // Step 2: Poll server for progress
        const interval = setInterval(function() {
            $.ajax({
                url: "{{ route('admin.vendors.export.progress') }}",
                method: 'GET',
                data: { export_id: exportId },
                success: function(response) {
                    updateProgress(response.progress, response.message);

                    if (response.status === 'completed') {
                        clearInterval(interval);
                        // Trigger file download
                        window.location.href = "{{ route('admin.vendors.export.download') }}?export_id=" + exportId;
                        resetButton($btn);
                        setTimeout(() => {
                            $('.progress-container').hide();
                        }, 3000);
                    } else if (response.status === 'failed') {
                        clearInterval(interval);
                        toastr.error('Export failed: ' + response.message);
                        resetButton($btn);
                        $('.progress-container').hide();
                    }
                },
                error: function() {
                    clearInterval(interval);
                    toastr.error('Error checking export progress');
                    resetButton($btn);
                    $('.progress-container').hide();
                }
            });
        }, 1000); // Poll every 1 second
    }

    function updateProgress(percent, message) {
        $('#progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
        $('#progress-percentage').text(percent + '%');
        $('#progress-status').text(message);
    }

    function resetButton($btn) {
        $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-excel"></i> Export');
    }
});
</script>
@endsection
@extends('admin.layouts.app')
@section('title', 'Vendors | Deal24hours')
@section('content')
<style>
    .dataTables_wrapper .dataTables_paginate .pagination {
        justify-content: center;
        margin-top: 1rem;
    }
    .dataTables_wrapper .dataTables_paginate .page-item .page-link {
        color: #6c757d;
        background: transparent;
        border: none;
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.25rem;
    }
    .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
        background-color: #ff6c2f !important;
        color: #fff !important;
        border: none;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background: transparent;
    }
    .dataTables_wrapper {
        padding: 0px 20px 20px 20px !important;
    }
    #vendors-table_processing.card {
        background: none !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    div.dataTables_processing>div:last-child>div {
        background: #ff6c2f !important;
    }
    .vendor-info {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Add progress bar styles */
    .progress-container {
        display: none;
        margin-top: 20px;
    }
    .progress-bar {
        height: 20px;
        background-color: #4CAF50;
        width: 0%;
        transition: width 0.3s;
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

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

                <!-- Progress bar container -->
                <div class="progress-container mt-3" style="padding: 0px 20px 20px 20px !important;">
                    <div class="d-flex justify-content-between">
                        <span id="progress-percentage">0%</span>
                        <span id="progress-status">Starting...</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
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
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const table = $('#vendors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.vendors.data') }}",
            data: function (d) {
                d.name = $('#name').val();
                d.email = $('#email').val();
                d.status = $('#status').val();
                d.store_name = $('#store_name').val();
                d.gst_no = $('#gst_no').val();
            }
        },
        searching: false,
        lengthChange: false,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'users.name', orderable: false, searchable: false },
            { data: 'email', name: 'users.email', orderable: false, searchable: false },
            { data: 'phone', name: 'users.phone', orderable: false, searchable: false },
            { 
                data: 'store_info', 
                name: 'vendor_profiles.store_name',
                render: function(data, type, row) {
                    return '<div class="vendor-info">' + data + '</div>';
                }, orderable: false, searchable: false
            },
            { data: 'products_info', name: 'products_info' },
            { data: 'status', name: 'users.status' , orderable: false, searchable: false},
            { data: 'is_profile_verified', name: 'users.is_profile_verified', orderable: false, searchable: false },
            { data: 'created_at', name: 'users.created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Filter button
    $('#search').on('click', function () {
        table.ajax.reload();
    });

    $('#reset').on('click', function () {
        $('#filter-form').trigger('reset');
        table.ajax.reload();
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
            url: "{{ route('admin.vendors.export.start') }}", // New route to start export
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}" // Include CSRF token for Laravel
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
                url: "{{ route('admin.vendors.export.progress') }}", // New route to check progress
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
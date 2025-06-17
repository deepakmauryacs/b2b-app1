@extends('admin.layouts.app')
@section('title', 'Vendors | Deal24hours')
@section('content')
<style>
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

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Vendors</h4>
                <div>
                    <button id="export-vendors" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Your existing filter form here -->
                <form id="filter-form" class="row g-2 align-items-end">
                    <!-- ... existing filter fields ... -->
                    <div class="col-md-4">
                        <button type="button" id="search" class="btn btn-primary">
                            <i class="bi bi-search"></i> SEARCH
                        </button>
                        <button type="button" id="reset" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise"></i> RESET
                        </button>
                    </div>
                </form>
                
                <!-- Progress bar container -->
                <div class="progress-container mt-3">
                    <div class="d-flex justify-content-between">
                        <span id="progress-percentage">0%</span>
                        <span id="progress-status">Starting...</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                
                <!-- Your existing DataTable -->
                <div class="table-responsive mt-3">
                    <table class="table align-middle mb-0 table-striped table-centered" id="vendors-table" style="width: 100%;">
                        <!-- ... table content ... -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this script section -->
<script>
$(document).ready(function() {
    // ... existing DataTable initialization ...

    // Export functionality with progress tracking
    $('#export-vendors').click(function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Exporting...');
        
        // Show progress container
        $('.progress-container').show();
        updateProgress(0, 'Preparing export...');
        
        // Start the export process
        startExport();
    });

    function startExport() {
        $.ajax({
            url: "{{ route('admin.vendors.export') }}",
            method: 'GET',
            xhr: function() {
                const xhr = new XMLHttpRequest();
                
                // Listen for progress events
                xhr.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        updateProgress(percent, 'Exporting data...');
                    }
                });
                
                return xhr;
            },
            success: function(response) {
                // Create a download link
                const blob = new Blob([response], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'vendors.xlsx';
                document.body.appendChild(a);
                a.click();
                
                // Clean up
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 100);
                
                updateProgress(100, 'Export completed!');
                $('#export-vendors').prop('disabled', false).html('<i class="bi bi-file-earmark-excel"></i> Export');
                
                // Hide progress after delay
                setTimeout(() => {
                    $('.progress-container').hide();
                }, 3000);
            },
            error: function() {
                toastr.error('Error occurred during export');
                $('#export-vendors').prop('disabled', false).html('<i class="bi bi-file-earmark-excel"></i> Export');
                $('.progress-container').hide();
            }
        });
    }

    function updateProgress(percent, message) {
        $('#progress-bar').css('width', percent + '%');
        $('#progress-percentage').text(percent + '%');
        $('#progress-status').text(message);
    }
});
</script>
@endsection
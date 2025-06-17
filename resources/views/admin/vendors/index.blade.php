@extends('admin.layouts.app')
@section('title', 'Vendors | Deal24hours')
@section('content')
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
                                <label class="form-label" for="name">Vendor Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="name" class="form-control" placeholder="Vendor Name">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="phone">Contact Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" id="phone" class="form-control" placeholder="Contact Number">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="text" id="email" class="form-control" placeholder="Vendor Email">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="status">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-check2-circle"></i>
                                    </span>
                                    <select id="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label" for="gst_no">GST No</label>
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

                                <button type="button" id="export-vendors" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Export
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="progress-container mt-3" style="display:none;">
                        <div class="progress">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                style="width: 0%" role="progressbar"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span id="progress-percentage">0%</span>
                            <span id="progress-status">Starting...</span>
                        </div>
                    </div>

                    <!-- Progress bar container for export -->
                    <!-- <div class="progress-container mt-3">
                        <div class="d-flex justify-content-between">
                            <span id="progress-percentage">0%</span>
                            <span id="progress-status">Starting...</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
     -->
                    <div class="table-responsive px-4 mb-3">
                        <table class="table align-middle mb-0 table-striped table-centered" id="vendors-table"
                            style="width: 100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Store Info</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- The tbody and tfoot (for pagination) will be dynamically loaded here --}}
                            <tbody id="vendors-table-body-content">
                                <tr>
                                    <td colspan="10" class="text-center">Loading Vendors...</td>
                                </tr>
                            </tbody>
                            <tfoot id="vendors-table-foot-content">
                                <tr>
                                    <td colspan="10" class="text-center"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{-- No separate div for pagination links anymore, as they are part of the fetched HTML --}}
                </div>
            </div>
        </div>
    </div>
    <!-- Export Range Modal -->
    <!-- Export Range Modal -->
    <div class="modal fade" id="exportRangeModal" tabindex="-1" aria-labelledby="exportRangeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Export Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select class="form-select" id="exportRange">
                        <option value="0-50000">0 - 50,000</option>
                        <option value="50000-100000">50,000 - 100,000</option>
                        <option value="100000-150000">100,000 - 150,000</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="startExportBtn" class="btn btn-primary">Start Export</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Container -->
    <div class="progress-container mt-3" style="display:none;">
        <div class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"
                role="progressbar"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <span id="progress-percentage">0%</span>
            <span id="progress-status">Starting...</span>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            // Initial fetch of data when the page loads
            fetchVendorsData(1);

            /**
             * Fetches rendered vendor table data from the server via AJAX and updates the table.
             * @param {number} page - The page number to fetch.
             */

            // **Correct Placement:** Declare the variable in a scope outside the function.
            // This is the most important line to fix the error.
            var currentAjaxRequest = null;

            function fetchVendorsData(page = 1, perPage = null) {
                // Now, when the function runs, it looks for 'currentAjaxRequest'
                // in the outer scope and finds the variable declared above.

                // If there's an ongoing AJAX request, abort it.
                if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                    currentAjaxRequest.abort();
                }

                // Show a loading indicator
                $('#vendors-table-body-content').html(
                    '<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
                    );
                $('#vendors-table-foot-content').empty();

                // Collect filter values
                const filters = {
                    name: $('#name').val(),
                    phone: $('#phone').val(),
                    email: $('#email').val(),
                    status: $('#status').val(),
                    gst_no: $('#gst_no').val(),
                };
                perPage = perPage || $('#perPage').val() || 10;

                // Store the new AJAX request in the single, shared variable
                currentAjaxRequest = $.ajax({
                    url: "{{ route('admin.vendors.render-table') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: perPage,
                        ...filters
                    },
                    success: function(response) {
                        // The response is the rendered HTML
                        const $responseHtml = $(response);
                        $('#vendors-table-body-content').html($responseHtml.filter('tbody').html());
                        $('#vendors-table-foot-content').html($responseHtml.filter('tfoot').html());
                    },
                    error: function(xhr) {
                        // Check if the error was due to an intentional abort
                        if (xhr.statusText === "abort") {
                            console.log("Previous request aborted.");
                            return; // Stop execution, don't show an error
                        }
                        console.error('Error fetching vendors:', xhr.responseText);
                        $('#vendors-table-body-content').html(
                            '<tr><td colspan="10" class="text-center text-danger">Error loading vendors. Please try again.</td></tr>'
                            );
                    },
                    complete: function() {
                        // Once the request is complete (success or error),
                        // set the variable to null so we don't try to abort a finished request.
                        currentAjaxRequest = null;
                    }
                });
            }

            // Event listener for the "SEARCH" button
            $('#search').on('click', function() {
                fetchVendorsData(1); // Reset to the first page when applying filters
            });

            // Event listener for the "RESET" button
            $('#reset').on('click', function() {
                // Reset all input, select, and textarea fields inside the filter container
                $('#filter-form').find('input, select, textarea').val('');
                fetchVendorsData(1);
            });


            // Delegated event listener for dynamically generated pagination links within the tfoot
            $(document).on('click', '#vendors-table-foot-content a.page-link', function(e) {
                e.preventDefault(); // Prevent default link behavior (page reload)
                const url = $(this).attr('href'); // Get the URL from the clicked link
                const page = new URL(url).searchParams.get('page'); // Extract the 'page' parameter
                if (page) { // Ensure a page number exists
                    fetchVendorsData(page); // Fetch data for the new page
                }
            });

            // Handle page length change
            // Use event delegation for dynamically loaded elements
            // Event delegation for page length change
            $(document).on('change', '#perPage', function() {
                fetchVendorsData(1, $(this).val());
            });


            // Handle profile verification toggle switch change event (remains the same)
            $(document).on('change', '.profile-verified-toggle', function() {
                var $toggle = $(this);
                var userId = $toggle.data('id');
                var isVerified = $toggle.is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.vendors.update-profile-verification') }}",
                    method: 'POST',
                    data: {
                        id: userId,
                        is_profile_verified: isVerified,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                            $toggle.prop('checked', !$toggle.is(':checked'));
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                        console.error("AJAX error:", xhr.responseText);
                        $toggle.prop('checked', !$toggle.is(':checked'));
                    }
                });
            });

            // --- Export Functionality ---
            $('#export-vendors').click(function() {
                $('#exportRangeModal').modal('show');
            });

            $('#startExportBtn').click(function() {
                const $btn = $('#export-vendors');
                const selectedRange = $('#exportRange').val();
                const [range_start, range_end] = selectedRange.split('-');

                $('#exportRangeModal').modal('hide');

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Exporting...');
                $('.progress-container').show();
                updateProgress(0, 'Preparing export...');

                const exportFilters = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    status: $('#status').val(),
                    gst_no: $('#gst_no').val(),
                    phone: $('#phone').val(),
                    range_start,
                    range_end,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('admin.vendors.export.start') }}",
                    method: 'POST',
                    data: exportFilters,
                    success: function(response) {
                        if (response.status === 'started') {
                            pollProgress(response.export_id, $btn);
                        } else {
                            toastr.error('Failed to start export');
                            resetButton($btn);
                            $('.progress-container').hide();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error initiating export');
                        resetButton($btn);
                        $('.progress-container').hide();
                    }
                });
            });

            function pollProgress(exportId, $btn) {
                const interval = setInterval(function() {
                    $.ajax({
                        url: "{{ route('admin.vendors.export.progress') }}",
                        method: 'GET',
                        data: {
                            export_id: exportId
                        },
                        success: function(response) {
                            updateProgress(response.progress, response.message);

                            if (response.status === 'completed') {
                                clearInterval(interval);
                                if (response.download_url) {
                                    window.location.href = response.download_url;
                                }
                                resetButton($btn);
                                setTimeout(() => $('.progress-container').hide(), 3000);
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
                }, 1000);
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

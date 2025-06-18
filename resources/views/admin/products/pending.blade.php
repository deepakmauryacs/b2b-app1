@extends('admin.layouts.app')
@section('title', 'Pending Products | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Pending Products</h4>
                </div>
                <div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Product Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                    <input type="text" id="product_name" class="form-control" placeholder="Product Name">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Vendor</label>
                                <div class="input-group select2-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <select id="vendor_id" class="form-select select2" style="width: 100%;">
                                        <option value="">Search vendor</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <button type="button" id="search" class="btn btn-primary">
                                    <i class="bi bi-search"></i> SEARCH
                                </button>
                                <button type="button" id="reset" class="btn btn-outline-danger">
                                    <i class="bi bi-arrow-clockwise"></i> RESET
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive px-4 mb-3">
                        <table class="table align-middle mb-0 table-striped table-centered" id="approved-products-table"
                            style="width: 100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Vendor</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Approved On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- The tbody and tfoot (for pagination) will be dynamically loaded here --}}
                            <tbody id="products-table-body-content">
                                <tr>
                                    <td colspan="10" class="text-center">Loading Vendors...</td>
                                </tr>
                            </tbody>
                            <tfoot id="products-table-foot-content">
                                <tr>
                                    <td colspan="10" class="text-center"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initial fetch of data when the page loads
            fetchProductsData(1);

            /**
             * Fetches rendered vendor table data from the server via AJAX and updates the table.
             * @param {number} page - The page number to fetch.
             */

            // **Correct Placement:** Declare the variable in a scope outside the function.
            // This is the most important line to fix the error.
            var currentAjaxRequest = null;

            function fetchProductsData(page = 1, perPage = null) {
                // Now, when the function runs, it looks for 'currentAjaxRequest'
                // in the outer scope and finds the variable declared above.

                // If there's an ongoing AJAX request, abort it.
                if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                    currentAjaxRequest.abort();
                }

                // Show a loading indicator
                $('#products-table-body-content').html(
                    '<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
                    );
                $('#products-table-foot-content').empty();

                // Collect filter values
                const filters = {
                    product_name: $('#product_name').val(),
                    vendor_id: $('#vendor_id').val(),
                };
                perPage = perPage || $('#perPage').val() || 10;

                // Store the new AJAX request in the single, shared variable
                currentAjaxRequest = $.ajax({
                    url: "{{ route('admin.pending.products.render-table') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: perPage,
                        ...filters
                    },
                    success: function(response) {
                        // The response is the rendered HTML
                        const $responseHtml = $(response);
                        $('#products-table-body-content').html($responseHtml.filter('tbody').html());
                        $('#products-table-foot-content').html($responseHtml.filter('tfoot').html());
                    },
                    error: function(xhr) {
                        // Check if the error was due to an intentional abort
                        if (xhr.statusText === "abort") {
                            console.log("Previous request aborted.");
                            return; // Stop execution, don't show an error
                        }
                        console.error('Error fetching vendors:', xhr.responseText);
                        $('#products-table-body-content').html(
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
                fetchProductsData(1); // Reset to the first page when applying filters
            });

            // Event listener for the "RESET" button
            $('#reset').on('click', function() {
                // Reset all input, select, and textarea fields inside the filter container
                $('#filter-form').find('input, select, textarea').val('');
                fetchProductsData(1);
            });


            // Delegated event listener for dynamically generated pagination links within the tfoot
            $(document).on('click', '#products-table-foot-content a.page-link', function(e) {
                e.preventDefault(); // Prevent default link behavior (page reload)
                const url = $(this).attr('href'); // Get the URL from the clicked link
                const page = new URL(url).searchParams.get('page'); // Extract the 'page' parameter
                if (page) { // Ensure a page number exists
                    fetchProductsData(page); // Fetch data for the new page
                }
            });

            // Handle page length change
            // Use event delegation for dynamically loaded elements
            // Event delegation for page length change
            $(document).on('change', '#perPage', function() {
                fetchProductsData(1, $(this).val());
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


        });

        $('#vendor_id').select2({
            placeholder: 'Search vendor',
            allowClear: true,
            ajax: {
                url: "{{ route('admin.vendors.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(vendor) {
                            return {
                                id: vendor.id,
                                text: vendor.name
                            };
                        })
                    };
                },
                cache: true
            }
        });
    </script>
@endsection

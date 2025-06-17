@extends('admin.layouts.app')
@section('title', 'Buyers | Deal24hours')
@section('content')
    <style>
        /* Additional styles can go here if needed */
    </style>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">All Buyers</h4>

                    <a href="{{ route('admin.buyers.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg" style="font-size: 16px;"></i> Add Buyer
                    </a>
                </div>
                <div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buyer Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="name" class="form-control" placeholder="Buyer Name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="text" id="email" class="form-control" placeholder="Buyer Email">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" id="phone" class="form-control" placeholder="Contact Number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="gst_no">GST No</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" id="gst_no" class="form-control" placeholder="GST Number">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
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
                                <button type="button" id="search" class="btn btn-primary">
                                    <i class="bi bi-search"></i> SEARCH
                                </button>

                                <button type="button" id="reset" class="btn btn-outline-danger">
                                    <i class="bi bi-arrow-clockwise"></i> RESET
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-striped table-centered" id="buyers-table"
                            style="width: 100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Store Info</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- Table body and pagination will be loaded via AJAX --}}
                            <tbody id="buyers-table-body-content">
                                <tr>
                                    <td colspan="9" class="text-center">Loading Buyers...</td>
                                </tr>
                            </tbody>
                            <tfoot id="buyers-table-foot-content">
                                <tr>
                                    <td colspan="9" class="text-center"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            fetchBuyersData(1);

            var currentAjaxRequest = null;

            function fetchBuyersData(page = 1, perPage = null) {
                if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                    currentAjaxRequest.abort();
                }

                $('#buyers-table-body-content').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                $('#buyers-table-foot-content').empty();

                const filters = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    gst_no: $('#gst_no').val(),
                    status: $('#status').val()
                };
                perPage = perPage || $('#perPage').val() || 10;

                currentAjaxRequest = $.ajax({
                    url: "{{ route('admin.buyers.render-table') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: perPage,
                        ...filters
                    },
                    success: function(response) {
                        const $responseHtml = $(response);
                        $('#buyers-table-body-content').html($responseHtml.filter('tbody').html());
                        $('#buyers-table-foot-content').html($responseHtml.filter('tfoot').html());
                    },
                    error: function(xhr) {
                        if (xhr.statusText === 'abort') {
                            return;
                        }
                        $('#buyers-table-body-content').html('<tr><td colspan="8" class="text-center text-danger">Error loading buyers. Please try again.</td></tr>');
                    },
                    complete: function() {
                        currentAjaxRequest = null;
                    }
                });
            }

            $('#search').on('click', function() {
                fetchBuyersData(1);
            });

            $('#reset').on('click', function() {
                $('#filter-form').find('input, select').val('');
                fetchBuyersData(1);
            });

            $(document).on('click', '#buyers-table-foot-content a.page-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const page = new URL(url).searchParams.get('page');
                if (page) {
                    fetchBuyersData(page);
                }
            });

            $(document).on('change', '#perPage', function() {
                fetchBuyersData(1, $(this).val());
            });

            // Handle profile verification toggle change
            $(document).on('change', '.profile-verified-toggle', function() {
                var $toggle = $(this);
                var userId = $toggle.data('id');
                var isVerified = $toggle.is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin.buyers.update-profile-verification') }}",
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
                        console.error('AJAX error:', xhr.responseText);
                        $toggle.prop('checked', !$toggle.is(':checked'));
                    }
                });
            });

        });
    </script>
@endsection

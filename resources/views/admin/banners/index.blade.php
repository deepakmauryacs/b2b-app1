@extends('admin.layouts.app')
@section('title', 'Banners | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Banners</h4>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Banner
                </a>
            </div>
            <div>
                <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                                <select id="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
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
                <div class="table-responsive px-4 mb-3">
                    <table class="table align-middle mb-0 table-striped table-centered" id="banners-table" style="width:100%">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Link</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="banners-table-body-content">
                            <tr>
                                <td colspan="7" class="text-center">Loading Banners...</td>
                            </tr>
                        </tbody>
                        <tfoot id="banners-table-foot-content">
                            <tr>
                                <td colspan="7" class="text-center"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        fetchBannersData(1);
        var currentAjaxRequest = null;

        function fetchBannersData(page = 1, perPage = null) {
            if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                currentAjaxRequest.abort();
            }

            $('#banners-table-body-content').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
            $('#banners-table-foot-content').empty();

            const filters = {
                status: $('#status').val()
            };
            perPage = perPage || $('#perPage').val() || 10;

            currentAjaxRequest = $.ajax({
                url: "{{ route('admin.banners.render-table') }}",
                method: 'GET',
                data: {
                    page: page,
                    per_page: perPage,
                    ...filters
                },
                success: function(response) {
                    const $responseHtml = $(response);
                    $('#banners-table-body-content').html($responseHtml.filter('tbody').html());
                    $('#banners-table-foot-content').html($responseHtml.filter('tfoot').html());
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') { return; }
                    $('#banners-table-body-content').html('<tr><td colspan="7" class="text-center text-danger">Error loading banners. Please try again.</td></tr>');
                },
                complete: function() {
                    currentAjaxRequest = null;
                }
            });
        }

        $('#search').on('click', function() { fetchBannersData(1); });
        $('#reset').on('click', function() { $('#filter-form').find('input, select').val(''); fetchBannersData(1); });
        $(document).on('click', '#banners-table-foot-content a.page-link', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page');
            if (page) { fetchBannersData(page); }
        });
        $(document).on('change', '#perPage', function() { fetchBannersData(1, $(this).val()); });
    });
</script>
@endsection

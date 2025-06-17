@extends('admin.layouts.app')
@section('title', 'Roles | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">All Roles</h4>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Role
                    </a>
                </div>
                <div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label" for="name">Role Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="name" class="form-control" placeholder="Role Name">
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
                        <table class="table align-middle mb-0 table-striped table-centered" id="roles-table" style="width:100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Parent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="roles-table-body-content">
                                <tr>
                                    <td colspan="4" class="text-center">Loading Roles...</td>
                                </tr>
                            </tbody>
                            <tfoot id="roles-table-foot-content">
                                <tr>
                                    <td colspan="4" class="text-center"></td>
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
            fetchRolesData(1);
            var currentAjaxRequest = null;
            function fetchRolesData(page = 1, perPage = null) {
                if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                    currentAjaxRequest.abort();
                }
                $('#roles-table-body-content').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                $('#roles-table-foot-content').empty();
                const filters = {
                    name: $('#name').val()
                };
                perPage = perPage || $('#perPage').val() || 10;
                currentAjaxRequest = $.ajax({
                    url: "{{ route('admin.roles.render-table') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: perPage,
                        ...filters
                    },
                    success: function(response) {
                        const $responseHtml = $(response);
                        $('#roles-table-body-content').html($responseHtml.filter('tbody').html());
                        $('#roles-table-foot-content').html($responseHtml.filter('tfoot').html());
                    },
                    error: function(xhr) {
                        if (xhr.statusText === 'abort') { return; }
                        $('#roles-table-body-content').html('<tr><td colspan="4" class="text-center text-danger">Error loading roles. Please try again.</td></tr>');
                    },
                    complete: function() {
                        currentAjaxRequest = null;
                    }
                });
            }
            $('#search').on('click', function() { fetchRolesData(1); });
            $('#reset').on('click', function() { $('#filter-form').find('input').val(''); fetchRolesData(1); });
            $(document).on('click', '#roles-table-foot-content a.page-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const page = new URL(url).searchParams.get('page');
                if (page) { fetchRolesData(page); }
            });
            $(document).on('change', '#perPage', function() { fetchRolesData(1, $(this).val()); });
        });
    </script>
@endsection

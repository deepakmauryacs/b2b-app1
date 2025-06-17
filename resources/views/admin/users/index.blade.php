@extends('admin.layouts.app')
@section('title', 'Users | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">All Users</h4>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Add User
                    </a>
                </div>
                <div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="name" class="form-control" placeholder="User Name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="text" id="email" class="form-control" placeholder="User Email">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Role</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-people"></i></span>
                                    <select id="role" class="form-select">
                                        <option value="">All</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
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
                    <div class="table-responsive px-4 mb-3">
                        <table class="table align-middle mb-0 table-striped table-centered" id="users-table" style="width:100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body-content">
                                <tr>
                                    <td colspan="6" class="text-center">Loading Users...</td>
                                </tr>
                            </tbody>
                            <tfoot id="users-table-foot-content">
                                <tr>
                                    <td colspan="6" class="text-center"></td>
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
            fetchUsersData(1);
            var currentAjaxRequest = null;

            function fetchUsersData(page = 1, perPage = null) {
                if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
                    currentAjaxRequest.abort();
                }
                $('#users-table-body-content').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                $('#users-table-foot-content').empty();

                const filters = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    role: $('#role').val(),
                    status: $('#status').val()
                };
                perPage = perPage || $('#perPage').val() || 10;

                currentAjaxRequest = $.ajax({
                    url: "{{ route('admin.users.render-table') }}",
                    method: 'GET',
                    data: {
                        page: page,
                        per_page: perPage,
                        ...filters
                    },
                    success: function(response) {
                        const $responseHtml = $(response);
                        $('#users-table-body-content').html($responseHtml.filter('tbody').html());
                        $('#users-table-foot-content').html($responseHtml.filter('tfoot').html());
                    },
                    error: function(xhr) {
                        if (xhr.statusText === 'abort') {
                            return;
                        }
                        $('#users-table-body-content').html('<tr><td colspan="6" class="text-center text-danger">Error loading users. Please try again.</td></tr>');
                    },
                    complete: function() {
                        currentAjaxRequest = null;
                    }
                });
            }

            $('#search').on('click', function() {
                fetchUsersData(1);
            });

            $('#reset').on('click', function() {
                $('#filter-form').find('input, select').val('');
                fetchUsersData(1);
            });

            $(document).on('click', '#users-table-foot-content a.page-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const page = new URL(url).searchParams.get('page');
                if (page) {
                    fetchUsersData(page);
                }
            });

            $(document).on('change', '#perPage', function() {
                fetchUsersData(1, $(this).val());
            });
        });
    </script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Categories | Deal24hours')
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

        #categories-table_processing.card {
            background: none !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }

        div.dataTables_processing>div:last-child>div {
            background: #ff6c2f !important;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">All Categories</h4>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg" style="font-size: 16px;"></i> Add Category
                    </a>
                </div>
                <div>
                    <div class="card-body">
                        <form id="filter-form" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Category Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                                    <input type="text" id="category_name" class="form-control"
                                        placeholder="Category Name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-check2-circle"></i> <!-- status icon -->
                                    </span>
                                    <select id="status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5">
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
                        <table class="table align-middle mb-0 table-striped table-centered" id="categories-table"
                            style="width: 100%;">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Parent</th>
                                    <th>Status</th>
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
        $(function() {
            const table = $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.categories.data') }}",
                    data: function(d) {
                        d.category_name = $('#category_name').val();
                        d.status = $('#status').val();
                    }
                },
                searching: false,
                lengthChange: false,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'parent',
                        name: 'parent',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Filter button
            $('#search').on('click', function() {
                table.ajax.reload();
            });

            $('#reset').on('click', function() {
                // Reset form safely
                $('#filter-form').trigger('reset');

                // Additionally clear values manually to be 100% safe
                $('#category_name').val('');
                $('#status').val('');

                // Reload DataTable
                table.ajax.reload();
            });

        });
    </script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Activity Logs | Deal24hours')
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
    #activities-table_processing.card {
        background: none !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    div.dataTables_processing>div:last-child>div {
        background: #ff6c2f !important;
    }
    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }
    .badge-approved {
        background-color: #28a745;
        color: #fff;
    }
    .badge-rejected {
        background-color: #dc3545;
        color: #fff;
    }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Activity Logs</h4>
            </div>
            <div>
                <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" id="description" class="form-control" placeholder="Search description">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subject Type</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <select id="subject_type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($subjectTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="text" id="date_from" class="form-control" placeholder="dd-mm-yyyy">
                                <span class="input-group-text">to</span>
                                <input type="text" id="date_to" class="form-control" placeholder="dd-mm-yyyy">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="search" class="btn btn-primary">
                                <i class="bi bi-search"></i> SEARCH
                            </button>
                            <button type="button" id="reset" class="btn btn-outline-danger mt-2 mt-md-0">
                                <i class="bi bi-arrow-clockwise"></i> RESET
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-striped table-centered" id="activities-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Date</th>
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
    flatpickr('#date_from', {dateFormat: 'd-m-Y'});
    flatpickr('#date_to', {dateFormat: 'd-m-Y'});
    const table = $('#activities-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.activity-logs.data') }}",
            data: function (d) {
                d.description = $('#description').val();
                d.subject_type = $('#subject_type').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        searching: false,
        lengthChange: false,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'description', name: 'description' },
            { data: 'causer', name: 'causer.name' },
            { data: 'subject', name: 'subject' },
            { data: 'created_at', name: 'created_at' },
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
        $('#description').val('');
        $('#subject_type').val('');
        $('#date_from').val('');
        $('#date_to').val('');
        table.ajax.reload();
    });
});
</script>
@endsection
@extends('admin.layouts.app')
@section('title', 'Pending Products | Deal24hours')
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
    #pending-products-table_processing.card {
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
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Pending Products Approval</h4>
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <select id="vendor_id" class="form-select">
                                    <option value="">All Vendors</option>
                                    @foreach(\App\Models\User::where('role', 'vendor')->get() as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
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

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-striped table-centered" id="pending-products-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Vendor</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Added On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const table = $('#pending-products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.products.pending.data') }}",
            data: function (d) {
                d.product_name = $('#product_name').val();
                d.vendor_id = $('#vendor_id').val();
            }
        },
        searching: false,
        lengthChange: false,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'product_name', name: 'product_name' },
            { data: 'vendor', name: 'vendor', orderable: false },
            { data: 'category', name: 'category', orderable: false },
            { data: 'price', name: 'price' },
            { data: 'stock_quantity', name: 'stock_quantity' },
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
        $('#product_name').val('');
        $('#vendor_id').val('');
        table.ajax.reload();
    });

    // Approve product
    $(document).on('click', '.approve-product', function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to approve this product?')) {
            $.ajax({
                url: "{{ route('admin.products.pending.approve', ':id') }}".replace(':id', productId),
                type: 'POST', // Laravel will convert to PUT via method spoofing
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: 'PUT' // Method spoofing for PUT request
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error approving product');
                }
            });
        }
    });

    // Reject product
    $(document).on('click', '.reject-product', function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to reject this product?')) {
            $.ajax({
                url: "{{ route('admin.products.pending.reject', ':id') }}".replace(':id', productId),
                type: 'POST', // Laravel will convert to PUT via method spoofing
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: 'PUT' // Method spoofing for PUT request
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error rejecting product');
                }
            });
        }
    });
});
</script>
@endsection
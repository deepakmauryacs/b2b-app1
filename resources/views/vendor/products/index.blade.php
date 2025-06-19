@extends('vendor.layouts.app')
@section('title', 'Vendor Profile | Deal24hours')
@section('content')
{{-- Your custom pagination CSS --}}
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
    	padding: 20px !important;
    }
    </style>
<!-- DataTables Bootstrap 5 integration CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<div class="row">
     <div class="col-md-12">
          <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">All Product List</h4>

                    <a href="{{ route('vendor.products.create') }}" class="btn btn-sm btn-primary">
                         <i class="bi bi-plus-lg" style="font-size: 16px;"></i> Add Product
                    </a>

               </div>
               <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                                <input type="text" id="product_name" class="form-control" placeholder="Product Name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                                <select id="status" class="form-select">
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
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
                    <div class="table-responsive">
                         <table class="table align-middle mb-0 table-hover table-centered" id="data-table" style="width: 100%;">
                              <thead class="bg-light-subtle">
                                   <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                   </tr>
                              </thead>
                              <tbody>
                                  


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
    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendor.products.data') }}",
            data: function (d) {
                d.product_name = $('#product_name').val();
                d.status = $('#status').val();
            }
        },
        searching: false,
        lengthChange: false,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'product_name', name: 'product_name', orderable: false, searchable: false },
            { data: 'price', name: 'price' },
            { data: 'stock_quantity', name: 'stock_quantity', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $('#search').on('click', function () {
        table.ajax.reload();
    });

    $('#reset').on('click', function () {
        $('#filter-form').trigger('reset');
        table.ajax.reload();
    });

    $(document).on('click', '.delete-product', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '{{ url('vendor/products/delete') }}/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status == 1) {
                        toastr.success(res.message);
                        table.ajax.reload();
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function () {
                    toastr.error('Failed to delete product.');
                }
            });
        }
    });
});
</script>
@endsection
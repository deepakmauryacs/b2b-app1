@extends('admin.layouts.app')
@section('title', 'Approved Products | Deal24hours')
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
    #approved-products-table_processing.card {
        background: none !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
    div.dataTables_processing>div:last-child>div {
        background: #ff6c2f !important;
    }
    .badge-approved {
        background-color: #28a745;
        color: #fff;
    }
    /* Ensure Select2 inherits full height in input group */
    .select2-group .select2-container {
        flex: 1 1 auto;
        width: 1% !important;
    }

    .select2-group .select2-selection--single {
        height: calc(2.375rem + 2px); /* Match Bootstrap input height */
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-left: 0;
        border-radius: 0 0.375rem 0.375rem 0;
    }

    .select2-group .select2-selection__rendered {
        padding-left: 0;
    }

    .select2-group .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }
   .select2-container--default .select2-selection--single {
        border-radius: 0px 4px 4px 0px !important;
    }
    .select2-container .select2-selection--single {
      height: 39px !important;
   }
   .select2-container--default .select2-selection--single .select2-selection__arrow {

      top: 6px !important;
    }
    .select2-container .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 6px 12px;
    outline: none !important;
    box-shadow: none !important;
    font-size: 14px;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #d8dfe7 !important;
}
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Approved Products</h4>
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

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-striped table-centered" id="approved-products-table" style="width: 100%;">
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
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add before your <script> block or inside your layout -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const table = $('#approved-products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.products.approved.data') }}",
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
            { data: 'approved_at', name: 'approved_at' },
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

    // Revoke approval
    $(document).on('click', '.revoke-approval', function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to revoke approval for this product? It will be moved back to pending.')) {
            $.ajax({
                url: "{{ route('admin.products.approved.revoke', ':id') }}".replace(':id', productId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: 'POST'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error revoking approval');
                }
            });
        }
    });
});
$('#vendor_id').select2({
    placeholder: 'Search vendor',
    allowClear: true,
    ajax: {
        url: "{{ route('admin.vendor.search') }}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data.map(function (vendor) {
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
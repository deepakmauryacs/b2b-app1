@extends('vendor.layouts.app')
@section('title', ($pageTitle ?? 'Product List') . ' | Deal24hours')
@section('content')
@push('styles')
<style>
    /* Minimal table styles */
    #products-table tfoot ul.pagination {
        justify-content: flex-end;
    }
</style>
@endpush
<div class="row">
     <div class="col-md-12">
          <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">{{ $pageTitle ?? 'All Product List' }}</h4>

                    <div class="d-flex gap-1">
                        <button type="button" id="export-products" class="btn btn-sm btn-success">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <a href="{{ route('vendor.products.create') }}" class="btn btn-sm btn-primary">
                             <i class="bi bi-plus-lg" style="font-size: 16px;"></i> Add Product
                        </a>
                    </div>

               </div>
               <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                                <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Product Name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                                <select id="status" name="status" class="form-select">
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
                         <table class="table align-middle mb-0 table-hover table-centered" id="products-table" style="width: 100%;">
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
                              {{-- Table body and pagination will be loaded via AJAX --}}
                              <tbody id="products-table-body-content">
                                  <tr>
                                      <td colspan="7" class="text-center">Loading Products...</td>
                                  </tr>
                              </tbody>
                              <tfoot id="products-table-foot-content">
                                  <tr>
                                      <td colspan="7" class="text-center"></td>
                                  </tr>
                              </tfoot>
                         </table>
                    </div>
                    <!-- end table-responsive -->
               </div>
               
          </div>
     </div>
</div>

<script src="{{ asset('assets/js/exceljs.min.js') }}"></script>
<script>
$(document).ready(function() {
    var defaultStatus = "{{ $statusDefault ?? '' }}";
    if (defaultStatus) {
        $('#status').val(defaultStatus).prop('disabled', true);
    }

    function validateFilters() {
        const name = $('#product_name').val().trim();
        if (name.length > 200) {
            toastr.error('Product name must be under 200 characters.');
            return false;
        }
        const status = $('#status').val();
        if (status && !['approved', 'pending', 'rejected'].includes(status)) {
            toastr.error('Invalid status selected.');
            return false;
        }
        return true;
    }

    fetchProductsData(1);

    var currentAjaxRequest = null;

    function fetchProductsData(page = 1, perPage = null) {
        if (!validateFilters()) {
            return;
        }
        if (currentAjaxRequest && currentAjaxRequest.readyState !== 4) {
            currentAjaxRequest.abort();
        }

        $('#products-table-body-content').html(
            '<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
        );
        $('#products-table-foot-content').empty();

        const filters = {
            product_name: $('#product_name').val(),
            status: $('#status').val(),
        };
        perPage = perPage || $('#perPage').val() || 10;

        currentAjaxRequest = $.ajax({
            url: "{{ route('vendor.products.render-table') }}",
            method: 'GET',
            data: {
                page: page,
                per_page: perPage,
                ...filters
            },
            success: function(response) {
                const $responseHtml = $(response);
                $('#products-table-body-content').html($responseHtml.filter('tbody').html());
                $('#products-table-foot-content').html($responseHtml.filter('tfoot').html());
            },
            error: function(xhr) {
                if (xhr.statusText === 'abort') {
                    return;
                }
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                }
                $('#products-table-body-content').html('<tr><td colspan="7" class="text-center text-danger">Error loading products.</td></tr>');
            },
            complete: function() {
                currentAjaxRequest = null;
            }
        });
    }

    $('#search').on('click', function() {
        if (validateFilters()) {
            fetchProductsData(1);
        }
    });

    $('#reset').on('click', function() {
        $('#filter-form').trigger('reset');
        $('#product_name').val('');
        if (defaultStatus) {
            $('#status').val(defaultStatus).prop('disabled', true);
        } else {
            $('#status').val('').prop('disabled', false);
        }
        fetchProductsData(1);
    });

    $(document).on('click', '#products-table-foot-content a.page-link', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if (page) {
            fetchProductsData(page);
        }
    });

    $(document).on('change', '#perPage', function() {
        fetchProductsData(1, $(this).val());
    });

    $(document).on('click', '.delete-product', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '{{ url('vendor/products/delete') }}/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.status == 1) {
                        toastr.success(res.message);
                        fetchProductsData(1);
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to delete product.');
                }
            });
        }
    });

    $('#export-products').on('click', function() {
        exportProducts();
    });

    async function exportProducts() {
        let offset = 0;
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Products');
        worksheet.addRow(['ID','Name','Price','Qty','Status','Created At']);

        while (true) {
            try {
                const chunk = await $.ajax({
                    url: "{{ route('vendor.products.export-data') }}",
                    method: 'GET',
                    data: { offset: offset, limit: 500 }
                });

                if (chunk.length === 0) {
                    break;
                }

                chunk.forEach(row => {
                    worksheet.addRow([
                        row.id,
                        row.product_name,
                        row.price,
                        row.quantity,
                        row.status,
                        row.created_at
                    ]);
                });
                offset += chunk.length;
            } catch (e) {
                toastr.error('Export failed');
                return;
            }
        }

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], {type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'products_'+Date.now()+'.xlsx';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
});
</script>@endsection
@extends('vendor.layouts.app')
@section('title', 'Inventory Management | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Inventory Management</h4>
            </div>
            <div class="card-body">
                <form id="filter-form" class="row g-2 align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                            <input type="text" id="product_name" class="form-control" placeholder="Product Name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button type="button" id="search" class="btn btn-primary">
                            <i class="bi bi-search"></i> SEARCH
                        </button>
                        <button type="button" id="reset" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise"></i> RESET
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered" id="inventory-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Stock</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table-body-content">
                            <tr>
                                <td colspan="5" class="text-center">Loading Inventory...</td>
                            </tr>
                        </tbody>
                        <tfoot id="inventory-table-foot-content">
                            <tr>
                                <td colspan="5" class="text-center"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    fetchInventoryData(1);
    var currentAjaxRequest = null;

    function fetchInventoryData(page=1, perPage=null){
        if(currentAjaxRequest && currentAjaxRequest.readyState !== 4){
            currentAjaxRequest.abort();
        }
        $('#inventory-table-body-content').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#inventory-table-foot-content').empty();
        const filters = { product_name: $('#product_name').val() };
        perPage = perPage || $('#perPage').val() || 10;
        currentAjaxRequest = $.ajax({
            url: "{{ route('vendor.inventory.render-table') }}",
            method: 'GET',
            data: { page: page, per_page: perPage, ...filters },
            success: function(response){
                const $responseHtml = $(response);
                $('#inventory-table-body-content').html($responseHtml.filter('tbody').html());
                $('#inventory-table-foot-content').html($responseHtml.filter('tfoot').html());
            },
            error: function(xhr){
                if(xhr.statusText === 'abort'){ return; }
                $('#inventory-table-body-content').html('<tr><td colspan="5" class="text-center text-danger">Error loading inventory.</td></tr>');
            },
            complete: function(){ currentAjaxRequest = null; }
        });
    }

    $('#search').on('click', function(){ fetchInventoryData(1); });
    $('#reset').on('click', function(){ $('#filter-form').trigger('reset'); fetchInventoryData(1); });
    $(document).on('click', '#inventory-table-foot-content a.page-link', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if(page){ fetchInventoryData(page); }
    });

    $(document).on('click','.update-stock',function(){
        const id=$(this).data('id');
        const qty=$(this).closest('tr').find('.stock-input').val();
        $.ajax({
            url: '{{ url('vendor/inventory/update') }}/'+id,
            type:'POST',
            data:{ _token:'{{ csrf_token() }}', stock_quantity: qty },
            success:function(res){ if(res.status==1){ toastr.success(res.message); fetchInventoryData(1); } else { toastr.error(res.message); } },
            error:function(xhr){ if(xhr.responseJSON && xhr.responseJSON.message){ toastr.error(xhr.responseJSON.message); } else { toastr.error('Error updating stock.'); } }
        });
    });
});
</script>
@endsection

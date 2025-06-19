@extends('admin.layouts.app')
@section('title', 'All Products | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Products</h4>
            </div>
            <div>
                <div class="card-body">
                    <form id="filter-form" class="row g-2 align-items-end">
                        <div class="col-md-3">
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
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                                <select id="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
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
                <div class="table-responsive px-4 mb-3">
                    <table class="table align-middle mb-0 table-striped table-centered" id="products-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Vendor</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Updated On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body-content">
                            <tr>
                                <td colspan="9" class="text-center">Loading Products...</td>
                            </tr>
                        </tbody>
                        <tfoot id="products-table-foot-content">
                            <tr>
                                <td colspan="9" class="text-center"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    fetchProductsData(1);
    var currentAjaxRequest = null;

    function fetchProductsData(page = 1, perPage = null){
        if(currentAjaxRequest && currentAjaxRequest.readyState !== 4){
            currentAjaxRequest.abort();
        }
        $('#products-table-body-content').html('<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#products-table-foot-content').empty();

        const filters = {
            product_name: $('#product_name').val(),
            vendor_id: $('#vendor_id').val(),
            status: $('#status').val()
        };
        perPage = perPage || $('#perPage').val() || 10;

        currentAjaxRequest = $.ajax({
            url: "{{ route('admin.products.all.render-table') }}",
            method: 'GET',
            data: {
                page: page,
                per_page: perPage,
                ...filters
            },
            success: function(response){
                const $responseHtml = $(response);
                $('#products-table-body-content').html($responseHtml.filter('tbody').html());
                $('#products-table-foot-content').html($responseHtml.filter('tfoot').html());
            },
            error: function(xhr){
                if(xhr.statusText === 'abort'){ return; }
                $('#products-table-body-content').html('<tr><td colspan="9" class="text-center text-danger">Error loading products.</td></tr>');
            },
            complete: function(){ currentAjaxRequest = null; }
        });
    }

    $('#search').on('click', function(){ fetchProductsData(1); });
    $('#reset').on('click', function(){
        $('#filter-form').find('input, select').val('').trigger('change');
        fetchProductsData(1);
    });
    $(document).on('click', '#products-table-foot-content a.page-link', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if(page){ fetchProductsData(page); }
    });
    $(document).on('change', '#perPage', function(){ fetchProductsData(1, $(this).val()); });

    $('#vendor_id').select2({
        placeholder: 'Search vendor',
        allowClear: true,
        ajax: {
            url: "{{ route('admin.vendors.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params){ return { q: params.term }; },
            processResults: function(data){
                return { results: data.map(function(v){ return { id: v.id, text: v.name }; }) };
            },
            cache: true
        }
    });
});
</script>
@endsection

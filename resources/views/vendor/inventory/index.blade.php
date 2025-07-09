@extends('vendor.layouts.app')
@section('title', 'Inventory Management | Deal24hours')
@push('styles')
<style>
    #inventory-table tfoot ul.pagination {
        justify-content: flex-end;
    }
    td.action-buttons > div {
        display: inline-flex;
        gap: .25rem;
    }
</style>
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Inventory Management</h4>
            </div>
            <div class="card-body">
                <div class="progress mb-2" id="export-progress-container" style="height:20px; display:none;">
                    <div id="export-progress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                </div>
                <div id="export-status" class="mb-2"></div>
                <form id="filter-form" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                            <input type="text" id="product_name" class="form-control" placeholder="Product Name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Warehouse</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shop"></i></span>
                            <select id="warehouse_filter" class="form-select">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endforeach
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
                        <button type="button" id="start-export" class="btn btn-primary">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered" id="inventory-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Warehouse</th>
                                <th>Current Stock</th>
                                <th>In Stock</th>
                                <th>Out Stock</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table-body-content">
                            <tr>
                                <td colspan="8" class="text-center">Loading Inventory...</td>
                            </tr>
                        </tbody>
                        <tfoot id="inventory-table-foot-content">
                            <tr>
                                <td colspan="8" class="text-center"></td>
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
        $('#inventory-table-body-content').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#inventory-table-foot-content').empty();
        const filters = { product_name: $('#product_name').val(), warehouse_id: $('#warehouse_filter').val() };
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
                $('#inventory-table-body-content').html('<tr><td colspan="8" class="text-center text-danger">Error loading inventory.</td></tr>');
            },
            complete: function(){ currentAjaxRequest = null; }
        });
    }

    $('#search').on('click', function(){
        fetchInventoryData(1);
    });
    $('#reset').on('click', function(){
        $('#filter-form').find('input, select').val('');
        fetchInventoryData(1);
    });
    $(document).on('click', '#inventory-table-foot-content a.page-link', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if(page){ fetchInventoryData(page); }
    });

    $(document).on('change', '#perPage', function(){
        fetchInventoryData(1, $(this).val());
    });

    $('#warehouse_filter').on('change', function(){
        fetchInventoryData(1);
    });

    $(document).on('change', '.warehouse-select', function(){
        const warehouseId = $(this).val();
        const productId = $(this).data('product-id');
        const $row = $(this).closest('tr');

        if(!warehouseId){
            $row.find('.current-stock').text($row.find('.current-stock').data('default'));
            return;
        }

        $.ajax({
            url: '{{ url('vendor/inventory/stock') }}/'+productId,
            method: 'GET',
            data: { warehouse_id: warehouseId },
            success: function(res){
                if(res.status){
                    $row.find('.current-stock').text(res.quantity);
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(){
                toastr.error('Error fetching stock.');
            }
        });
    });

    $(document).on('click','.update-stock',function(){
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        const inQty = $row.find('.stock-input-in').val();
        const outQty = $row.find('.stock-input-out').val();
        const warehouseId = $row.find('.warehouse-select').val();

        if(!warehouseId){
            toastr.error('Please select a warehouse.');
            return;
        }

        if(!/^\d*$/.test(inQty) || !/^\d*$/.test(outQty)){
            toastr.error('Please enter valid quantities.');
            return;
        }

        const inVal = parseInt(inQty) || 0;
        const outVal = parseInt(outQty) || 0;

        if(inVal === 0 && outVal === 0){
            toastr.error('Please enter a quantity to add or remove.');
            return;
        }

        $.ajax({
            url: '{{ url('vendor/inventory/update') }}/'+id,
            type:'POST',
            data:{ _token:'{{ csrf_token() }}', in_stock: inVal, out_stock: outVal, warehouse_id: warehouseId },
            success:function(res){
                if(res.status==1){
                    toastr.success(res.message);
                    fetchInventoryData(1);
                } else {
                    toastr.error(res.message);
                }
            },
            error:function(xhr){
                if(xhr.responseJSON && xhr.responseJSON.message){
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Error updating stock.');
                }
            }
        });
    });

    let exporting = false;
    $('#start-export').on('click', function(){
        if(exporting) return;
        exporting = true;
        $('#export-progress').css('width','0%').text('0%');
        $('#export-progress-container').show();
        $('#export-status').text('Preparing export...');

        $.ajax({
            url: '{{ route('vendor.inventory.export.init') }}',
            method: 'GET',
            success: function(init){
                const total = init.total;
                const limit = init.chunk_size;
                let offset = 0;
                const workbook = new ExcelJS.Workbook();
                const sheet = workbook.addWorksheet('Inventory');
                sheet.addRow(['Product','Warehouse','Quantity','Total Quantity','Updated At']);

                const fetchChunk = () => {
                    if(offset >= total){
                        workbook.xlsx.writeBuffer().then(buffer => {
                            const blob = new Blob([buffer], {type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'inventory_'+Date.now()+'.xlsx';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            $('#export-progress').css('width', '100%').text('100%');
                            $('#export-status').text('Export complete');
                            setTimeout(() => window.location.reload(), 1500);
                            exporting = false;
                        });
                        return;
                    }
                    $('#export-status').text('Exporting '+(offset+1)+'-'+Math.min(offset+limit,total)+' of '+total);
                    $.ajax({
                        url: '{{ route('vendor.inventory.export.chunk') }}',
                        method: 'GET',
                        data: {offset: offset, limit: limit},
                        success: function(res){
                            res.rows.forEach(r => {
                                sheet.addRow([r.product_name, r.warehouse_name, r.quantity, r.total_quantity, r.updated_at]);
                            });
                            offset += limit;
                            const percent = Math.round(Math.min(offset,total)/total*100);
                            $('#export-progress').css('width', percent+'%').text(percent+'%');
                            fetchChunk();
                        },
                        error: function(xhr){
                            $('#export-status').text(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error fetching data');
                            exporting = false;
                        }
                    });
                };
                fetchChunk();
            },
            error: function(xhr){
                $('#export-status').text(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error initializing export');
                exporting = false;
            }
        });
    });

});
</script>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/exceljs.min.js') }}"></script>
@endpush

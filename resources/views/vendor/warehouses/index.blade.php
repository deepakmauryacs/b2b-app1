@extends('vendor.layouts.app')
@section('title', 'Warehouse Management | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Warehouse Management</h4>
                <button type="button" class="btn btn-sm btn-primary" id="addWarehouseBtn">
                    <i class="bi bi-plus-lg"></i> Add Warehouse
                </button>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-box"></i></span>
                            <input type="text" id="name" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <button type="button" id="search" class="btn btn-primary">
                            <i class="bi bi-search"></i> SEARCH
                        </button>
                        <button type="button" id="reset" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise"></i> RESET
                        </button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered" id="warehouse-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Products</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="warehouse-table-body-content">
                            <tr><td colspan="7" class="text-center">Loading...</td></tr>
                        </tbody>
                        <tfoot id="warehouse-table-foot-content">
                            <tr><td colspan="7" class="text-center"></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Warehouse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="warehouseForm">
                    <input type="hidden" id="warehouse_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="w_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" id="w_address" name="address">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" id="w_city" name="city">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" id="w_state" name="state">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="w_pincode" name="pincode">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveWarehouse">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    const modal = new bootstrap.Modal(document.getElementById('warehouseModal'));
    let currentAjax = null;
    const rules = {
        w_name: [
            {condition: v => !v.trim(), message: 'Name is required.'}
        ],
        w_pincode: [
            {condition: v => v && !/^\d+$/.test(v), message: 'Pincode must be numeric.'}
        ]
    };

    function showError($input, msg){
        $input.addClass('is-invalid');
        $input.next('.invalid-feedback').remove();
        $input.after(`<div class="invalid-feedback d-block">${msg}</div>`);
        toastr.error(msg);
    }

    function clearError($input){
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').remove();
    }

    function validateField($input, rls){
        let ok = true;
        const val = $input.val();
        for(const r of rls){
            if(r.condition(val)){
                showError($input, r.message);
                ok = false;
                break;
            }
        }
        if(ok) clearError($input);
        return ok;
    }

    fetchWarehouses(1);

    function fetchWarehouses(page=1, perPage=null){
        if(currentAjax && currentAjax.readyState !== 4){ currentAjax.abort(); }
        $('#warehouse-table-body-content').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#warehouse-table-foot-content').empty();
        const data = { name: $('#name').val(), page: page, per_page: perPage || $('#perPage').val() || 10 };
        currentAjax = $.ajax({
            url: '{{ route('vendor.warehouses.render-table') }}',
            method: 'GET',
            data: data,
            success: function(res){ const $html = $(res); $('#warehouse-table-body-content').html($html.filter('tbody').html()); $('#warehouse-table-foot-content').html($html.filter('tfoot').html()); },
            error: function(xhr){ if(xhr.statusText==='abort') return; $('#warehouse-table-body-content').html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>'); },
            complete: function(){ currentAjax = null; }
        });
    }

    $('#search').on('click', function(){ fetchWarehouses(1); });
    $('#reset').on('click', function(){ $('#filterForm').trigger('reset'); fetchWarehouses(1); });
    $(document).on('click', '#warehouse-table-foot-content a.page-link', function(e){ e.preventDefault(); const page = new URL($(this).attr('href')).searchParams.get('page'); if(page){ fetchWarehouses(page); } });
    $(document).on('change', '#perPage', function(){ fetchWarehouses(1, $(this).val()); });

    $('#addWarehouseBtn').on('click', function(){ $('#warehouseForm').trigger('reset'); $('#warehouse_id').val(''); modal.show(); });
    $(document).on('click', '.edit-warehouse', function(){ const info = $(this).data('info'); $('#warehouse_id').val($(this).data('id')); $('#w_name').val(info.name); $('#w_address').val(info.address); $('#w_city').val(info.city); $('#w_state').val(info.state); $('#w_pincode').val(info.pincode); modal.show(); });

    $('#saveWarehouse').on('click', function(){
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let valid = true;
        $.each(rules, function(field, r){
            const $inp = $('#' + field);
            if(!validateField($inp, r)) valid = false;
        });
        if(!valid) return;

        const id = $('#warehouse_id').val();
        const url = id ? '{{ url('vendor/warehouses/update') }}/'+id : '{{ route('vendor.warehouses.store') }}';
        $.ajax({
            url: url,
            method: id ? 'PUT' : 'POST',
            data: $('#warehouseForm').serialize(),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res){ if(res.status){ toastr.success(res.message); modal.hide(); fetchWarehouses(1); } },
            error: function(xhr){
                if(xhr.status === 422 && xhr.responseJSON.errors){
                    $.each(xhr.responseJSON.errors, function(k, v){
                        const $in = $('#w_' + k);
                        if($in.length) showError($in, v[0]);
                    });
                } else if(xhr.responseJSON && xhr.responseJSON.message){
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Error');
                }
            }
        });
    });

    $(document).on('click', '.delete-warehouse', function(){ if(!confirm('Delete this warehouse?')) return; const id=$(this).data('id'); $.ajax({ url:'{{ url('vendor/warehouses/delete') }}/'+id, method:'DELETE', data:{_token:'{{ csrf_token() }}'}, success:function(res){ if(res.status){ toastr.success(res.message); fetchWarehouses(1); } }, error:function(){ toastr.error('Error'); } }); });
});
</script>
@endsection


@extends('vendor.layouts.app')
@section('title', $pageTitle ?? 'Help & Support' . ' | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">{{ $pageTitle ?? 'Help & Support' }}</h4>
                <a href="{{ route('vendor.help-support.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Request
                </a>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" id="name" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                            <select id="status" class="form-select">
                                <option value="">Select</option>
                                <option value="open">Open</option>
                                <option value="pending">Pending</option>
                                <option value="on_hold">On hold</option>
                                <option value="solved">Solved</option>
                                <option value="closed">Closed</option>
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
                    <table class="table align-middle mb-0 table-hover table-centered" id="helps-table" style="width: 100%;">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="helps-table-body-content">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                        <tfoot id="helps-table-foot-content">
                            <tr><td colspan="5" class="text-center"></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    let currentAjax = null;
    fetchHelps(1);

    function fetchHelps(page = 1, perPage = null){
        if(currentAjax && currentAjax.readyState !== 4){
            currentAjax.abort();
        }
        $('#helps-table-body-content').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        $('#helps-table-foot-content').empty();
        const data = {
            name: $('#name').val(),
            status: $('#status').val(),
            page: page,
            per_page: perPage || $('#perPage').val() || 10
        };
        currentAjax = $.ajax({
            url: '{{ route('vendor.help-support.render-table') }}',
            method: 'GET',
            data: data,
            success: function(res){
                const $html = $(res);
                $('#helps-table-body-content').html($html.filter('tbody').html());
                $('#helps-table-foot-content').html($html.filter('tfoot').html());
            },
            error: function(xhr){
                if(xhr.statusText === 'abort') return;
                $('#helps-table-body-content').html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
            },
            complete: function(){
                currentAjax = null;
            }
        });
    }

    $('#search').on('click', function(){
        fetchHelps(1);
    });
    $('#reset').on('click', function(){
        $('#filterForm').trigger('reset');
        fetchHelps(1);
    });
    $(document).on('click', '#helps-table-foot-content a.page-link', function(e){
        e.preventDefault();
        const page = new URL($(this).attr('href')).searchParams.get('page');
        if(page){ fetchHelps(page); }
    });
    $(document).on('change', '#perPage', function(){
        fetchHelps(1, $(this).val());
    });
});
</script>
@endsection

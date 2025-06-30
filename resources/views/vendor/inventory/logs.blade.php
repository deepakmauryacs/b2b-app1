@extends('vendor.layouts.app')
@section('title', 'Stock Logs | Deal24hours')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Stock Logs - {{ $product->product_name }}</h4>
                <a href="{{ route('vendor.inventory.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">&larr; Back</a>
            </div>
            <div class="card-body" id="stock-log-page-content">
                @include('vendor.inventory._stock_logs', ['logs' => $logs])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    let currentAjaxRequest = null;
    function fetchLogs(page = 1, perPage = null){
        if(currentAjaxRequest && currentAjaxRequest.readyState !== 4){
            currentAjaxRequest.abort();
        }
        $('#stock-log-page-content').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        perPage = perPage || $('#perPage').val() || 10;
        currentAjaxRequest = $.ajax({
            url: '{{ route('vendor.inventory.logs', $product->id) }}',
            method: 'GET',
            data: { page: page, per_page: perPage },
            success: function(res){
                $('#stock-log-page-content').html(res);
            },
            error: function(xhr){
                if(xhr.statusText === 'abort'){ return; }
                $('#stock-log-page-content').html('<div class="text-center text-danger">Error loading logs.</div>');
            },
            complete: function(){ currentAjaxRequest = null; }
        });
    }

    $(document).on('click', '#stock-log-page-content .pagination a.page-link', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        if(page){ fetchLogs(page); }
    });

    $(document).on('change', '#perPage', function(){
        fetchLogs(1, $(this).val());
    });
});
</script>
@endpush

@extends('vendor.layouts.app')
@section('title', 'Stock Logs | Deal24hours')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Stock Logs</h4>
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
$(document).on('change', '#perPage', function(){
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', $(this).val());
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
});
</script>
@endpush

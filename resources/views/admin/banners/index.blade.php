@extends('admin.layouts.app')
@section('title', 'Banners | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">All Banners</h4>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Banner
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-striped table-centered" id="banners-table" style="width:100%">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Link</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    const table = $('#banners-table').DataTable({
        processing:true,
        serverSide:true,
        ajax:'{{ route('admin.banners.data') }}',
        columns:[
            {data:'id', name:'id'},
            {data:'banner_img', name:'banner_img', orderable:false, searchable:false},
            {data:'banner_link', name:'banner_link', orderable:false, searchable:false},
            {data:'banner_start_date', name:'banner_start_date', orderable:false, searchable:false},
            {data:'banner_end_date', name:'banner_end_date', orderable:false, searchable:false},
            {data:'status', name:'status', orderable:false, searchable:false},
            {data:'action', name:'action', orderable:false, searchable:false}
        ]
    });
});
</script>
@endsection

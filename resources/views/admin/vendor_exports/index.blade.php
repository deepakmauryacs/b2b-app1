@extends('admin.layouts.app')
@section('title', 'Vendor Exports | Deal24hours')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Vendor Exports</h4>
                <a href="{{ route('admin.vendor-exports.create') }}" class="btn btn-primary">New Export</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Range</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exports as $export)
                                <tr>
                                    <td>{{ $export->id }}</td>
                                    <td>{{ number_format($export->range_start) }} - {{ number_format($export->range_end) }}</td>
                                    <td>{{ ucfirst($export->status) }}</td>
                                    <td>
                                        @if($export->status === 'completed')
                                            <a href="{{ route('admin.vendor-exports.download', $export->id) }}" class="btn btn-sm btn-success">Download</a>
                                        @else
                                            <span class="text-muted">Processing</span>
                                        @endif
                                        <form action="{{ route('admin.vendor-exports.destroy', $export->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete export?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $exports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

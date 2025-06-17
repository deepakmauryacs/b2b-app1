@extends('admin.layouts.app')
@section('title', 'Vendor Export | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">New Vendor Export</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.vendor-exports.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Range Start</label>
                        <input type="number" name="range_start" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Range End</label>
                        <input type="number" name="range_end" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Start Export</button>
                    <a href="{{ route('admin.vendor-exports.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

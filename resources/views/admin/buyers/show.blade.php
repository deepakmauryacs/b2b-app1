@extends('admin.layouts.app')
@section('title', 'View Buyer | Deal24hours')
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Buyer Details</h4>
                <a href="{{ route('admin.buyers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light-subtle">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="form-control-static"><b>Name:</b> {{ $buyer->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="form-control-static"><b>Email:</b> {{ $buyer->email }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="form-control-static"><b>Phone:</b> {{ $buyer->phone }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="form-control-static">
                                        <b>Status:</b>
                                        <span class="badge {{ $buyer->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $buyer->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light-subtle">
                                <h5 class="mb-0">Store Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="form-control-static"><b>Pincode:</b> {{ $buyer->pincode ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <p class="form-control-static">
                                        <b>Address:</b>
                                        {{ $buyer->address ?? 'N/A' }}<br>
                                        {{ $buyer->city ?? '' }} {{ $buyer->pincode ?? '' }}<br>
                                        {{ $buyer->state ?? '' }}, {{ $buyer->country ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')
@section('title', 'View Vendor | Deal24hours')
@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Vendor Details</h4>
                    <a href="{{ route('admin.vendors.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Name:</b> {{ $vendor->name }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Email:</b> {{ $vendor->email }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Phone:</b> {{ $vendor->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Status:</b>
                                            <span class="badge {{ $vendor->status == '1' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $vendor->status == '1' ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Profile Verified:</b>
                                            <span
                                                class="badge {{ $vendor->is_profile_verified == '1' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $vendor->is_profile_verified == '1' ? 'Verified' : 'Not Verified' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Store Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Store Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Store Name:</b> {{ $vendor->store_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Store Email:</b> {{ $vendor->profile_email ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Store Phone:</b> {{ $vendor->profile_phone ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>GST Number:</b> {{ $vendor->gst_no ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Address:</b>
                                            {{ $vendor->address ?? 'N/A' }}<br>
                                            {{ $vendor->city ?? '' }} {{ $vendor->pincode ?? '' }}<br>
                                            {{ $vendor->state ?? '' }}, {{ $vendor->country ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Documents</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>GST Document:</b>
                                        </p>
                                        @if ($vendor->gst_doc)
                                            @if (pathinfo($vendor->gst_doc, PATHINFO_EXTENSION) === 'pdf')
                                                <a href="{{ asset($vendor->gst_doc) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="bi bi-file-earmark-pdf"></i> View PDF Document
                                                </a>
                                            @else
                                                <a href="{{ asset($vendor->gst_doc) }}" target="_blank"
                                                    class="mt-2 d-inline-block">
                                                    <img src="{{ asset($vendor->gst_doc) }}" alt="GST Document"
                                                        class="img-thumbnail" style="max-height: 200px;">
                                                </a>
                                            @endif
                                        @else
                                            <p class="text-muted">No GST document uploaded</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Store Logo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Logo:</b>
                                        </p>
                                        @if ($vendor->store_logo)
                                            <img src="{{ asset($vendor->store_logo) }}" alt="Store Logo"
                                                class="img-thumbnail mt-2" style="max-height: 200px;">
                                        @else
                                            <p class="text-muted">No store logo uploaded</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms Acceptance -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Terms & Conditions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="accept_terms"
                                            {{ $vendor->accept_terms ? 'checked' : 'disabled' }}>
                                        <label class="form-check-label" for="accept_terms">
                                            <b>Vendor has accepted terms and conditions</b>
                                        </label>
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

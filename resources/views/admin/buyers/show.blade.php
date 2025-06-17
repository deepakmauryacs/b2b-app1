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
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light-subtle">
                                    <h5 class="mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Name:</b> {{ $buyer->name }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Email:</b> {{ $buyer->email }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Phone:</b> {{ $buyer->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Status:</b>
                                            <span class="badge {{ $buyer->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $buyer->status == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Profile Verified:</b>
                                            <span class="badge {{ $buyer->is_profile_verified == 1 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $buyer->is_profile_verified == 1 ? 'Verified' : 'Not Verified' }}
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
                                            <b>Store Name:</b> {{ $buyer->store_name ?? $buyer->buyerProfile->store_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Store Email:</b> {{ $buyer->profile_email ?? $buyer->buyerProfile->email ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Store Phone:</b> {{ $buyer->profile_phone ?? $buyer->buyerProfile->phone ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>GST Number:</b> {{ $buyer->gst_no ?? $buyer->buyerProfile->gst_no ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="form-control-static">
                                            <b>Address:</b>
                                            {{ $buyer->address ?? $buyer->buyerProfile->address ?? 'N/A' }}<br>
                                            {{ ($buyer->city ?? $buyer->buyerProfile->city) ?? '' }} {{ ($buyer->pincode ?? $buyer->buyerProfile->pincode) ?? '' }}<br>
                                            {{ ($buyer->state ?? $buyer->buyerProfile->state) ?? '' }}, {{ ($buyer->country ?? $buyer->buyerProfile->country) ?? '' }}
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
                                        @php
                                            $gstDoc = $buyer->gst_doc ?? $buyer->buyerProfile->gst_doc;
                                        @endphp
                                        @if ($gstDoc)
                                            @if (pathinfo($gstDoc, PATHINFO_EXTENSION) === 'pdf')
                                                <a href="{{ asset($gstDoc) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="bi bi-file-earmark-pdf"></i> View PDF Document
                                                </a>
                                            @else
                                                <a href="{{ asset($gstDoc) }}" target="_blank" class="mt-2 d-inline-block">
                                                    <img src="{{ asset($gstDoc) }}" alt="GST Document" class="img-thumbnail" style="max-height: 200px;">
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
                                        @php
                                            $logo = $buyer->store_logo ?? $buyer->buyerProfile->store_logo;
                                        @endphp
                                        @if ($logo)
                                            <img src="{{ asset($logo) }}" alt="Store Logo" class="img-thumbnail mt-2" style="max-height: 200px;">
                                        @else
                                            <p class="text-muted">No store logo uploaded</p>
                                        @endif
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

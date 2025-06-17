@extends('admin.layouts.app')
@section('title', 'Product Details | Deal24hours')

@push('styles')
    <style>
        .product-image {
            max-height: 200px;
            width: auto;
            object-fit: contain;
            /* Ensures the image fits within its bounds without distortion */
            display: block;
            /* Ensures image takes its own line below the label */
            margin-top: 0.5rem;
            /* Space between label and image */
        }

        /*
         * Custom styling for form-control-plaintext to ensure consistent display.
         * This makes inputs look like plain text but keeps form structure.
         */
        .form-control-plaintext {
            display: block;
            width: 100%;
            padding: .375rem 0;
            margin-bottom: 0;
            line-height: 1.5;
            color: #212529;
            /* Bootstrap's default text color */
            background-color: transparent;
            border: solid transparent;
            border-width: 1px 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0;
        }

        .form-control-plaintext:focus {
            outline: 0;
            box-shadow: none;
        }

        /* Optional: Style for the label column if you want a two-column layout effect */
        .detail-label {
            font-weight: bold;
            color: #495057;
            /* Slightly darker than default text for labels */
            margin-bottom: 0.25rem;
            /* Small space below label */
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-xl-12"> {{-- Adjust column width and offset as desired --}}
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Product Details For: {{ $product->product_name }}</h4>
                    <div class="d-flex gap-2 align-items-center">
                        {{-- Product status badge --}}
                        @php
                            $statusClass =
                                [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                ][$product->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusClass }} text-light fs-14 py-1 px-2">
                            {{ ucfirst($product->status) }}
                        </span>
                        {{-- Back to List Button --}}
                        <a href="javascript:void(0);" onclick="window.history.back();"
                            class="badge border border-secondary text-secondary px-2 py-1 fs-13">
                            ← Back to List
                        </a>
                        {{-- Optional: Edit Product Button --}}
                        {{-- <a href="{{ route('admin.products.edit', $product->id) }}" class="badge bg-primary text-light px-2 py-1 fs-13">
                        Edit Product
                    </a> --}}
                    </div>
                </div>

                <div class="card-body">


                    {{-- Using Bootstrap grid inside the form for a two-column layout --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Name:</label>
                            <input type="text" class="form-control-plaintext"
                                value="{{ $product->product_name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Price:</label>
                            <input type="text" class="form-control-plaintext"
                                value="₹{{ number_format($product->price, 2) }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Unit:</label>
                            <input type="text" class="form-control-plaintext" value="{{ $product->unit ?? 'N/A' }}"
                                disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Vendor:</label>
                            <input type="text" class="form-control-plaintext"
                                value="{{ $product->vendor->name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Category:</label>
                            <input type="text" class="form-control-plaintext"
                                value="{{ $product->category->name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Stock Quantity:</label>
                            <input type="text" class="form-control-plaintext" value="{{ $product->stock_quantity }}"
                                disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">Minimum Order Qty:</label>
                            <input type="text" class="form-control-plaintext" value="{{ $product->min_order_qty }}"
                                disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">HSN Code:</label>
                            <input type="text" class="form-control-plaintext" value="{{ $product->hsn_code ?? 'N/A' }}"
                                disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="detail-label">GST Rate:</label>
                            <input type="text" class="form-control-plaintext"
                                value="{{ $product->gst_rate ? $product->gst_rate . '%' : 'N/A' }}" disabled>
                        </div>

                        {{-- Description takes full width for better readability --}}
                        <div class="col-12 mb-3">
                            <label class="detail-label">Description:</label>
                            <textarea class="form-control-plaintext" rows="5" disabled>{{ $product->description ?? 'N/A' }}</textarea>
                        </div>

                        {{-- Product Image --}}
                        <div class="col-12 mb-3">
                            <label class="detail-label">Product Image:</label>
                            @if ($product->product_image)
                                <img src="{{ asset('storage/' . $product->product_image) }}" alt="Product Image"
                                    class="img-thumbnail product-image">
                            @else
                                <span class="text-muted d-block mt-2">No image available</span>
                            @endif
                        </div>
                    </div>

                    {{-- No approval/rejection buttons as this is a general display page --}}

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- No page-specific JavaScript required for a read-only display. --}}
@endpush

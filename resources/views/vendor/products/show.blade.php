@extends('vendor.layouts.app')
@section('title', 'Product Details | Deal24hours')

@push('styles')
<style>
    .product-image {
        max-height: 200px;
        width: auto;
        object-fit: contain;
        display: block;
        margin-top: 0.5rem;
    }
    .form-control-plaintext {
        display: block;
        width: 100%;
        padding: .375rem 0;
        margin-bottom: 0;
        line-height: 1.5;
        color: #212529;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
        border-radius: 0;
    }
    .form-control-plaintext:focus {
        outline: 0;
        box-shadow: none;
    }
    .detail-label {
        font-weight: bold;
        color: #495057;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Product Details: {{ $product->product_name }}</h4>
                <a href="{{ route('vendor.products.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">
                    &larr; Back to List
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Name:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->product_name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Price:</label>
                        <input type="text" class="form-control-plaintext" value="₹{{ number_format($product->price, 2) }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Unit:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->unit ?? 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Category:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->category->name ?? 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Stock Quantity:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->stock_quantity }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Minimum Order Qty:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->min_order_qty }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">HSN Code:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->hsn_code ?? 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">GST Rate:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->gst_rate ? $product->gst_rate.'%' : 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Status:</label>
                        <input type="text" class="form-control-plaintext" value="{{ ucfirst($product->status) }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="detail-label">Created At:</label>
                        <input type="text" class="form-control-plaintext" value="{{ $product->created_at->format('d-m-Y') }}" disabled>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="detail-label">Description:</label>
                        <textarea class="form-control-plaintext" rows="5" disabled>{{ $product->description ?? 'N/A' }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="detail-label">Product Image:</label>
                        @if ($product->product_image)
                            <img src="{{ asset('storage/' . $product->product_image) }}" alt="Product Image" class="img-thumbnail product-image">
                        @else
                            <span class="text-muted d-block mt-2">No image available</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

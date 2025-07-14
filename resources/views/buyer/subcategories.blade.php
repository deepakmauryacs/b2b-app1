@extends('buyer.layouts.app')
@section('title', 'Fixfellow - Sub Categories')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('buyer.index') }}" class="btn btn-link">&larr; Back to Categories</a>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @forelse($subcategories as $sub)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="{{ route('buyer.product-page', $sub->id) }}" class="card text-center shadow-sm">
                                        <div class="card-body py-3">
                                            <h6 class="mb-0">{{ $sub->name }}</h6>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center mb-0">No sub categories found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

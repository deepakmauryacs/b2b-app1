@extends('buyer.layouts.app')
@section('title', 'Products')
@section('content')
 <!-- page-title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="page-title-heading">
                        <h1 class="title">Product </h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span class="mr-1"><i class="ti ti-home"></i></span>
                        <a title="Homepage" href="index.html">Home</a>
                        <span class="ttm-bread-sep">&nbsp;/&nbsp;</span>
                        <span class="ttm-textcolor-skincolor">Product List</span>
                    </div>
                </div>
            </div>
        </div>  
    </div>                    
</div>
<!-- page-title end-->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-12">
                                        <!-- Sorting Dropdown -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <select class="form-select" id="input-sort" name="sort-type" aria-label="Sort products">
                                    <option value="">Sort by:</option>
                                    <option value="1">Name (A - Z)</option>
                                    <option value="2">Name (Z - A)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row" id="productContainer">
                            <!-- Placeholder skeleton loading -->
                            <div class="col-md-3 col-sm-6 mb-4 placeholder-container">
                                <div class="card h-100 border shadow-sm placeholder-glow">
                                    <div class="placeholder" style="height:180px; background-color:#e9ecef;"></div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-3 placeholder col-8 mx-auto" style="height:24px;"></h6>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                            <button class="btn btn-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-4 placeholder-container">
                                <div class="card h-100 border shadow-sm placeholder-glow">
                                    <div class="placeholder" style="height:180px; background-color:#e9ecef;"></div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-3 placeholder col-8 mx-auto" style="height:24px;"></h6>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                            <button class="btn btn-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-4 placeholder-container">
                                <div class="card h-100 border shadow-sm placeholder-glow">
                                    <div class="placeholder" style="height:180px; background-color:#e9ecef;"></div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-3 placeholder col-8 mx-auto" style="height:24px;"></h6>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                            <button class="btn btn-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-4 placeholder-container">
                                <div class="card h-100 border shadow-sm placeholder-glow">
                                    <div class="placeholder" style="height:180px; background-color:#e9ecef;"></div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title mb-3 placeholder col-8 mx-auto" style="height:24px;"></h6>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                            <button class="btn btn-primary btn-sm disabled placeholder col-12" style="height:38px;"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button id="loadMoreBtn" class="btn btn-primary">Load More</button>
                        </div>
                    
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var page = 1;
    var currentSort = '';
    var isLoading = false;
    
    // Initial load
    loadProducts(page, currentSort);

    // Load more button click handler
    document.getElementById('loadMoreBtn').addEventListener('click', function () {
        if (!isLoading) {
            page++;
            loadProducts(page, currentSort);
        }
    });

    // Sort dropdown change handler
    document.getElementById('input-sort').addEventListener('change', function() {
        currentSort = this.value;
        page = 1; // Reset to first page when sorting changes
        document.getElementById('productContainer').innerHTML = ''; // Clear current products
        loadProducts(page, currentSort);
    });

    function loadProducts(pg, sort) {
        isLoading = true;
        var btn = document.getElementById('loadMoreBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
        
        axios.get('{{ route('buyer.products.list') }}', { 
            params: { 
                page: pg,
                sort: sort 
            } 
        })
        .then(function (res) {
            if (res.data.status) {
                // Remove placeholders on first load
                if (pg === 1) {
                    document.querySelectorAll('.placeholder-container').forEach(el => el.remove());
                }
                
                appendProducts(res.data.products);
                
                if (res.data.has_more) {
                    btn.disabled = false;
                    btn.innerHTML = 'Load More';
                } else {
                    btn.style.display = 'none';
                }
            } else {
                btn.style.display = 'none';
            }
            isLoading = false;
        })
        .catch(function (error) {
            if (error.response && error.response.status === 422) {
                alert(error.response.data.message);
            }
            btn.disabled = false;
            btn.innerHTML = 'Load More';
            isLoading = false;
        });
    }

    function appendProducts(list) {
        var container = document.getElementById('productContainer');

        list.forEach(function (prod) {
            var imageSection = prod.product_image
                ? '<div class="position-relative" style="height:180px; overflow:hidden;">' + 
                    '<img src="' + prod.product_image + '" class="card-img-top h-100 object-fit-cover" alt="' + prod.product_name + '">' +
                  '</div>'
                : '<div class="d-flex align-items-center justify-content-center bg-light border-bottom" style="height:180px;">' +
                    '<span class="fw-bold text-muted">' + prod.product_name + '</span>' +
                  '</div>';

            var html = '<div class="col-md-3 col-sm-6 mb-4">' +
                        '<div class="card h-100 border shadow-sm">' +
                            imageSection +
                            '<div class="card-body text-center">' +
                                '<h6 class="card-title mb-3">' + prod.product_name + '</h6>' +
                                '<div class="d-grid gap-2">' +
                                    '<button class="btn btn-outline-primary btn-sm"><i class="bi bi-chat-text"></i> Get Best Price</button>' +
                                    '<button class="btn btn-primary btn-sm"><i class="bi bi-plus-square"></i> Add to RFQ</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

            container.insertAdjacentHTML('beforeend', html);
        });
    }
});
</script>
@endpush
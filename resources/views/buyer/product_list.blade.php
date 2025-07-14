@extends('buyer.layouts.app')
@section('title', 'Products')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" id="productContainer"></div>
                        <div class="text-center mt-3">
                            <button id="loadMoreBtn" class="btn btn-primary">Load More</button>
                        </div>
                    </div>
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
    loadProducts(page);

    document.getElementById('loadMoreBtn').addEventListener('click', function () {
        page++;
        loadProducts(page);
    });

    function loadProducts(pg) {
        var btn = document.getElementById('loadMoreBtn');
        btn.disabled = true;
        axios.get('{{ route('buyer.products.list') }}', { params: { page: pg } })
            .then(function (res) {
                if (res.data.status) {
                    appendProducts(res.data.products);
                    if (res.data.has_more) {
                        btn.disabled = false;
                    } else {
                        btn.style.display = 'none';
                    }
                } else {
                    btn.style.display = 'none';
                }
            })
            .catch(function (error) {
                if (error.response && error.response.status === 422) {
                    alert(error.response.data.message);
                }
                btn.disabled = false;
            });
    }

    function appendProducts(list) {
        var container = document.getElementById('productContainer');
        list.forEach(function (prod) {
            var img = prod.product_image ? '<img src="' + prod.product_image + '" class="card-img-top" alt="' + prod.product_name + '">' : '';
            var html = '<div class="col-md-3 col-sm-6 mb-3">' +
                        '<div class="card h-100 border shadow-sm">' +
                            img +
                            '<div class="card-body text-center">' +
                                '<h6 class="card-title mb-1">' + prod.product_name + '</h6>' +
                                '<small class="text-muted">' + (prod.date || '') + '</small>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
            container.insertAdjacentHTML('beforeend', html);
        });
    }
});
</script>
@endpush

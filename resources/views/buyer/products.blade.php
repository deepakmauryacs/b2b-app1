@extends('buyer.layouts.app')
@section('title', 'Fixfellow - Products')
@section('content')
<section class="py-5">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <a href="javascript:history.back()" class="btn btn-link">&larr; Back</a>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" id="productCards"></div>
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
    var subId = {{ $subCategoryId }};
    axios.get('/buyer/products/' + subId)
        .then(function (res) { render(res.data); })
        .catch(function () { render([]); });

    function render(list) {
        var container = document.getElementById('productCards');
        container.innerHTML = '';
        if (list.length) {
            list.forEach(function (prod) {
                var imageSection = prod.product_image ? '<img src="' + prod.product_image + '" class="card-img-top" alt="' + prod.product_name + '">' : '';
                var card = '<div class="col-md-3 col-sm-6 mb-3">' +
                        '<div class="card h-100 border shadow-sm">' +
                            imageSection +
                            '<div class="card-body text-center">' +
                                '<h6 class="card-title mb-0">' + prod.product_name + '</h6>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                container.insertAdjacentHTML('beforeend', card);
            });
        } else {
            container.innerHTML = '<div class="col-12"><p class="text-center mb-0">No products found.</p></div>';
        }
    }
});
</script>
@endpush

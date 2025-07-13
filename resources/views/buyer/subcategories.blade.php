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
                        <div class="row" id="subCategoryCards"></div>
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
    var categoryId = {{ $categoryId }};
    axios.get('/buyer/sub-categories/' + categoryId)
        .then(function (res) { render(res.data); })
        .catch(function () { render([]); });

    function render(list) {
        var container = document.getElementById('subCategoryCards');
        container.innerHTML = '';
        if (list.length) {
            list.forEach(function (sub) {
                var html = '<div class="col-md-3 col-sm-6 mb-3">' +
                    '<div class="card text-center shadow-sm subcategory-card" data-id="' + sub.id + '">' +
                        '<div class="card-body py-3">' +
                            '<h6 class="mb-0">' + sub.name + '</h6>' +
                        '</div>' +
                    '</div>' +
                '</div>';
                container.insertAdjacentHTML('beforeend', html);
            });
            attachHandlers();
        } else {
            container.innerHTML = '<div class="col-12"><p class="text-center mb-0">No sub categories found.</p></div>';
        }
    }

    function attachHandlers() {
        document.querySelectorAll('.subcategory-card').forEach(function (el) {
            el.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                if (!/^\d+$/.test(id)) return;
                window.location.href = '/buyer/sub-category/' + id + '/products';
            });
        });
    }
});
</script>
@endpush

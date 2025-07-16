@extends('buyer.layouts.app')
@section('title', 'Fixfellow - Tools Store Ecommerce')
@section('content')
<div id="flipkartStyleSlider" class="carousel slide" data-bs-ride="carousel" style="padding: 15px;">
   <div class="carousel-inner">
      <div class="carousel-item active">
         <img src="https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/74f0ad81e44e6e6f.jpg" class="d-block w-100" alt="Sofa Slide">
      </div>
      <div class="carousel-item">
         <img src="https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/833050f518e0f8cb.jpeg" class="d-block w-100" alt="Slide 2">
      </div>
      <div class="carousel-item">
         <img src="https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/74f0ad81e44e6e6f.jpg" class="d-block w-100" alt="Slide 3">
      </div>
   </div>
   <button class="carousel-control-prev" type="button" data-bs-target="#flipkartStyleSlider" data-bs-slide="prev">
   <span class="carousel-control-prev-icon" aria-hidden="true"></span>
   <span class="visually-hidden">Previous</span>
   </button>
   <button class="carousel-control-next" type="button" data-bs-target="#flipkartStyleSlider" data-bs-slide="next">
   <span class="carousel-control-next-icon" aria-hidden="true"></span>
   <span class="visually-hidden">Next</span>
   </button>
</div>
<!--fillter-section-->
<section class="py-5"> 
   <div class="container">
      <!-- Category List Heading -->
      <div class="row mb-3">
         <div class="col-12">
            <h5>Category List</h5>
         </div>
      </div>
      <!-- Categories Row -->
      <div class="row mb-5" id="categoryCards">
          <div class="col-md-3 col-sm-6 mb-3" aria-hidden="true">
              <div class="card placeholder-glow">
                  <div class="card-body py-3">
                      <span class="placeholder col-6"></span>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3" aria-hidden="true">
              <div class="card placeholder-glow">
                  <div class="card-body py-3">
                      <span class="placeholder col-6"></span>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3" aria-hidden="true">
              <div class="card placeholder-glow">
                  <div class="card-body py-3">
                      <span class="placeholder col-6"></span>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3" aria-hidden="true">
              <div class="card placeholder-glow">
                  <div class="card-body py-3">
                      <span class="placeholder col-6"></span>
                  </div>
              </div>
          </div>
      </div>





      <div class="row mb-4">
         <div class="col-12">
            <h5>Our Top Selling Products</h5>
         </div>
      </div>
      <div class="row" id="topProductCards">
         <div class="col-md-3 col-sm-6 mb-4" aria-hidden="true">
            <div class="card placeholder-glow">
               <div class="card-body py-5">
                  <span class="placeholder col-12" style="height: 180px;"></span>
               </div>
            </div>
         </div>
         <div class="col-md-3 col-sm-6 mb-4" aria-hidden="true">
            <div class="card placeholder-glow">
               <div class="card-body py-5">
                  <span class="placeholder col-12" style="height: 180px;"></span>
               </div>
            </div>
         </div>
         <div class="col-md-3 col-sm-6 mb-4" aria-hidden="true">
            <div class="card placeholder-glow">
               <div class="card-body py-5">
                  <span class="placeholder col-12" style="height: 180px;"></span>
               </div>
            </div>
         </div>
         <div class="col-md-3 col-sm-6 mb-4" aria-hidden="true">
            <div class="card placeholder-glow">
               <div class="card-body py-5">
                  <span class="placeholder col-12" style="height: 180px;"></span>
               </div>
            </div>
         </div>
      </div>
      </div>

   </div>
</section>


<!--fillter-section end-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function attachCategoryHandlers() {
        document.querySelectorAll('.category-card').forEach(function (el) {
            el.addEventListener('click', function (e) {
                var slug = this.getAttribute('data-slug');
                if (!slug) return;
                e.preventDefault();
                var categoryUrl = '{{ route('buyer.sub-category-page', ['slug' => '__slug__']) }}'.replace('__slug__', slug);
                window.location.href = categoryUrl;
            });
        });
    }

    // Load categories
    axios.get('{{ route('buyer.categories') }}').then(function (response) {
        var container = document.getElementById('categoryCards');
        container.innerHTML = '';
        var data = response.data;
        if (data.length) {
            var urlTemplate = '{{ route('buyer.sub-category-page', ['slug' => '__slug__']) }}';
            data.forEach(function (cat) {
                var url = urlTemplate.replace('__slug__', cat.slug);
                var html = '<div class="col-md-3 col-sm-6 mb-3">' +
                    '<a href="' + url + '" class="card text-center shadow-sm category-card" style="background-color: #e6f2ff;" data-slug="' + cat.slug + '">' +
                    '<div class="card-body py-3">' +
                    '<h6 class="mb-0">' + cat.name + '</h6>' +
                    '</div></a></div>';
                container.insertAdjacentHTML('beforeend', html);
            });
            attachCategoryHandlers();
        } else {
            container.innerHTML = '<div class="col-12"><p class="text-center mb-0">No categories found.</p></div>';
        }
    });

    // Load top products
    axios.get('{{ route('buyer.top-products') }}').then(function (response) {
        var container = document.getElementById('topProductCards');
        container.innerHTML = '';
        var data = response.data;
        if (data.length) {
            data.forEach(function (prod) {
                var imageSection = prod.product_image
                    ? '<div class="position-relative"><img src="' + prod.product_image + '" class="card-img-top" alt="' + prod.product_name + '"></div>'
                    : '<div class="d-flex align-items-center justify-content-center" style="height:180px;background-color: #e7f2ff;margin: 20px;"><span class="fw-bold">' + prod.product_name + '</span></div>';
                var card = '<div class="col-md-3 col-sm-6 mb-4">' +
                    '<div class="card h-100 border shadow-sm">' +
                        imageSection +
                        '<div class="card-body text-center">' +
                            '<h6 class="card-title mb-3">' + prod.product_name + '</h6>' +
                            '<div class="d-grid gap-2">' +
                                '<button class="btn btn-outline-primary btn-sm gap-5"><i class="bi bi-chat-text"></i> Get Best Price</button>' +
                                '<button class="btn btn-primary btn-sm gap-5"><i class="bi bi-plus-square"></i> Add to RFQ</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
                container.insertAdjacentHTML('beforeend', card);
            });
        } else {
            container.innerHTML = '<div class="col-12"><p class="text-center mb-0">No products found.</p></div>';
        }
    });

});
</script>
@endpush

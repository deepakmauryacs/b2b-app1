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

      <div class="row">
         <div class="col-md-12">
            <div class="card">
               <div class="card-body">
                  <form id="newsletterForm" class="ajax">
                     <div class="mb-3">
                        <label for="newsletterEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="newsletterEmail" name="email" required>
                     </div>
                     <div class="mb-3">
                        <label for="subscribeDate" class="form-label">Subscribe Date</label>
                        <input type="text" class="form-control date-picker" id="subscribeDate" name="subscribe_date" placeholder="dd-mm-yyyy" required>
                     </div>
                     <button type="submit" class="btn btn-primary">Subscribe</button>
                  </form>
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
            el.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                if (!/^\d+$/.test(id)) return;
                window.location.href = '/buyer/category/' + id + '/sub-categories';
            });
        });
    }

    // Load categories
    axios.get('{{ route('buyer.categories') }}').then(function (response) {
        var container = document.getElementById('categoryCards');
        container.innerHTML = '';
        var data = response.data;
        if (data.length) {
            data.forEach(function (cat) {
                var html = '<div class="col-md-3 col-sm-6 mb-3">' +
                    '<div class="card text-center shadow-sm category-card" data-id="' + cat.id + '">' +
                    '<div class="card-body py-3">' +
                    '<h6 class="mb-0">' + cat.name + '</h6>' +
                    '</div></div></div>';
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
                    : '<div class="d-flex align-items-center justify-content-center" style="height:180px;"><span class="fw-bold">' + prod.product_name + '</span></div>';
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

    // Newsletter subscription
    var newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var emailField = document.getElementById('newsletterEmail');
            var dateField = document.getElementById('subscribeDate');
            var email = emailField.value.trim();
            var date = dateField.value.trim();
            var valid = true;
            emailField.classList.remove('is-invalid');
            dateField.classList.remove('is-invalid');

            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                emailField.classList.add('is-invalid');
                valid = false;
            }
            if (!date || !/^\d{2}-\d{2}-\d{4}$/.test(date)) {
                dateField.classList.add('is-invalid');
                valid = false;
            }
            if (!valid) {
                return;
            }

            var formData = new FormData(newsletterForm);
            axios.post('{{ route('newsletter.subscribe') }}', formData)
                .then(function (res) {
                    if (res.data.success) {
                        toastr.success(res.data.message);
                        newsletterForm.reset();
                    }
                })
                .catch(function (error) {
                    if (error.response && error.response.status === 422) {
                        var errors = error.response.data.errors;
                        Object.keys(errors).forEach(function (k) {
                            var field = k === 'email' ? emailField : dateField;
                            field.classList.add('is-invalid');
                            toastr.error(errors[k][0]);
                        });
                    } else {
                        toastr.error('Something went wrong');
                    }
                });
        });
    }
});
</script>@endpush

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
          <div class="col-md-3 col-sm-6 mb-3">
              <div class="card text-center shadow-sm category-card">
                  <div class="card-body py-3">
                      <h6 class="mb-0">Industrial Machinery</h6>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
              <div class="card text-center shadow-sm category-card">
                  <div class="card-body py-3">
                      <h6 class="mb-0">Tools</h6>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
              <div class="card text-center shadow-sm category-card">
                  <div class="card-body py-3">
                      <h6 class="mb-0">Equipment</h6>
                  </div>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
              <div class="card text-center shadow-sm category-card">
                  <div class="card-body py-3">
                      <h6 class="mb-0">Supplies</h6>
                  </div>
              </div>
          </div>
      </div>



      <div class="row mb-4">
         <div class="col-12">
            <h5>Our Top Selling Products</h5>
         </div>
      </div>
      <div class="row">
         <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100 border shadow-sm">
               <div class="position-relative">
                  <img src="{{ asset('assets/buyer_assets/images/product/pro-front-09.png') }}" class="card-img-top" alt="Hammer Drill">
               </div>
               <div class="card-body text-center">
                  <h6 class="card-title mb-3">
                     <a href="product-layout1.html" class="text-decoration-none text-dark">Hammer Drill</a>
                  </h6>
                  <div class="d-grid gap-2">
                     <button class="btn btn-outline-primary btn-sm gap-5"><i class="bi bi-chat-text"></i> Get Best Price</button>
                     <button class="btn btn-primary btn-sm gap-5"><i class="bi bi-plus-square"></i> Add to RFQ</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


<!--fillter-section end-->
@endsection
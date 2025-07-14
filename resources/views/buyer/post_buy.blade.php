@extends('buyer.layouts.app')
@section('title', 'Post Buy Requirement')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<!--site-main start-->
 <!-- page-title -->
        <div class="ttm-page-title-row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-title-heading">
                                <h1 class="title">Get Best Deal</h1>
                            </div>
                            <div class="breadcrumb-wrapper">
                                <span class="mr-1"><i class="ti ti-home"></i></span>
                                <a title="Homepage" href="index.html">Home</a>
                                <span class="ttm-bread-sep">&nbsp;/&nbsp;</span>
                                <span class="ttm-textcolor-skincolor">Get Best Deal</span>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>                    
        </div><!-- page-title end-->
<div class="site-main" style="margin-top: 300px;">
<section class="contact-section bg-layer bg-layer-equal-height clearfix">
    <div class="container">
        <div class="row g-0">
            <div class="col-lg-8 col-md-7">
                <div class="ttm-col-bgcolor-yes ttm-bg ttm-bgcolor-grey spacing-2">
                    <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                    <div class="layer-content">
                        <div class="section-title style2">
                            <div class="title-header">
                                <h5>Post Buy Requirement</h5>
                                <h2 class="title">Share your requirement and start receiving the best offers—fast and hassle-free!*</h2>
                            </div>
                        </div>

                        <form id="buyReqForm" class="ttm-contactform wrap-form clearfix" method="POST" action="{{ route('buyer.post-buy.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>
                                        <span class="text-input">
                                            <i class="bi bi-box-seam"></i>
                                            <input name="product_name" type="text" value="" placeholder="Enter the product you are looking for..." required>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <label>
                                        <span class="text-input">
                                            <i class="bi bi-phone"></i>
                                            <div class="input-group">
                                                <input name="mobile_number" type="text" value="" placeholder="Mobile Number" class="form-control" required>
                                            </div>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label>
                                        <span class="text-input">
                                            <i class="bi bi-calendar"></i>
                                            <input name="expected_date" id="expected_date" type="text" value="" class="form-control date-picker" placeholder="Expected Date">
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <input type="submit" class="submit ttm-btn ttm-btn-size-md ttm-btn-shape-square ttm-btn-style-fill ttm-btn-color-skincolor" value="Get Best Deal">
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5">
                <div class="ttm-col-bgcolor-yes ttm-bg ttm-bgcolor-skincolor spacing-3 text-white text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 100%; padding: 40px 20px;">
                    <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                    <div class="layer-content py-5">
                        <i class="bi bi-lightning-charge" style="font-size: 50px;"></i>
                        <h2 class="mt-3 fw-bold" style="font-size: 28px;">#Deal24Hours</h2>
                        <blockquote class="fs-5 mt-3" style="font-style: italic; line-height: 1.6;">
                            “Why wait? Post your requirement now and get multiple best-price quotes within 24 hours from trusted sellers.”
                        </blockquote>
                        <p class="mt-3 fs-6 fst-italic">Fast • Free • Reliable</p>
                    </div>
                </div>
            </div>


        </div><!-- row end -->
    </div>
</section>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function(){
    if (typeof flatpickr !== 'undefined') {
        $('#expected_date').flatpickr({dateFormat:'d-m-Y'});
    }
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    $('#buyReqForm').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        $.post(form.attr('action'), form.serialize())
            .done(function(res){
                if(res.status){
                    toastr.success(res.message);
                    form[0].reset();
                } else {
                    toastr.error(res.message || 'An error occurred');
                }
            })
            .fail(function(xhr){
                if(xhr.status === 422){
                    $.each(xhr.responseJSON.errors, function(key, val){
                        var inp = form.find('[name="'+key+'"]');
                        inp.addClass('is-invalid');
                        inp.after('<div class="invalid-feedback d-block">'+val[0]+'</div>');
                        toastr.error(val[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred');
                }
            });
    });
});
</script>
@endpush

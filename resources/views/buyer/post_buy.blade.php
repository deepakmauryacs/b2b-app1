@extends('buyer.layouts.app')
@section('title','Post Buy Requirement')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Post Buy Requirement</h4>
            </div>
            <div class="card-body">
                <form id="buyReqForm" action="{{ route('buyer.post-buy.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter the product you are looking for...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="country_code" id="country_code" class="form-select" style="max-width:120px">
                                <option value="91">+91</option>
                                <option value="1">+1</option>
                                <option value="44">+44</option>
                            </select>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" placeholder="Enter Mobile Number">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expected_date" class="form-label">Expected Date</label>
                        <input type="text" name="expected_date" id="expected_date" class="form-control date-picker" placeholder="dd-mm-yyyy">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Get Best Deal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                }else{
                    toastr.error(res.message || 'An error occurred');
                }
            })
            .fail(function(xhr){
                if(xhr.status===422){
                    $.each(xhr.responseJSON.errors, function(key,val){
                        var inp = form.find('[name="'+key+'"]');
                        inp.addClass('is-invalid');
                        inp.after('<div class="invalid-feedback d-block">'+val[0]+'</div>');
                        toastr.error(val[0]);
                    });
                }else{
                    toastr.error(xhr.responseJSON?.message || 'An error occurred');
                }
            });
    });
});
</script>
@endpush

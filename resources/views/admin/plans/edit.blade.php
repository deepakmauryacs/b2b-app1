@extends('admin.layouts.app')
@section('title', 'Edit Plan | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Plan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST" id="planForm">
                    @csrf
                    @method('PUT')
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $plan->name }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $plan->price }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Plan For <span class="text-danger">*</span></label>
                            <select name="plan_for" id="plan_for" class="form-select">
                                <option value="vendor" {{ $plan->plan_for == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                <option value="buyer" {{ $plan->plan_for == 'buyer' ? 'selected' : '' }}>Buyer</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select">
                                <option value="active" {{ $plan->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $plan->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    function validateForm(){
        let ok = true;
        $('#planForm').find('input, select').each(function(){
            if(!$(this).val()){
                $(this).addClass('is-invalid');
                ok = false;
            }else{
                $(this).removeClass('is-invalid');
            }
        });
        return ok;
    }
    $('#planForm').on('submit', function(e){
        e.preventDefault();
        if(!validateForm()){
            toastr.error('Please fix the validation errors.');
            return;
        }
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            beforeSend: function(){
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
            },
            success: function(res){
                if(res.status){
                    toastr.success(res.message);
                    setTimeout(function(){ window.location.href = res.redirect; }, 1000);
                }else{
                    toastr.error(res.message || 'An error occurred');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    $.each(xhr.responseJSON.errors, function(k,v){
                        $('[name="'+k+'"]').addClass('is-invalid');
                        toastr.error(v[0]);
                    });
                }else{
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete: function(){
                $btn.prop('disabled', false).html('Update');
            }
        });
    });
});
</script>
@endsection

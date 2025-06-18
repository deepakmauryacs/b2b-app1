@extends('admin.layouts.app')
@section('title', 'Add Plan | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Add Plan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.plans.store') }}" method="POST" id="planForm">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Plan For <span class="text-danger">*</span></label>
                            <select name="plan_for" id="plan_for" class="form-select">
                                <option value="vendor">Vendor</option>
                                <option value="buyer">Buyer</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
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
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
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
                $btn.prop('disabled', false).html('Save');
            }
        });
    });
});
</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Add Subscription | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Add Subscription</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.vendor-subscriptions.store') }}" method="POST" id="subscriptionForm">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select select2" style="width:100%" required>
                                <option value="">Search Vendor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <select name="plan_name" id="plan_name" class="form-select" required>
                                <option value="">Select Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan }}">{{ $plan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration <span class="text-danger">*</span></label>
                            <select name="duration" id="duration" class="form-select" required>
                                @for($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ $i }} Month{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('admin.vendor-subscriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    function validateForm(){
        let ok = true;
        $('#subscriptionForm').find('select, input').each(function(){
            if(!$(this).val()){
                $(this).addClass('is-invalid');
                ok = false;
            }else{
                $(this).removeClass('is-invalid');
            }
        });
        return ok;
    }
    $('#subscriptionForm').on('submit', function(e){
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

    // Initialize Select2 for vendor search
    $('#user_id').select2({
        placeholder: 'Search Vendor',
        allowClear: true,
        ajax: {
            url: "{{ route('admin.vendors.search') }}",
            dataType: 'json',
            delay: 250,
            data: function(params){
                return {
                    q: params.term
                };
            },
            processResults: function(data){
                return {
                    results: data.map(function(item){
                        return { id: item.id, text: item.name };
                    })
                };
            },
            cache: true
        }
    });
});
</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Add Banner | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Add Banner</h4>
                <a href="{{ route('admin.banners.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">&larr; Back to List</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banners.store') }}" method="POST" id="bannerForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <label class="form-label">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" name="banner_img" id="banner_img" class="form-control" accept="image/*">
                            <small class="text-muted">Max file size: 2MB</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Link</label>
                            <input type="url" name="banner_link" id="banner_link" class="form-control" placeholder="https://example.com">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Start Date</label>
                            <input type="text" name="banner_start_date" id="banner_start_date" class="form-control date-picker" placeholder="dd-mm-yyyy">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">End Date</label>
                            <input type="text" name="banner_end_date" id="banner_end_date" class="form-control date-picker" placeholder="dd-mm-yyyy">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Banner Type</label>
                            <select name="banner_type" id="banner_type" class="form-select">
                                <option value="1" selected>Home Slider</option>
                            </select>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">Save Banner</button>
                        </div>
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
        $('.is-invalid').removeClass('is-invalid');
        const dateRegex = /^\d{2}-\d{2}-\d{4}$/;
        const fileInput = $('#banner_img')[0];
        if(fileInput.files.length === 0){
            $('#banner_img').addClass('is-invalid');
            ok = false;
        }else{
            const file = fileInput.files[0];
            const validTypes = ['image/jpeg','image/png','image/jpg','image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            if(!validTypes.includes(file.type) || file.size > maxSize){
                $('#banner_img').addClass('is-invalid');
                ok = false;
            }
        }
        if($('#banner_start_date').val() && !dateRegex.test($('#banner_start_date').val())){
            $('#banner_start_date').addClass('is-invalid');
            ok = false;
        }
        if($('#banner_end_date').val() && !dateRegex.test($('#banner_end_date').val())){
            $('#banner_end_date').addClass('is-invalid');
            ok = false;
        }
        return ok;
    }
    $('#bannerForm').on('submit', function(e){
        e.preventDefault();
        if(!validateForm()){
            toastr.error('Please fix the validation errors.');
            return;
        }
        var form = $(this);
        var formData = new FormData(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData:false,
            contentType:false,
            beforeSend:function(){
                form.find('button[type="submit"]').prop('disabled',true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
            },
            success:function(res){
                if(res.status){
                    toastr.success(res.message);
                    setTimeout(()=>{ window.location.href = res.redirect; },1000);
                }else{
                    toastr.error(res.message || 'An error occurred');
                }
            },
            error:function(xhr){
                if(xhr.status===422){
                    $.each(xhr.responseJSON.errors,function(k,v){
                        $('[name="'+k+'"]').addClass('is-invalid');
                        toastr.error(v[0]);
                    });
                }else{
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete:function(){
                form.find('button[type="submit"]').prop('disabled',false).html('Save Banner');
            }
        });
    });
});
</script>
@endsection

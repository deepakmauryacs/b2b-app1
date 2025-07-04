@extends('admin.layouts.app')
@section('title', 'Vendor Export | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">New Vendor Export</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.vendor-exports.store') }}" id="exportForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Range Start</label>
                        <input type="number" name="range_start" id="range_start" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Range End</label>
                        <input type="number" name="range_end" id="range_end" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Start Export</button>
                    <a href="{{ route('admin.vendor-exports.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function validateForm(){
        let valid = true;
        $('#exportForm .is-invalid').removeClass('is-invalid');
        const start = $('#range_start').val();
        const end = $('#range_end').val();
        if(!start || isNaN(start)){
            $('#range_start').addClass('is-invalid');
            valid = false;
        }
        if(!end || isNaN(end) || parseInt(end) <= parseInt(start)){
            $('#range_end').addClass('is-invalid');
            valid = false;
        }
        return valid;
    }

    $('#exportForm').on('submit', function(e){
        e.preventDefault();
        if(!validateForm()){
            toastr.error('Please fix the validation errors.');
            return;
        }

        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function(){
                form.find('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Starting...');
            },
            success: function(res){
                if(res.status){
                    toastr.success(res.message);
                    setTimeout(function(){ window.location.href = res.redirect; }, 1000);
                }else{
                    toastr.error(res.message || 'An error occurred.');
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    $.each(xhr.responseJSON.errors, function(k,v){
                        form.find('[name="'+k+'"]').addClass('is-invalid');
                        toastr.error(v[0]);
                    });
                }else{
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete: function(){
                form.find('button[type="submit"]').prop('disabled', false).html('Start Export');
            }
        });
    });
});
</script>
@endpush

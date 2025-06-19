@extends('vendor.layouts.app')
@section('title', 'Add Help & Support | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Add Help & Support</h4>
                <a href="{{ route('vendor.help-support.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">&larr; Back to List</a>
            </div>
            <div class="card-body">
                <form id="helpForm" action="{{ route('vendor.help-support.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" placeholder="Your Name">
                        </div>
                        <div class="col-md-6">
                            <label for="contact_no" class="form-label">Contact No <span class="text-danger">*</span></label>
                            <input type="text" id="contact_no" name="contact_no" class="form-control" value="{{ old('contact_no') }}" placeholder="Contact Number">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" placeholder="Email">
                        </div>
                        <div class="col-md-6">
                            <label for="attachment" class="form-label">Attachment</label>
                            <input type="file" id="attachment" name="attachment" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="4" placeholder="Message">{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    const $form = $('#helpForm');
    const $btn = $form.find('button[type="submit"]');

    function showError($input, msg){
        $input.addClass('is-invalid');
        $input.next('.invalid-feedback').remove();
        $input.after(`<div class="invalid-feedback d-block">${msg}</div>`);
        toastr.error(msg);
    }

    function clearError($input){
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').remove();
    }

    function validateField($input, rules){
        let ok = true;
        const val = $input.val().trim();
        for(const rule of rules){
            if(rule.condition(val, $input)){
                showError($input, rule.message);
                ok = false;
                break;
            }
        }
        if(ok) clearError($input);
        return ok;
    }

    const rules = {
        name: [{condition:v=>!v,message:'Name is required.'}],
        contact_no: [{condition:v=>!v,message:'Contact number is required.'}],
        email: [
            {condition:v=>!v,message:'Email is required.'},
            {condition:v=>!/^([^\s@]+@[^\s@]+\.[^\s@]+)$/.test(v),message:'Enter valid email.'}
        ],
        message:[{condition:v=>!v,message:'Message is required.'}],
        attachment:[{
            condition:(v,$i)=>{if(!v) return false;const ext=v.split('.').pop().toLowerCase();return ['jpg','jpeg','png','pdf','doc','docx'].indexOf(ext)===-1;},
            message:'Invalid file type.'
        }]
    };

    $form.on('submit', function(e){
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        let valid = true;
        $.each(rules, function(field, r){
            const $input = $('#' + field);
            if(!validateField($input, r)) valid = false;
        });
        if(!valid) return;
        const formData = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(){ $btn.prop('disabled', true); },
            success: function(res){
                toastr.success(res.message || 'Submitted successfully');
                if(res.redirect){ window.location.href = res.redirect; }
            },
            error: function(xhr){
                if(xhr.status===422){
                    $.each(xhr.responseJSON.errors, function(k,v){ showError($('#'+k), v[0]); });
                }else{
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete:function(){ $btn.prop('disabled', false); }
        });
    });
});
</script>
@endsection

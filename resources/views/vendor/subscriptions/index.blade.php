@extends('vendor.layouts.app')
@section('title', 'Subscription | Deal24hours')
@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Subscription</h4>
                @if(isset($subscription))
                    <a href="{{ route('vendor.subscription.invoice') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-printer"></i> View Invoice
                    </a>
                @endif
            </div>
            <div class="card-body">
                <form id="subscriptionForm" action="{{ route('vendor.subscription.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6 col-lg-4">
                            <label for="plan_name" class="form-label">Plan Name</label>
                            <select id="plan_name" name="plan_name" class="form-select">
                                <option value="">Select Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan }}" {{ ($subscription->plan_name ?? '') == $plan ? 'selected' : '' }}>{{ $plan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6 col-lg-4">
                            <label for="duration" class="form-label">Duration</label>
                            <select id="duration" name="duration" class="form-select">
                                @for($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}" {{ ($duration ?? 1) == $i ? 'selected' : '' }}>{{ $i }} Month{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Save Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    const $form = $('#subscriptionForm');
    const $submitBtn = $form.find('button[type="submit"]');

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    };

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
            if(rule.condition(val)){
                showError($input, rule.message);
                ok = false;
                break;
            }
        }
        if(ok) clearError($input);
        return ok;
    }

    const rules = {
        plan_name: [
            {condition: v => !v, message: 'Plan name is required.'}
        ],
        duration: [
            {condition: v => !v, message: 'Duration is required.'}
        ]
    };

    $form.on('submit', function(e){
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let valid = true;
        $.each(rules, function(field, r){
            const $inp = $('#' + field);
            if(!validateField($inp, r)) valid = false;
        });
        if(!valid) return;

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            beforeSend: function(){
                $submitBtn.prop('disabled', true);
            },
            success: function(res){
                toastr.success(res.message || 'Saved successfully');
                if(res.reload){
                    setTimeout(()=> window.location.reload(), 1000);
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    $.each(xhr.responseJSON.errors, function(k, v){
                        const $in = $('[name="'+k+'"]');
                        showError($in, v[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete: function(){
                $submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection

@extends('vendor.layouts.app')
@section('title', 'Change Password | Deal24hours')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:lock-password-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    Change Password
                </h4>
            </div>
            <div class="card-body">
                <form id="changePasswordForm" method="POST" action="{{ route('vendor.password.update') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
    const $form = $('#changePasswordForm');
    const $submitBtn = $form.find('button[type="submit"]');

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    };

    function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        $input.next('.invalid-feedback').remove();
        $input.after(`<div class="invalid-feedback d-block">${message}</div>`);
        toastr.error(message);
    }

    function clearError(input) {
        const $input = $(input);
        $input.removeClass('is-invalid');
        $input.next('.invalid-feedback').remove();
    }

    function validateField($input, rules) {
        let isValid = true;
        const value = $input.val() ? $input.val().trim() : '';
        for (const rule of rules) {
            if (rule.condition(value)) {
                showError($input, rule.message);
                isValid = false;
                break;
            }
        }
        if (isValid) clearError($input);
        return isValid;
    }

    const validationRules = {
        current_password: [
            { condition: val => !val, message: 'Current password is required.' }
        ],
        password: [
            { condition: val => !val, message: 'New password is required.' },
            { condition: val => val.length < 8, message: 'Password must be at least 8 characters.' }
        ],
        password_confirmation: [
            { condition: val => !val, message: 'Confirm password is required.' },
            { condition: val => val !== $('#password').val(), message: 'Passwords do not match.' }
        ]
    };

    $form.on('submit', function(e) {
        e.preventDefault();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        let isValid = true;
        for (const field in validationRules) {
            const $input = $(`#${field}`);
            if (!validateField($input, validationRules[field])) {
                isValid = false;
            }
        }

        if (isValid) {
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                beforeSend: function() { $submitBtn.prop('disabled', true); },
                success: function(response) {
                    toastr.success(response.message || 'Password updated successfully!');
                    if (response.reload) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            showError($(`#${key}`), messages[0]);
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                    }
                },
                complete: function() { $submitBtn.prop('disabled', false); }
            });
        }
    });
});
</script>
@endsection

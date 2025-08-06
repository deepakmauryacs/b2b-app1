@extends('admin.layouts.app')
@section('title', 'Edit User Account | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit User Account</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.user-accounts.update', $account->id) }}" method="POST" id="accountForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $account->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $account->phone }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <input type="text" name="user_type" class="form-control" value="{{ $account->user_type }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <input type="text" name="gender" class="form-control" value="{{ $account->gender }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">GST No</label>
                            <input type="text" name="gst_no" class="form-control" value="{{ $account->gst_no }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OTP</label>
                            <input type="text" name="otp" class="form-control" value="{{ $account->otp }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ $account->latitude }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ $account->longitude }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="1" {{ $account->status == '1' ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ $account->status == '2' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referral Code</label>
                            <input type="text" name="referral_code" class="form-control" value="{{ $account->referral_code }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referral By</label>
                            <input type="text" name="referral_by" class="form-control" value="{{ $account->referral_by }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Verified</label>
                            <select name="is_verified" class="form-select" required>
                                <option value="1" {{ $account->is_verified == '1' ? 'selected' : '' }}>Verified</option>
                                <option value="2" {{ $account->is_verified == '2' ? 'selected' : '' }}>Not Verified</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product and Services</label>
                            <textarea name="product_and_services" class="form-control" rows="3">{{ $account->product_and_services }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parent ID</label>
                            <input type="number" name="parent_id" class="form-control" value="{{ $account->parent_id }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.user-accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function validateForm() {
                let isValid = true;
                $('#accountForm .error-message').remove();
                $('#accountForm input[required], #accountForm select[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="text-danger error-message">This field is required</span>');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return isValid;
            }
            $('#accountForm').on('submit', function(e) {
                e.preventDefault();
                if (!validateForm()) {
                    toastr.error('Please fix the validation errors.');
                    return false;
                }
                var formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = response.redirect || "{{ route('admin.user-accounts.index') }}";
                            }, 1000);
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).html('Update');
                    }
                });
            });
        });
    </script>
@endsection

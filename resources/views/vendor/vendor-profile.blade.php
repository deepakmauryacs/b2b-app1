@extends('vendor.layouts.app')
@section('title', 'Vendor Profile | Deal24hours')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title d-flex align-items-center gap-1">
                    <iconify-icon icon="solar:user-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    Vendor Profile
                </h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
               <form id="vendorProfileForm" action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Store Name -->
                        <div class="mb-3 col-md-6">
                            <label for="store_name" class="form-label">Company / Store Name</label>
                            <input type="text" id="store_name" name="store_name" class="form-control" value="{{ old('store_name', $vendor->store_name ?? '') }}" placeholder="Store Name">
                        </div>
                        <!-- Email -->
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $vendor->email ?? '') }}" placeholder="Email">
                        </div>
                        <!-- Phone -->
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $vendor->phone ?? '') }}" placeholder="Phone Number">
                        </div>
                       
                        
                        <!-- Country -->
                        <div class="mb-3 col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" id="country" name="country" class="form-control" value="{{ old('country', $vendor->country ?? '') }}" placeholder="Country">
                        </div>

                        <!-- State -->
                        <div class="mb-3 col-md-6">
                            <label for="state" class="form-label">State</label>
                            <input type="text" id="state" name="state" class="form-control" value="{{ old('state', $vendor->state ?? '') }}" placeholder="State">
                        </div>

                        <!-- City -->
                        <div class="mb-3 col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" id="city" name="city" class="form-control" value="{{ old('city', $vendor->city ?? '') }}" placeholder="City">
                        </div>
                        
                        <!-- Pincode -->
                        <div class="mb-3 col-md-6">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="form-control" value="{{ old('pincode', $vendor->pincode ?? '') }}" placeholder="Pincode">
                        </div>

                         <!-- Address -->
                        <div class="mb-3 col-md-6">
                            <label for="address" class="form-label">Store Address</label>
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $vendor->address ?? '') }}" placeholder="Store Address">
                        </div>

                        <!-- GST Number -->
                        <div class="mb-3 col-md-6">
                            <label for="gst_no" class="form-label">GST Number</label>
                            <input type="text" id="gst_no" name="gst_no" class="form-control" value="{{ old('gst_no', $vendor->gst_no ?? '') }}" placeholder="GST Number">
                        </div>
                        <!-- GST Document -->
                        <div class="mb-3 col-md-6">
                            <label for="gst_doc" class="form-label">GST Document  <small class="text-danger">(Maximum allowed file size 1MB, pdf, jpg, jpeg, png)</small></label>
                            <input type="file" id="gst_doc" name="gst_doc" class="form-control">
                            @if(!empty($vendor->gst_doc))
                                <div class="mt-2">
                                    <a href="{{ asset('/' . $vendor->gst_doc) }}" target="_blank">View GST Document</a>
                                </div>
                            @endif
                        </div>
                        <!-- Store Logo -->
                        <div class="mb-3 col-md-6">
                            <label for="store_logo" class="form-label">Company / Store Logo <small class="text-danger">(Maximum allowed file size 1MB, pdf, jpg, jpeg, png)</small></label>
                            <input type="file" id="store_logo" name="store_logo" class="form-control">
                            @if(!empty($vendor->store_logo))
                                <div class="mt-2">
                                    <img src="{{ asset('/' . $vendor->store_logo) }}" alt="Store Logo" style="height: 60px;">
                                </div>
                            @endif
                        </div>
                        <div class="mb-3 col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="accept_terms" name="accept_terms" value="1" {{ old('accept_terms', $vendor->accept_terms ?? 0) ? 'checked' : '' }}>

                                <label class="form-check-label" for="accept_terms">
                                    I accept the <a href="#" target="_blank">Terms and Conditions</a>
                                </label>
                            </div>
                        </div>

                    </div>
                   
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
    // Cache jQuery selectors
    const $form = $('#vendorProfileForm');
    const $submitBtn = $('button[type="submit"]');
    
    // Initialize toastr if not already done
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    };

    // Helper functions
    function showError(input, message) {
        const $input = $(input);
        $input.addClass('is-invalid');
        
        // Handle different input types (radio/checkbox vs others)
        if ($input.is(':checkbox') || $input.is(':radio')) {
            $input.closest('.form-check').find('.invalid-feedback').remove();
            $input.closest('.form-check').append(
                `<div class="invalid-feedback d-block">${message}</div>`
            );
        } else {
            $input.next('.invalid-feedback').remove();
            $input.after(`<div class="invalid-feedback d-block">${message}</div>`);
        }
        
        toastr.error(message);
    }

    function clearError(input) {
        const $input = $(input);
        $input.removeClass('is-invalid');
        
        if ($input.is(':checkbox') || $input.is(':radio')) {
            $input.closest('.form-check').find('.invalid-feedback').remove();
        } else {
            $input.next('.invalid-feedback').remove();
        }
    }

    function validateField($input, rules) {
        let isValid = true;
        const value = $input.val() ? $input.val().trim() : '';
        
        for (const rule of rules) {
            if (rule.condition(value, $input)) {
                showError($input, rule.message);
                isValid = false;
                break;
            }
        }
        
        if (isValid) {
            clearError($input);
        }
        
        return isValid;
    }

    // Validation rules
    const validationRules = {
        store_name: [
            {
                condition: val => !val,
                message: "Store name is required."
            }
        ],
        email: [
            {
                condition: val => !val,
                message: "Email is required."
            },
            {
                condition: val => !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
                message: "Enter a valid email."
            }
        ],
        phone: [
            {
                condition: val => !val,
                message: "Phone is required."
            },
            {
                condition: val => !/^\d{10,15}$/.test(val),
                message: "Phone must be 10-15 digits."
            }
        ],
        country: [
            {
                condition: val => !val,
                message: "Country is required."
            }
        ],
        state: [
            {
                condition: val => !val,
                message: "State is required."
            }
        ],
        city: [
            {
                condition: val => !val,
                message: "City is required."
            }
        ],
        pincode: [
            {
                condition: val => !val,
                message: "Pincode is required."
            },
            {
                condition: val => !/^\d{4,10}$/.test(val),
                message: "Pincode must be 4-10 digits."
            }
        ],
        address: [
            {
                condition: val => !val,
                message: "Store address is required."
            }
        ],
        gst_doc: [
            {
                condition: (val, $input) => {
                    if (!val) return false;
                    const allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                    const ext = val.split('.').pop().toLowerCase();
                    return $.inArray(ext, allowed) === -1;
                },
                message: "Allowed file types: pdf, jpg, jpeg, png."
            }
        ],
        store_logo: [
            {
                condition: (val, $input) => {
                    if (!val) return false;
                    const allowed = ['jpg', 'jpeg', 'png'];
                    const ext = val.split('.').pop().toLowerCase();
                    return $.inArray(ext, allowed) === -1;
                },
                message: "Allowed file types: jpg, jpeg, png."
            }
        ],
        accept_terms: [
            {
                condition: (val, $input) => !$input.is(':checked'),
                message: "You must accept the Terms and Conditions."
            }
        ]
    };

    // Form submission handler
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('.alert').remove();

        let isValid = true;

        // Validate all fields
        for (const field in validationRules) {
            const $input = $(`#${field}`);
            if (!$input.validateField || $input.is(':visible')) {
                if (!validateField($input, validationRules[field])) {
                    isValid = false;
                }
            }
        }

        // Focus first invalid field
        const $firstError = $('.is-invalid:visible').first();
        if ($firstError.length) {
            $firstError.focus();
        }

        if (isValid) {
            const formData = new FormData(this);
            
            $.ajax({
                url: $form.attr('action') || "{{ route('vendor.profile.update') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || 
                                   $('input[name="_token"]').val()
                },
                beforeSend: function() {
                    $submitBtn.prop('disabled', true);
                },
                success: function(response) {
                    toastr.success(response.message || 'Profile updated successfully!');
                    
                    // Reload after delay if needed
                    if (response.reload) {
                        setTimeout(() => window.location.reload(), 1000);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorList = '<ul class="mb-0">';
                        
                        $.each(errors, function (key, value) {
                            const $input = $(`[name="${key}"]`);
                            showError($input, value[0]);
                            errorList += `<li>${value[0]}</li>`;
                        });
                        
                        errorList += '</ul>';
                        $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' + errorList + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').insertBefore($form);
                    } else {
                        const errorMsg = xhr.responseJSON?.message || 
                                         'Something went wrong. Try again later.';
                        toastr.error(errorMsg);
                        $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' + errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>').insertBefore($form);
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                }
            });
        }
    });

    // Add validateField method to jQuery objects
    $.fn.validateField = function(rules) {
        return validateField(this, rules);
    };
});
</script>
@endsection



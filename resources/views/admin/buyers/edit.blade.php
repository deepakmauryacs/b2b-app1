@extends('admin.layouts.app')
@section('title', 'Edit Buyer | Deal24hours')
@section('content')
    <style>
        .error {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .file-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
        }

        .document-preview {
            max-width: 100%;
            max-height: 200px;
        }

        .file-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .section-title {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Edit Buyer</h4>
                    <a href="{{ route('admin.buyers.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.buyers.update', $buyer->id) }}" method="POST" id="buyerForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row gy-3">
                            <!-- Basic Information Section -->
                            <div class="col-12">
                                <h5 class="section-title">Basic Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter buyer name" value="{{ old('name', $buyer->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="Enter email" value="{{ old('email', $buyer->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    placeholder="Enter phone" value="{{ old('phone', $buyer->phone) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="1" {{ old('status', $buyer->status) == '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="2" {{ old('status', $buyer->status) == '2' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                            <!-- Store Details Section -->
                            <div class="col-12 mt-4">
                                <h5 class="section-title">Store Details</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="store_name" class="form-label">Store Name</label>
                                <input type="text" name="store_name" id="store_name" class="form-control"
                                    placeholder="Enter store name" value="{{ old('store_name', $buyer->store_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="gst_no" class="form-label">GST Number</label>
                                <input type="text" name="gst_no" id="gst_no" class="form-control"
                                    placeholder="Enter GST number" value="{{ old('gst_no', $buyer->gst_no) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" name="country" id="country" class="form-control"
                                    placeholder="Enter country" value="{{ old('country', $buyer->country) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="state" class="form-label">State</label>
                                <input type="text" name="state" id="state" class="form-control"
                                    placeholder="Enter state" value="{{ old('state', $buyer->state) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" name="city" id="city" class="form-control"
                                    placeholder="Enter city" value="{{ old('city', $buyer->city) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" name="pincode" id="pincode" class="form-control"
                                    placeholder="Enter pincode" value="{{ old('pincode', $buyer->pincode) }}">
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter full address">{{ old('address', $buyer->address) }}</textarea>
                            </div>

                            <!-- Documents Section -->
                            <div class="col-12 mt-4">
                                <h5 class="section-title">Documents</h5>
                            </div>

                            <!-- GST Document -->
                            <div class="col-md-6">
                                <label for="gst_doc" class="form-label">GST Document
                                    <small class="text-danger">(Maximum allowed file size 1MB, pdf, jpg, jpeg, png)</small>
                                </label>
                                <input type="file" id="gst_doc" name="gst_doc" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">

                                @if (!empty($buyer->gst_doc))
                                    <div class="file-actions" id="gstDocActions">
                                        <a href="{{ asset('/' . $buyer->gst_doc) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye-fill"></i> View Document
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteGstDoc()">
                                            <i class="bi bi-trash-fill"></i> Remove
                                        </button>
                                    </div>

                                    @if (pathinfo($buyer->gst_doc, PATHINFO_EXTENSION) !== 'pdf')
                                        <div id="gstDocPreview">
                                            <img src="{{ asset('/' . $buyer->gst_doc) }}" alt="GST Document"
                                                class="img-thumbnail document-preview mt-2">
                                        </div>
                                    @endif

                                    <input type="hidden" name="existing_gst_doc" value="{{ $buyer->gst_doc }}">
                                @endif
                                <div id="newGstDocPreview" class="mt-2 d-none"></div>
                            </div>

                            <!-- Store Logo -->
                            <div class="col-md-6">
                                <label for="store_logo" class="form-label">Company / Store Logo
                                    <small class="text-danger">(Maximum allowed file size 1MB, jpg, jpeg, png)</small>
                                </label>
                                <input type="file" id="store_logo" name="store_logo" class="form-control"
                                    accept=".jpg,.jpeg,.png">

                                @if (!empty($buyer->store_logo))
                                    <div class="file-actions" id="logoActions">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteLogo()">
                                            <i class="bi bi-trash-fill"></i> Remove
                                        </button>
                                    </div>
                                    <div id="logoPreview">
                                        <img src="{{ asset('/' . $buyer->store_logo) }}" alt="Store Logo"
                                            class="img-thumbnail mt-2" style="max-height: 150px;">
                                    </div>
                                    <input type="hidden" name="existing_store_logo" value="{{ $buyer->store_logo }}">
                                @endif
                                <div id="newLogoPreview" class="mt-2 d-none">
                                    <img id="logoPreviewImage" src="#" alt="Logo Preview" class="img-thumbnail"
                                        style="max-height: 150px; display: none;">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Buyer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000
            };

            // Custom validation function
            function validateForm() {
                let isValid = true;

                // Reset previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.error-message').remove();

                // Validate name
                const name = $('#name').val();
                if (!name || name.length < 3) {
                    $('#name').addClass('is-invalid');
                    $('#name').after(
                        '<span class="error error-message">Name must be at least 3 characters long</span>');
                    toastr.error('Name must be at least 3 characters long');
                    isValid = false;
                }

                // Validate email
                const email = $('#email').val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    $('#email').addClass('is-invalid');
                    $('#email').after('<span class="error error-message">Email is required</span>');
                    toastr.error('Email is required');
                    isValid = false;
                } else if (!emailRegex.test(email)) {
                    $('#email').addClass('is-invalid');
                    $('#email').after(
                    '<span class="error error-message">Please enter a valid email address</span>');
                    toastr.error('Please enter a valid email address');
                    isValid = false;
                }

                // Validate phone
                const phone = $('#phone').val();
                if (!phone) {
                    $('#phone').addClass('is-invalid');
                    $('#phone').after('<span class="error error-message">Phone number is required</span>');
                    toastr.error('Phone number is required');
                    isValid = false;
                }

                return isValid;
            }

            // AJAX form submission
            $('#buyerForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    toastr.error("Please fix the validation errors.");
                    return false;
                }

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
                            );
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = response.redirect ||
                                    "{{ route('admin.buyers.index') }}";
                            }, 1000);
                        } else {
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON) {
                            if (Array.isArray(xhr.responseJSON.message)) {
                                $.each(xhr.responseJSON.message, function(index, error) {
                                    toastr.error(error);
                                });
                            } else if (xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            } else if (xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    toastr.error(value[0]);
                                });
                            }
                        }
                        if (!xhr.responseJSON || !xhr.responseJSON.message) {
                            toastr.error('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).html(
                            '<i class="bi bi-save"></i> Update Buyer');
                    }
                });
            });

            // Preview GST Document before upload
            document.getElementById('gst_doc').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewDiv = document.getElementById('newGstDocPreview');

                if (file) {
                    previewDiv.classList.remove('d-none');

                    if (file.type === 'application/pdf') {
                        previewDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-file-earmark-pdf-fill"></i> ${file.name} (PDF file)
                    </div>
                `;
                    } else if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewDiv.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail document-preview">
                        <div class="mt-1">${file.name}</div>
                    `;
                        }
                        reader.readAsDataURL(file);
                    }

                    // Hide existing document preview and actions if any
                    const existingPreview = document.getElementById('gstDocPreview');
                    const existingActions = document.getElementById('gstDocActions');
                    if (existingPreview) existingPreview.classList.add('d-none');
                    if (existingActions) existingActions.classList.add('d-none');
                }
            });

            // Preview Store Logo before upload
            document.getElementById('store_logo').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewDiv = document.getElementById('newLogoPreview');
                const previewImage = document.getElementById('logoPreviewImage');

                if (file) {
                    previewDiv.classList.remove('d-none');

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        previewDiv.innerHTML = '';
                        previewDiv.appendChild(previewImage);
                        previewDiv.innerHTML += `<div class="mt-1">${file.name}</div>`;
                    }
                    reader.readAsDataURL(file);

                    // Hide existing logo preview and actions if any
                    const existingPreview = document.getElementById('logoPreview');
                    const existingActions = document.getElementById('logoActions');
                    if (existingPreview) existingPreview.classList.add('d-none');
                    if (existingActions) existingActions.classList.add('d-none');
                }
            });
        });

        function confirmDeleteGstDoc() {
            if (confirm('Are you sure you want to remove the GST document?')) {
                // Create a hidden input to indicate deletion
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_gst_doc';
                input.value = '1';
                document.getElementById('buyerForm').appendChild(input);

                // Hide all related elements
                document.getElementById('gstDocPreview').classList.add('d-none');
                document.getElementById('gstDocActions').classList.add('d-none');
                document.querySelector('[name="existing_gst_doc"]').value = '';
            }
        }

        function confirmDeleteLogo() {
            if (confirm('Are you sure you want to remove the store logo?')) {
                // Create a hidden input to indicate deletion
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_store_logo';
                input.value = '1';
                document.getElementById('buyerForm').appendChild(input);

                // Hide all related elements
                document.getElementById('logoPreview').classList.add('d-none');
                document.getElementById('logoActions').classList.add('d-none');
                document.querySelector('[name="existing_store_logo"]').value = '';
            }
        }
    </script>
@endsection

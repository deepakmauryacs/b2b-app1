@extends('vendor.layouts.app')
@section('title', 'Edit Product | Deal24hours')
@section('content')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .note-editor.note-airframe .note-editing-area .note-editable,
    .note-editor.note-frame .note-editing-area .note-editable {
        background-color: #f9f7f7 !important;
    }
    .note-modal-footer {
        height: 50px !important;
    }
    .note-frame {
        font-family: 'DM Sans';
    }
    .note-editor .note-toolbar .note-color-all .note-dropdown-menu, .note-popover .popover-content .note-color-all .note-dropdown-menu {
        min-width: 160px !important;
    }
    .error {
        color: #dc3545 !important;
        font-size: 0.875em !important;
        margin-top: 0.25rem;
        display: block !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .current-image {
        max-width: 100px;
        margin-top: 10px;
    }
</style>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Edit Product</h4>
                <a href="{{ route('vendor.products.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">
                    ← Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf
                    @method('PUT')
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value=""> -- Select Category -- </option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            
                        </div>

                        {{-- Sub-category --}}
                        <div class="col-md-6">
                            <label for="sub_category_id" class="form-label">Sub-Category</label>
                            <select name="sub_category_id" id="sub_category_id" class="form-select">
                                <option value="">-- Select Sub-Category --</option>
                                @foreach($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}" {{ old('sub_category_id', $product->sub_category_id) == $subCategory->id ? 'selected' : '' }}>{{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                            
                        </div>

                        {{-- Product Name --}}
                        <div class="col-md-12">
                            <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter product name" value="{{ old('product_name', $product->product_name) }}" required />
                           
                        </div>

                        {{-- Slug (optional) --}}
                        <div class="col-md-12">
                            <label for="slug" class="form-label">Slug (optional)</label>
                            <input type="text" name="slug" id="slug" class="form-control" placeholder="Enter custom slug or leave blank" value="{{ old('slug', $product->slug) }}" />
                            
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control summernote" rows="4" placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                            
                        </div>

                        {{-- Price --}}
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="0.00" value="{{ old('price', $product->price) }}" required />
                            
                        </div>

                        {{-- Unit --}}
                        <div class="col-md-4">
                            <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" id="unit" class="form-control" placeholder="e.g., pcs, kg" value="{{ old('unit', $product->unit) }}" required />
                           
                        </div>

                        {{-- Minimum Order Quantity --}}
                        <div class="col-md-4">
                            <label for="min_order_qty" class="form-label">Min Order Qty <span class="text-danger">*</span></label>
                            <input type="number" name="min_order_qty" id="min_order_qty" class="form-control" placeholder="1" min="1" value="{{ old('min_order_qty', $product->min_order_qty) }}" required />
                            
                        </div>

                        {{-- Stock Quantity and Warehouse fields removed as per requirements --}}

                        {{-- HSN Code --}}
                        <div class="col-md-4">
                            <label for="hsn_code" class="form-label">HSN Code</label>
                            <input type="text" name="hsn_code" id="hsn_code" class="form-control" placeholder="Enter HSN code" value="{{ old('hsn_code', $product->hsn_code) }}" />
                           
                        </div>

                        {{-- GST Rate --}}
                        <div class="col-md-4">
                            <label for="gst_rate" class="form-label">GST Rate (%)</label>
                            <input type="number" name="gst_rate" id="gst_rate" class="form-control" placeholder="e.g., 5, 12, 18" min="0" max="100" value="{{ old('gst_rate', $product->gst_rate) }}" />
                            
                        </div>

                        {{-- Product Image --}}
                        <div class="col-md-6">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" name="product_image" id="product_image" class="form-control" accept="image/jpeg,image/png,image/jpg" />
                            <small class="text-muted">Max file size: 2MB (JPEG, JPG or PNG only)</small>
                            @if($product->image)
                                <div class="mt-2">
                                    <p>Current Image:</p>
                                    <img src="{{ Storage::url($product->image) }}" alt="Product Image" class="current-image">
                                </div>
                            @endif
                           
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 d-none"> <!-- d-none is Bootstrap class to hide -->
                            <input type="hidden" name="status" value="pending">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" selected>Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        {{-- Submit Button --}}
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Initialize Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000
    };

    // Initialize Summernote
    $("#description").summernote({
        height: 200,
        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["fontsize", ["fontsize"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["height", ["height"]],
            ["insert", ["link"]],
            ["view", ["fullscreen", "codeview", "help"]],
        ],
        placeholder: "Enter product description...",
    });

    // Generate slug from product name
    $("#product_name").on("keyup", function () {
        var text = $(this).val();
        var slug = text
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)+/g, "");
        $("#slug").val(slug);
    });

    // Custom validation function
    function validateForm() {
        console.log("Validating form...");
        let isValid = true;
        
        // Reset previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.error-message').remove();
        
        // Validate category
        if (!$('#category_id').val()) {
            $('#category_id').addClass('is-invalid');
            $('#category_id').

        after('<span class="error error-message">Please select a category</span>');
            toastr.error('Please select a category');
            isValid = false;
        }
        
        // Validate product name
        const productName = $('#product_name').val();
        if (!productName || productName.length < 3) {
            $('#product_name').addClass('is-invalid');
            $('#product_name').after('<span class="error error-message">Product name must be at least 3 characters long</span>');
            toastr.error('Product name must be at least 3 characters long');
            isValid = false;
        } else if (productName.length > 255) {
            $('#product_name').addClass('is-invalid');
            $('#product_name').after('<span class="error error-message">Product name cannot exceed 255 characters</span>');
            toastr.error('Product name cannot exceed 255 characters');
            isValid = false;
        }
        
        // Validate slug format if provided
        const slug = $('#slug').val();
        if (slug && !/^[a-z0-9-]+$/.test(slug)) {
            $('#slug').addClass('is-invalid');
            $('#slug').after('<span class="error error-message">Slug can only contain lowercase letters, numbers and hyphens</span>');
            toastr.error('Slug can only contain lowercase letters, numbers and hyphens');
            isValid = false;
        }
        
        // Validate price
        const price = parseFloat($('#price').val());
        if (isNaN(price)) {
            $('#price').addClass('is-invalid');
            $('#price').after('<span class="error error-message">Please enter a valid price</span>');
            toastr.error('Please enter a valid price');
            isValid = false;
        } else if (price <= 0) {
            $('#price').addClass('is-invalid');
            $('#price').after('<span class="error error-message">Price must be greater than 0</span>');
            toastr.error('Price must be greater than 0');
            isValid = false;
        }
        
        // Validate unit
        const unit = $('#unit').val().trim();
        if (!unit) {
            $('#unit').addClass('is-invalid');
            $('#unit').after('<span class="error error-message">Unit is required</span>');
            toastr.error('Unit is required');
            isValid = false;
        }
        
        // Validate min order quantity
        const minOrderQty = parseInt($('#min_order_qty').val());
        if (!$('#min_order_qty').val() || isNaN(minOrderQty)) {
            $('#min_order_qty').addClass('is-invalid');
            $('#min_order_qty').after('<span class="error error-message">Please enter a valid minimum order quantity</span>');
            toastr.error('Please enter a valid minimum order quantity');
            isValid = false;
        } else if (minOrderQty < 1) {
            $('#min_order_qty').addClass('is-invalid');
            $('#min_order_qty').after('<span class="error error-message">Minimum order quantity must be at least 1</span>');
            toastr.error('Minimum order quantity must be at least 1');
            isValid = false;
        }
        
        // Stock quantity and warehouse validation removed
        
        // Validate GST rate
        const gstRate = parseFloat($('#gst_rate').val());
        if ($('#gst_rate').val() && isNaN(gstRate)) {
            $('#gst_rate').addClass('is-invalid');
            $('#gst_rate').after('<span class="error error-message">Please enter a valid percentage</span>');
            toastr.error('Please enter a valid percentage for GST rate');
            isValid = false;
        } else if ($('#gst_rate').val() && (gstRate < 0 || gstRate > 100)) {
            $('#gst_rate').addClass('is-invalid');
            $('#gst_rate').after('<span class="error error-message">GST rate must be between 0 and 100</span>');
            toastr.error('GST rate must be between 0 and 100');
            isValid = false;
        }
        
        // Validate image file
        const fileInput = $('#product_image')[0];
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!validTypes.includes(file.type)) {
                $('#product_image').addClass('is-invalid');
                $('#product_image').after('<span class="error error-message">Only JPEG, JPG or PNG images are allowed</span>');
                toastr.error('Only JPEG, JPG or PNG images are allowed');
                isValid = false;
            } else if (file.size > maxSize) {
                $('#product_image').addClass('is-invalid');
                $('#product_image').after('<span class="error error-message">Image size must be less than 2MB</span>');
                toastr.error('Image size must be less than 2MB');
                isValid = false;
            }
        }
        
        return isValid;
    }

    // AJAX form submission
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
      
        if (!validateForm()) {
            toastr.error("Please fix the validation errors.");
            return false;
        }
        
        var formData = new FormData(this);
        
        // Add Summernote content to form data
        var description = $('#description').summernote('code');
        formData.append('description', description);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST', // Laravel handles PUT via _method field
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            },
            success: function(response) {
                if (response.status == 1) {
                    toastr.success(response.message);
                    // Reset form
                    $('#productForm').trigger('reset');
                    if (typeof $('#description').summernote === 'function') {
                        $('#description').summernote('reset');
                    }
                    // Redirect using the URL from the response
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 300);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.log("AJAX Error:", xhr.responseJSON);
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Update Product');
            }
        });
    });

    // Load subcategories when category changes
    $('#category_id').on('change', function () {
        let categoryId = $(this).val();
        $('#sub_category_id').html('<option value="">-- Select Sub-Category --</option>');

        if (categoryId) {
            let url = "{{ route('vendor.get-subcategories', ['parentId' => '___id___']) }}";
            url = url.replace('___id___', categoryId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $.each(data, function (key, value) {
                        $('#sub_category_id').append('<option value="' + value.id + '"' + (value.id == '{{ old('sub_category_id', $product->sub_category_id) }}' ? ' selected' : '') + '>' + value.name + '</option>');
                    });
                },
                error: function(xhr) {
                    toastr.error('Failed to load subcategories. Please try again.');
                }
            });
        }
    });

    // Trigger subcategory load on page load if category is pre-selected
    if ($('#category_id').val()) {
        $('#category_id').trigger('change');
    }
});
</script>
@endsection

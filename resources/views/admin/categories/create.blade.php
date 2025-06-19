@extends('admin.layouts.app')
@section('title', 'Add Category | Deal24hours')
@section('content')
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

        .note-editor .note-toolbar .note-color-all .note-dropdown-menu,
        .note-popover .popover-content .note-color-all .note-dropdown-menu {
            min-width: 160px !important;
        }

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
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Add New Category</h4>
                    <a href="{{ route('admin.categories.index') }}"
                        class="badge border border-secondary text-secondary px-2 py-1 fs-13">
                        ← Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST" id="categoryForm">
                        @csrf
                        <div class="row gy-3">
                            {{-- Category Name --}}
                            <div class="col-md-12">
                                <label for="name" class="form-label">Category Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter category name" value="{{ old('name') }}" />
                            </div>

                            {{-- Slug (optional) --}}
                            <div class="col-md-12">
                                <label for="slug" class="form-label">Slug (optional)</label>
                                <input type="text" name="slug" id="slug" class="form-control"
                                    placeholder="Enter custom slug or leave blank" value="{{ old('slug') }}" />
                            </div>

                            {{-- Parent Category --}}
                            <div class="col-md-12">
                                <label for="parent_id" class="form-label">Parent Category</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">Main Category (No Parent)</option>
                                    @foreach ($mainCategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-12">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Save Category
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

            // Generate slug from category name
            $("#name").on("keyup", function() {
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

                // Validate category name
                const name = $('#name').val();
                if (!name || name.length < 3) {
                    $('#name').addClass('is-invalid');
                    $('#name').after(
                        '<span class="error error-message">Category name must be at least 3 characters long</span>'
                        );
                    toastr.error('Category name must be at least 3 characters long');
                    isValid = false;
                } else if (name.length > 255) {
                    $('#name').addClass('is-invalid');
                    $('#name').after(
                        '<span class="error error-message">Category name cannot exceed 255 characters</span>');
                    toastr.error('Category name cannot exceed 255 characters');
                    isValid = false;
                }

                // Validate slug format if provided
                const slug = $('#slug').val();
                if (slug && !/^[a-z0-9-]+$/.test(slug)) {
                    $('#slug').addClass('is-invalid');
                    $('#slug').after(
                        '<span class="error error-message">Slug can only contain lowercase letters, numbers and hyphens</span>'
                        );
                    toastr.error('Slug can only contain lowercase letters, numbers and hyphens');
                    isValid = false;
                }

                // Validate parent category
                const parentId = $('#parent_id').val();
                if (parentId && isNaN(parentId)) {
                    $('#parent_id').addClass('is-invalid');
                    $('#parent_id').after(
                        '<span class="error error-message">Please select a valid parent category</span>');
                    toastr.error('Please select a valid parent category');
                    isValid = false;
                }

                return isValid;
            }

            // AJAX form submission
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    toastr.error("Please fix the validation errors.");
                    return false;
                }

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                            );
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            toastr.success(response.message);
                            // Reset form
                            $('#categoryForm')[0].reset();
                            // Redirect using the URL from response or fallback
                            setTimeout(function() {
                                window.location.href = response.redirect ||
                                    "{{ route('admin.categories.index') }}";
                            }, 1000);
                        } else {
                            // Handle non-validation errors
                            toastr.error(response.message || 'An error occurred');
                        }
                    },
                    error: function(xhr) {
                        console.log("AJAX Error:", xhr.responseJSON);
                        // Handle different error response formats
                        if (xhr.responseJSON) {
                            // Handle validation errors (array format)
                            if (Array.isArray(xhr.responseJSON.message)) {
                                $.each(xhr.responseJSON.message, function(index, error) {
                                    toastr.error(error);
                                });
                            }
                            // Handle single error message
                            else if (xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            }
                            // Handle Laravel validation error format
                            else if (xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    toastr.error(value[0]);
                                });
                            }
                        }
                        // Fallback error
                        if (!xhr.responseJSON || !xhr.responseJSON.message) {
                            toastr.error('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        $('button[type="submit"]').prop('disabled', false).html(
                        'Save Category');
                    }
                });
            });
        });
    </script>
@endsection

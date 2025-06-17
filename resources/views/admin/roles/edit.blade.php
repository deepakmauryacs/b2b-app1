@extends('admin.layouts.app')
@section('title', 'Edit Role | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Edit Role</h4>
                    <a href="{{ route('admin.roles.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">← Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="roleForm">
                        @csrf
                        @method('PUT')
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}" />
                            </div>
                            <div class="col-md-12">
                                <label for="parent_id" class="form-label">Parent Role</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}" @if($role->parent_id == $r->id) selected @endif>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Permissions</label>
                                <div class="mb-3">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                        <label class="form-check-label" for="select_all">Select All Permissions</label>
                                    </div>

                                    @php($rolePerms = $role->permissions->keyBy('module'))
                                    @php($actions = ['view' => 'Show', 'add' => 'Add', 'edit' => 'Edit', 'export' => 'Export'])
                                    @foreach ($modules as $module)
                                        @php($perm = $rolePerms->get($module))
                                        <div class="card mb-3 module-block" data-module="{{ $module }}">
                                            <div class="card-header fw-semibold text-capitalize">{{ $module }}</div>
                                            <div class="card-body d-flex flex-wrap gap-3">
                                                @foreach ($actions as $key => $label)
                                                    <div class="d-flex flex-column align-items-start border rounded p-3" style="min-width: 180px;">
                                                        <label class="form-check-label mb-2" for="{{ $module }}_{{ $key }}">
                                                            {{ $label }} {{ ucfirst($module) }}
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input
                                                                class="form-check-input perm-checkbox"
                                                                type="checkbox"
                                                                name="permissions[{{ $module }}][]"
                                                                value="{{ $key }}"
                                                                data-module="{{ $module }}"
                                                                id="{{ $module }}_{{ $key }}" @if($perm && $perm->{'can_'.$key}) checked @endif>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        $(function() {
            function updateGlobalSelect() {
                $('#select_all').prop('checked', $('.perm-checkbox:checked').length === $('.perm-checkbox').length);
            }

            $('#select_all').on('change', function() {
                const checked = $(this).prop('checked');
                $('.perm-checkbox').prop('checked', checked);
            });

            $('.perm-checkbox').on('change', function() {
                updateGlobalSelect();
            });

            updateGlobalSelect();

            $('#roleForm').validate({
                rules: {
                    name: 'required'
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
                        beforeSend: function() {
                            $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                toastr.success('Role updated successfully');
                                setTimeout(function() { window.location.href = "{{ route('admin.roles.index') }}"; }, 1000);
                            } else {
                                toastr.error('Failed to update role');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, val) { toastr.error(val[0]); });
                            } else {
                                toastr.error('An error occurred');
                            }
                        },
                        complete: function() {
                            $('button[type="submit"]').prop('disabled', false).html('Update Role');
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endsection

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
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle" id="permissionsTable">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Module</th>
                                                @php($actions = $actions ?? ['add','edit','view','export'])
                                                @foreach($actions as $action)
                                                    <th class="text-capitalize">{{ $action }}</th>
                                                @endforeach
                                                <th><input type="checkbox" id="checkAll"> All</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($rolePerms = $role->permissions->keyBy('module'))
                                            @foreach($modules as $module)
                                                @php($perm = $rolePerms->get($module))
                                                <tr>
                                                    <td class="text-capitalize">{{ $module }}</td>
                                                    @foreach($actions as $action)
                                                        <td class="text-center">
                                                            <input class="form-check-input action-checkbox" type="checkbox" name="permissions[{{ $module }}][]" value="{{ $action }}" id="{{ $module }}_{{ $action }}" @if($perm && $perm->{'can_'.$action}) checked @endif>
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center">
                                                        <input type="checkbox" class="module-check" data-module="{{ $module }}" @if($perm && $perm->can_add && $perm->can_edit && $perm->can_view && $perm->can_export) checked @endif>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#checkAll').on('change', function() {
                $('.action-checkbox, .module-check').prop('checked', this.checked);
            });

            $('.module-check').on('change', function() {
                const row = $(this).closest('tr');
                row.find('.action-checkbox').prop('checked', this.checked);
                updateCheckAll();
            });

            $('.action-checkbox').on('change', function() {
                const row = $(this).closest('tr');
                const allChecked = row.find('.action-checkbox').length === row.find('.action-checkbox:checked').length;
                row.find('.module-check').prop('checked', allChecked);
                updateCheckAll();
            });

            function updateCheckAll() {
                const total = $('.action-checkbox').length;
                const checked = $('.action-checkbox:checked').length;
                $('#checkAll').prop('checked', total === checked);
            }

            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                if (!$('#name').val().trim()) {
                    toastr.error('Role name is required');
                    return;
                }
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
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
            });
        });
    </script>
@endsection

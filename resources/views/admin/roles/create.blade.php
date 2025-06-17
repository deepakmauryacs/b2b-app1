@extends('admin.layouts.app')
@section('title', 'Add Role | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Add New Role</h4>
                    <a href="{{ route('admin.roles.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">← Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST" id="roleForm">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name" value="{{ old('name') }}" />
                            </div>
                            <div class="col-md-12">
                                <label for="parent_id" class="form-label">Parent Role</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Permissions</label>
                                <div class="row gy-2">
                                    @php($actions = $actions ?? ['add','edit','view','export'])
                                    @foreach ($modules as $module)
                                        <div class="col-12">
                                            <strong class="d-block mb-1 text-capitalize">{{ $module }}</strong>
                                            @foreach ($actions as $action)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="permissions[{{ $module }}][]" value="{{ $action }}" id="{{ $module }}_{{ $action }}">
                                                    <label class="form-check-label" for="{{ $module }}_{{ $action }}">{{ ucfirst($action) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Save Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            toastr.success('Role created successfully');
                            setTimeout(function() { window.location.href = "{{ route('admin.roles.index') }}"; }, 1000);
                        } else {
                            toastr.error('Failed to create role');
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
                        $('button[type="submit"]').prop('disabled', false).html('Save Role');
                    }
                });
            });
        });
    </script>
@endsection

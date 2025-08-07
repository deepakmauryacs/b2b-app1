@extends('admin.layouts.app')
@section('title', 'Role Permissions | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Permissions - {{ $role->name }}</h4>
                    <a href="{{ route('admin.roles.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">← Back to List</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST" id="permissionForm">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                        <label class="form-check-label" for="select_all">Select All Permissions</label>
                                    </div>
                                    @php($rolePerms = $role->permissions->keyBy('module_id'))
                                    @foreach ($modules as $module)
                                        @php($perm = $rolePerms->get($module->id))
                                        <div class="card mb-3 module-block" data-module="{{ $module->id }}">
                                            <div class="card-header fw-semibold text-capitalize">{{ $module->name }}</div>
                                            <div class="card-body d-flex flex-wrap gap-3">
                                                @foreach ($actions as $key => $label)
                                                    <div class="d-flex flex-column align-items-start border rounded p-3" style="min-width: 180px;">
                                                        <label class="form-check-label mb-2" for="{{ $module->id }}_{{ $key }}">{{ $label }} {{ ucfirst($module->name) }}</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[{{ $module->id }}][]" value="{{ $key }}" data-module="{{ $module->id }}" id="{{ $module->id }}_{{ $key }}" @if($perm && $perm->{'can_'.$key}) checked @endif>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        $(function(){
            function updateGlobalSelect() {
                $('#select_all').prop('checked', $('.perm-checkbox:checked').length === $('.perm-checkbox').length);
            }

            $('#select_all').on('change', function(){
                const checked = $(this).prop('checked');
                $('.perm-checkbox').prop('checked', checked);
            });

            $('.perm-checkbox').on('change', function(){
                updateGlobalSelect();
            });

            updateGlobalSelect();

            $('#permissionForm').validate({
                submitHandler: function(form){
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
                        beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                        },
                        success: function(res){
                            if(res.status === 'success') {
                                toastr.success('Permissions updated successfully');
                                setTimeout(function(){ window.location.href = "{{ route('admin.roles.index') }}"; }, 1000);
                            } else {
                                toastr.error('Failed to update permissions');
                            }
                        },
                        error: function(xhr){
                            if(xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, val){ toastr.error(val[0]); });
                            } else {
                                toastr.error('An error occurred');
                            }
                        },
                        complete: function(){
                            $('button[type="submit"]').prop('disabled', false).html('Save Permissions');
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endsection

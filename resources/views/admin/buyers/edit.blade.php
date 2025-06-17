@extends('admin.layouts.app')
@section('title', 'Edit Buyer | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                    <h4 class="card-title flex-grow-1">Edit Buyer</h4>
                    <a href="{{ route('admin.buyers.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.buyers.update', $buyer->id) }}" method="POST" id="buyerForm">
                        @csrf
                        @method('PUT')
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter buyer name" value="{{ old('name', $buyer->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" value="{{ old('email', $buyer->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone" value="{{ old('phone', $buyer->phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <small>(leave blank to keep unchanged)</small></label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password">
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="1" {{ old('status', $buyer->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $buyer->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

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
@endsection

@extends('admin.layouts.app')
@section('title', 'User Accounts | Deal24hours')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">User Accounts</h4>
                    <a href="{{ route('admin.user-accounts.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Account
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>User Type</th>
                                <th>Status</th>
                                <th>Verified</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts as $index => $account)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $account->name }}</td>
                                    <td>{{ $account->email }}</td>
                                    <td>{{ $account->phone }}</td>
                                    <td>{{ $account->user_type }}</td>
                                    <td>{{ $account->status == '1' ? 'Active' : 'Inactive' }}</td>
                                    <td>{{ $account->is_verified == '1' ? 'Verified' : 'Not Verified' }}</td>
                                    <td>{{ $account->created_at ? $account->created_at->format('d-m-Y') : '' }}</td>
                                    <td>
                                        <a href="{{ route('admin.user-accounts.edit', $account->id) }}" class="btn btn-sm btn-soft-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No user accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

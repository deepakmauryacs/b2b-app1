@extends('admin.layouts.app')
@section('title', 'Vendor Subscriptions | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1 mb-0">Vendor Subscriptions</h4>
                <a href="{{ route('admin.vendor-subscriptions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Add Subscription
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Plan</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subscription->user->name }}</td>
                                <td>{{ $subscription->plan_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge {{ $subscription->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.vendor-subscriptions.show', $subscription->id) }}" class="btn btn-secondary btn-sm" title="View">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <a href="{{ route('admin.vendor-subscriptions.print', $subscription->id) }}" class="btn btn-info btn-sm" title="Print" target="_blank">
                                        <iconify-icon icon="solar:printer-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                    <a href="{{ route('admin.vendor-subscriptions.edit', $subscription->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

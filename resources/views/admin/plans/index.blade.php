@extends('admin.layouts.app')
@section('title', 'Plans | Deal24hours')
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1 mb-0">Plans</h4>
                <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Add Plan
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>For</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $plan->name }}</td>
                                <td>{{ $plan->price }}</td>
                                <td>{{ ucfirst($plan->plan_for) }}</td>
                                <td>
                                    <span class="badge {{ $plan->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-soft-primary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $plans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

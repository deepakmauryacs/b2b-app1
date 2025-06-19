@extends('vendor.layouts.app')
@section('title', 'Subscription Invoice | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Subscription Invoice</h4>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <th>Plan</th>
                            <td>{{ $subscription->plan_name }}</td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ ucfirst($subscription->status) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

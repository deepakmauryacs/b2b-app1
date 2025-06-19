@extends('vendor.layouts.app')
@section('title', 'View Help & Support | Deal24hours')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Help & Support Details</h4>
                <a href="{{ route('vendor.help-support.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">&larr; Back to List</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <td>{{ $help->name }}</td>
                    </tr>
                    <tr>
                        <th>Contact No</th>
                        <td>{{ $help->contact_no }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $help->email }}</td>
                    </tr>
                    <tr>
                        <th>Message</th>
                        <td>{{ $help->message }}</td>
                    </tr>
                    @if($help->attachment)
                    <tr>
                        <th>Attachment</th>
                        <td><a href="{{ asset('storage/'.$help->attachment) }}" target="_blank">View</a></td>
                    </tr>
                    @endif
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst(str_replace('_',' ',$help->status)) }}</td>
                    </tr>
                    @if($help->reply_message)
                    <tr>
                        <th>Reply</th>
                        <td>{{ $help->reply_message }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created At</th>
                        <td>{{ \Carbon\Carbon::parse($help->created_at)->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

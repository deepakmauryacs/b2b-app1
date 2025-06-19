@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">Activity Log Details</h4>
                <a href="{{ route('admin.activity-logs.index') }}" class="badge border border-secondary text-secondary px-2 py-1 fs-13">
                    ← Back to List
                </a>
            </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5>Basic Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:30%;">ID</th>
                            <td>{{ $activity->id }}</td>
                        </tr>
                        <tr>
                            <th style="width:30%;">Description</th>
                            <td>{{ $activity->description }}</td>
                        </tr>
                        <tr>
                            <th style="width:30%;">Performed By</th>
                            <td>
                                @if($activity->causer)
                                    {{ $activity->causer->name }} (ID: {{ $activity->causer->id }})
                                @else
                                    System
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="width:30%;">Date/Time</th>
                            <td>{{ $activity->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-12">
                    <h5>Changes</h5>
                    @if($activity->changes->count())
                        <table class="table table-bordered">
                            @foreach($activity->changes['attributes'] as $key => $value)
                            <tr>
                                <th style="width:30%;">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>
                                    @if(is_array($value))
                                        <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    @else
                        <p>No changes recorded</p>
                    @endif
                </div>
            </div>
            
            @if($activity->properties->count())
            <div class="mt-4">
                <h5>Additional Properties</h5>
                <pre>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
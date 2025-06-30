<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="bg-light-subtle">
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Old Qty</th>
                <th>New Qty</th>
                <th>User</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                    <td>{{ $log->product->product_name }}</td>
                    <td>{{ $log->old_quantity }}</td>
                    <td>{{ $log->new_quantity }}</td>
                    <td>{{ $log->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <x-custom-pagination :paginator="$logs" />
            </tr>
        </tfoot>
    </table>
</div>

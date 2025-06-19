{{-- Partial view for buyer subscriptions table --}}
<tbody>
    @forelse($subscriptions as $subscription)
        <tr>
            <td>{{ ($subscriptions->currentPage() - 1) * $subscriptions->perPage() + $loop->iteration }}</td>
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
                <a href="{{ route('admin.buyer-subscriptions.show', $subscription->id) }}" class="btn btn-secondary btn-sm" title="View">
                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                </a>
                <a href="{{ route('admin.buyer-subscriptions.print', $subscription->id) }}" class="btn btn-info btn-sm" title="Print" target="_blank">
                    <iconify-icon icon="solar:printer-broken" class="align-middle fs-18"></iconify-icon>
                </a>
                <a href="{{ route('admin.buyer-subscriptions.edit', $subscription->id) }}" class="btn btn-soft-primary btn-sm" title="Edit">
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
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$subscriptions" />
    </tr>
</tfoot>

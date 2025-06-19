<tbody>
    @forelse($helps as $help)
        <tr>
            <td>{{ $loop->iteration + ($helps->currentPage() - 1) * $helps->perPage() }}</td>
            <td>{{ $help->name }}</td>
            <td>
                @php
                    $class = match($help->status){
                        'open' => 'badge border border-info text-info px-2 py-1 fs-13',
                        'pending' => 'badge border border-warning text-warning px-2 py-1 fs-13',
                        'on_hold' => 'badge border border-secondary text-secondary px-2 py-1 fs-13',
                        'solved' => 'badge border border-success text-success px-2 py-1 fs-13',
                        default => 'badge border border-dark text-dark px-2 py-1 fs-13'
                    };
                @endphp
                <span class="{{ $class }}">{{ ucfirst(str_replace('_',' ',$help->status)) }}</span>
            </td>
            <td>{{ \Carbon\Carbon::parse($help->created_at)->format('d-m-Y') }}</td>
            <td>
                <a href="{{ route('vendor.help-support.show', $help->id) }}" class="btn btn-sm btn-soft-info" title="View">
                    <i class="bi bi-eye"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center">No records found.</td></tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$helps" />
    </tr>
</tfoot>

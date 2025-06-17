{{-- This partial renders buyers table body and pagination --}}
<tbody>
    @forelse($buyers as $index => $buyer)
        <tr>
            <td>{{ ($buyers->currentPage() - 1) * $buyers->perPage() + $loop->iteration }}</td>
            <td>{{ htmlspecialchars($buyer->name) }}</td>
            <td>{{ htmlspecialchars($buyer->email) }}</td>
            <td>{{ htmlspecialchars($buyer->phone ?? 'N/A') }}</td>
            <td>
                @php
                    $status = $buyer->status ? 'active' : 'inactive';
                    $statusClass = $buyer->status ? 'badge border border-success text-success px-2 py-1 fs-13' : 'badge border border-danger text-danger px-2 py-1 fs-13';
                @endphp
                <span class="{{ $statusClass }}">{{ ucfirst($status) }}</span>
            </td>
            <td>{{ \Carbon\Carbon::parse($buyer->created_at)->format('d M Y') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.buyers.edit', $buyer->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-soft-danger delete-buyer" data-id="{{ $buyer->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">No buyers found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$buyers" />
    </tr>
</tfoot>

{{-- This partial renders users table body and pagination --}}
<tbody>
    @forelse($users as $index => $user)
        <tr>
            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
            <td>{{ htmlspecialchars($user->name) }}</td>
            <td>{{ htmlspecialchars($user->email) }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>
                @php
                    $status = $user->status ? 'active' : 'inactive';
                    $statusClass = $user->status ? 'badge border border-success text-success px-2 py-1 fs-13' : 'badge border border-danger text-danger px-2 py-1 fs-13';
                @endphp
                <span class="{{ $statusClass }}">{{ ucfirst($status) }}</span>
            </td>
            <td>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                    <iconify-icon icon="solar:pen-2-broken" class="fs-18"></iconify-icon>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No users found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$users" />
    </tr>
</tfoot>

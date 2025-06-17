{{-- This partial renders roles table body and pagination --}}
<tbody>
    @forelse($roles as $index => $role)
        <tr>
            <td>{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</td>
            <td>{{ htmlspecialchars($role->name) }}</td>
            <td>{{ $role->parent ? htmlspecialchars($role->parent->name) : '-' }}</td>
            <td>
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                    <iconify-icon icon="solar:pen-2-broken" class="fs-18"></iconify-icon>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center">No roles found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$roles" />
    </tr>
</tfoot>

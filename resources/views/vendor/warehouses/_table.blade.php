<tbody>
@forelse($warehouses as $warehouse)
<tr>
    <td>{{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + $loop->iteration }}</td>
    <td>{{ $warehouse->name }}</td>
    <td>{{ $warehouse->city }}</td>
    <td>{{ $warehouse->state }}</td>
    <td>{{ $warehouse->created_at->format('d-m-Y') }}</td>
    <td>
        <button class="btn btn-sm btn-warning edit-warehouse" data-id="{{ $warehouse->id }}" data-info='@json($warehouse->only(['name','address','city','state','pincode']))'>
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-danger delete-warehouse" data-id="{{ $warehouse->id }}">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center">No records found.</td>
</tr>
@endforelse
</tbody>
<tfoot>
<tr>
    <x-custom-pagination :paginator="$warehouses" />
</tr>
</tfoot>

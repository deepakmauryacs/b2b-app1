<tbody>
@forelse($products as $product)
<tr>
    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
    <td>{{ $product->product_name }}</td>
    <td><input type="number" class="form-control form-control-sm stock-input" value="{{ $product->stock_quantity }}" min="0"></td>
    <td>{{ \Carbon\Carbon::parse($product->updated_at)->format('d-m-Y') }}</td>
    <td>
        <button class="btn btn-sm btn-primary update-stock" data-id="{{ $product->id }}"><i class="bi bi-save"></i> Update</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">No records found.</td>
</tr>
@endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

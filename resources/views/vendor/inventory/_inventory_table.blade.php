<tbody>
@forelse($products as $product)
@php
    $diff = $product->latestStockLog ? $product->latestStockLog->new_quantity - $product->latestStockLog->old_quantity : 0;
@endphp
<tr>
    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
    <td>{{ $product->product_name }}</td>
    <td><input type="number" class="form-control form-control-sm stock-input" value="{{ $product->stock_quantity }}" min="0"></td>
    <td>{{ $diff > 0 ? $diff : '-' }}</td>
    <td>{{ $diff < 0 ? abs($diff) : '-' }}</td>
    <td>{{ \Carbon\Carbon::parse($product->updated_at)->format('d-m-Y') }}</td>
    <td>
        <button class="btn btn-sm btn-primary update-stock" data-id="{{ $product->id }}"><i class="bi bi-save"></i> Update</button>
        <button class="btn btn-sm btn-info view-stock-log" data-id="{{ $product->id }}"><i class="bi bi-clock-history"></i> Stock Log</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center">No records found.</td>
</tr>
@endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

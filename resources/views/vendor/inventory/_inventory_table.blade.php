<tbody>
@forelse($products as $product)
<tr>
    <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
    <td>{{ $product->product_name }}</td>
    <td>
        <select class="form-select form-select-sm warehouse-select" data-product-id="{{ $product->id }}">
            <option value="">Select</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}" @selected(isset($warehouseId) && $warehouseId == $w->id)>{{ $w->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="current-stock" data-default="{{ $product->stock_quantity }}">
        @if(isset($warehouseId) && $warehouseId)
            {{ $product->warehouseStocks->first()->quantity ?? 0 }}
        @else
            {{ $product->stock_quantity }}
        @endif
    </td>
    <td><input type="number" class="form-control form-control-sm stock-input-in" value="0" min="0"></td>
    <td><input type="number" class="form-control form-control-sm stock-input-out" value="0" min="0"></td>
    <td>{{ \Carbon\Carbon::parse($product->updated_at)->format('d-m-Y') }}</td>
    <td>
        <button class="btn btn-sm btn-primary update-stock" data-id="{{ $product->id }}"><i class="bi bi-save"></i> Update</button>
        <button class="btn btn-sm btn-info view-stock-log" data-id="{{ $product->id }}"><i class="bi bi-clock-history"></i> Stock Log</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center">No records found.</td>
</tr>
@endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

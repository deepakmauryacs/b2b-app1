<tbody>
    @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
            <td>{{ $product->product_name }}</td>
            <td>₹{{ number_format($product->price, 2) }}</td>
            <td>{{ $product->stock_quantity }}</td>
            <td>
                @php
                    if ($product->status === 'approved') {
                        $class = 'badge border border-success text-success px-2 py-1 fs-13';
                    } elseif ($product->status === 'pending') {
                        $class = 'badge border border-warning text-warning px-2 py-1 fs-13';
                    } else {
                        $class = 'badge border border-danger text-danger px-2 py-1 fs-13';
                    }
                @endphp
                <span class="{{ $class }}">{{ ucfirst($product->status) }}</span>
            </td>
            <td>{{ \Carbon\Carbon::parse($product->created_at)->format('d-m-Y') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendor.products.show', $product->id) }}" class="btn btn-sm btn-soft-info" title="View">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn btn-sm btn-soft-warning" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-soft-danger delete-product" data-id="{{ $product->id }}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">No products found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

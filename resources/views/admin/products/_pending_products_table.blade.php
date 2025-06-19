<tbody>
    @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->vendor->name ?? 'N/A' }}</td>
            <td>{{ $product->category->name ?? 'N/A' }}</td>
            <td>₹{{ number_format($product->price, 2) }}</td>
            <td>{{ $product->stock_quantity }}</td>
            <td>{{ $product->updated_at->format('d-m-Y') }}</td> {{-- Assuming approved_at is updated_at --}}
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.approved.show', $product->id) }}" class="btn btn-sm btn-soft-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button class="btn btn-sm btn-soft-warning revoke-approval" data-id="{{ $product->id }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">No pending products found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        {{-- Pass the $products paginator to your component --}}
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

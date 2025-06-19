<tbody>
    @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->vendor->name ?? 'N/A' }}</td>
            <td>{{ $product->category->name ?? 'N/A' }}</td>
            <td>₹{{ number_format($product->price, 2) }}</td>
            <td>{{ $product->stock_quantity }}</td>
            <td>
                @php
                    $statusClass = [
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                    ][$product->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($product->status) }}</span>
            </td>
            <td>{{ $product->updated_at->format('d-m-Y') }}</td>
            <td>
                <a href="{{ route('admin.products.approved.show', $product->id) }}" class="btn btn-sm btn-soft-info">
                    <i class="bi bi-eye"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center">No products found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$products" />
    </tr>
</tfoot>

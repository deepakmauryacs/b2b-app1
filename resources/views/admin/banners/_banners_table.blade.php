<tbody>
    @forelse($banners as $banner)
        <tr>
            <td>{{ $loop->iteration + ($banners->currentPage() - 1) * $banners->perPage() }}</td>
            <td>
                @if($banner->banner_img)
                    <img src="{{ asset('storage/'.$banner->banner_img) }}" alt="Banner" style="max-height:50px;">
                @else
                    N/A
                @endif
            </td>
            <td>{{ $banner->banner_link ?? '-' }}</td>
            <td>{{ $banner->banner_start_date ? \Carbon\Carbon::parse($banner->banner_start_date)->format('d-m-Y') : '-' }}</td>
            <td>{{ $banner->banner_end_date ? \Carbon\Carbon::parse($banner->banner_end_date)->format('d-m-Y') : '-' }}</td>
            <td>
                @php
                    $status = $banner->status == 1 ? 'active' : 'inactive';
                    $class  = $banner->status == 1 ? 'badge border border-success text-success px-2 py-1 fs-13' : 'badge border border-danger text-danger px-2 py-1 fs-13';
                @endphp
                <span class="{{ $class }}">{{ ucfirst($status) }}</span>
            </td>
            <td>
                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-soft-primary btn-sm">
                    <i class="bi bi-pencil"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">No banners found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$banners" />
    </tr>
</tfoot>

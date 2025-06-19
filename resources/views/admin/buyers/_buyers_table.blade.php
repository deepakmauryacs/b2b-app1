{{-- This partial renders buyers table body and pagination --}}
<tbody>
    @forelse($buyers as $index => $buyer)
        <tr>
            <td>{{ ($buyers->currentPage() - 1) * $buyers->perPage() + $loop->iteration }}</td>
            <td>{{ htmlspecialchars($buyer->name) }}</td>
            <td>{{ htmlspecialchars($buyer->email) }}</td>
            <td>{{ htmlspecialchars($buyer->phone ?? 'N/A') }}</td>
            <td>
                <div class="buyer-info">
                    @php
                        $profile = $buyer->buyerProfile;
                        $info = [];
                        if ($profile?->gst_no) {
                            $info[] = '<b>GST:</b> ' . htmlspecialchars($profile->gst_no);
                        }
                        if ($profile?->pincode) {
                            $info[] = '<b>Pincode:</b> ' . htmlspecialchars($profile->pincode);
                        }
                        if ($profile?->address) {
                            $info[] = '<b>Address:</b> ' . htmlspecialchars(Str::limit($profile->address, 150));
                        }
                    @endphp
                    {!! implode('<br>', $info) !!}
                </div>
            </td>
            <td>
                @php
                    $status = $buyer->status ? 'active' : 'inactive';
                    $statusClass = $buyer->status ? 'badge border border-success text-success px-2 py-1 fs-13' : 'badge border border-danger text-danger px-2 py-1 fs-13';
                @endphp
                <span class="{{ $statusClass }}">{{ ucfirst($status) }}</span>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input profile-verified-toggle" data-id="{{ $buyer->id }}" {{ $buyer->is_profile_verified == 1 ? 'checked' : '' }}>
                </div>
            </td>
            <td>{{ \Carbon\Carbon::parse($buyer->created_at)->format('d-m-Y') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.buyers.show', $buyer->id) }}" class="btn btn-sm btn-soft-info" title="View">
                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    <a href="{{ route('admin.buyers.edit', $buyer->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center">No buyers found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        <x-custom-pagination :paginator="$buyers" />
    </tr>
</tfoot>

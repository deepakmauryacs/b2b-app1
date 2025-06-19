{{-- This partial is included to render the table body and pagination links --}}
{{-- It receives the $vendors paginator instance --}}

<tbody>
    @forelse ($vendors as $index => $vendor)
        <tr>
            <td>{{ ($vendors->currentPage() - 1) * $vendors->perPage() + $loop->iteration }}</td>
            <td>{{ htmlspecialchars($vendor->name) }}</td>
            <td>{{ htmlspecialchars($vendor->email) }}</td>
            <td>{{ htmlspecialchars($vendor->phone ?? 'N/A') }}</td>
            <td>
                <div class="vendor-info">
                    @php
                        $profile = $vendor->vendorProfile;
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
                <div class="gap-2">
                    <span class="badge bg-success">Approved: {{ $vendor->approved_products_count }}</span>
                    <span class="badge bg-warning">Pending: {{ $vendor->pending_products_count }}</span>
                </div>
            </td>
            <td>
                @php
                    $status = $vendor->status == 1 ? 'active' : 'inactive';
                    $statusClass =
                        $vendor->status == 1
                            ? 'badge border border-success text-success px-2 py-1 fs-13'
                            : 'badge border border-danger text-danger px-2 py-1 fs-13';
                @endphp
                <span class="{{ $statusClass }}">{{ ucfirst($status) }}</span>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input profile-verified-toggle" data-id="{{ $vendor->id }}"
                        {{ $vendor->is_profile_verified == 1 ? 'checked' : '' }}>
                </div>
            </td>
            <td>{{ \Carbon\Carbon::parse($vendor->created_at)->format('d-m-Y') }}</td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-soft-info"
                        title="View">
                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-soft-primary"
                        title="Edit">
                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                    </a>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center">No vendors found.</td>
        </tr>
    @endforelse
</tbody>
<tfoot>
    <tr>
        {{-- Pass the $vendors paginator to your component --}}
        <x-custom-pagination :paginator="$vendors" />
    </tr>
</tfoot>

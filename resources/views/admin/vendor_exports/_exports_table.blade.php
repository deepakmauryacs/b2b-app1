<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Export History</h4>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-md-inline">Auto refreshes while exports run</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-exports">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-centered mb-0">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>#</th>
                        <th>Range</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Created</th>
                        <th>Last Update</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColors = [
                            'in_progress' => 'bg-warning text-dark',
                            'completed' => 'bg-success',
                            'failed' => 'bg-danger',
                        ];
                    @endphp
                    @forelse ($exports as $export)
                        <tr>
                            <td>{{ ($exports->currentPage() - 1) * $exports->perPage() + $loop->iteration }}</td>
                            <td>
                                {{ number_format($export->range_start + 1) }}
                                –
                                {{ number_format($export->range_end) }}
                            </td>
                            <td>
                                @php
                                    $status = $export->status ?? 'unknown';
                                    $badgeClass = $statusColors[$status] ?? 'bg-secondary';
                                    $label = \Illuminate\Support\Str::headline($status);
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    @if ($status === 'in_progress')
                                        <span class="spinner-border spinner-border-sm align-middle me-1" role="status"></span>
                                    @endif
                                    {{ $label }}
                                </span>
                            </td>
                            <td>
                                @if ($status === 'completed' && $export->file_name)
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('admin.vendor-exports.download', $export->id) }}">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @elseif ($status === 'failed')
                                    <span class="text-danger">Failed</span>
                                @else
                                    <span class="text-muted">Processing…</span>
                                @endif
                            </td>
                            <td>
                                <span class="d-block">{{ $export->created_at?->format('d M Y, h:i A') }}</span>
                                <span class="text-muted small">{{ $export->created_at?->diffForHumans() }}</span>
                            </td>
                            <td>
                                <span class="d-block">{{ $export->updated_at?->format('d M Y, h:i A') }}</span>
                                <span class="text-muted small">{{ $export->updated_at?->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-file-earmark-excel display-6 d-block mb-2"></i>
                                    No exports have been generated yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($exports->hasPages())
        <div class="card-footer d-flex justify-content-center exports-pagination">
            {{ $exports->onEachSide(1)->links() }}
        </div>
    @endif
</div>

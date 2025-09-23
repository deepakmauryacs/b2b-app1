@extends('admin.layouts.app')
@section('title', 'Vendor Export | Deal24hours')
@section('content')
@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $exports */
@endphp
<div class="row">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h4 class="card-title mb-0">Start a New Export</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Generate heavy exports without loading all records in the browser. The export runs in the
                    background and the downloadable file will appear in the history panel once it is ready.</p>
                <form id="exportForm" method="POST" action="{{ route('admin.vendor-exports.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="preset-range">Quick Range (optional)</label>
                        <select id="preset-range" class="form-select">
                            <option value="">Select a quick range</option>
                            <option value="0-25000">1 - 25,000</option>
                            <option value="25000-50000">25,001 - 50,000</option>
                            <option value="50000-75000">50,001 - 75,000</option>
                            <option value="75000-100000">75,001 - 100,000</option>
                        </select>
                        <div class="form-text">Use the quick range selector or provide custom offsets below.</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label" for="range_start">Range Start</label>
                            <input type="number" name="range_start" id="range_start" class="form-control" min="0"
                                placeholder="0" required>
                            <div class="form-text">Use <strong>0</strong> to start from the very first record.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="range_end">Range End</label>
                            <input type="number" name="range_end" id="range_end" class="form-control" min="1"
                                placeholder="25000" required>
                            <div class="form-text">End value is exclusive. Example: 25000 exports the first 25,000 rows.</div>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center gap-2 mt-3" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            Large databases are processed in batches on the server. You can safely navigate away and return to
                            download the file later.
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="start-export">
                            <span class="default-text"><i class="bi bi-cloud-upload"></i> Start Export</span>
                            <span class="loading-text d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Starting…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8 mt-4 mt-xl-0">
        <div id="export-history">
            @include('admin.vendor_exports._exports_table', ['exports' => $exports])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const exportsIndexUrl = "{{ route('admin.vendor-exports.index') }}";
        let currentPage = {{ $exports->currentPage() }};
        let autoRefreshTimer = null;
        const hasPendingInitially = @json($hasPending ?? false);
        const loaderHtml = `
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <div class="fw-semibold">Loading exports...</div>
                    </div>
                </div>
            </div>`;

        const $form = $('#exportForm');
        const $startInput = $('#range_start');
        const $endInput = $('#range_end');
        const $submitButton = $('#start-export');
        const $presetRange = $('#preset-range');

        function setAutoRefresh(enabled) {
            if (autoRefreshTimer) {
                clearInterval(autoRefreshTimer);
                autoRefreshTimer = null;
            }
            if (enabled) {
                autoRefreshTimer = setInterval(() => fetchExports(currentPage, false), 15000);
            }
        }

        function renderErrors(errors) {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();

            Object.entries(errors).forEach(([field, messages]) => {
                const $input = $form.find(`[name="${field}"]`);
                if ($input.length) {
                    $input.addClass('is-invalid');
                    const message = Array.isArray(messages) ? messages[0] : messages;
                    $input.after(`<div class="invalid-feedback">${message}</div>`);
                }
            });
        }

        function toggleButtonLoading(isLoading) {
            if (isLoading) {
                $submitButton.prop('disabled', true);
                $submitButton.find('.default-text').addClass('d-none');
                $submitButton.find('.loading-text').removeClass('d-none');
            } else {
                $submitButton.prop('disabled', false);
                $submitButton.find('.default-text').removeClass('d-none');
                $submitButton.find('.loading-text').addClass('d-none');
            }
        }

        function fetchExports(page = currentPage, showLoader = true) {
            $.ajax({
                url: exportsIndexUrl,
                method: 'GET',
                data: { page },
                dataType: 'json',
                beforeSend: function() {
                    if (showLoader) {
                        $('#export-history').html(loaderHtml);
                    }
                },
                success: function(response) {
                    $('#export-history').html(response.html);
                    currentPage = response.page || page;
                    setAutoRefresh(response.hasPending);
                },
                error: function(xhr) {
                    console.error('Unable to refresh exports', xhr.responseText);
                    $('#export-history').html(`
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="text-center text-danger">
                                    <i class="bi bi-exclamation-triangle display-6 d-block mb-2"></i>
                                    Failed to load export history. Please try again.
                                </div>
                            </div>
                        </div>`);
                }
            });
        }

        $presetRange.on('change', function() {
            const value = $(this).val();
            if (value) {
                const [start, end] = value.split('-');
                $startInput.val(start);
                $endInput.val(end);
            }
        });

        $form.on('submit', function(event) {
            event.preventDefault();
            toggleButtonLoading(true);
            renderErrors({});

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    toastr.success(response.message || 'Export has been queued successfully.');
                    $form[0].reset();
                    currentPage = 1;
                    fetchExports(1);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        renderErrors(xhr.responseJSON.errors || {});
                    } else {
                        const message = xhr.responseJSON?.message || 'Something went wrong while starting the export.';
                        toastr.error(message);
                    }
                },
                complete: function() {
                    toggleButtonLoading(false);
                }
            });
        });

        $(document).on('click', '#refresh-exports', function(event) {
            event.preventDefault();
            fetchExports(currentPage);
        });

        $(document).on('click', '.exports-pagination a', function(event) {
            event.preventDefault();
            const url = $(this).attr('href');
            if (!url) {
                return;
            }
            const page = new URL(url, window.location.origin).searchParams.get('page') || 1;
            currentPage = parseInt(page, 10) || 1;
            fetchExports(currentPage);
        });

        setAutoRefresh(hasPendingInitially);
    });
</script>
@endpush

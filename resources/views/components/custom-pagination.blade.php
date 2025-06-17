@props(['paginator']) {{-- Define the prop that will be passed into the component --}}

@php
    $onEachSide = 2; // Number of pages to show on each side of the current page
    $window = $onEachSide * 2; // Total pages in the "window" around current page

    // Get the current 'per_page' value from the request, or use the paginator's default
    $currentPerPage = request('per_page', $paginator->perPage());
@endphp

<td colspan="10" class="text-center"> {{-- Adjust colspan as needed for your table --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        {{-- Page Length Selector --}}
        <form id="pageLengthForm" class="d-inline-block" style="margin-bottom:0;">
            <label for="perPage" class="me-2">Show</label>
            <select name="perPage" id="perPage" class="form-select form-select-sm d-inline-block w-auto"
                style="display:inline-block; width:auto;">
                @foreach([10, 25, 50, 100] as $length)
                    <option value="{{ $length }}" {{ $currentPerPage == $length ? 'selected' : '' }}>
                        {{ $length }}
                    </option>
                @endforeach
            </select>
            <span class="ms-2">entries</span>
        </form>

        {{-- Custom Pagination Links --}}
        @if ($paginator->total() > 0)
            <div>
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() . '&per_page=' . $currentPerPage }}" rel="prev">&laquo;</a>
                        </li>
                    @endif

                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();

                        $startPage = max($currentPage - $onEachSide, 1);
                        $endPage = min($currentPage + $onEachSide, $lastPage);

                        // Adjust window if current page is near beginning
                        if ($currentPage <= $onEachSide) {
                            $endPage = min($window + 1, $lastPage);
                        }
                        // Adjust window if current page is near end
                        if ($currentPage >= $lastPage - $onEachSide) {
                            $startPage = max($lastPage - $window, 1);
                        }
                    @endphp

                    {{-- First Page Link & Ellipsis --}}
                    @if ($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) . '&per_page=' . $currentPerPage }}">1</a></li>
                        @if ($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    {{-- Page Number Links --}}
                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) . '&per_page=' . $currentPerPage }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    {{-- Last Page Link & Ellipsis --}}
                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url($lastPage) . '&per_page=' . $currentPerPage }}">{{ $lastPage }}</a></li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() . '&per_page=' . $currentPerPage }}" rel="next">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</td>

{{-- The JavaScript should also be pushed from the component --}}

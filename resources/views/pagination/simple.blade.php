@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" role="navigation">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex justify-content-between flex-fill">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="btn btn-outline-secondary disabled">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a class="btn btn-outline-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif

                {{-- Page Info --}}
                <div class="d-flex align-items-center p-2">
                    <span class="text-muted">
                        Halaman {{ $paginator->currentPage() }}
                    </span>
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a class="btn btn-outline-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="btn btn-outline-secondary disabled">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif

@if($paginator->hasPages())
    <nav class="pagination" aria-label="Halaman katalog">
        @if($paginator->onFirstPage())
            <span aria-disabled="true">&larr; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Sebelumnya</a>
        @endif

        <span class="active">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &rarr;</a>
        @else
            <span aria-disabled="true">Berikutnya &rarr;</span>
        @endif
    </nav>
@endif
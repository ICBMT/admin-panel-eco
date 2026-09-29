{{-- Pagination markup matching the Spark Admin template (tables-basic.html) --}}
@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination mb-0 gap-1">
            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link border-0" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous"
                    @if ($paginator->onFirstPage()) aria-disabled="true" @endif>
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            {{-- Numbered pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border-0">{{ $element }}</span>
                    </li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $paginator->currentPage() === $page ? 'active' : '' }}">
                            <a class="page-link border-0" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link border-0" href="{{ $paginator->nextPageUrl() }}" aria-label="Next"
                    @if (!$paginator->hasMorePages()) aria-disabled="true" @endif>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif

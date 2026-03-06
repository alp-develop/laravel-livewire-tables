@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item"><button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="page-link">&laquo;</button></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            @php $hideMobile = abs($page - $paginator->currentPage()) > 2 && $page !== 1 && $page !== $paginator->lastPage(); @endphp
                            <li class="page-item{{ $hideMobile ? ' lt-page-hide-mobile' : '' }}"><button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="page-link">{{ $page }}</button></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item"><button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="page-link">&raquo;</button></li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif

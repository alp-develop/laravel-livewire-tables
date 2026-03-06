@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <span class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-l-md cursor-default">&laquo;</span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:outline-none lt-focus-ring-500">&laquo;</button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white lt-bg-600 border lt-border-600 cursor-default">{{ $page }}</span>
                            </span>
                        @else
                            @php $hideMobile = abs($page - $paginator->currentPage()) > 2 && $page !== 1 && $page !== $paginator->lastPage(); @endphp
                            <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none lt-focus-ring-500{{ $hideMobile ? ' lt-page-hide-mobile' : '' }}">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:outline-none lt-focus-ring-500">&raquo;</button>
            @else
                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 rounded-r-md cursor-default">&raquo;</span>
            @endif
        </span>
    </nav>
@endif

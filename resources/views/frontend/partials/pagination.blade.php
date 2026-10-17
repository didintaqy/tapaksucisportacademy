@if ($paginator->hasPages())
    <div class="page-pagination wow fadeInUp" data-wow-delay="0.4s">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled"><span><i class="fa-solid fa-angle-left"></i></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-angle-left"></i></a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active"><a href="{{ $url }}">{{ $page }}</a></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fa-solid fa-angle-right"></i></a></li>
            @else
                <li class="disabled"><span><i class="fa-solid fa-angle-right"></i></span></li>
            @endif
        </ul>
    </div>
@endif

@props(['label', 'column'])

<th>
    <div class="d-flex align-items-center justify-content-between">
        <span>{{ $label }}</span>
        <div class="d-flex flex-column ms-2" style="font-size:0.7rem;line-height:1;">
            <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => 'asc']) }}"
               class="{{ (request('sort') == $column && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
            <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => 'desc']) }}"
               class="{{ (request('sort') == $column && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
        </div>
    </div>
</th>
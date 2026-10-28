<form action="{{ url()->current() }}" method="GET" class="mb-3">
    @foreach(request()->except(['page', 'line']) as $name => $value)
        @if(is_array($value))
            @foreach($value as $nestedName => $nestedValue)
                @if(!is_array($nestedValue))
                    <input type="hidden" name="{{ $name }}[{{ $nestedName }}]" value="{{ $nestedValue }}">
                @endif
            @endforeach
        @else
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
    @endforeach
    <div class="d-flex align-items-center flex-wrap">
        <span class="small text-muted font-weight-bold mr-2 mb-1">Filter Line:</span>
        <div class="btn-group btn-group-sm flex-wrap" role="group" aria-label="Filter berdasarkan line">
            <button type="submit" name="line" value="" class="btn {{ request('line', '') === '' ? 'btn-primary' : 'btn-outline-primary' }}" aria-pressed="{{ request('line', '') === '' ? 'true' : 'false' }}">
                Semua
            </button>
            @foreach(range(1, 8) as $lineOption)
                @php($isSelectedLine = (string) request('line', '') === (string) $lineOption)
                <button type="submit" name="line" value="{{ $lineOption }}" class="btn {{ $isSelectedLine ? 'btn-primary' : 'btn-outline-primary' }}" aria-pressed="{{ $isSelectedLine ? 'true' : 'false' }}">
                    Line {{ $lineOption }}
                </button>
            @endforeach
        </div>
    </div>
    @error('line')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror
</form>

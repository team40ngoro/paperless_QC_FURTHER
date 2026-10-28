<form action="{{ url()->current() }}" method="GET" class="row align-items-end mb-3">
    @foreach(request()->except(['page', 'tanggal_mulai', 'tanggal_akhir']) as $name => $value)
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
    <div class="col-md-3 mb-2">
        <label for="list_tanggal_mulai" class="small text-muted font-weight-bold">Tanggal Mulai</label>
        <input type="date" id="list_tanggal_mulai" name="tanggal_mulai" class="form-control form-control-sm" value="{{ old('tanggal_mulai', $tanggalMulai ?? request('tanggal_mulai')) }}">
        @error('tanggal_mulai')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-3 mb-2">
        <label for="list_tanggal_akhir" class="small text-muted font-weight-bold">Tanggal Akhir</label>
        <input type="date" id="list_tanggal_akhir" name="tanggal_akhir" class="form-control form-control-sm" value="{{ old('tanggal_akhir', $tanggalAkhir ?? request('tanggal_akhir')) }}">
        @error('tanggal_akhir')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6 mb-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter mr-1"></i> Filter Tanggal
        </button>
        @if(request()->filled('tanggal_mulai') || request()->filled('tanggal_akhir'))
            @php($queryWithoutDates = request()->except(['page', 'tanggal_mulai', 'tanggal_akhir']))
            <a href="{{ url()->current() . (count($queryWithoutDates) ? '?' . http_build_query($queryWithoutDates) : '') }}" class="btn btn-outline-secondary btn-sm" data-clear-date-filter>
                <i class="fas fa-times mr-1"></i> Hapus Filter
            </a>
        @endif
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const currentUrl = new URL(window.location.href);
    const dateParameters = ['tanggal_mulai', 'tanggal_akhir']
        .filter((name) => currentUrl.searchParams.has(name));

    if (dateParameters.length === 0) {
        return;
    }

    document.querySelectorAll('form[method="GET"], form:not([method])').forEach((form) => {
        if (form.querySelector('[name="tanggal_mulai"], [name="tanggal_akhir"]')) {
            return;
        }

        const actionUrl = new URL(form.action || window.location.href, window.location.href);
        if (actionUrl.origin !== currentUrl.origin || actionUrl.pathname !== currentUrl.pathname) {
            return;
        }

        dateParameters.forEach((name) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = currentUrl.searchParams.get(name);
            form.appendChild(input);
        });
    });

    document.querySelectorAll('a[href]').forEach((link) => {
        const href = link.getAttribute('href');
        if (
            link.hasAttribute('data-clear-date-filter') ||
            link.hasAttribute('data-toggle') ||
            link.hasAttribute('data-target') ||
            !href ||
            href.startsWith('#')
        ) {
            return;
        }

        const targetUrl = new URL(link.href, window.location.href);
        if (targetUrl.origin !== currentUrl.origin || targetUrl.pathname !== currentUrl.pathname) {
            return;
        }

        dateParameters.forEach((name) => {
            if (!targetUrl.searchParams.has(name)) {
                targetUrl.searchParams.set(name, currentUrl.searchParams.get(name));
            }
        });
        link.href = targetUrl.toString();
    });
});
</script>

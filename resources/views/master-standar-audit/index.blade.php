@extends('layouts.app')

@section('title', 'Master Standar Audit')

@section('container')
<style>
    .msa { --p:#6f42c1; --p-dark:#5a32a3; --p-soft:#f3edfc; --ink:#1f2937; --mute:#6b7280; --line:#e9e5f2; --bg:#f6f4fb; --danger:#dc3545; }
    .msa-page { background:var(--bg); padding-top:15px;}
    .msa .card { border:1px solid var(--line); border-radius:14px; box-shadow:0 1px 2px rgba(60,40,110,.05); }
    .msa .text-purple { color:var(--p)!important; }

    /* Hero */
    .msa-hero { background:linear-gradient(90deg,#0a7cff 0%,#0066d6 55%,#0052b4 100%); border-radius:16px; color:#fff; padding:22px 26px; margin-bottom:22px; position:relative; overflow:hidden; }
    .msa-hero::after { content:""; position:absolute; right:-40px; top:-60px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.08); }
    .msa-hero h1 { font-size:1.45rem; font-weight:700; margin:0 0 4px; }
    .msa-hero p { margin:0; opacity:.85; font-size:.9rem; max-width:640px; }
    .msa-hero .ico { width:46px; height:46px; border-radius:12px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }

    /* Stat chips */
    .msa-stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .msa-stat { flex:1 1 130px; background:#fff; border:1px solid var(--line); border-radius:12px; padding:12px 14px; display:flex; align-items:center; gap:12px; }
    .msa-stat .dot { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:15px; }
    .msa-stat b { display:block; font-size:1.25rem; line-height:1.1; color:var(--ink); }
    .msa-stat span { font-size:.75rem; color:var(--mute); }

    /* ===== FORM CARD (sticky, scroll di dalam body, footer selalu terlihat) ===== */
    .msa-form-card { position:sticky; top:12px; display:flex; flex-direction:column; max-height:calc(100vh - 24px); }
    .msa-form-card .card-header { background:#fff; border-bottom:1px solid var(--line); border-radius:14px 14px 0 0; padding:16px 20px; flex:0 0 auto; }
    .msa-form { display:flex; flex-direction:column; flex:1 1 auto; min-height:0; margin:0; }
    .msa-form-body { flex:1 1 auto; min-height:0; overflow-y:auto; padding:18px 20px 6px; scrollbar-width:thin; }
    .msa-form-footer { flex:0 0 auto; background:#fff; border-top:1px solid var(--line); border-radius:0 0 14px 14px; padding:12px 20px 14px; box-shadow:0 -6px 12px -10px rgba(60,40,110,.25); }
    .msa-form-error { display:none; font-size:.78rem; color:var(--danger); margin-bottom:8px; }
    .msa-form-error.show { display:block; }

    .msa-group { margin-bottom:20px; }
    .msa-group-title { font-size:.8rem; font-weight:700; color:var(--p); margin-bottom:10px; padding-bottom:6px; border-bottom:1px dashed var(--line); display:flex; align-items:center; gap:6px; }
    .msa label.lbl { font-size:.82rem; font-weight:600; color:var(--ink); margin-bottom:4px; }
    .msa .form-control { border-radius:9px; border:1px solid #d9d3e8; font-size:.9rem; }
    .msa .form-control:focus { border-color:var(--p); box-shadow:0 0 0 3px rgba(111,66,193,.15); }
    .msa .form-control.is-invalid { border-color:var(--danger); background-image:none; }
    .msa .form-control.is-invalid:focus { box-shadow:0 0 0 3px rgba(220,53,69,.15); }

    .btn-purple { background:var(--p); border:0; border-radius:10px; padding:11px; font-weight:600; color:#fff; transition:background .15s; }
    .btn-purple:hover { background:var(--p-dark); color:#fff; }
    .btn-purple:disabled { background:#b9a6df; cursor:not-allowed; }

    .tools-row { display:none; align-items:center; justify-content:space-between; gap:8px; margin-bottom:10px; }
    .tools-row .tbl-info { font-size:.76rem; color:var(--mute); }
    .tools-row .tbl-info code { color:var(--p-dark); background:var(--p-soft); border-radius:6px; padding:1px 7px; }
    .tools-row .btn-link { font-size:.78rem; text-decoration:none; }

    /* ===== Kolom: kartu per kolom (ringkas) ===== */
    .col-card { border:1px solid var(--line); border-radius:10px; background:#fff; margin-bottom:8px; transition:background .15s,border-color .15s; }
    .col-card.is-on { background:var(--p-soft); border-color:#cdbdf0; }
    .col-card.is-off { background:#fafafa; }
    .col-card.is-off .col-body { opacity:.5; }
    .col-card.has-error { border-color:var(--danger); }
    .col-head { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:8px 12px 0; }
    .col-head .custom-control-label { cursor:pointer; font-size:.82rem; }
    .col-head code { font-size:.82rem; color:#c2185b; }
    .col-table { font-size:.68rem; color:var(--mute); background:#fff; border:1px solid var(--line); border-radius:999px; padding:1px 8px; white-space:nowrap; }
    .col-label { font-size:.74rem; color:var(--mute); padding:0 12px; margin:2px 0 6px 28px; }
    .col-body { padding:0 12px 10px; }
    .col-body .form-control { font-size:.8rem; height:32px; padding:2px 8px; background:#fff; }
    .col-error { display:none; font-size:.72rem; color:var(--danger); margin-top:4px; }
    .col-card.has-error .col-error { display:block; }
    .autofix-row { font-size:.8rem; padding-top:3px; padding-bottom:3px; margin-left:6px; }
    .msa .custom-control-input:checked ~ .custom-control-label::before { background-color:var(--p); border-color:var(--p); }

    /* ===== Tabel daftar aturan (ringkas, 5 kolom) ===== */
    .msa-table { margin-bottom:0; }
    .msa-table thead th { background:#faf8ff; color:var(--mute); font-size:.75rem; font-weight:700; border-top:0; border-bottom:1px solid var(--line); padding:12px; white-space:nowrap; }
    .msa-table td { padding:12px; vertical-align:middle; border-color:#f0ecf8; }
    .msa-table tbody tr:hover { background:#fbf9ff; }
    .msa-table .cell-title { font-weight:700; font-size:.85rem; color:var(--ink); white-space:nowrap; line-height:1.3; }
    .msa-table .col-name { display:block; font-weight:600; font-size:.85rem; color:var(--ink); line-height:1.35; min-width:150px; }
    .msa-table .stack { display:flex; flex-direction:column; align-items:flex-start; gap:4px; }

    /* Kolom aksi menempel di kanan agar selalu terlihat */
    .msa-table .sticky-col { position:sticky; right:0; background:#fff; box-shadow:-6px 0 8px -8px rgba(60,40,110,.3); z-index:1; }
    .msa-table thead th.sticky-col { background:#faf8ff; }
    .msa-table tbody tr:hover .sticky-col { background:#fbf9ff; }

    .range-pill { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; border-radius:999px; padding:3px 10px; font-size:.78rem; font-weight:600; color:var(--ink); white-space:nowrap; }
    .range-pill i { color:var(--mute); font-style:normal; font-weight:400; }
    .range-pill .unit { color:var(--mute); font-weight:500; margin-left:2px; }
    .area-tag { display:inline-flex; align-items:center; gap:5px; background:#fef3c7; color:#92400e; border-radius:999px; padding:2px 9px; font-size:.72rem; font-weight:600; white-space:nowrap; }
    .global-tag { display:inline-flex; align-items:center; gap:5px; background:#eef2ff; color:#4338ca; border-radius:999px; padding:2px 9px; font-size:.72rem; font-weight:600; white-space:nowrap; }
    .type-tag { display:inline-flex; align-items:center; gap:5px; border-radius:8px; padding:3px 9px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .msa-empty { padding:48px 20px; text-align:center; color:var(--mute); }
    .msa-empty .big { width:72px; height:72px; margin:0 auto 14px; border-radius:50%; background:var(--p-soft); color:var(--p); display:flex; align-items:center; justify-content:center; font-size:28px; }
    .msa-empty h6 { color:var(--ink); font-weight:700; }

    /* Priority */
    .prio { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .prio-item { border:1px solid var(--line); border-radius:12px; padding:14px; background:#fff; border-top-width:4px; }
    .prio-item.p1 { border-top-color:#f59e0b; } .prio-item.p2 { border-top-color:#6366f1; } .prio-item.p3 { border-top-color:#94a3b8; }
    .prio-item h6 { font-size:.85rem; font-weight:700; margin-bottom:4px; color:var(--ink); }
    .prio-item p { font-size:.78rem; color:var(--mute); margin:0; }

    @media (max-width:991px){ .msa-form-card{ position:static; max-height:none; } .msa-form-body{ overflow:visible; } }
    @media (max-width:767px){ .prio{grid-template-columns:1fr;} }
</style>

<div class="content-wrapper msa msa-page">
    <section class="content pt-3">
        <div class="container-fluid">

            {{-- HERO --}}
            <div class="msa-hero d-flex align-items-center">
                <div class="ico mr-3"><i class="fas fa-sliders-h"></i></div>
                <div>
                    <h1>Master Standar Audit</h1>
                    <p>Atur batas suhu dan koreksi otomatis data audit untuk tiap modul. Aturan khusus area selalu didahulukan daripada aturan global.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius:12px;">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            {{-- STATS --}}
            <div class="msa-stats">
                <div class="msa-stat">
                    <div class="dot" style="background:#f3edfc;color:#6f42c1;"><i class="fas fa-layer-group"></i></div>
                    <div><b>{{ $standars->count() }}</b><span>Total aturan</span></div>
                </div>
                <div class="msa-stat">
                    <div class="dot" style="background:#dcfce7;color:#166534;"><i class="fas fa-magic"></i></div>
                    <div><b>{{ $standars->where('auto_fix_status', true)->count() }}</b><span>Auto fix aktif</span></div>
                </div>
            </div>

            <div class="row">
                {{-- LEFT: FORM --}}
                <div class="col-lg-5 mb-4">
                    <div class="card msa-form-card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-purple"><i class="fas fa-plus-circle mr-1"></i> Form Input Aturan Master Audit</h6>
                        </div>

                        <form action="{{ route('master-standar-audit.store') }}" method="POST" id="mainAuditForm" class="msa-form" novalidate>
                            @csrf

                            <div class="msa-form-body">
                                {{-- Target --}}
                                <div class="msa-group">
                                    <div class="msa-group-title"><i class="fas fa-bullseye"></i> 1. Pilih Target Modul</div>

                                    <div class="form-group">
                                        <label class="lbl">Modul QC <span class="text-danger">*</span></label>
                                        <select name="module_name" id="moduleSelectMain" class="form-control" required>
                                            <option value="">-- Pilih modul QC --</option>
                                            @foreach($modules as $mod)
                                                <option value="{{ $mod['id'] }}">{{ $mod['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- SMART COLUMN LIST --}}
                                <div class="msa-group">
                                    <div class="msa-group-title"><i class="fas fa-list-check"></i> 2. Kolom Modul & Standar Parameter</div>
                                    <div id="columnTools" class="tools-row">
                                        <span class="tbl-info" id="tableInfo"></span>
                                        <span style="white-space:nowrap;">
                                            <button type="button" id="btnCheckAll" class="btn btn-link text-purple p-0 mr-2 font-weight-bold">Centang Semua</button>
                                            <button type="button" id="btnUncheckAll" class="btn btn-link text-muted p-0 font-weight-bold">Batal Centang</button>
                                        </span>
                                    </div>

                                    <div id="smartColumnsContainer">
                                        <div class="text-muted text-center py-4 border rounded bg-light small" id="initialHelpMsg">
                                            <i class="fas fa-arrow-up mr-1 text-purple"></i> Pilih Modul QC di atas untuk menampilkan kolom-kolomnya.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer: tombol simpan selalu terlihat --}}
                            <div class="msa-form-footer">
                                <div class="msa-form-error" id="formError" role="alert"></div>
                                <button type="submit" id="btnSubmitForm" class="btn btn-success btn-block shadow-sm" disabled>
                                    <i class="fas fa-save mr-1"></i> Simpan Aturan Audit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: TABLE + PRIORITY --}}
                <div class="col-lg-7 mb-4">
                    <div class="card">
                        <div class="card-header bg-white" style="border-radius:14px 14px 0 0; border-bottom:1px solid var(--line); padding:16px 20px;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-purple mb-2 mb-md-0">
                                    <i class="fas fa-list mr-1"></i> Daftar aturan
                                    <span class="badge badge-pill px-3 py-1 ml-2" style="background:var(--p-soft);color:var(--p-dark); font-size: 0.8rem;">
                                        Total {{ $standars->total() }} aturan
                                    </span>
                                </h6>

                                {{-- Dropdown Select Modul Filter & Per-Page --}}
                                <form method="GET" action="{{ route('master-standar-audit.index') }}" class="form-inline">
                                    <div class="form-group mr-2 mb-1 mb-md-0">
                                        <select name="module" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius:6px; max-width:240px;">
                                            <option value="">-- Semua Modul QC --</option>
                                            @foreach($modules as $m)
                                                <option value="{{ $m['id'] }}" {{ ($selectedModule ?? '') == $m['id'] ? 'selected' : '' }}>
                                                    {{ $m['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if(!empty($selectedModule))
                                        <a href="{{ route('master-standar-audit.index') }}" class="btn btn-outline-danger btn-sm mr-2" title="Reset Filter">
                                            <i class="fas fa-times mr-1"></i> Reset
                                        </a>
                                    @endif

                                    <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()" style="border-radius:6px; width:70px;">
                                        <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table msa-table">
                                    <thead>
                                        <tr>
                                            <th>Modul / Area</th>
                                            <th>Kolom</th>
                                            <th>Tipe / Range</th>
                                            <th class="text-center">Auto fix</th>
                                            <th class="text-center sticky-col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tone  = ['suhu'=>['#e0f2fe','#075985'],'suhu_ruang'=>['#dbeafe','#1e40af'],'suhu_kondisi'=>['#e2e8f0','#334155'],'suhu_pusat'=>['#e5e7eb','#111827'],'suhu_pemasakan'=>['#fee2e2','#991b1b'],'status_checklist'=>['#dcfce7','#166534']];
                                            $icons = ['suhu'=>'🌡️','suhu_ruang'=>'🏠','suhu_kondisi'=>'❄️','suhu_pusat'=>'🎯','suhu_pemasakan'=>'🔥','status_checklist'=>'✅'];
                                            $fmt = fn($v) => $v !== null ? rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') : '-';
                                        @endphp
                                        @forelse($standars as $item)
                                            @php $c = $tone[$item->parameter_type] ?? ['#f1f5f9','#334155']; @endphp
                                            <tr>
                                                {{-- Modul + Area --}}
                                                <td>
                                                    <div class="cell-title">{{ ucwords(str_replace(['_', '-'], ' ', $item->module_name)) }}</div>
                                                        @if($item->area)
                                                            <div class="mt-1">
                                                                <span class="area-tag"><i class="fas fa-map-marker-alt"></i>{{ $item->area->area }}</span>
                                                            </div>
                                                        @endif
                                                </td>

                                                {{-- Kolom --}}
                                                <td>
                                                    <span class="col-name">{{ $item->column_label }}</span>
                                                </td>

                                                {{-- Tipe + Range --}}
                                                <td>
                                                    <div class="stack">
                                                        <span class="type-tag" style="background:{{ $c[0] }};color:{{ $c[1] }};">
                                                            {{ $icons[$item->parameter_type] ?? '' }} {{ ucfirst(str_replace('_', ' ', $item->parameter_type)) }}
                                                        </span>
                                                        @if($item->parameter_type !== 'status_checklist')
                                                            <span class="range-pill">{{ $fmt($item->min_value) }}<i>–</i>{{ $fmt($item->max_value) }}<span class="unit">°C</span></span>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- Auto fix --}}
                                                <td class="text-center">
                                                    @if($item->auto_fix_status)
                                                        <i class="fas fa-check-circle text-success" title="Auto fix aktif"></i>
                                                    @else
                                                        <i class="fas fa-minus-circle text-muted" title="Auto fix nonaktif"></i>
                                                    @endif
                                                </td>

                                                {{-- Aksi (sticky) --}}
                                                <td class="text-center sticky-col">
                                                    <div class="d-flex justify-content-center align-items-center" style="gap:4px;">
                                                        <button type="button" class="btn btn-sm btn-light text-warning btn-edit-audit" title="Edit Aturan" style="border-radius:8px;"
                                                            data-uuid="{{ $item->uuid }}"
                                                            data-raw-module="{{ $item->module_name }}"
                                                            data-module="{{ ucwords(str_replace(['_', '-'], ' ', $item->module_name)) }}"
                                                            data-table="{{ $item->table_name }}"
                                                            data-column="{{ $item->column_name }}"
                                                            data-area-id="{{ $item->area_id }}"
                                                            data-type="{{ $item->parameter_type }}"
                                                            data-min="{{ $item->min_value }}"
                                                            data-max="{{ $item->max_value }}"
                                                            data-autofix="{{ $item->auto_fix_status ? 1 : 0 }}"
                                                            data-update-url="{{ route('master-standar-audit.update', $item->uuid) }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('master-standar-audit.destroy', $item->uuid) }}" method="POST" onsubmit="return confirm('Hapus aturan standar ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus" style="border-radius:8px;">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-0">
                                                    <div class="msa-empty">
                                                        <div class="big"><i class="fas fa-clipboard-list"></i></div>
                                                        <h6>Belum ada aturan standar</h6>
                                                        <p class="mb-0 small">Selama belum ada aturan, sistem memakai koreksi umum:<br>status NOT OK / Silang otomatis menjadi OK / Ceklis.<br>Tambahkan aturan pertama lewat form di sebelah kiri.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pagination Footer --}}
                        @if($standars->hasPages())
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3" style="border-radius:0 0 14px 14px;">
                                <div class="text-muted small">
                                    Menampilkan {{ $standars->firstItem() ?? 0 }} - {{ $standars->lastItem() ?? 0 }} dari {{ $standars->total() }} data
                                </div>
                                <div>
                                    {{ $standars->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Prioritas --}}
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="text-purple font-weight-bold mb-3"><i class="fas fa-sort-amount-down mr-1"></i> Urutan penerapan aturan</h6>
                            <div class="prio">
                                <div class="prio-item p1">
                                    <h6><i class="fas fa-map-marker-alt text-warning mr-1"></i> Khusus area</h6>
                                    <p>Dipakai lebih dulu jika area data aktual cocok dengan aturan.</p>
                                </div>
                                <div class="prio-item p2">
                                    <h6><i class="fas fa-globe mr-1" style="color:#6366f1;"></i> Global</h6>
                                    <p>Dipakai jika tidak ada aturan khusus area yang cocok.</p>
                                </div>
                                <div class="prio-item p3">
                                    <h6><i class="fas fa-shield-alt text-secondary mr-1"></i> Koreksi umum</h6>
                                    <p>Kolom yang tidak terdaftar: NOT OK / Silang / NG / Reject diubah ke OK / Ceklis.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form                  = document.getElementById('mainAuditForm');
        const moduleSelect          = document.getElementById('moduleSelectMain');
        const smartColumnsContainer = document.getElementById('smartColumnsContainer');
        const columnTools           = document.getElementById('columnTools');
        const btnCheckAll           = document.getElementById('btnCheckAll');
        const btnUncheckAll         = document.getElementById('btnUncheckAll');
        const btnSubmitForm         = document.getElementById('btnSubmitForm');
        const formError             = document.getElementById('formError');

        const ALL_AREAS = @json($areas);
        const HELP_HTML = '<div class="text-muted text-center py-4 border rounded bg-light small"><i class="fas fa-arrow-up mr-1 text-purple"></i> Pilih Modul QC di atas untuk menampilkan kolom-kolomnya.</div>';

        const esc = s => String(s ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));

        /* ---------- helpers ---------- */
        const isChecklistType = t => t === 'status_checklist';
        const cards = () => Array.from(document.querySelectorAll('.col-card'));

        function setError(msg) {
            formError.textContent = msg || '';
            formError.classList.toggle('show', !!msg);
        }

        // Aktif / nonaktifkan satu kartu kolom (tampilan + inert agar input tidak bisa diedit)
        function applyCardState(card) {
            const on = card.querySelector('.col-enable-check').checked;
            card.classList.toggle('is-on', on);
            card.classList.toggle('is-off', !on);
            const body = card.querySelector('.col-body');
            if (on) { body.removeAttribute('inert'); }
            else    { body.setAttribute('inert', ''); clearCardError(card); }
        }

        function clearCardError(card) {
            card.classList.remove('has-error');
            card.querySelectorAll('.item-min-val,.item-max-val').forEach(i => i.classList.remove('is-invalid'));
        }

        // Validasi satu kartu. Mengembalikan pesan error atau '' jika valid.
        function validateCard(card, showUi) {
            const on = card.querySelector('.col-enable-check').checked;
            const type = card.querySelector('.item-ptype-select').value;
            if (!on || isChecklistType(type)) { if (showUi) clearCardError(card); return ''; }

            const minEl = card.querySelector('.item-min-val');
            const maxEl = card.querySelector('.item-max-val');
            const min = parseFloat(minEl.value);
            const max = parseFloat(maxEl.value);
            let msg = '';

            if (isNaN(min) && isNaN(max)) msg = 'Isi minimal salah satu: Min atau Max.';
            else if (!isNaN(min) && !isNaN(max) && min > max) msg = 'Min tidak boleh lebih besar dari Max.';

            if (showUi) {
                const bothEmpty = isNaN(min) && isNaN(max);
                const reversed  = !!msg && !bothEmpty;
                card.classList.toggle('has-error', !!msg);
                minEl.classList.toggle('is-invalid', reversed || bothEmpty);
                maxEl.classList.toggle('is-invalid', reversed || bothEmpty);
                card.querySelector('.col-error').textContent = msg;
            }
            return msg;
        }

        function updateSubmitCount() {
            const checkedCount = document.querySelectorAll('.col-enable-check:checked').length;
            const checkedAreaCount = document.querySelectorAll('input[name^="area_items"][name$="[enabled]"]:checked').length;
            if (checkedCount > 0 || checkedAreaCount > 0) {
                let msgParts = [];
                if (checkedCount > 0) msgParts.push(`${checkedCount} kolom`);
                if (checkedAreaCount > 0) msgParts.push(`${checkedAreaCount} area`);
                btnSubmitForm.innerHTML = `<i class="fas fa-check-double text-warning mr-1"></i> Simpan Aturan Audit (${msgParts.join(' + ')})`;
                btnSubmitForm.disabled = false;
            } else {
                btnSubmitForm.innerHTML = `<i class="fas fa-save mr-1"></i> Simpan Aturan Audit`;
                btnSubmitForm.disabled = true;
            }
        }

        /* ---------- ambil kolom saat modul dipilih ---------- */
        if (moduleSelect) {
            moduleSelect.addEventListener('change', function () {
                const selectedMod = this.value;
                const areaGroup = document.getElementById('areaFormGroup');

                if (selectedMod === 'area_proses') {
                    if (areaGroup) areaGroup.style.display = 'block';
                } else {
                    if (areaGroup) areaGroup.style.display = 'none';
                    document.querySelectorAll('.area-checkbox-item').forEach(c => c.checked = false);
                }

                smartColumnsContainer.innerHTML = '<div class="text-center py-4 border rounded bg-light small text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar kolom modul...</div>';
                columnTools.style.display = 'none';
                setError('');
                updateSubmitCount();

                if (!selectedMod) {
                    smartColumnsContainer.innerHTML = HELP_HTML;
                    return;
                }

                fetch(`{{ route('master-standar-audit.get-columns') }}?module=${encodeURIComponent(selectedMod)}`)
                    .then(response => response.json())
                    .then(data => {
                        const columns = data || [];
                        if (columns.length > 0) {
                            renderSmartColumns(columns, selectedMod);
                            columnTools.style.display = 'flex';
                        } else {
                            smartColumnsContainer.innerHTML = '<div class="text-warning text-center py-4 border rounded bg-light small"><i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada kolom auditable terdaftar untuk modul ini.</div>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching columns:', err);
                        smartColumnsContainer.innerHTML = '<div class="text-danger text-center py-4 border rounded bg-light small"><i class="fas fa-times-circle mr-1"></i> Gagal memuat daftar kolom.</div>';
                    });
            });
        }

        /* ---------- render daftar kolom ---------- */
        function renderSmartColumns(columns, selectedMod) {
            const tables = [...new Set(columns.map(c => c.table_name || selectedMod))];
            const singleTable = tables.length === 1 ? tables[0] : null;

            let html = '';

            document.getElementById('tableInfo').innerHTML = singleTable
                ? `Tabel <code>${esc(singleTable)}</code> &middot; ${columns.length} kolom`
                : `${columns.length} kolom`;

            columns.forEach((col, idx) => {
                if (selectedMod === 'area_proses' && col.name === 'pemeriksaan_suhu_ruang') {
                    // Render Matriks Suhu per Area khusus untuk area_proses
                    html += `
                    <div class="card border-purple shadow-sm my-3" style="border-radius:12px; overflow:hidden;">
                        <div class="card-header bg-purple text-white font-weight-bold d-flex align-items-center justify-content-between py-2">
                            <span><i class="fas fa-temperature-high mr-2"></i> Standar Suhu Ruang per Area</span>
                            <span class="badge badge-light text-purple">${ALL_AREAS.length} Area Terdaftar</span>
                        </div>
                        <div class="p-2 bg-light border-bottom text-muted small">
                            <i class="fas fa-info-circle mr-1 text-purple"></i> Isi batas Suhu Min & Max sesuai dengan karakteristik masing-masing area produksi.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" style="font-size:0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="45" class="text-center">Aktif</th>
                                        <th>Target Area</th>
                                        <th width="125">Min (°C)</th>
                                        <th width="125">Max (°C)</th>
                                        <th width="100" class="text-center">Auto Fix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${ALL_AREAS.map(area => `
                                        <tr>
                                            <td class="text-center align-middle">
                                                <div class="custom-control custom-checkbox ml-1">
                                                    <input type="checkbox" class="custom-control-input" id="area_item_chk_${area.id}" name="area_items[${area.id}][enabled]" value="1" checked>
                                                    <label class="custom-control-label" for="area_item_chk_${area.id}"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle font-weight-bold text-dark">
                                                <i class="fas fa-map-marker-alt text-purple mr-1"></i> ${esc(area.area)}
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="area_items[${area.id}][min_value]" class="form-control form-control-sm" placeholder="Min °C">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="area_items[${area.id}][max_value]" class="form-control form-control-sm" placeholder="Max °C">
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="custom-control custom-checkbox ml-1">
                                                    <input type="checkbox" class="custom-control-input" id="area_item_afix_${area.id}" name="area_items[${area.id}][auto_fix_status]" value="1" checked>
                                                    <label class="custom-control-label" for="area_item_afix_${area.id}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>`;
                    return;
                }

                const checklist = isChecklistType(col.type);
                const colLabel  = col.label || col.name;
                const colTable  = col.table_name || selectedMod;
                // "suhu (Suhu Chillroom (°C))" -> "Suhu Chillroom (°C)"
                let cleanLabel = String(colLabel);
                const prefix = `${col.name} (`;
                if (cleanLabel.startsWith(prefix) && cleanLabel.endsWith(')')) {
                    cleanLabel = cleanLabel.slice(prefix.length, -1);
                }
                const showLabel = cleanLabel && cleanLabel !== col.name;

                html += `
                <div class="col-card is-on">
                    <div class="col-head">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input col-enable-check" id="chk_${idx}" name="items[${idx}][enabled]" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="chk_${idx}"><code>${esc(col.name)}</code></label>
                        </div>
                        ${singleTable ? '' : `<span class="col-table">${esc(colTable)}</span>`}
                    </div>
                    ${showLabel ? `<div class="col-label">${esc(cleanLabel)}</div>` : '<div style="height:6px"></div>'}

                    <div class="col-body">
                        <input type="hidden" name="items[${idx}][column_name]" value="${esc(col.name)}">
                        <input type="hidden" name="items[${idx}][table_name]" value="${esc(colTable)}">

                        <div class="item-range-box" style="display:${checklist ? 'none' : 'block'};">
                            <div class="form-row">
                                <div class="col-5">
                                    <select name="items[${idx}][parameter_type]" class="form-control item-ptype-select" aria-label="Tipe parameter">
                                        <option value="suhu" ${col.type === 'suhu' ? 'selected' : ''}>🌡️ Suhu produk</option>
                                        <option value="suhu_ruang" ${col.type === 'suhu_ruang' ? 'selected' : ''}>🏠 Suhu ruang</option>
                                        <option value="suhu_kondisi" ${col.type === 'suhu_kondisi' ? 'selected' : ''}>❄️ Suhu kondisi</option>
                                        <option value="suhu_pusat" ${col.type === 'suhu_pusat' ? 'selected' : ''}>🎯 Suhu pusat</option>
                                        <option value="suhu_pemasakan" ${col.type === 'suhu_pemasakan' ? 'selected' : ''}>🔥 Suhu pemasakan</option>
                                        <option value="status_checklist" ${checklist ? 'selected' : ''}>✅ Status / ceklis</option>
                                    </select>
                                </div>
                                <div class="col-3"><input type="number" step="0.01" name="items[${idx}][min_value]" class="form-control item-min-val" placeholder="Min °C" aria-label="Min °C"></div>
                                <div class="col-4"><input type="number" step="0.01" name="items[${idx}][max_value]" class="form-control item-max-val" placeholder="Max °C" aria-label="Max °C"></div>
                            </div>
                            <div class="col-error"></div>
                        </div>

                        <div class="item-autofix-box" style="display:${checklist ? 'block' : 'none'};">
                            <div class="form-row align-items-center">
                                <div class="col-5 item-ptype-slot"></div>
                                <div class="col-7">
                                    <div class="custom-control custom-checkbox autofix-row">
                                        <input type="checkbox" class="custom-control-input" name="items[${idx}][auto_fix_status]" id="afix_${idx}" value="1" checked>
                                        <label class="custom-control-label text-success font-weight-bold" for="afix_${idx}">Auto fix OK / Ceklis</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            smartColumnsContainer.innerHTML = html;
            cards().forEach(card => bindCard(card));

            // Binding listener pada input area matriks jika ada
            smartColumnsContainer.querySelectorAll('input[name^="area_items"]').forEach(inp => {
                inp.addEventListener('change', () => { updateSubmitCount(); setError(''); });
                inp.addEventListener('input', () => { updateSubmitCount(); setError(''); });
            });

            updateSubmitCount();
        }

        function bindCard(card) {
            const select     = card.querySelector('.item-ptype-select');
            const rangeBox   = card.querySelector('.item-range-box');
            const autoFixBox = card.querySelector('.item-autofix-box');
            const slot       = card.querySelector('.item-ptype-slot');
            const rangeRow   = rangeBox.querySelector('.form-row .col-5');

            // Dropdown tipe dipindah antara baris range dan baris auto-fix agar tetap satu baris
            function syncType() {
                const checklist = isChecklistType(select.value);
                if (checklist) {
                    slot.appendChild(select);
                    rangeBox.style.display = 'none';
                    autoFixBox.style.display = 'block';
                    clearCardError(card);
                } else {
                    rangeRow.appendChild(select);
                    rangeBox.style.display = 'block';
                    autoFixBox.style.display = 'none';
                }
            }

            select.addEventListener('change', syncType);
            syncType();

            card.querySelector('.col-enable-check').addEventListener('change', () => {
                applyCardState(card);
                updateSubmitCount();
                setError('');
            });

            card.querySelectorAll('.item-min-val,.item-max-val').forEach(inp => {
                inp.addEventListener('input', () => { validateCard(card, true); setError(''); });
            });

            applyCardState(card);
        }

        /* ---------- centang semua / batal ---------- */
        function setAll(state) {
            cards().forEach(card => {
                card.querySelector('.col-enable-check').checked = state;
                applyCardState(card);
            });
            updateSubmitCount();
            setError('');
        }
        if (btnCheckAll)   btnCheckAll.addEventListener('click',   () => setAll(true));
        if (btnUncheckAll) btnUncheckAll.addEventListener('click', () => setAll(false));

        /* ---------- validasi saat simpan ---------- */
        form.addEventListener('submit', function (e) {
            if (!moduleSelect.value) {
                e.preventDefault();
                setError('Pilih Modul QC terlebih dahulu.');
                moduleSelect.focus();
                return;
            }

            if (moduleSelect.value === 'area_proses') {
                const checkedAreas = document.querySelectorAll('input[name^="area_items"][name$="[enabled]"]:checked');
                if (checkedAreas.length === 0) {
                    e.preventDefault();
                    setError('Pilih minimal satu Target Area yang aktif di tabel matriks.');
                    return;
                }
            }

            let firstBad = null;
            let badCount = 0;
            cards().forEach(card => {
                const msg = validateCard(card, true);
                if (msg) { badCount++; if (!firstBad) firstBad = card; }
            });

            if (badCount > 0) {
                e.preventDefault();
                setError(`${badCount} kolom belum valid. Periksa range Min/Max yang ditandai merah.`);
                firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const bad = firstBad.querySelector('.item-min-val.is-invalid, .item-max-val.is-invalid');
                if (bad) bad.focus({ preventScroll: true });
            }
        });
    });
</script>

{{-- MODAL EDIT ATURAN AUDIT --}}
<div class="modal fade" id="editAuditModal" tabindex="-1" role="dialog" aria-labelledby="editAuditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-gradient-primary text-white" style="padding: 16px 20px;">
                <h5 class="modal-title font-weight-bold" id="editAuditModalLabel" style="font-size: 1.1rem;">
                    <i class="fas fa-edit mr-2"></i> Edit Aturan Audit
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAuditForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 20px;">
                    {{-- Info Kolom Readonly --}}
                    <div class="card bg-light border-0 mb-3" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block font-weight-bold">MODUL QC</small>
                                    <span id="modalModulName" class="font-weight-bold text-dark">-</span>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block font-weight-bold">TABEL</small>
                                    <code id="modalTableName">-</code>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block font-weight-bold">KOLOM AUDIT</small>
                                    <code id="modalColumnName" style="font-size: 0.95rem; color: #c2185b;">-</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Input Area --}}
                    <div class="form-group" id="editAreaGroup" style="display: none;">
                        <label class="lbl">Target Area <span class="text-danger">*</span></label>
                        <select name="area_id" id="editAreaId" class="form-control">
                            <option value="">-- Pilih Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Form Input Tipe Parameter --}}
                    <div class="form-group">
                        <label class="lbl">Tipe Parameter <span class="text-danger">*</span></label>
                        <select name="parameter_type" id="editParameterType" class="form-control" required>
                            <option value="suhu">🌡️ Suhu produk</option>
                            <option value="suhu_ruang">🏠 Suhu ruang</option>
                            <option value="suhu_kondisi">❄️ Suhu kondisi</option>
                            <option value="suhu_pusat">🎯 Suhu pusat</option>
                            <option value="suhu_pemasakan">🔥 Suhu pemasakan</option>
                            <option value="status_checklist">✅ Status / ceklis</option>
                        </select>
                    </div>

                    {{-- Range Box (Min & Max) --}}
                    <div id="editRangeBox">
                        <div class="form-row">
                            <div class="col-6 form-group mb-2">
                                <label class="lbl">Min °C</label>
                                <input type="number" step="0.01" name="min_value" id="editMinValue" class="form-control" placeholder="Nilai Min">
                            </div>
                            <div class="col-6 form-group mb-2">
                                <label class="lbl">Max °C</label>
                                <input type="number" step="0.01" name="max_value" id="editMaxValue" class="form-control" placeholder="Nilai Max">
                            </div>
                        </div>
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Isi minimal salah satu: Min atau Max.</small>
                    </div>

                    {{-- Auto Fix Box --}}
                    <div class="custom-control custom-checkbox mt-3">
                        <input type="checkbox" class="custom-control-input" name="auto_fix_status" id="editAutoFix" value="1">
                        <label class="custom-control-label text-success font-weight-bold" for="editAutoFix">
                            <i class="fas fa-magic mr-1"></i> Auto fix OK / Ceklis saat audit
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="padding: 12px 20px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons     = document.querySelectorAll('.btn-edit-audit');
        const editForm        = document.getElementById('editAuditForm');
        const modalModulName  = document.getElementById('modalModulName');
        const modalTableName  = document.getElementById('modalTableName');
        const modalColumnName = document.getElementById('modalColumnName');
        const editAreaId      = document.getElementById('editAreaId');
        const editParamType   = document.getElementById('editParameterType');
        const editMinValue    = document.getElementById('editMinValue');
        const editMaxValue    = document.getElementById('editMaxValue');
        const editAutoFix     = document.getElementById('editAutoFix');
        const editRangeBox    = document.getElementById('editRangeBox');

        function toggleRangeVisibility() {
            if (editParamType.value === 'status_checklist') {
                editRangeBox.style.display = 'none';
            } else {
                editRangeBox.style.display = 'block';
            }
        }

        editParamType.addEventListener('change', toggleRangeVisibility);

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const updateUrl = this.dataset.updateUrl;
                const rawModule = this.dataset.rawModule;
                const module    = this.dataset.module;
                const table     = this.dataset.table;
                const column    = this.dataset.column;
                const areaId    = this.dataset.areaId;
                const type      = this.dataset.type;
                const min       = this.dataset.min;
                const max       = this.dataset.max;
                const autofix   = this.dataset.autofix;

                const editAreaGroup = document.getElementById('editAreaGroup');

                if (rawModule === 'area_proses' || table === 'area_proses') {
                    if (editAreaGroup) editAreaGroup.style.display = 'block';
                    if (editAreaId) editAreaId.setAttribute('required', 'required');
                } else {
                    if (editAreaGroup) editAreaGroup.style.display = 'none';
                    if (editAreaId) {
                        editAreaId.removeAttribute('required');
                        editAreaId.value = '';
                    }
                }

                editForm.action             = updateUrl;
                modalModulName.textContent  = module;
                modalTableName.textContent  = table || '-';
                modalColumnName.textContent = column;

                if (editAreaId) editAreaId.value = areaId || '';
                editParamType.value = type;
                editMinValue.value  = min !== '' && min !== null ? min : '';
                editMaxValue.value  = max !== '' && max !== null ? max : '';
                editAutoFix.checked = autofix == 1;

                toggleRangeVisibility();

                $('#editAuditModal').modal('show');
            });
        });
    });
</script>
@endsection
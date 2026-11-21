@extends('layouts.app')

@section('container')
<div class="main-panel">

    <div class="content-wrapper">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <!-- HEADER -->
                <div class="card main-card shadow-sm">
                    <div class="card-header text-center bg-white border-bottom">
                        <h4 class="title mb-1">
                            <i class="fas fa-magic text-primary mr-2"></i>
                            Mulai Cooking Produksi
                        </h4>
                        <p class="subtitle mb-0">
                            Pilih jenis proses yang akan dijalankan
                        </p>
                    </div>

                    <div class="card-body">
                        @php
                            $superadmin = auth()->user()->role === 'superadmin';
                            $userRole = (int) (auth()->user()->id_role ?? 0);
                        @endphp

                        <div class="row justify-content-center">
                            <!-- PROSES 1: [QF-09/00] Frying -> IQF -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="process-card border-primary">
                                    <div class="icon-wrap bg-primary-soft">
                                        <i class="fas fa-fire text-danger"></i>
                                        <span class="arrow">→</span>
                                        <i class="fas fa-snowflake text-primary"></i>
                                    </div>
                                    <h6 class="process-title">[QF-09/00] Frying → IQF</h6>
                                    <ul class="process-list">
                                        <li>Pencetakan</li>
                                        <li>Predust</li>
                                        <li>Battering & Breader</li>
                                        <li>Fryer 1 - 5</li>
                                        <li>Hasil Penggorengan</li>
                                    </ul>

                                    <div class="btn-group-custom">
                                        @if($userRole !== 3 && ($superadmin || in_array($userRole, [1, 2, 5], true) || auth()->user()->can('create-wizard-proses1')))
                                            @if($draftProses1)
                                                <button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#modalProses1">
                                                    Mulai
                                                </button>
                                            @else
                                                <a href="{{ route('wizard.proses1.create') }}" class="btn btn-primary btn-sm btn-block">
                                                    Mulai
                                                </a>
                                            @endif
                                        @endif
                                        <a href="{{ route('wizard.proses1.index') }}" class="btn btn-outline-primary btn-sm btn-block">
                                            {{ $userRole === 3 ? 'Lihat Data & Input QC' : 'Riwayat' }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- PROSES 3 (PROSES 2 di urutan UI) -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="process-card border-warning">
                                    <div class="icon-wrap bg-warning-soft">
                                        <i class="fas fa-fire text-danger"></i>
                                        <span class="arrow">→</span>
                                        <i class="fas fa-temperature-high text-warning"></i>
                                        <span class="arrow">→</span>
                                        <i class="fas fa-snowflake text-primary"></i>
                                    </div>
                                    <h6 class="process-title">[QF-10/00] Frying → Roasted → IQF</h6>
                                    <ul class="process-list">
                                        <li>Pencetakan</li>
                                        <li>Predust</li>
                                        <li>Battering & Breader</li>
                                        <li>Fryer 1 - 5</li>
                                        <li>Roasting Fan</li>
                                        <li>Hasil Roasting</li>
                                    </ul>

                                    <div class="btn-group-custom">
                                        @if($userRole !== 3 && ($superadmin || in_array($userRole, [1, 2, 5], true) || auth()->user()->can('create-wizard-proses2')))
                                            @if($draftProses2)
                                                <button type="button" class="btn btn-warning btn-sm btn-block text-white" data-toggle="modal" data-target="#modalProses2">
                                                    Mulai
                                                </button>
                                            @else
                                                <a href="{{ route('wizard.proses2.create') }}" class="btn btn-warning btn-sm btn-block text-white">
                                                    Mulai
                                                </a>
                                            @endif
                                        @endif
                                        <a href="{{ route('wizard.proses2.index') }}" class="btn btn-outline-warning btn-sm btn-block">
                                            {{ $userRole === 3 ? 'Lihat Data & Input QC' : 'Riwayat' }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- PROSES 4 (PROSES 3 di urutan UI) -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="process-card border-info">
                                    <div class="icon-wrap bg-info-soft">
                                        <i class="fas fa-wind text-info"></i>
                                        <span class="arrow">→</span>
                                        <i class="fas fa-snowflake text-primary"></i>
                                    </div>
                                    <h6 class="process-title">[QF-10/00] Roasted → IQF</h6>
                                    <ul class="process-list">
                                        <li>Input Roasted</li>
                                        <li>Roasted Fan</li>
                                        <li>Hasil Roasting</li>
                                    </ul>

                                    <div class="btn-group-custom">
                                        @if($userRole !== 3 && ($superadmin || in_array($userRole, [1, 2, 5], true) || auth()->user()->can('create-wizard-proses3')))
                                            @if($draftProses3)
                                                <button type="button" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#modalProses3">
                                                    Mulai
                                                </button>
                                            @else
                                                <a href="{{ route('wizard.proses3.create') }}" class="btn btn-info btn-sm btn-block">
                                                    Mulai
                                                </a>
                                            @endif
                                        @endif
                                        <a href="{{ route('wizard.proses3.index') }}" class="btn btn-outline-info btn-sm btn-block">
                                            {{ $userRole === 3 ? 'Lihat Data & Input QC' : 'Riwayat' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="process-card border-purple">

                                    <div class="icon-wrap bg-purple-soft">
                                        <i class="fas fa-balance-scale text-purple"></i>
                                    </div>

                                    <h6 class="process-title">[QF-11/00] Verifikasi Berat Produk</h6>
                                    <div class="btn-group-custom">
                                        @if($superadmin || auth()->user()->can('create-verifikasi-berat-produk'))
                                            <a href="{{ route('verifikasi-berat-produk.create') }}" class="btn btn-purple btn-sm btn-block">
                                                Mulai
                                            </a>
                                        @endif
                                        <a href="{{ url('/qc-sistem/verifikasi-berat-produk') }}" class="btn btn-outline-purple btn-sm btn-block">
                                            Riwayat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL SELECTION -->
                @php
                    $drafts = [
                        ['id' => 'modalProses1', 'title' => 'Penggorengan → IQF', 'draft' => $draftProses1, 'create_url' => route('wizard.proses1.create'), 'storage_key' => 'wizard_proses1_state_v1'],
                        ['id' => 'modalProses2', 'title' => 'Penggorengan → Roasting → IQF', 'draft' => $draftProses2, 'create_url' => route('wizard.proses2.create'), 'storage_key' => 'wizard_proses2_state_v1'],
                        ['id' => 'modalProses3', 'title' => 'Roasting → IQF', 'draft' => $draftProses3, 'create_url' => route('wizard.proses3.create'), 'storage_key' => 'wizard_proses3_state_v1'],
                        ['id' => 'modalProses4', 'title' => 'Tumbling → Aging', 'draft' => $draftProses4, 'create_url' => route('wizard.proses4.create'), 'storage_key' => 'wizard_proses4_state_v1'],
                    ];
                @endphp

                @foreach($drafts as $d)
                @if($d['draft'])
                <div class="modal fade" id="{{ $d['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title font-weight-bold">
                                    <i class="fas fa-exclamation-circle text-warning mr-2"></i>
                                    Data Belum Selesai Ditemukan
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>


<style>

/* LAYOUT */
.content-wrapper {
    padding: 2rem;
}

/* MAIN CARD */
.main-card {
    border-radius: 14px;
    border: none;
}

/* HEADER */
.title {
    font-weight: 600;
    font-size: 1.3rem;
}

.subtitle {
    font-size: 0.85rem;
    color: #888;
}

/* PROCESS CARD */
.process-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    height: 100%;
    border: 2px solid;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
}

.process-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}

/* ICON */
.icon-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    margin-bottom: 10px;
}

.arrow {
    margin: 0 8px;
    color: #999;
}

/* SOFT BACKGROUND */
.bg-primary-soft { background: rgba(0,123,255,0.08); }
.bg-warning-soft { background: rgba(255,193,7,0.15); }
.bg-info-soft    { background: rgba(23,162,184,0.12); }
.bg-success-soft { background: rgba(40,167,69,0.12); }
.bg-purple-soft  { background: rgba(111,66,193,0.10); }

/* PURPLE (dipakai kartu Verifikasi Berat Produk, biar tidak terkesan disabled seperti abu-abu) */
.border-purple { border-color: #6f42c1 !important; }
.text-purple   { color: #6f42c1 !important; }
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: #fff;
}
.btn-purple:hover,
.btn-purple:focus {
    background-color: #5c359c;
    border-color: #5c359c;
    color: #fff;
}
.btn-outline-purple {
    color: #6f42c1;
    border: 1px solid #6f42c1;
    background-color: transparent;
}
.btn-outline-purple:hover,
.btn-outline-purple:focus {
    background-color: #6f42c1;
    color: #fff;
}

/* TEXT */
.process-title {
    font-weight: 600;
    margin-bottom: 3px;
}

.process-subtitle {
    font-size: 12px;
    color: #777;
    margin-bottom: 10px;
}

/* LIST */
.process-list {
    text-align: left;
    font-size: 12px;
    color: #666;
    margin-bottom: 15px;
    padding-left: 16px;
}

/* BUTTON GROUP */
.btn-group-custom .btn {
    margin-bottom: 6px;
}

/* MOBILE */
@media(max-width:768px){
    .content-wrapper {
        padding: 1rem;
    }
}

</style>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Daftar semua storage key wizard
        const wizardKeys = [
            'wizard_proses1_state_v1',
            'wizard_proses2_state_v1',
            'wizard_proses3_state_v1',
            'wizard_proses4_state_v1'
        ];

        // SETIAP KALI HALAMAN PILIHAN DIBUKA:
        // Cek apakah ada data yang bukan milik user ini (tambahkan pengaman tambahan)
        const currentUserId = "{{ Auth::id() }}";
        
        wizardKeys.forEach(key => {
            try {
                const saved = sessionStorage.getItem(key);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    // Jika user_id di draf tidak cocok dengan user yang login, hapus paksa.
                    if (parsed.user_id && parsed.user_id !== currentUserId) {
                        sessionStorage.removeItem(key);
                        console.log('Cleared stuck session for key: ' + key);
                    }
                }
            } catch (e) {}
        });

        // Tambahkan event ke tombol Mulai yang tidak punya draf
        document.querySelectorAll('a.btn-block[href*="create"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('href');
                let key = '';
                if (url.includes('wizard-proses1')) key = 'wizard_proses1_state_v1';
                else if (url.includes('wizard-proses2')) key = 'wizard_proses2_state_v1';
                else if (url.includes('wizard-proses3')) key = 'wizard_proses3_state_v1';
                else if (url.includes('wizard-proses4')) key = 'wizard_proses4_state_v1';
                
                if (key) {
                    sessionStorage.removeItem(key);
                }
            });
        });
    });
</script>
@endpush
@endsection

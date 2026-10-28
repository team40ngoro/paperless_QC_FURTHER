@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-industry text-primary"></i> Verifikasi Persiapan Cold Mixing</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Verifikasi Persiapan Cold Mixing</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#forming" data-toggle="tab">Persiapan Cold Mixing</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="forming">
                      <form class="form-horizontal mb-4" id="main-chillroom-form" method="POST" action="{{ route('persiapan-bahan-forming.store') }}">
                        @csrf
                          <div id="form-container-chillroom">
                            <div class="chillroom-form">
                                <div class="form-group mb-2">
                                    <label>Shift</label>
                                    <select name="shift_id" class="form-control" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                           name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label>Jam <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                    @error('jam')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-2">
                                    <label>Nama Produk</label>
                                    <select name="id_produk" id="id_produk_select" class="form-control" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach($jenis_produk as $produk)
                                            <option value="{{ $produk->id }}" data-status-bahan="{{ strtolower($produk->status_bahan ?? '') }}" {{ (old('id_produk', $selectedProdukId ?? null) == $produk->id) ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kode Produksi</label>
                                    <input type="text" name="kode_produksi_emulsi" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kode Produksi Emulsi Oil</label>
                                    <div id="kode-emulsi-oil-container">
                                        <div class="input-group mb-2 kode-emulsi-oil-item">
                                            <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" placeholder="Masukkan kode produksi emulsi oil">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Pilih Nomor Formula</label>
                                    <select name="id_formula" id="id_formula_select" class="form-control" required>
                                        <option value="">Pilih Nomor Formula</option>
                                        {{-- Akan diisi AJAX --}}
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="berat_sampling">Gramase (gram)</label>
                                    <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling') }}"></select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Suhu Adonan (STD) <small class="text-muted">(filter by produk)</small></label>
                                    <select name="id_suhu_adonan" id="id_suhu_adonan_select" class="form-control" required>
                                        <option value="">Pilih Suhu Adonan</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Waktu Mulai Mixing</label>
                                        <input type="text" name="waktu_mulai_mixing" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Waktu Selesai Mixing</label>
                                        <input type="text" name="waktu_selesai_mixing" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Data Bahan Forming Berdasarkan Nomor Formula</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tabel-bahan-forming">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomor Formula</th>
                                                    <th>Nama RM</th>
                                                    <th>Berat RM</th>
                                                    <th>Kode Produksi Bahan</th>
                                                    <th>Suhu</th>
                                                    <th>Sensori</th>
                                                    <th>Keterangan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($bahan as $b)
                                                    <tr data-berat-rm="{{ $b->berat_rm }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $b->formula->nomor_formula ?? '-' }}</td>
                                                        <td>{{ $b->nama_rm }}</td>
                                                        <td class="text-center">{{ $b->berat_rm }}</td>
                                                        <td>
                                                            <input type="text" name="kode_produksi_bahan[]" value="" class="form-control" placeholder="Kode Produksi Bahan">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id_bahan_forming[]" value="{{ $b->id }}">
                                                            <input type="text" name="suhu[]" value="" class="form-control" placeholder="Suhu">
                                                        </td>
                                                        <td>
                                                            <select name="sensori[]" class="form-control">
                                                                <option value="OK">OK</option>
                                                                <option value="Tidak OK">Tidak OK</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="keterangan_bahan[]" value="" class="form-control" placeholder="Keterangan">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm hapus-baris-bahan" title="Hapus Bahan">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            @php
                                                $getFloatCreate = function($val) {
                                                    if (is_null($val) || $val === '' || is_array($val)) return 0.0;
                                                    $clean = preg_replace('/[^\d\.\-]/', '', str_replace(',', '.', (string)$val));
                                                    return is_numeric($clean) ? (float)$clean : 0.0;
                                                };
                                                $beratListCreate = collect($bahan)->map(fn($b) => $getFloatCreate($b->berat_rm ?? 0));
                                                $totalBeratCreate = $beratListCreate->sum();
                                                $avgBeratCreate   = $beratListCreate->count() > 0 ? $beratListCreate->avg() : 0;
                                            @endphp
                                            <tfoot class="bg-light font-weight-bold" id="tfoot-bahan-forming">
                                                <tr>
                                                    <td colspan="3" class="text-right">Total Berat RM:</td>
                                                    <td class="text-center text-primary" id="total-berat-rm-create">{{ number_format($totalBeratCreate, 3) }}</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <!-- <tr>
                                                    <td colspan="3" class="text-right">Rata-Rata Berat RM:</td>
                                                    <td class="text-center text-success" id="avg-berat-rm-create">{{ number_format($avgBeratCreate, 3) }}</td>
                                                    <td colspan="5"></td>
                                                </tr> -->
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-3">Suhu Aktual</h5>
                                    </div>
                                    @for($i=1;$i<=5;$i++)
                                        <div class="form-group col-md-2">
                                            <label>Aktual {{ $i }}</label>
                                            <input type="number" step="0.1" name="aktual_suhu_{{ $i }}" class="form-control" placeholder="0.0" id="aktual_suhu_{{ $i }}">
                                        </div>
                                    @endfor
                                    <div class="form-group col-md-2">
                                        <label>Hasil Suhu Aktual</label>
                                        <input type="number" step="0.01" name="total_aktual_suhu" class="form-control" id="total_aktual_suhu" placeholder="Otomatis terisi" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kondisi Hasil Penggilingan</label>
                                    <select name="kondisi" class="form-control">
                                        <option value="✔">✔ OK</option>
                                        <option value="✘">✘ Tidak OK</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Kondisi Hasil Pencampuran</label>
                                    <select name="kondisi_hasil_pencampuran" class="form-control">
                                        <option value="✔">✔ OK</option>
                                        <option value="✘">✘ Tidak OK</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Produk Rework <small class="text-muted">(opsional)</small></label>
                                    <select name="produk_rework_id" id="produk_rework_id_select"
                                            class="form-control select2-rework"
                                            data-placeholder="Pilih produk rework...">
                                        <option value="">-</option>
                                    </select>
                                    <small class="text-muted">Pilih Nama Produk terlebih dahulu untuk memuat opsi</small>
                                </div>
                                <div class="form-group mb-2">
                                    <label>Rework (kg)</label>
                                    <input type="text" name="rework" class="form-control">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Catatan</label>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </div>
                                <hr>
                            </div>
                          </div>
                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-save mr-1"></i>Simpan Data</button>
                        <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-md ml-2">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </form>
                  </div>
                </div><!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
<script>
let isRedirecting = false; // scope script-level agar dapat diakses semua DOMContentLoaded

document.addEventListener('DOMContentLoaded', function () {
    function hitungTotalSuhuAktual() {
        let total = 0;
        let count = 0;
        for (let i = 1; i <= 5; i++) {
            let input = document.getElementById('aktual_suhu_' + i);
            if (input && input.value !== '') {
                let val = parseFloat(input.value);
                if (!isNaN(val)) {  
                    total += val;
                    count++;
                }
            }
        }
        let avg = count > 0 ? (total / count) : 0;
        document.getElementById('total_aktual_suhu').value = avg.toFixed(2);
    }
    
    for (let i = 1; i <= 5; i++) {
        let input = document.getElementById('aktual_suhu_' + i);
        if (input) {
            input.addEventListener('input', hitungTotalSuhuAktual);
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    let kodeEmulsiOilContainer = document.getElementById('kode-emulsi-oil-container');
    
    // Event delegation untuk tombol add dan remove
    kodeEmulsiOilContainer.addEventListener('click', function(e) {
        if (e.target.closest('.add-kode-emulsi-oil')) {
            // Clone item pertama
            let firstItem = kodeEmulsiOilContainer.querySelector('.kode-emulsi-oil-item');
            let newItem = firstItem.cloneNode(true);
            
            // Reset value input
            newItem.querySelector('input').value = '';
            
            // Pastikan baris baru tidak punya tombol tambah (hanya tombol hapus)
            let addBtn = newItem.querySelector('.add-kode-emulsi-oil');
            if (addBtn) {
                addBtn.style.display = 'none';
            }
            
            // Tambahkan ke container
            kodeEmulsiOilContainer.appendChild(newItem);
            
            // Update visibility tombol remove
            updateButtonsVisibility();
        }
        
        if (e.target.closest('.remove-kode-emulsi-oil')) {
            let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
            if (items.length > 1) {
                e.target.closest('.kode-emulsi-oil-item').remove();
                updateButtonsVisibility();
            }
        }
    });
    
    function updateButtonsVisibility() {
        let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item');
        
        items.forEach(function(item, index) {
            let addBtn = item.querySelector('.add-kode-emulsi-oil');
            let removeBtn = item.querySelector('.remove-kode-emulsi-oil');

            if (addBtn) {
                addBtn.style.display = (index === 0) ? 'inline-block' : 'none';
            }

            if (removeBtn) {
                removeBtn.style.display = (items.length <= 1) ? 'none' : 'inline-block';
            }
        });
    }
    
    // Initial setup
    updateButtonsVisibility();
});

document.addEventListener('DOMContentLoaded', function() {
    const produkSelect = document.getElementById('id_produk_select');
    if (!produkSelect) return;

    const formulaSelect = document.getElementById('id_formula_select');
    const suhuSelect = document.getElementById('id_suhu_adonan_select');

    // Jika masuk dari redirect dengan query ?id_produk=..., paksa select agar tampil di UI (termasuk Select2)
    // CATATAN: hanya update tampilan Select2 saja (change.select2), JANGAN trigger 'change' penuh
    // karena handleProdukChange() sudah dipanggil eksplisit di bawah — dua kali load = duplikat opsi.
    try {
        const url = new URL(window.location.href);
        const preselectProduk = url.searchParams.get('id_produk');
        if (preselectProduk && produkSelect.value !== String(preselectProduk)) {
            const hasOption = Array.from(produkSelect.options).some(o => String(o.value) === String(preselectProduk));
            if (hasOption) {
                produkSelect.value = String(preselectProduk);
                // Hanya update tampilan Select2, tidak trigger handler
                if (window.jQuery) {
                    window.jQuery(produkSelect).trigger('change.select2');
                }
            }
        }
    } catch (e) {}

    async function loadFormulaFormingByProduk(idProduk) {
        if (!formulaSelect) return;
        formulaSelect.innerHTML = '<option value="">Pilih Nomor Formula</option>';
        if (!idProduk) return;

        const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        (data || []).forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nomor_formula;
            formulaSelect.appendChild(opt);
        });
    }

    async function loadSuhuAdonanByProduk(idProduk) {
        if (!suhuSelect) return;
        suhuSelect.innerHTML = '<option value="">Pilih Suhu Adonan</option>';
        if (!idProduk) return;

        const res = await fetch("{{ url('/qc-sistem/get-suhu-adonan-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        (data || []).forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.std_suhu ?? item.suhu ?? item.id;
            suhuSelect.appendChild(opt);
        });
    }

    // Inisialisasi Select2 rework — harus dilakukan SEBELUM handleProdukChange dipanggil
    function initReworkSelect2() {
        if (!window.jQuery) return;
        const $ = window.jQuery;
        if ($.fn.select2) {
            $('#produk_rework_id_select').select2({
                placeholder: 'Pilih produk rework...',
                allowClear: true,
                width: '100%',
            });
        }
    }

    async function loadReworkProdukByProduk(idProduk) {
        if (!window.jQuery || !window.jQuery.fn.select2) return;
        const $ = window.jQuery;
        const $el = $('#produk_rework_id_select');
        if (!$el.length) return;

        // Hancurkan instance Select2 lama hanya jika sudah pernah diinit
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.empty();

        if (!idProduk) {
            // Reinit dengan hanya opsi '-'
            $el.append(new Option('-', '', false, false));
            $el.select2({ placeholder: 'Pilih produk rework...', allowClear: true, width: '100%' });
            return;
        }

        try {
            const res = await fetch("{{ url('/qc-sistem/ajax/rework-produk-by-produk') }}/" + encodeURIComponent(idProduk), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) {
                console.warn('[Rework AJAX] Response not OK:', res.status, res.url);
                // Tetap reinit Select2 meski gagal
                $el.select2({ placeholder: 'Pilih produk rework...', allowClear: true, width: '100%' });
                return;
            }
            const data = await res.json();
            console.log('[Rework AJAX] Data diterima:', data);

            // Selalu tambahkan opsi '-' di posisi pertama agar field bisa dikosongkan
            $el.append(new Option('-', '', false, false));

            // Append semua opsi ke native <select> terlebih dahulu
            (data || []).forEach(function(p) {
                $el.append(new Option(p.nama_produk, p.id, false, false));
            });
        } catch (e) {
            console.error('[Rework AJAX] Error:', e);
        }

        // Selalu reinit Select2 setelah opsi ditambahkan agar tampil dengan benar
        $el.select2({
            placeholder: 'Pilih produk rework...',
            allowClear: true,
            width: '100%',
        });
    }

    async function produkHasNonFormingFormula(idProduk) {
        if (!idProduk) return false;
        const res = await fetch("{{ url('/qc-sistem/ajax/nomor-formula-non-forming-by-produk') }}/" + encodeURIComponent(idProduk), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) return false;
        const data = await res.json();
        return Array.isArray(data) && data.length > 0;
    }

    function redirectIfNonForming() {
        if (isRedirecting) return;
        const selectedOption = produkSelect.selectedOptions ? produkSelect.selectedOptions[0] : produkSelect.options[produkSelect.selectedIndex];
        const rawStatusBahan = (selectedOption?.getAttribute('data-status-bahan') || '').toString().trim().toLowerCase();
        const normalizedStatus = rawStatusBahan.replace(/[\s_]+/g, '-');
        const idProduk = produkSelect.value;

        const isNonForming = normalizedStatus === 'non-forming' || normalizedStatus.startsWith('non-') || normalizedStatus === 'nonforming' || rawStatusBahan.startsWith('non');

        if (idProduk && isNonForming) {
            const url = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(idProduk);
            const path = window.location.pathname || '';
            if (path.indexOf('/qc-sistem/persiapan-bahan-non-forming') !== -1) {
                return;
            }
            isRedirecting = true;
            window.location.href = url;
            return;
        }

        // Fallback: hanya jika status_bahan kosong/unknown, tapi master non-forming-nya ada
        if (idProduk && !rawStatusBahan) {
            produkHasNonFormingFormula(idProduk)
                .then(function(hasNonForming) {
                    if (hasNonForming) {
                        const url = "{{ route('persiapan-bahan-non-forming.create') }}" + '?id_produk=' + encodeURIComponent(idProduk);
                        const path = window.location.pathname || '';
                        if (path.indexOf('/qc-sistem/persiapan-bahan-non-forming') !== -1) {
                            return;
                        }
                        isRedirecting = true;
                        window.location.href = url;
                    }
                })
                .catch(function() {});
        }
    }

    async function handleProdukChange() {
        const idProduk = produkSelect.value;

        // Sinkronkan query string id_produk agar URL selalu sesuai pilihan produk
        try {
            const url = new URL(window.location.href);
            if (idProduk) {
                url.searchParams.set('id_produk', idProduk);
            } else {
                url.searchParams.delete('id_produk');
            }
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}

        // Cek redirect terlebih dulu (non-forming). Jangan tergantung hasil AJAX forming.
        redirectIfNonForming();

        // Load data forming dengan aman (jika endpoint error / bukan JSON, tidak mematikan script)
        try {
            await Promise.all([
                loadFormulaFormingByProduk(idProduk),
                loadSuhuAdonanByProduk(idProduk),
                loadReworkProdukByProduk(idProduk),
            ]);
        } catch (e) {
            // ignore
        }
    }

    function getFloatJS(val) {
        if (val === null || val === undefined || val === '') return 0;
        let str = String(val).replace(/,/g, '.');
        let clean = str.replace(/[^\d.-]/g, '');
        let num = parseFloat(clean);
        return isNaN(num) ? 0 : num;
    }

    function hitungTotalRataRataBeratRM() {
        const table = document.getElementById('tabel-bahan-forming');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        let sum = 0;
        let count = 0;
        rows.forEach(row => {
            if (row.cells.length < 4) return;
            let valAttr = row.getAttribute('data-berat-rm');
            let textVal = row.cells[3] ? row.cells[3].innerText.trim() : '0';
            let val = (valAttr !== null && valAttr !== '') ? valAttr : textVal;
            let num = getFloatJS(val);
            sum += num;
            count++;
        });

        let avg = count > 0 ? sum / count : 0;

        const totalEl = document.getElementById('total-berat-rm-create');
        const avgEl = document.getElementById('avg-berat-rm-create');

        if (totalEl) totalEl.textContent = sum.toFixed(3);
        if (avgEl) avgEl.textContent = avg.toFixed(3);
    }

    // Row deletion event delegation
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.hapus-baris-bahan');
        if (btn) {
            const tr = btn.closest('tr');
            if (tr) {
                tr.remove();
                // Update line numbers
                const table = document.getElementById('tabel-bahan-forming');
                if (table) {
                    table.querySelectorAll('tbody tr').forEach((row, i) => {
                        if (row.cells[0]) row.cells[0].textContent = i + 1;
                    });
                }
                hitungTotalRataRataBeratRM();
            }
        }
    });

    if (formulaSelect) {
        formulaSelect.addEventListener('change', async function() {
            const formulaId = this.value;
            const tbody = document.querySelector('#tabel-bahan-forming tbody');
            if (!tbody) return;

            if (!formulaId) {
                tbody.innerHTML = '';
                hitungTotalRataRataBeratRM();
                return;
            }

            try {
                const res = await fetch("{{ url('/qc-sistem/ajax/bahan-forming-by-formula') }}/" + encodeURIComponent(formulaId), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                tbody.innerHTML = '';
                (data || []).forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-berat-rm', item.berat_rm ?? 0);
                    tr.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>${item.formula?.nomor_formula ?? '-'}</td>
                        <td>${item.nama_rm ?? ''}</td>
                        <td class="text-center">${item.berat_rm ?? 0}</td>
                        <td><input type="text" name="kode_produksi_bahan[]" value="" class="form-control" placeholder="Kode Produksi Bahan"></td>
                        <td>
                            <input type="hidden" name="id_bahan_forming[]" value="${item.id}">
                            <input type="text" name="suhu[]" value="" class="form-control" placeholder="Suhu">
                        </td>
                        <td>
                            <select name="sensori[]" class="form-control">
                                <option value="OK">OK</option>
                                <option value="Tidak OK">Tidak OK</option>
                            </select>
                        </td>
                        <td><input type="text" name="keterangan_bahan[]" value="" class="form-control" placeholder="Keterangan"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm hapus-baris-bahan" title="Hapus Bahan">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                hitungTotalRataRataBeratRM();
            } catch (e) {
                console.error('Error fetching bahan forming by formula', e);
            }
        });
    }

    // Init Select2 rework sebelum event listener dan pemanggilan awal
    initReworkSelect2();

    // PENTING: Select2 hanya fire jQuery change events, bukan native DOM events.
    // Harus pakai jQuery .on('change') agar handler terpanggil saat user memilih via Select2.
    if (window.jQuery) {
        window.jQuery(produkSelect).on('change', handleProdukChange);
    } else {
        produkSelect.addEventListener('change', handleProdukChange);
    }

    handleProdukChange();
    hitungTotalRataRataBeratRM();
});
</script>
@endsection
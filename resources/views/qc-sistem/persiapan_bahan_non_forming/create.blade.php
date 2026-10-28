@extends('layouts.app')

@section('container')
<div class="content-wrapper">
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
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#non-forming" data-toggle="tab">Persiapan Cold Mixing</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="non-forming">
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form class="form-horizontal mb-4" method="POST" action="{{ route('persiapan-bahan-non-forming.store') }}">
                                        @csrf

                                        <div class="form-group mb-2">
                                            <label>Shift</label>
                                            <select name="shift_id" class="form-control" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift }}</option>
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
                                            <select name="id_produk" id="id_produk_select_non_forming" class="form-control" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($produks as $produk)
                                                    <option value="{{ $produk->id }}" data-status-bahan="{{ strtolower($produk->status_bahan ?? '') }}" {{ (old('id_produk', $selectedProdukId) == $produk->id) ? 'selected' : '' }}>{{ $produk->nama_produk }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Kode Produksi</label>
                                            <input type="text" name="kode_produksi" class="form-control @error('kode_produksi') is-invalid @enderror" value="{{ old('kode_produksi') }}">
                                            @error('kode_produksi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Kode Produksi Emulsi Oil</label>
                                            <div id="kode-emulsi-oil-container-non-forming">
                                                <div class="input-group mb-2 kode-emulsi-oil-item-non-forming">
                                                    <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" placeholder="Masukkan kode produksi emulsi oil" value="{{ is_array(old('kode_produksi_emulsi_oil')) ? (old('kode_produksi_emulsi_oil')[0] ?? '') : '' }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil-non-forming">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil-non-forming" style="display: none;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @if(is_array(old('kode_produksi_emulsi_oil')))
                                                    @foreach(old('kode_produksi_emulsi_oil') as $idx => $val)
                                                        @if($idx > 0)
                                                            <div class="input-group mb-2 kode-emulsi-oil-item-non-forming">
                                                                <input type="text" name="kode_produksi_emulsi_oil[]" class="form-control" placeholder="Masukkan kode produksi emulsi oil" value="{{ $val }}">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-success btn-sm add-kode-emulsi-oil-non-forming" style="display: none;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-kode-emulsi-oil-non-forming">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            @error('kode_produksi_emulsi_oil')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @error('kode_produksi_emulsi_oil.*')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Waktu Mulai Mixing</label>
                                                <input type="text" name="waktu_mulai_mixing" class="form-control @error('waktu_mulai_mixing') is-invalid @enderror" value="{{ old('waktu_mulai_mixing') }}">
                                                @error('waktu_mulai_mixing')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Waktu Selesai Mixing</label>
                                                <input type="text" name="waktu_selesai_mixing" class="form-control @error('waktu_selesai_mixing') is-invalid @enderror" value="{{ old('waktu_selesai_mixing') }}">
                                                @error('waktu_selesai_mixing')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Pilih Nomor Formula</label>
                                            <select name="id_no_formula_non_forming" id="id_formula_select_non_forming" class="form-control" required>
                                                <option value="">Pilih Nomor Formula</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label for="berat_sampling">Gramase (gram)</label>
                                            <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling') }}"></select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Suhu Adonan (STD) <small class="text-muted">(filter by produk)</small></label>
                                            <select name="id_suhu_adonan" id="id_suhu_adonan_select_non_forming" class="form-control">
                                                <option value="">Pilih Suhu Adonan</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Data Bahan Non Forming Berdasarkan Nomor Formula</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="tabel-bahan-non-forming">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nomor Formula</th>
                                                            <th>Nama RM</th>
                                                            <th class="text-center">Berat RM</th>
                                                            <th>Kode Produksi Bahan</th>
                                                            <th>Suhu</th>
                                                            <th>Sensori</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="bg-light font-weight-bold" id="tfoot-bahan-non-forming">
                                                        <tr>
                                                            <td colspan="3" class="text-right">Total Berat RM:</td>
                                                            <td class="text-center text-primary" id="total-berat-rm-non-forming-create">0.000</td>
                                                            <td colspan="4"></td>
                                                        </tr>
                                                        <!-- <tr>
                                                            <td colspan="3" class="text-right">Rata-Rata Berat RM:</td>
                                                            <td class="text-center text-success" id="avg-berat-rm-non-forming-create">0.000</td>
                                                            <td colspan="4"></td>
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
                                                    <input type="number" step="0.1" name="aktual_suhu_{{ $i }}" class="form-control" placeholder="0.0" id="aktual_suhu_{{ $i }}" value="{{ old('aktual_suhu_'.$i) }}">
                                                </div>
                                            @endfor
                                            <div class="form-group col-md-2">
                                                <label>Hasil Suhu Aktual</label>
                                                <input type="number" step="0.01" name="total_aktual_suhu" class="form-control" id="total_aktual_suhu" placeholder="Otomatis terisi" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Kondisi</label>
                                            <select name="kondisi" class="form-control">
                                                <option value="✔" {{ old('kondisi') == '✔' ? 'selected' : '' }}>✔ OK</option>
                                                <option value="✘" {{ old('kondisi') == '✘' ? 'selected' : '' }}>✘ Tidak OK</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Rework (kg)</label>
                                            <input type="text" name="rework" class="form-control" value="{{ old('rework') }}">
                                        </div>

                                        <div class="form-group mb-2">
                                            <label>Catatan</label>
                                            <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-save mr-1"></i>Simpan Data</button>
                                        <a href="{{ route('persiapan-bahan-forming.index') }}" class="btn btn-secondary btn-md ml-2">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Kembali
                                        </a>
                                    </form>
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
        const totalEl = document.getElementById('total_aktual_suhu');
        if (totalEl) {
            totalEl.value = avg.toFixed(2);
        }
    }

    for (let i = 1; i <= 5; i++) {
        let input = document.getElementById('aktual_suhu_' + i);
        if (input) {
            input.addEventListener('input', hitungTotalSuhuAktual);
        }
    }

    hitungTotalSuhuAktual();

    const baseUrl = "{{ url('/qc-sistem') }}";

    const produkSelect = document.getElementById('id_produk_select_non_forming');
    const formulaSelect = document.getElementById('id_formula_select_non_forming');
    const suhuAdonanSelect = document.getElementById('id_suhu_adonan_select_non_forming');
    const tbody = document.querySelector('#tabel-bahan-non-forming tbody');

    const kodeEmulsiOilContainer = document.getElementById('kode-emulsi-oil-container-non-forming');
    if (kodeEmulsiOilContainer) {
        kodeEmulsiOilContainer.addEventListener('click', function(e) {
            if (e.target.closest('.add-kode-emulsi-oil-non-forming')) {
                let firstItem = kodeEmulsiOilContainer.querySelector('.kode-emulsi-oil-item-non-forming');
                if (!firstItem) return;
                let newItem = firstItem.cloneNode(true);
                let input = newItem.querySelector('input');
                if (input) input.value = '';

                let addBtn = newItem.querySelector('.add-kode-emulsi-oil-non-forming');
                if (addBtn) addBtn.style.display = 'none';

                let removeBtn = newItem.querySelector('.remove-kode-emulsi-oil-non-forming');
                if (removeBtn) removeBtn.style.display = 'inline-block';

                kodeEmulsiOilContainer.appendChild(newItem);
                updateButtonsVisibility();
            }

            if (e.target.closest('.remove-kode-emulsi-oil-non-forming')) {
                let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item-non-forming');
                if (items.length > 1) {
                    e.target.closest('.kode-emulsi-oil-item-non-forming').remove();
                    updateButtonsVisibility();
                }
            }
        });

        function updateButtonsVisibility() {
            let items = kodeEmulsiOilContainer.querySelectorAll('.kode-emulsi-oil-item-non-forming');
            items.forEach(function(item, index) {
                let addBtn = item.querySelector('.add-kode-emulsi-oil-non-forming');
                let removeBtn = item.querySelector('.remove-kode-emulsi-oil-non-forming');

                if (addBtn) {
                    addBtn.style.display = (index === 0) ? 'inline-block' : 'none';
                }
                if (removeBtn) {
                    removeBtn.style.display = (items.length <= 1) ? 'none' : 'inline-block';
                }
            });
        }

        updateButtonsVisibility();
    }

    function resetSelect(selectEl, placeholder) {
        selectEl.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        selectEl.appendChild(opt);
    }

    function redirectIfForming() {
        const selectedOption = produkSelect.selectedOptions ? produkSelect.selectedOptions[0] : produkSelect.options[produkSelect.selectedIndex];
        const rawStatusBahan = (selectedOption?.getAttribute('data-status-bahan') || '').toString().trim().toLowerCase();
        const normalizedStatus = rawStatusBahan.replace(/[\s_]+/g, '-');
        const idProduk = produkSelect.value;

        const isForming = normalizedStatus === 'forming' || normalizedStatus.indexOf('form') === 0;
        if (idProduk && isForming) {
            const url = "{{ route('persiapan-bahan-forming.create') }}" + '?id_produk=' + encodeURIComponent(idProduk);
            const path = window.location.pathname || '';
            if (path.indexOf('/qc-sistem/persiapan-bahan-forming') !== -1) {
                return true;
            }
            window.location.href = url;
            return true;
        }
        return false;
    }

    function gotoWithProdukParam(routeUrl, idProduk) {
        if (!idProduk) {
            window.location.href = routeUrl;
            return;
        }
        window.location.href = routeUrl + '?id_produk=' + encodeURIComponent(idProduk);
    }

    function syncProdukToUrl(idProduk) {
        try {
            const url = new URL(window.location.href);
            if (idProduk) {
                url.searchParams.set('id_produk', idProduk);
            } else {
                url.searchParams.delete('id_produk');
            }
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>'"]/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;','\'':'&#39;'}[c]) || c;
        });
    }

    async function loadFormulas(idProduk) {
        resetSelect(formulaSelect, 'Pilih Nomor Formula');
        if (!idProduk) return;

        try {
            const res = await fetch(baseUrl + '/ajax/nomor-formula-non-forming-by-produk/' + idProduk);
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            const data = await res.json();

            // Jika produk tidak punya formula non-forming, jangan redirect (bisa memicu loop). Beri info dan stop.
            if (!Array.isArray(data) || data.length === 0) {
                alert('Produk ini tidak memiliki Formula Non Forming. Silakan pilih produk lain.');
                return;
            }

            data.forEach(function (item) {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.nomor_formula;
                formulaSelect.appendChild(opt);
            });
        } catch (e) {
            alert('Gagal mengambil data Formula Non Forming. Silakan coba lagi atau pilih produk lain.');
        }
    }

    async function loadSuhuAdonan(idProduk) {
        resetSelect(suhuAdonanSelect, 'Pilih Suhu Adonan');
        if (!idProduk) return;

        const res = await fetch(baseUrl + '/get-suhu-adonan-by-produk/' + idProduk);
        const data = await res.json();
        data.forEach(function (item) {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.std_suhu ?? item.suhu_adonan ?? item.nama_suhu ?? item.nama ?? item.id;
            suhuAdonanSelect.appendChild(opt);
        });
    }

    function getFloatJS(val) {
        if (val === null || val === undefined || val === '') return 0;
        let str = String(val).replace(/,/g, '.');
        let clean = str.replace(/[^\d.-]/g, '');
        let num = parseFloat(clean);
        return isNaN(num) ? 0 : num;
    }

    function hitungTotalRataRataBeratRM() {
        const table = document.getElementById('tabel-bahan-non-forming');
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

        const totalEl = document.getElementById('total-berat-rm-non-forming-create');
        const avgEl = document.getElementById('avg-berat-rm-non-forming-create');

        if (totalEl) totalEl.textContent = sum.toFixed(3);
        if (avgEl) avgEl.textContent = avg.toFixed(3);
    }

    async function loadBahanByFormula(idFormula) {
        tbody.innerHTML = '';
        if (!idFormula) {
            hitungTotalRataRataBeratRM();
            return;
        }

        const res = await fetch(baseUrl + '/ajax/bahan-non-forming-by-formula/' + idFormula);
        const data = await res.json();

        const nomorFormulaText = formulaSelect.options[formulaSelect.selectedIndex]?.text || '-';

        data.forEach(function (b, idx) {
            const tr = document.createElement('tr');
            tr.setAttribute('data-berat-rm', b.berat_rm ?? 0);
            tr.innerHTML = `
                <td>${idx + 1}</td>
                <td>${escapeHtml(nomorFormulaText)}</td>
                <td>${escapeHtml(b.nama_rm)}</td>
                <td class="text-center">${escapeHtml(b.berat_rm)}</td>
                <td>
                    <input type="hidden" name="id_bahan_non_forming[]" value="${escapeHtml(b.id)}">
                    <input type="text" name="kode_produksi_bahan[]" class="form-control" placeholder="Kode Produksi Bahan">
                </td>
                <td>
                    <input type="text" name="suhu[]" class="form-control" placeholder="Suhu">
                </td>
                <td>
                    <select name="sensori[]" class="form-control">
                        <option value="OK">OK</option>
                        <option value="Tidak OK">Tidak OK</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="keterangan_bahan[]" class="form-control" placeholder="Keterangan">
                </td>
            `;
            tbody.appendChild(tr);
        });
        hitungTotalRataRataBeratRM();
    }

    async function handleProdukChange(idProduk) {
        syncProdukToUrl(idProduk);

        if (!idProduk) {
            resetSelect(formulaSelect, 'Pilih Nomor Formula');
            resetSelect(suhuAdonanSelect, 'Pilih Suhu Adonan');
            tbody.innerHTML = '';
            return;
        }

        // Kalau produk forming, langsung redirect
        if (redirectIfForming()) {
            return;
        }

        // Update via AJAX tanpa refresh
        resetSelect(formulaSelect, 'Pilih Nomor Formula');
        resetSelect(suhuAdonanSelect, 'Pilih Suhu Adonan');
        tbody.innerHTML = '';

        await loadFormulas(idProduk);
        await loadSuhuAdonan(idProduk);
    }

    produkSelect.addEventListener('change', function () {
        handleProdukChange(this.value);
    });

    // Jika select pakai Select2, event change kadang tidak terpicu sesuai ekspektasi.
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
        window.jQuery(document).on('select2:select', '#id_produk_select_non_forming', function () {
            handleProdukChange(window.jQuery(this).val());
        });
    }

    formulaSelect.addEventListener('change', function () {
        loadBahanByFormula(this.value);
    });

    const preselectedProduk = produkSelect.value;
    if (preselectedProduk) {
        if (redirectIfForming()) {
            return;
        }

        loadFormulas(preselectedProduk).then(function () {
            const oldFormula = @json(old('id_no_formula_non_forming'));
            if (oldFormula) {
                formulaSelect.value = String(oldFormula);
                loadBahanByFormula(oldFormula);
            }
        });
        loadSuhuAdonan(preselectedProduk).then(function () {
            const oldSuhu = @json(old('id_suhu_adonan'));
            if (oldSuhu) {
                suhuAdonanSelect.value = String(oldSuhu);
            }
        });
    }
});
</script>
@endsection

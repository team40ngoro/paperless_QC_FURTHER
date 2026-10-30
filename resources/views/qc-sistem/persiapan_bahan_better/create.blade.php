@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-history text-purple"></i> Verifikasi Pembuatan Better</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Persiapan Bahan Better</li>
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
                  <li class="nav-item"><a class="nav-link active" href="#better" data-toggle="tab">Pembuatan Better</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-6">
                          <div class="info-box bg-success">
                              <span class="info-box-icon">
                                  <i class="fas fa-check" style="font-size: 2rem;"></i>
                              </span>
                              <div class="info-box-content">
                                  <span class="info-box-text">Tanda Centang (✓)</span>
                                  <span class="info-box-number">tidak ada  penyimpangan warna, aroma (aroma segar, tidak ada bau busuk)</span>
                                  <div class="progress">
                                      <div class="progress-bar bg-success" style="width: 100%"></div>
                                  </div>
                                  <!-- <span class="progress-description">
                                      Kondisi baik dan memenuhi standar
                                  </span> -->
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="info-box bg-danger">
                              <span class="info-box-icon">
                                  <i class="fas fa-times" style="font-size: 2rem;"></i>
                              </span>
                              <div class="info-box-content">
                                  <span class="info-box-text">Tanda Silang (✗)</span>
                                  <span class="info-box-number">Sensori Tidak Sesuai</span>
                                  <div class="progress">
                                      <div class="progress-bar bg-danger" style="width: 100%"></div>
                                  </div>
                                  <!-- <span class="progress-description">
                                      Kondisi tidak sesuai standar
                                  </span> -->
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="alert alert-info mt-2">
                      <i class="fas fa-lightbulb"></i>
                      <strong>Petunjuk:</strong> Pilih tanda centang (✓) jika kondisi sesuai standar, dan tanda silang (✗) jika ditemukan ketidaksesuaian yang perlu diperbaiki.
                  </div>
              </div>
              <div class="card-body">
                <div class="tab-pane active" id="better">
                  <form action="{{ route('persiapan-bahan-better.store') }}" method="POST">
                    @csrf
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
                        <div class="mb-3">
                            <label>Nama Produk</label>
                            <select name="id_produk" id="id_produk_select_better" class="form-control" required>
                                <option value="">Pilih Produk</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Better</label>
                            <select name="id_better" id="id_better_select_better" class="form-control" required>
                                <option value="">Pilih Better</option>
                                {{-- Akan diisi AJAX --}}
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="berat_sampling">Gram</label>
                            <select name="gramase" id="nilai_select_berat_sampling" class="form-control select2" data-nilai-berat data-selected="{{ old('berat_sampling') }}"></select>
                        </div>
                        <!-- <div class="form-group mb-3">
                            <label>Gramasi <span class="text-danger">*</span> <small class="text-muted">(gram)</small></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="gramase" placeholder="Masukkan Gramasi dalam gram" required>
                        </div> -->
                        <div class="alert alert-warning py-2 mb-3" role="alert">
                            <i class="fas fa-exclamation-circle text-dark"></i> <span class="text-dark"><strong>Catatan:</strong> Silakan pilih <strong>Nama Produk & Jenis Better</strong> terlebih dahulu agar tabel formula/bahan dapat muncul.</span>
                        </div>
                        <div class="form-group mb-3">
                            <label>Kode Produksi Larutan Batter</label>
                            <input type="text" name="kode_produksi_produk" class="form-control" placeholder="Masukkan Kode Produksi" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Suhu Air (0-10) (°C)</label>
                            <input type="number" step="0.01" class="form-control" id="global_suhu_air" placeholder="Masukkan Suhu Air" required>
                        </div>

                        <div id="better-input-table-wrapper" style="display:none;" class="mb-3">
                            <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%">Nama Bahan</th>
                                        <th style="width: 30%">Kode Produksi <strong>(-)</strong></th>
                                        <th style="width: 30%">Berat (kg)</th>
                                    </tr>
                                </thead>
                                <tbody id="better-input-rows"></tbody>
                            </table>
                            </div>
                        </div>
                        
                        <div id="std-aktual-table-better"></div>
                        
                        <div id="sensori-wrapper" style="display:none;" class="form-group mb-3 mt-3">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="mb-0 font-weight-bold">Sensori</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control form-control-sm" id="global_sensori">
                                        <option value="✔">✔ OK</option>
                                        <option value="✘">✘ Tidak OK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                        <a href="{{ route('persiapan-bahan-better.index') }}" class="btn btn-secondary ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </form>
                </div> 
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
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
  
@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for gramase field
    $('#nilai_select_berat_sampling').select2({
        placeholder: 'Pilih Gramase',
        allowClear: true
    });
    
    // Populate gramase dropdown when product is selected
    $('#id_produk_select_better').on('change', function() {
        const produkId = $(this).val();
        const $gramaseSelect = $('#nilai_select_berat_sampling');
        
        // Reset gramase dropdown
        $gramaseSelect.empty();
        $gramaseSelect.append('<option value="">Pilih Gramase</option>');
        
        if (produkId && typeof nilaiBerat !== 'undefined' && nilaiBerat.length > 0) {
            $.each(nilaiBerat, function(i, val) {
                if (val !== '-') { // Skip the '-' value
                    $gramaseSelect.append('<option value="' + val + '">' + val + ' gram</option>');
                }
            });
            
            // Trigger select2 to refresh
            $gramaseSelect.trigger('change');
        }
    });

    // Load better options when product is selected
    $('#id_produk_select_better').on('change', function() {
        const produkId = $(this).val();
        const $betterSelect = $('#id_better_select_better');
        
        $betterSelect.empty();
        $betterSelect.append('<option value="">Pilih Better</option>');
        
        if (!produkId) return;
        
        $.ajax({
            url: '{{ route("ajax.better-by-produk-better") }}',
            type: 'GET',
            data: { id_produk: produkId },
            success: function(response) {
                if (Array.isArray(response)) {
                    $.each(response, function(i, better) {
                        $betterSelect.append('<option value="' + better.id + '">' + better.nama_better + '</option>');
                    });
                } else if (response.success && response.data) {
                    $.each(response.data, function(i, better) {
                        $betterSelect.append('<option value="' + better.id + '">' + better.nama_better + '</option>');
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading better options:', xhr);
            }
        });
    });

    // Load formula/bahan table when both product and better are selected
    $('#id_better_select_better').on('change', function() {
        const produkId = $('#id_produk_select_better').val();
        const betterId = $(this).val();
        
        if (!produkId || !betterId) {
            $('#better-input-table-wrapper').hide();
            $('#std-aktual-table-better').empty();
            $('#sensori-wrapper').hide();
            return;
        }
        
        // Load formula data
        $.ajax({
            url: '/api/get-formula-better',
            type: 'GET',
            data: { 
                id_produk: produkId,
                id_better: betterId
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderBetterFormula(response.data);
                    renderStdAktualTable(response.data.std_data || []);
                }
            },
            error: function(xhr) {
                console.error('Error loading formula:', xhr);
            }
        });
    });

    function renderBetterFormula(data) {
        const $tbody = $('#better-input-rows');
        $tbody.empty();
        
        if (data.formula && Array.isArray(data.formula)) {
            $.each(data.formula, function(i, item) {
                const row = `
                    <tr>
                        <td>
                            <textarea class="form-control form-control-sm" readonly rows="1" style="resize:none;">${item.nama_formula || '-'}</textarea>
                            <input type="hidden" name="better_rows[${i}][master_nama_formula_better]" value="${item.nama_formula || ''}">
                            <input type="hidden" name="better_rows[${i}][suhu_air]" class="row_suhu_air">
                            <input type="hidden" name="better_rows[${i}][sensori]" class="row_sensori" value="✔">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="better_rows[${i}][kode_produksi_better]" placeholder="Opsional">
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="better_rows[${i}][master_berat]" value="${item.berat || 0}" required>
                        </td>
                    </tr>
                `;
                $tbody.append(row);
            });
            $('#better-input-table-wrapper').show();
            $('#sensori-wrapper').show();
        }
    }

    function renderStdAktualTable(stdData) {
        const $container = $('#std-aktual-table-better');
        $container.empty();
        
        if (stdData && stdData.length > 0) {
            let html = '<div class="card card-primary card-outline mb-3"><div class="card-header"><h5 class="card-title"><i class="fas fa-chart-bar"></i> Standar & Aktual</h5></div><div class="card-body">';
            
            $.each(stdData, function(idx, std) {
                html += `
                    <div class="row align-items-center mb-3">
                        <div class="col-md-3"><label class="mb-0">Standar Viscositas (s)</label></div>
                        <div class="col-md-3"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><span class="font-weight-bold">${std.std_viskositas || '-'}</span></div>
                        <div class="col-md-3"><label class="mb-0">Aktual Viscositas (s)</label></div>
                        <div class="col-md-3">
                            <input type="hidden" name="id_std_salinitas_viskositas[]" value="${std.id}">
                            <input type="number" step="0.01" name="aktual_vis[]" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="row align-items-center mb-3">
                        <div class="col-md-3"><label class="mb-0">Standar Salinity (%)</label></div>
                        <div class="col-md-3"><span class="mr-2 text-muted" style="font-size:0.85rem"><i>(otomatis)</i></span><span class="font-weight-bold">${std.std_salinitas || '-'}</span></div>
                        <div class="col-md-3"><label class="mb-0">Aktual Salinity (%)</label></div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="aktual_sal[]" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="row align-items-center mb-3">
                        <div class="col-md-3"><label class="mb-0">Suhu Akhir (°C)</label></div>
                        <div class="col-md-9">
                            <input type="number" step="0.01" name="aktual_suhu_air[]" class="form-control form-control-sm" required>
                        </div>
                    </div>
                `;
                if (idx < stdData.length - 1) {
                    html += '<hr>';
                }
            });
            
            html += '</div></div>';
            $container.html(html);
        }
    }

    // Sync global suhu air to all rows
    $(document).on('input', '#global_suhu_air', function() {
        $('.row_suhu_air').val($(this).val());
    });
    
    // Sync global sensori to all rows
    $(document).on('change', '#global_sensori', function() {
        $('.row_sensori').val($(this).val());
    });
});
</script>
@endpush

@endsection
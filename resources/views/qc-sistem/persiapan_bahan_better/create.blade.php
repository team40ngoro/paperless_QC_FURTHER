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
                  <div class="alert alert-info py-2 mb-3">
                      <i class="fas fa-info-circle mr-1"></i>
                      <strong>Catatan:</strong> Form ini untuk pembuatan data persiapan bahan better oleh Produksi. Field <strong>Sensori</strong>, <strong>Suhu</strong>, <strong>Aktual Viscositas</strong>, dan <strong>Aktual Salinity</strong> dikunci dan akan diverifikasi serta diisi oleh <strong>QC Inspektor</strong> pada tahap verifikasi (edit).
                  </div>
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
                            <label>Suhu Air (0-10) (°C) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="global_suhu_air" name="suhu_air_global" value="{{ old('suhu_air_global') }}" placeholder="Masukkan Suhu Air (0-10)" required>
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
                                    <label class="mb-0 font-weight-bold">Sensori : (ok / tdk oke) <small class="text-muted">(QC)</small></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control form-control-sm bg-light font-weight-bold" value="Menunggu Verifikasi QC" disabled readonly>
                                    <small class="text-muted"><i class="fas fa-lock mr-1"></i> Kolom sensori dikunci dan hanya dapat diisi oleh QC Inspektor saat verifikasi.</small>
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
});
</script>
@endpush

@endsection
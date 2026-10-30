@extends('layouts.app')
@section('container')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Verifikasi Pembuatan Emulsi</li>
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
                  <li class="nav-item"><a class="nav-link active" href="#emulsi" data-toggle="tab">Verifikasi Pembuatan Emulsi</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="emulsi">
                    <form class="form-horizontal mb-4" method="POST" action="{{ route('persiapan-bahan-emulsi.store') }}">
                        @csrf
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Shift</label>
                            <div class="col-sm-10">
                                <select name="shift_id" class="form-control" required>
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Tanggal <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                       name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jam <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="time" class="form-control @error('jam') is-invalid @enderror" name="jam" value="{{ old('jam', \Carbon\Carbon::now('Asia/Jakarta')->format('H:i')) }}" required>
                                @error('jam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kode Produksi Emulsi</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="kode_produksi_emulsi" required>
                                </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Produk</label>
                            <div class="col-sm-10">
                                <select name="id_produk" id="id_produk_emulsi" class="form-control" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($jenis_produk as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Gramase (gram)</label>
                            <div class="col-sm-10">
                                <select name="berat_sampling" id="nilai_select_berat_sampling" class="form-control" data-nilai-berat data-selected="{{ old('berat_sampling') }}"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Emulsi</label>
                            <div class="col-sm-10">
                                <select name="nama_emulsi_id" id="nama_emulsi_id_emulsi" class="form-control" required>
                                    <option value="">Pilih Nama Emulsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Total Pemakaian</label>
                            <div class="col-sm-10">
                                <select name="total_pemakaian_id" id="total_pemakaian_id_emulsi" class="form-control" required>
                                    <option value="">Pilih Total Pemakaian</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="proses_emulsi_id_emulsi" class="col-sm-2 col-form-label">Jumlah Proses Emulsi</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="proses_emulsi_id_emulsi" name="nomor_emulsi_id" required>
                                    <option value="">Pilih Jumlah Proses Emulsi</option>
                                </select>
                            </div>
                        </div>
                        <h7>Table Detail Emulsi Akan Muncul Setelah Anda Memilih Semua Opsi</h7>
                        <div id="multiple-tables-container" class="mt-3">
                            {{-- DIISI OLEH AJAX - Multiple tables dengan Suhu & Hasil Emulsi --}}
                        </div>
                        <button type="submit" class="btn btn-primary btn-md"><i class="fas fa-save"></i> Simpan Data</button>
                        <a href="{{ route('persiapan-bahan-emulsi.index') }}" class="ml-2 btn btn-secondary btn-md"><i class="fas fa-arrow-left"></i> Kembali</a>
                    </form>
                </div>
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
  
@endsection
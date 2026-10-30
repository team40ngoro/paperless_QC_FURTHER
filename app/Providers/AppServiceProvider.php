<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Gunakan migration personal_access_tokens milik aplikasi, bukan bawaan Sanctum
        \Laravel\Sanctum\Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Naikkan batas waktu eksekusi & memori untuk semua ekspor PDF/Excel secara global
        if (request()->is('*export*') || request()->is('*pdf*') || request()->is('*download*') || request()->is('*print*') || request()->has('export')) {
            @set_time_limit(600);
            @ini_set('memory_limit', '1024M');
        }

        // Daftarkan Observer untuk PersiapanBahanForming
        \App\Models\PersiapanBahanForming::observe(\App\Observers\PersiapanBahanFormingObserver::class);
        
        // Daftarkan Observer untuk PembuatanEmulsi
        \App\Models\PembuatanEmulsi::observe(\App\Observers\PembuatanEmulsiObserver::class);
        
        // Daftarkan Observer untuk PersiapanBahanBetter
        \App\Models\PersiapanBahanBetter::observe(\App\Observers\PersiapanBahanBetterObserver::class);
        
        // Daftarkan Observer untuk Chillroom
        \App\Models\PenerimaanChillroom::observe(\App\Observers\ChillroomObserver::class);
        
        // Daftarkan Observer untuk Seasoning
        \App\Models\Seasoning::observe(\App\Observers\SeasoningObserver::class);
        
        // Daftarkan Observer untuk Shoestring
        \App\Models\Shoestring::observe(\App\Observers\ShoestringObserver::class);
        
        // Daftarkan Observer untuk Rebox
        \App\Models\Rebox::observe(\App\Observers\ReboxObserver::class);
        
        // Daftarkan Observer untuk InputMetalDetector
        \App\Models\InputMetalDetector::observe(\App\Observers\InputMetalDetectorObserver::class);
        
        // Daftarkan Observer untuk ProsesTumbling
        \App\Models\ProsesTumbling::observe(\App\Observers\ProsesTumblingObserver::class);
        
        // Daftarkan Observer untuk ProsesMarinadeModel
        \App\Models\ProsesMarinadeModel::observe(\App\Observers\ProsesMarinadeObserver::class);
        
        // Daftarkan Observer untuk BahanBakuTumbling
        \App\Models\BahanBakuTumbling::observe(\App\Observers\BahanBakuTumblingObserver::class);
        
        // Daftarkan Observer untuk PengemasanProduk
        \App\Models\PengemasanProduk::observe(\App\Observers\PengemasanProdukObserver::class);
        
        // Daftarkan Obsever Untuk PengemasanPlastik
        \App\Models\PengemasanPlastik::observe(\App\Observers\PengemasanPlastikObserver::class);
        
        // Daftarkan Observer untuk ProdukYum
        \App\Models\ProdukYum::observe(\App\Observers\ProdukYumObserver::class);
        
        // Daftarkan Observer untuk Penggorengan
        \App\Models\Penggorengan::observe(\App\Observers\PenggorenganObserver::class);
        
        // Daftarkan Observer untuk PembuatanPredust
        \App\Models\PembuatanPredust::observe(\App\Observers\PembuatanPredustObserver::class);
        
        // Daftarkan Observer untuk ProsesBattering
        \App\Models\ProsesBattering::observe(\App\Observers\ProsesBatteringObserver::class);
        
        // Daftarkan Observer untuk ProsesBreader
        \App\Models\ProsesBreader::observe(\App\Observers\ProsesBreaderObserver::class);
        
        // Daftarkan Observer untuk HasilPenggorengan
        \App\Models\HasilPenggorengan::observe(\App\Observers\HasilPenggorenganObserver::class);
        
        // Daftarkan Observer untuk PembekuanIqfPenggorengan
        \App\Models\PembekuanIqfPenggorengan::observe(\App\Observers\PembekuanIqfPenggorenganObserver::class);
        
        // Daftarkan Observer untuk InputRoasting
        \App\Models\InputRoasting::observe(\App\Observers\InputRoastingObserver::class);
        
        // Daftarkan Observer untuk BahanBakuRoasting
        \App\Models\BahanBakuRoasting::observe(\App\Observers\BahanBakuRoastingObserver::class);
        
        // Daftarkan Observer untuk ProsesRoastingFan
        \App\Models\ProsesRoastingFan::observe(\App\Observers\ProsesRoastingFanObserver::class);
        
        // Daftarkan Observer untuk HasilProsesRoasting
        \App\Models\HasilProsesRoasting::observe(\App\Observers\HasilProsesRoastingObserver::class);
        
        // Daftarkan Observer untuk BeratProdukBag
        \App\Models\BeratProdukBag::observe(\App\Observers\BeratProdukBagObserver::class);
        
        // Daftarkan Observer untuk BeratProdukBox
        \App\Models\BeratProdukBox::observe(\App\Observers\BeratProdukBoxObserver::class);
        
        // Daftarkan Observer untuk PengemasanKarton
        \App\Models\PengemasanKarton::observe(\App\Observers\PengemasanKartonObserver::class);
        \App\Models\ProdukForming::observe(\App\Observers\ProdukFormingObserver::class);
        \App\Models\ProdukNonForming::observe(\App\Observers\ProdukNonFormingObserver::class);
        \App\Models\ProdukYum::observe(\App\Observers\ProdukYumObserver::class);
        \App\Models\Dokumentasi::observe(\App\Observers\DokumentasiObserver::class);
        \App\Models\PembuatanSample::observe(\App\Observers\PembuatanSampleObserver::class);
        \App\Models\PemeriksaanProdukCookingMixerFla::observe(\App\Observers\PemeriksaanProdukCookingMixerFlaObserver::class);
        \App\Models\PemeriksaanRheonMachine::observe(\App\Observers\PemeriksaanRheonMachineObserver::class);
        \App\Models\PemeriksaanRiceBites::observe(\App\Observers\PemeriksaanRiceBitesObserver::class);
        \App\Models\PemasakanNasi::observe(\App\Observers\PemasakanNasiObserver::class);
        \App\Models\KetidaksesuaianPlastik::observe(\App\Observers\KetidaksesuaianPlastikObserver::class);
        \App\Models\KetidaksesuaianBendaAsing::observe(\App\Observers\KetidaksesuaianBendaAsingObserver::class);
        \App\Models\GmpKaryawan::observe(\App\Observers\GmpKaryawanObserver::class);
        \App\Models\KontrolSanitasi::observe(\App\Observers\KontrolSanitasiObserver::class);
        \App\Models\BarangMudahPecah::observe(\App\Observers\BarangMudahPecahObserver::class);
        \App\Models\PemeriksaanBendaAsing::observe(\App\Observers\PemeriksaanBendaAsingObserver::class);
        \App\Models\PemeriksaanProsesProduksi::observe(\App\Observers\PemeriksaanProsesProduksiObserver::class);
        \App\Models\PemeriksaanBahanKemas::observe(\App\Observers\PemeriksaanBahanKemasObserver::class);
        \App\Models\Timbangan::observe(\App\Observers\TimbanganObserver::class);
        \App\Models\Thermometer::observe(\App\Observers\ThermometerObserver::class);
        \App\Models\VerifikasiBeratProduk::observe(\App\Observers\VerifikasiBeratObserver::class);
        \App\Models\ProsesTwahing::observe(\App\Observers\ProsesTwahingObserver::class);

        // Register View Composer untuk men-cache query ::all() yang sering dipanggil
        View::composer([
            'qc-sistem.*',
            'layouts.app',
        ], \App\View\Composers\CommonDataComposer::class);
    }
}

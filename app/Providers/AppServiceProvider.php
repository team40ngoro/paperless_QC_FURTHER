<?php

namespace App\Providers;

use App\Models\BahanBakuRoasting;
use App\Models\BahanBakuTumbling;
use App\Models\BarangMudahPecah;
use App\Models\BeratProdukBag;
use App\Models\BeratProdukBox;
use App\Models\Dokumentasi;
use App\Models\GmpKaryawan;
use App\Models\HasilPenggorengan;
use App\Models\HasilProsesRoasting;
use App\Models\InputMetalDetector;
use App\Models\InputRoasting;
use App\Models\KetidaksesuaianBendaAsing;
use App\Models\KetidaksesuaianPlastik;
use App\Models\KontrolSanitasi;
use App\Models\PemasakanNasi;
use App\Models\PembekuanIqfPenggorengan;
use App\Models\PembuatanEmulsi;
use App\Models\PembuatanPredust;
use App\Models\PembuatanSample;
use App\Models\PemeriksaanBahanKemas;
use App\Models\PemeriksaanBendaAsing;
use App\Models\PemeriksaanProdukCookingMixerFla;
use App\Models\PemeriksaanProsesProduksi;
use App\Models\PemeriksaanRheonMachine;
use App\Models\PemeriksaanRiceBites;
use App\Models\PenerimaanChillroom;
use App\Models\PengemasanKarton;
use App\Models\PengemasanPlastik;
use App\Models\PengemasanProduk;
use App\Models\Penggorengan;
use App\Models\PersiapanBahanBetter;
use App\Models\PersiapanBahanForming;
use App\Models\ProdukForming;
use App\Models\ProdukNonForming;
use App\Models\ProdukYum;
use App\Models\ProsesBattering;
use App\Models\ProsesBreader;
use App\Models\ProsesMarinadeModel;
use App\Models\ProsesRoastingFan;
use App\Models\ProsesTumbling;
use App\Models\ProsesTwahing;
use App\Models\Rebox;
use App\Models\Seasoning;
use App\Models\Shoestring;
use App\Models\Thermometer;
use App\Models\Timbangan;
use App\Models\VerifikasiBeratProduk;
use App\Observers\BahanBakuRoastingObserver;
use App\Observers\BahanBakuTumblingObserver;
use App\Observers\BarangMudahPecahObserver;
use App\Observers\BeratProdukBagObserver;
use App\Observers\BeratProdukBoxObserver;
use App\Observers\ChillroomObserver;
use App\Observers\DokumentasiObserver;
use App\Observers\GmpKaryawanObserver;
use App\Observers\HasilPenggorenganObserver;
use App\Observers\HasilProsesRoastingObserver;
use App\Observers\InputMetalDetectorObserver;
use App\Observers\InputRoastingObserver;
use App\Observers\KetidaksesuaianBendaAsingObserver;
use App\Observers\KetidaksesuaianPlastikObserver;
use App\Observers\KontrolSanitasiObserver;
use App\Observers\PemasakanNasiObserver;
use App\Observers\PembekuanIqfPenggorenganObserver;
use App\Observers\PembuatanEmulsiObserver;
use App\Observers\PembuatanPredustObserver;
use App\Observers\PembuatanSampleObserver;
use App\Observers\PemeriksaanBahanKemasObserver;
use App\Observers\PemeriksaanBendaAsingObserver;
use App\Observers\PemeriksaanProdukCookingMixerFlaObserver;
use App\Observers\PemeriksaanProsesProduksiObserver;
use App\Observers\PemeriksaanRheonMachineObserver;
use App\Observers\PemeriksaanRiceBitesObserver;
use App\Observers\PengemasanKartonObserver;
use App\Observers\PengemasanPlastikObserver;
use App\Observers\PengemasanProdukObserver;
use App\Observers\PenggorenganObserver;
use App\Observers\PersiapanBahanBetterObserver;
use App\Observers\PersiapanBahanFormingObserver;
use App\Observers\ProdukFormingObserver;
use App\Observers\ProdukNonFormingObserver;
use App\Observers\ProdukYumObserver;
use App\Observers\ProsesBatteringObserver;
use App\Observers\ProsesBreaderObserver;
use App\Observers\ProsesMarinadeObserver;
use App\Observers\ProsesRoastingFanObserver;
use App\Observers\ProsesTumblingObserver;
use App\Observers\ProsesTwahingObserver;
use App\Observers\ReboxObserver;
use App\Observers\SeasoningObserver;
use App\Observers\ShoestringObserver;
use App\Observers\ThermometerObserver;
use App\Observers\TimbanganObserver;
use App\Observers\VerifikasiBeratObserver;
use App\View\Composers\CommonDataComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        Sanctum::ignoreMigrations();
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
        PersiapanBahanForming::observe(PersiapanBahanFormingObserver::class);

        // Daftarkan Observer untuk PembuatanEmulsi
        PembuatanEmulsi::observe(PembuatanEmulsiObserver::class);

        // Daftarkan Observer untuk PersiapanBahanBetter
        PersiapanBahanBetter::observe(PersiapanBahanBetterObserver::class);

        // Daftarkan Observer untuk Chillroom
        PenerimaanChillroom::observe(ChillroomObserver::class);

        // Daftarkan Observer untuk Seasoning
        Seasoning::observe(SeasoningObserver::class);

        // Daftarkan Observer untuk Shoestring
        Shoestring::observe(ShoestringObserver::class);

        // Daftarkan Observer untuk Rebox
        Rebox::observe(ReboxObserver::class);

        // Daftarkan Observer untuk InputMetalDetector
        InputMetalDetector::observe(InputMetalDetectorObserver::class);

        // Daftarkan Observer untuk ProsesTumbling
        ProsesTumbling::observe(ProsesTumblingObserver::class);

        // Daftarkan Observer untuk ProsesMarinadeModel
        ProsesMarinadeModel::observe(ProsesMarinadeObserver::class);

        // Daftarkan Observer untuk BahanBakuTumbling
        BahanBakuTumbling::observe(BahanBakuTumblingObserver::class);

        // Daftarkan Observer untuk PengemasanProduk
        PengemasanProduk::observe(PengemasanProdukObserver::class);

        // Daftarkan Obsever Untuk PengemasanPlastik
        PengemasanPlastik::observe(PengemasanPlastikObserver::class);

        // Daftarkan Observer untuk ProdukYum
        ProdukYum::observe(ProdukYumObserver::class);

        // Daftarkan Observer untuk Penggorengan
        Penggorengan::observe(PenggorenganObserver::class);

        // Daftarkan Observer untuk PembuatanPredust
        PembuatanPredust::observe(PembuatanPredustObserver::class);

        // Daftarkan Observer untuk ProsesBattering
        ProsesBattering::observe(ProsesBatteringObserver::class);

        // Daftarkan Observer untuk ProsesBreader
        ProsesBreader::observe(ProsesBreaderObserver::class);

        // Daftarkan Observer untuk HasilPenggorengan
        HasilPenggorengan::observe(HasilPenggorenganObserver::class);

        // Daftarkan Observer untuk PembekuanIqfPenggorengan
        PembekuanIqfPenggorengan::observe(PembekuanIqfPenggorenganObserver::class);

        // Daftarkan Observer untuk InputRoasting
        InputRoasting::observe(InputRoastingObserver::class);

        // Daftarkan Observer untuk BahanBakuRoasting
        BahanBakuRoasting::observe(BahanBakuRoastingObserver::class);

        // Daftarkan Observer untuk ProsesRoastingFan
        ProsesRoastingFan::observe(ProsesRoastingFanObserver::class);

        // Daftarkan Observer untuk HasilProsesRoasting
        HasilProsesRoasting::observe(HasilProsesRoastingObserver::class);

        // Daftarkan Observer untuk BeratProdukBag
        BeratProdukBag::observe(BeratProdukBagObserver::class);

        // Daftarkan Observer untuk BeratProdukBox
        BeratProdukBox::observe(BeratProdukBoxObserver::class);

        // Daftarkan Observer untuk PengemasanKarton
        PengemasanKarton::observe(PengemasanKartonObserver::class);
        ProdukForming::observe(ProdukFormingObserver::class);
        ProdukNonForming::observe(ProdukNonFormingObserver::class);
        ProdukYum::observe(ProdukYumObserver::class);
        Dokumentasi::observe(DokumentasiObserver::class);
        PembuatanSample::observe(PembuatanSampleObserver::class);
        PemeriksaanProdukCookingMixerFla::observe(PemeriksaanProdukCookingMixerFlaObserver::class);
        PemeriksaanRheonMachine::observe(PemeriksaanRheonMachineObserver::class);
        PemeriksaanRiceBites::observe(PemeriksaanRiceBitesObserver::class);
        PemasakanNasi::observe(PemasakanNasiObserver::class);
        KetidaksesuaianPlastik::observe(KetidaksesuaianPlastikObserver::class);
        KetidaksesuaianBendaAsing::observe(KetidaksesuaianBendaAsingObserver::class);
        GmpKaryawan::observe(GmpKaryawanObserver::class);
        KontrolSanitasi::observe(KontrolSanitasiObserver::class);
        BarangMudahPecah::observe(BarangMudahPecahObserver::class);
        PemeriksaanBendaAsing::observe(PemeriksaanBendaAsingObserver::class);
        PemeriksaanProsesProduksi::observe(PemeriksaanProsesProduksiObserver::class);
        PemeriksaanBahanKemas::observe(PemeriksaanBahanKemasObserver::class);
        Timbangan::observe(TimbanganObserver::class);
        Thermometer::observe(ThermometerObserver::class);
        VerifikasiBeratProduk::observe(VerifikasiBeratObserver::class);
        ProsesTwahing::observe(ProsesTwahingObserver::class);

        // Register View Composer untuk men-cache query ::all() yang sering dipanggil
        View::composer([
            'qc-sistem.*',
            'layouts.app',
        ], CommonDataComposer::class);
    }
}

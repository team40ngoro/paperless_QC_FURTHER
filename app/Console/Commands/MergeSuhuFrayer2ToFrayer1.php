<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeSuhuFrayer2ToFrayer1 extends Command
{
    protected $signature   = 'frayer:merge-frayer2';
    protected $description = 'Merge data suhu_frayer_2 ke suhu_frayer_1 berdasarkan id_produk + id_plan yang cocok';

    public function handle(): int
    {
        $this->info('🚀 Mulai proses merge data Suhu Frayer 2 → Suhu Frayer 1...');
        $this->newLine();

        // Ambil semua data dari tabel lama suhu_frayer_2
        $frayer2Records = DB::table('suhu_frayer_2')->get();

        if ($frayer2Records->isEmpty()) {
            $this->warn('⚠️  Tidak ada data di tabel suhu_frayer_2. Proses selesai.');
            return self::SUCCESS;
        }

        $this->info("📦 Ditemukan {$frayer2Records->count()} record di suhu_frayer_2.");
        $this->newLine();

        $updated  = 0;
        $notFound = 0;
        $skipped  = 0;

        foreach ($frayer2Records as $row) {
            // Cari record di suhu_frayer_1 berdasarkan id_produk + id_plan yang sama
            $frayer1 = DB::table('suhu_frayer_1')
                ->where('id_produk', $row->id_produk)
                ->where('id_plan', $row->id_plan)
                ->first();

            if (!$frayer1) {
                $this->warn("  ⛔ Tidak ditemukan pasangan di suhu_frayer_1 untuk id_produk={$row->id_produk}, id_plan={$row->id_plan}");
                $notFound++;
                continue;
            }

            // Jika sudah ada data frayer_2 di frayer_1, skip (tidak overwrite)
            if (!empty($frayer1->suhu_frayer_2) || !empty($frayer1->waktu_penggorengan_2)) {
                $this->line("  ⏭️  Skip (sudah ada data) → id_produk={$row->id_produk}, id_plan={$row->id_plan}");
                $skipped++;
                continue;
            }

            // Update baris di suhu_frayer_1 dengan data dari suhu_frayer_2
            DB::table('suhu_frayer_1')
                ->where('id', $frayer1->id)
                ->update([
                    'suhu_frayer_2'       => $row->suhu_frayer_2,
                    'waktu_penggorengan_2'=> $row->waktu_penggorengan_2 ?? null,
                    'updated_at'          => now(),
                ]);

            $this->info("  ✅ Merged → id_produk={$row->id_produk}, suhu={$row->suhu_frayer_2}, waktu={$row->waktu_penggorengan_2}");
            $updated++;
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅  Berhasil di-merge : {$updated} record");
        $this->warn("⏭️  Di-skip (sudah ada): {$skipped} record");
        $this->error("⛔  Tidak ada pasangan : {$notFound} record");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->info('🎉 Proses merge selesai! Silakan cek halaman Data Suhu Frayer 1,3,4,5 untuk memverifikasi hasilnya.');

        return self::SUCCESS;
    }
}

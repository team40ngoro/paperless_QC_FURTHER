<?php

namespace App\Console\Commands;

use App\Models\TotalPemakaianEmulsi;
use Illuminate\Console\Command;

class CleanDuplicateTotalPemakaianEmulsi extends Command
{
    protected $signature = 'emulsi:clean-duplicates {--dry-run : Hanya tampilkan yang akan diubah tanpa menyimpan}';
    protected $description = 'Membersihkan data duplikat total_pemakaian_emulsi per produk dengan auto-numbering (180, 180(1), 180(2), ...)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Memulai pembersihan duplikat total_pemakaian_emulsi...');

        $products = TotalPemakaianEmulsi::select('id_produk')
            ->distinct()
            ->pluck('id_produk');

        $totalFixed = 0;

        foreach ($products as $idProduk) {
            $records = TotalPemakaianEmulsi::where('id_produk', $idProduk)
                ->orderBy('total_pemakaian')
                ->orderBy('created_at')
                ->get();

            $groups = $records->groupBy('total_pemakaian');

            foreach ($groups as $value => $group) {
                if ($group->count() <= 1) continue;

                $baseValue = trim($value);
                $baseValue = preg_replace('/\(\d+\)$/', '', $baseValue);
                $baseValue = trim($baseValue);

                $first = $group->shift();

                foreach ($group as $duplicate) {
                    $counter = 1;
                    while (true) {
                        $candidate = $baseValue . '(' . $counter . ')';
                        $exists = TotalPemakaianEmulsi::where('id_produk', $idProduk)
                            ->where('total_pemakaian', $candidate)
                            ->exists();

                        if (!$exists) {
                            break;
                        }
                        $counter++;
                    }

                    $this->line("  Produk #{$idProduk}: '{$duplicate->total_pemakaian}' -> '{$candidate}' (ID: {$duplicate->id})");
                    $totalFixed++;

                    if (!$dryRun) {
                        $duplicate->update(['total_pemakaian' => $candidate]);
                    }
                }
            }
        }

        if ($dryRun) {
            $this->warn("DRY-RUN: {$totalFixed} record akan diperbaiki (tidak disimpan).");
            $this->info('Jalankan tanpa --dry-run untuk menerapkan perubahan.');
        } else {
            $this->info("Selesai! {$totalFixed} record duplikat telah diperbaiki.");
        }

        return 0;
    }
}
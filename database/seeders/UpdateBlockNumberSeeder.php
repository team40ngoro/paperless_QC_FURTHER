<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProsesRoastingFan;
use App\Models\SuhuBlok;
use Illuminate\Support\Facades\DB;

class UpdateBlockNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset all block numbers first
        ProsesRoastingFan::query()->update(['block_number' => null]);
        
        // Get all records
        $records = ProsesRoastingFan::with('suhuBlok')->get();
        
        // Group by session to assign block numbers correctly
        $sessions = $records->groupBy(function($record) {
            return $record->tanggal->format('Y-m-d H:i:s') . '_' . $record->id_shift . '_' . $record->id_produk . '_' . ($record->aktual_lama_proses ?? 'null');
        });
        
        foreach ($sessions as $sessionRecords) {
            // Get all suhu_blok for this product to determine form positions
            $firstRecord = $sessionRecords->first();
            $allSuhuBlok = SuhuBlok::where('id_produk', $firstRecord->id_produk)
                ->orderBy('id')
                ->get()
                ->keyBy('id');
            
            // Map each record to its correct block number based on form position
            foreach ($sessionRecords as $record) {
                // Find position of this suhu_blok in the ordered list
                $position = 1;
                foreach ($allSuhuBlok as $suhuBlok) {
                    if ($suhuBlok->id == $record->id_suhu_blok) {
                        break;
                    }
                    $position++;
                }
                
                // Form shows blocks in reverse order: position 1 = Blok 4, position 2 = Blok 3, etc.
                $blockNumber = 5 - $position; // Convert to form block number
                $record->update(['block_number' => $blockNumber]);
            }
        }
        
        $this->command->info('Updated ' . $records->count() . ' records with correct block numbers.');
    }
}

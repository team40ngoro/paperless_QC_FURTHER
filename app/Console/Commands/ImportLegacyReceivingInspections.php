<?php

namespace App\Console\Commands;

use App\Models\DataShift;
use App\Services\ReceivingInspection\ReceivingInspectionRegistry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportLegacyReceivingInspections extends Command
{
    protected $signature = 'receiving-inspections:import-legacy
                            {--area= : Only import one area (CHILLROOM, PREMIX, SHOESTRING, KEMASAN)}
                            {--dry-run : Read and report without writing}
                            {--batch=500 : Rows per transaction batch}';

    protected $description = 'Import legacy QC/Production receiving rows into the merged receiving_inspections table without guessing cross-links.';

    public function handle(ReceivingInspectionRegistry $registry): int
    {
        $areas = $this->option('area')
            ? [strtoupper((string) $this->option('area'))]
            : $registry->areas();

        $sources = [
            'CHILLROOM' => ['table' => 'penerimaan_chillroom', 'map' => 'mapChillroom'],
            'PREMIX' => ['table' => 'seasonings', 'map' => 'mapPremix'],
            'SHOESTRING' => ['table' => 'shoestrings', 'map' => 'mapShoestring'],
            'KEMASAN' => ['table' => 'pemeriksaan_bahan_kemas', 'map' => 'mapKemasan'],
        ];

        foreach ($areas as $area) {
            if (!isset($sources[$area])) {
                $this->error('Unknown area: ' . $area);
                return self::FAILURE;
            }
        }

        $summary = [];

        foreach ($areas as $area) {
            $source = $sources[$area];
            if (!Schema::hasTable($source['table'])) {
                $summary[$area] = ['read' => 0, 'imported' => 0, 'skipped' => 0, 'failed' => 0, 'note' => 'table missing'];
                continue;
            }

            $counts = ['read' => 0, 'imported' => 0, 'skipped' => 0, 'failed' => 0];
            $batchSize = max(1, (int) $this->option('batch'));

            DB::table($source['table'])
                ->orderBy('id')
                ->chunk($batchSize, function ($rows) use ($area, $source, $registry, &$counts) {
                    $payloads = [];
                    foreach ($rows as $row) {
                        $counts['read']++;
                        if (isset($row->is_audit) && (int) $row->is_audit === 1) {
                            $counts['skipped']++;
                            continue;
                        }

                        $legacyId = $source['table'] . ':' . $row->id;
                        $exists = DB::table('receiving_inspections')
                            ->where('area', $area)
                            ->where('legacy_source', 'PRODUCTION')
                            ->where('legacy_source_id', $legacyId)
                            ->exists();

                        if ($exists) {
                            $counts['skipped']++;
                            continue;
                        }

                        try {
                            $payload = $this->{$source['map']}($row, $area, $registry, $legacyId);
                            if (!$payload) {
                                $counts['failed']++;
                                continue;
                            }
                            $payloads[] = $payload;
                        } catch (\Throwable $e) {
                            $counts['failed']++;
                        }
                    }

                    if ($this->option('dry-run') || empty($payloads)) {
                        return;
                    }

                    DB::transaction(function () use ($payloads) {
                        DB::table('receiving_inspections')->insert($payloads);
                    });
                    $counts['imported'] += count($payloads);
                });

            $summary[$area] = $counts;
        }

        $this->table(
            ['Area', 'Read', 'Imported', 'Skipped', 'Failed'],
            collect($summary)->map(fn ($counts, $area) => [
                $area,
                $counts['read'] ?? 0,
                $counts['imported'] ?? 0,
                $counts['skipped'] ?? 0,
                $counts['failed'] ?? 0,
            ])->values()->all()
        );

        if ($this->option('dry-run')) {
            $this->warn('Dry run: no rows were written.');
        }

        $this->line('No old QC-only tables were found; legacy QC rows are not guessed or cross-linked.');

        return self::SUCCESS;
    }

    private function mapChillroom($row, string $area, ReceivingInspectionRegistry $registry, string $legacyId): ?array
    {
        $date = $this->date($row->tanggal ?? null);
        if (!$date) {
            return null;
        }

        $notes = trim((string) ($row->keterangan ?? ''));
        $condition = null;
        $lowerNotes = strtolower($notes);
        if (str_contains($lowerNotes, 'frozen') || str_contains($lowerNotes, 'beku')) {
            $condition = 'FROZEN';
        } elseif (str_contains($lowerNotes, 'fresh') || str_contains($lowerNotes, 'segar')) {
            $condition = 'FRESH';
        }

        $standardWeight = $this->numericOrNull($row->standar_berat ?? null);
        $measurements = $this->numbers($row->nilai_jumlah_rm ?? null);

        $production = [
            'prod_arrival_time' => $this->time($row->jam_kedatangan ?? null),
            'prod_rm_name' => $row->nama_rm ?? null,
            'prod_production_code' => $row->kode_produksi ?? null,
            'prod_weight_per_pack' => $this->numericOrNull($row->berat ?? null),
            'prod_rm_temp_c' => $this->numericOrNull($row->suhu ?? null),
            'prod_sensory' => $registry->normalizeOkNotOk($row->sensori ?? null),
            'prod_packaging' => $registry->normalizeOkNotOk($row->kemasan ?? null),
            'prod_rm_condition' => $condition,
            'prod_notes' => $notes !== '' ? $notes : ($row->catatan_rm ?? null),
            'prod_is_meat_rm' => $standardWeight !== null || count($measurements) > 0,
            'prod_std_weight_per_pc_g' => $standardWeight,
            'prod_actual_weight_per_pc_g' => $this->average($measurements),
            'prod_sample_count' => count($measurements) > 0 ? count($measurements) : null,
            'prod_recap_percent' => null,
            'prod_rm_notes' => $row->catatan_rm ?? null,
            'legacy_raw' => $this->legacyRaw($row, ['standar_berat', 'jumlah_rm', 'nilai_jumlah_rm', 'status_rm', 'kode_form']),
        ];

        return $this->record($area, $date, $production, [], $row, $registry, $legacyId);
    }

    private function mapPremix($row, string $area, ReceivingInspectionRegistry $registry, string $legacyId): ?array
    {
        $date = $this->date($row->tanggal ?? null);
        if (!$date) {
            return null;
        }

        $production = [
            'prod_check_time' => $this->time($row->jam ?? null),
            'prod_rm_name' => $row->nama_rm ?? null,
            'prod_production_code' => $row->kode_produksi ?? null,
            'prod_weight_per_pack' => $this->numericOrNull($row->berat ?? null),
            'prod_shift' => $this->shift($row->shift_id ?? null),
            'prod_sensory' => $registry->normalizeOkNotOk($row->sensori ?? null),
            'prod_packaging' => $registry->normalizeOkNotOk($row->kemasan ?? null),
            'prod_notes' => $row->keterangan ?? null,
            'legacy_raw' => $this->legacyRaw($row, ['kode_form']),
        ];

        return $this->record($area, $date, $production, [], $row, $registry, $legacyId);
    }

    private function mapShoestring($row, string $area, ReceivingInspectionRegistry $registry, string $legacyId): ?array
    {
        $date = $this->date($row->tanggal ?? null);
        if (!$date) {
            return null;
        }

        $production = [
            'prod_shift' => $this->shift($row->shift_id ?? null),
            'prod_production_date' => $date,
            'prod_production_time' => $this->time($row->jam ?? null),
            'prod_producer_name' => $row->nama_produsen ?? null,
            'prod_production_code' => $row->kode_produksi ?? null,
            'prod_expiry_date' => $this->date($row->best_before ?? null),
            'prod_gramasi' => $this->numericOrNull($row->berat_sampling ?? null),
            'prod_notes' => $row->catatan ?? null,
            'legacy_raw' => $this->legacyRaw($row, ['sampling_defect', 'sampling_defect_qty', 'total_defect', 'kode_form']),
        ];

        $qc = [
            'qc_producer_name' => $row->nama_produsen ?? null,
            'qc_producer_rm_code' => $row->kode_produksi ?? null,
            'qc_producer_rm_date' => $this->date($row->best_before ?? null),
            'qc_no_defects' => empty($row->total_defect) || (int) $row->total_defect === 0,
        ];

        return $this->record($area, $date, $production, $qc, $row, $registry, $legacyId);
    }

    private function mapKemasan($row, string $area, ReceivingInspectionRegistry $registry, string $legacyId): ?array
    {
        $date = $this->date($row->tanggal ?? null);
        if (!$date) {
            return null;
        }

        $production = [
            'prod_shift' => $this->shift($row->shift_id ?? null),
            'prod_production_code' => $row->kode_produksi ?? null,
            'prod_producer_name' => null,
            'prod_packaging_name' => $row->nama_kemasan ?? null,
            'prod_packaging_condition' => $row->kondisi_bahan_kemasan ?? null,
            'prod_notes' => $row->keterangan ?? null,
            'legacy_raw' => $this->legacyRaw($row, ['kode_form']),
        ];

        return $this->record($area, $date, $production, [], $row, $registry, $legacyId);
    }

    private function record(string $area, string $date, array $production, array $qc, $row, ReceivingInspectionRegistry $registry, string $legacyId): array
    {
        $codes = $registry->formCodes($area);
        $production = array_merge($registry->defaults($area, 'PRODUCTION'), $production);
        $qc = array_merge($registry->defaults($area, 'QC'), $qc);

        return [
            'uuid' => (string) Str::uuid(),
            'area' => $area,
            'inspection_date' => $date,
            'qc_form_code' => $codes['qc_form_code'],
            'prod_form_code' => $codes['prod_form_code'],
            'prod_status' => 'SUBMITTED',
            'qc_status' => 'DRAFT',
            'prod_filled_by' => $this->userId($row->user_id ?? $row->created_by ?? null),
            'prod_submitted_at' => $this->timestamp($row->produksi_approved_at ?? $row->updated_at ?? null),
            'qc_filled_by' => null,
            'qc_submitted_at' => null,
            'production_data' => json_encode($production),
            'qc_data' => json_encode($qc),
            'has_qc_finding' => false,
            'created_by' => $this->userId($row->user_id ?? $row->created_by ?? null),
            'legacy_source' => 'PRODUCTION',
            'legacy_source_id' => $legacyId,
            'legacy_unlinked' => true,
            'created_at' => $this->timestamp($row->created_at ?? null) ?? now(),
            'updated_at' => $this->timestamp($row->updated_at ?? null) ?? now(),
            'deleted_at' => null,
        ];
    }

    private function date($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function timestamp($value): ?string
    {
        if (empty($value) || str_starts_with((string) $value, '0000-00-00')) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function time($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function numericOrNull($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim(str_replace(',', '.', (string) $value));
        return is_numeric($value) ? (float) $value : null;
    }

    private function numbers($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if ($decoded === null) {
                return is_numeric(trim(str_replace(',', '.', $value))) ? [(float) $value] : [];
            }
        }

        $numbers = [];
        $walk = function ($item) use (&$walk, &$numbers) {
            if (is_array($item)) {
                foreach ($item as $child) {
                    $walk($child);
                }
                return;
            }
            if (is_numeric($item)) {
                $numbers[] = (float) $item;
            }
        };
        $walk($decoded);

        return $numbers;
    }

    private function average(array $numbers): ?float
    {
        return empty($numbers) ? null : array_sum($numbers) / count($numbers);
    }

    private function userId($id): ?int
    {
        if (empty($id)) {
            return null;
        }
        return DB::table('users')->where('id', $id)->exists() ? (int) $id : null;
    }

    private function legacyRaw($row, array $keys): array
    {
        $raw = [];
        foreach ($keys as $key) {
            $raw[$key] = $row->{$key} ?? null;
        }
        return $raw;
    }


    private function shift($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $shift = DataShift::query()->where('id', $value)->orWhere('shift', $value)->first();
        return $shift->shift ?? (string) $value;
    }
}

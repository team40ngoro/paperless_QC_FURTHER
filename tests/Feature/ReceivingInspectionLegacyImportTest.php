<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReceivingInspectionLegacyImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_shoestring_legacy_rows_import_idempotently(): void
    {
        if (!Schema::hasTable('shoestrings')) {
            $this->markTestSkipped('Legacy shoestrings table is not available.');
        }

        $this->artisan('receiving-inspections:import-legacy', [
            '--area' => 'SHOESTRING',
            '--batch' => 100,
        ])->assertExitCode(0);

        $firstCount = DB::table('receiving_inspections')
            ->where('area', 'SHOESTRING')
            ->where('legacy_source', 'PRODUCTION')
            ->count();

        $this->assertGreaterThan(0, $firstCount);

        $this->artisan('receiving-inspections:import-legacy', [
            '--area' => 'SHOESTRING',
            '--batch' => 100,
        ])->assertExitCode(0);

        $secondCount = DB::table('receiving_inspections')
            ->where('area', 'SHOESTRING')
            ->where('legacy_source', 'PRODUCTION')
            ->count();

        $this->assertSame($firstCount, $secondCount);

        $this->assertDatabaseHas('receiving_inspections', [
            'area' => 'SHOESTRING',
            'legacy_source' => 'PRODUCTION',
            'legacy_unlinked' => true,
        ]);
    }
}

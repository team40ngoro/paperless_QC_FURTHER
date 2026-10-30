<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title as ChartTitle;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class AllProcessExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        if (!empty($filters['from_date'])) {
            $filters['from_date'] = Carbon::parse($filters['from_date'])->format('Y-m-d H:i:s');
        }

        if (!empty($filters['to_date'])) {
            $filters['to_date'] = Carbon::parse($filters['to_date'])->format('Y-m-d H:i:s');
        }

        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new ExecutiveSummarySheet($this->filters),
        ];
    }

    public static function queryPanelData($offset, $filters)
    {
        $fForming    = [];
        $fNonForming = [];
        $fBetter     = [];
        $fFrying     = [];
        $fRoasting   = [];
        $fPacking    = [];
        $fKarton     = [];
        $bindings    = [];

        if (!empty($filters['id_plan']) && $filters['id_plan'] !== '$__all' && $filters['id_plan'] !== 'all') {
            $fForming[]    = "f.plan_id = :plan_id1";
            $fNonForming[] = "nf.plan_id = :plan_id2";
            $fBetter[]     = "b.id_plan = :plan_id3";
            $fFrying[]     = "hp.id_plan = :plan_id4";
            $fRoasting[]   = "hr.id_plan = :plan_id5";
            $fPacking[]    = "pp.id_plan = :plan_id6";
            $fKarton[]     = "pk.id_plan = :plan_id7";
            for ($i = 1; $i <= 7; $i++) {
                $bindings["plan_id{$i}"] = $filters['id_plan'];
            }
        }

        if (!empty($filters['id_produk']) && $filters['id_produk'] !== '$__all' && $filters['id_produk'] !== 'all') {
            $fForming[]    = "nf.id_produk = :prod_id1";
            $fNonForming[] = "nfnf.id_produk = :prod_id2";
            $fBetter[]     = "b.id_produk = :prod_id3";
            $fFrying[]     = "hp.id_produk = :prod_id4";
            $fRoasting[]   = "hr.id_produk = :prod_id5";
            $fPacking[]    = "pp_prod.id_produk = :prod_id6";
            $fKarton[]     = "pk_prod.id_produk = :prod_id7";
            for ($i = 1; $i <= 7; $i++) {
                $bindings["prod_id{$i}"] = $filters['id_produk'];
            }
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $fForming[]    = "f.tanggal BETWEEN :from1 AND :to1";
            $fNonForming[] = "nf.tanggal BETWEEN :from2 AND :to2";
            $fBetter[]     = "b.tanggal BETWEEN :from3 AND :to3";
            $fFrying[]     = "hp.tanggal BETWEEN :from4 AND :to4";
            $fRoasting[]   = "hr.tanggal BETWEEN :from5 AND :to5";
            $fPacking[]    = "pp.tanggal BETWEEN :from6 AND :to6";
            $fKarton[]     = "pk.tanggal BETWEEN :from7 AND :to7";
            for ($i = 1; $i <= 7; $i++) {
                $bindings["from{$i}"] = $filters['from_date'];
                $bindings["to{$i}"]   = $filters['to_date'];
            }
        }

        $wForming    = count($fForming)    ? ' AND ' . implode(' AND ', $fForming)    : '';
        $wNonForming = count($fNonForming) ? ' AND ' . implode(' AND ', $fNonForming) : '';
        $wBetter     = count($fBetter)     ? ' AND ' . implode(' AND ', $fBetter)     : '';
        $wFrying     = count($fFrying)     ? ' AND ' . implode(' AND ', $fFrying)     : '';
        $wRoasting   = count($fRoasting)   ? ' AND ' . implode(' AND ', $fRoasting)   : '';
        $wPacking    = count($fPacking)    ? ' AND ' . implode(' AND ', $fPacking)    : '';
        $wKarton     = count($fKarton)     ? ' AND ' . implode(' AND ', $fKarton)     : '';

        $limitOffset = $offset > 0 ? "LIMIT 1 OFFSET {$offset}" : "LIMIT 1";

        $sql = "
        SELECT * FROM (
          (
            SELECT 1 as ordering, 'MIXING' AS process, '#3b82f6' as color, product, code, time AS jam FROM (
              (
                SELECT 
                  p.nama_produk AS product,
                  f.kode_produksi_emulsi AS code,
                  f.jam AS time,
                  f.id AS order_id,
                  f.created_at AS created_at
                FROM persiapan_bahan_forming f
                LEFT JOIN nomor_formula nf ON f.id_formula = nf.id
                LEFT JOIN jenis_produk p ON nf.id_produk = p.id
                WHERE 1=1 {$wForming}
                ORDER BY f.id DESC
                {$limitOffset}
              )
              UNION ALL
              (
                SELECT 
                  p.nama_produk AS product,
                  nf.kode_produksi AS code,
                  nf.jam AS time,
                  nf.id AS order_id,
                  nf.created_at AS created_at
                FROM persiapan_bahan_non_forming nf
                LEFT JOIN no_formula_non_forming nfnf ON nf.id_no_formula_non_forming = nfnf.id
                LEFT JOIN jenis_produk p ON nfnf.id_produk = p.id
                WHERE 1=1 {$wNonForming}
                ORDER BY nf.id DESC
                {$limitOffset}
              )
              ORDER BY created_at DESC
              LIMIT 1
            ) AS mixing_sub
          )

          UNION ALL

          (
            SELECT 2 as ordering, 'BATTERING' AS process, '#f97316' as color, p.nama_produk AS product, b.kode_produksi_produk AS code, b.jam AS jam
            FROM persiapan_bahan_better b
            LEFT JOIN jenis_produk p ON b.id_produk = p.id
            WHERE 1=1 {$wBetter}
            ORDER BY b.id DESC
            {$limitOffset}
          )

          UNION ALL

          (
            SELECT 3 as ordering, 'FRYING' AS process, '#3b82f6' as color, p.nama_produk AS product, COALESCE(pg.kode_produksi, '-') AS code, hp.jam AS jam
            FROM hasil_penggorengan hp
            LEFT JOIN jenis_produk p ON hp.id_produk = p.id
            LEFT JOIN penggorengan pg ON hp.penggorengan_uuid = pg.uuid
            WHERE 1=1 {$wFrying}
            ORDER BY hp.id DESC
            {$limitOffset}
          )

          UNION ALL

          (
            SELECT 4 as ordering, 'ROASTING / STEAMING' AS process, '#6366f1' as color, p.nama_produk AS product, COALESCE(ir.kode_produksi, '-') AS code, hr.jam AS jam
            FROM hasil_proses_roasting hr
            LEFT JOIN jenis_produk p ON hr.id_produk = p.id
            LEFT JOIN input_roasting ir ON hr.input_roasting_uuid = ir.uuid
            WHERE 1=1 {$wRoasting}
            ORDER BY hr.id DESC
            {$limitOffset}
          )

          UNION ALL

          (
            SELECT 5 as ordering, 'PACKING' AS process, '#ec4899' as color, p.nama_produk AS product, COALESCE(pp_prod.kode_produksi, '-') AS code, pp.jam AS jam
            FROM pengemasan_plastik pp
            LEFT JOIN pengemasan_produk pp_prod ON pp.id_pengemasan_produk = pp_prod.id
            LEFT JOIN jenis_produk p ON pp_prod.id_produk = p.id
            WHERE 1=1 {$wPacking}
            ORDER BY pp.id DESC
            {$limitOffset}
          )

          UNION ALL

          (
            SELECT 6 as ordering, 'CARTOONING' AS process, '#f59e0b' as color, p.nama_produk AS product, COALESCE(pk_prod.kode_produksi, '-') AS code, pk.jam AS jam
            FROM pengemasan_karton pk
            LEFT JOIN pengemasan_produk pk_prod ON pk.id_pengemasan_produk = pk_prod.id
            LEFT JOIN jenis_produk p ON pk_prod.id_produk = p.id
            WHERE 1=1 {$wKarton}
            ORDER BY pk.id DESC
            {$limitOffset}
          )
        ) AS union_result
        ORDER BY ordering ASC
        ";

        return DB::select($sql, $bindings);
    }
}

// ---------------------------------------------------------
// Executive Summary Sheet (Consolidated Panel 1 to 16)
// ---------------------------------------------------------
class ExecutiveSummarySheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithCharts
{
    protected $filters;
    protected $roleId;
    protected $isRestrictedRole;
    protected $panel1;
    protected $panel2;
    protected $panel3;
    protected $panel4;
    protected $panel5;
    protected $panel6;
    protected $panel7;
    protected $panel8;
    protected $panel9;
    protected $panel10;
    protected $panel11;
    protected $panel12;
    protected $panel13;
    protected $panel14;
    protected $panel15;
    protected $panel16;

    public function __construct($filters)
    {
        $this->filters = $filters;

        $user = Auth::user();
        $userRoleId = $user ? (int) ($user->id_role ?? (is_numeric($user->role) ? $user->role : 0)) : 0;
        $this->roleId = isset($filters['role_id']) ? (int) $filters['role_id'] : $userRoleId;
        $this->isRestrictedRole = in_array($this->roleId, [2, 3]);

        $this->panel1  = new Panel1MeatprepComponent($filters);
        $this->panel2  = new Panel2CookingComponent($filters);
        $this->panel3  = new Panel3PackingCartoningComponent($filters);
        $this->panel4  = new Panel4CurrentProcessingComponent($filters);
        $this->panel5  = new Panel5ProduksiSebelumnyaComponent($filters);
        $this->panel6  = new Panel6SuhuRuangComponent($filters);
        $this->panel7  = new Panel7TrendSuhuComponent($filters);
        $this->panel8  = new Panel8CleaningAreaComponent($filters);

        if (!$this->isRestrictedRole) {
            $this->panel9  = new Panel9TrendFryingComponent($filters);
            $this->panel10 = new Panel10TrendBeratPackComponent($filters);
            $this->panel11 = new Panel11TrendBeratBoxComponent($filters);
            $this->panel12 = new Panel12TopIssueComponent($filters);
            $this->panel13 = new Panel13SuhuIqfComponent($filters);
            $this->panel14 = new Panel14GmpKaryawanComponent($filters);
            $this->panel15 = new Panel15RoastingComponent($filters);
            $this->panel16 = new Panel16HasilMdComponent($filters);
        }
    }

    public function title(): string
    {
        return 'Ringkasan Eksekutif';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['RINGKASAN EKSEKUTIF - MONITORING DASHBOARD PRODUKSI', '', '', '', '', '', '', ''];
        $rows[] = ['Tanggal Cetak: ' . Carbon::now()->format('d-m-Y H:i:s'), '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', ''];

        $currentRow = 4;
        $rows = array_merge($rows, $this->panel1->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel2->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel3->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel4->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel5->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel6->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel7->buildRows($currentRow));
        $rows = array_merge($rows, $this->panel8->buildRows($currentRow));

        if (!$this->isRestrictedRole) {
            $rows = array_merge($rows, $this->panel9->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel10->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel11->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel12->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel13->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel14->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel15->buildRows($currentRow));
            $rows = array_merge($rows, $this->panel16->buildRows($currentRow));
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Global header styles
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '777777']],
        ]);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

        // Apply component styles
        $this->panel1->applyStyles($sheet);
        $this->panel2->applyStyles($sheet);
        $this->panel3->applyStyles($sheet);
        $this->panel4->applyStyles($sheet);
        $this->panel5->applyStyles($sheet);
        $this->panel6->applyStyles($sheet);
        $this->panel7->applyStyles($sheet);
        $this->panel8->applyStyles($sheet);

        if (!$this->isRestrictedRole) {
            $this->panel9->applyStyles($sheet);
            $this->panel10->applyStyles($sheet);
            $this->panel11->applyStyles($sheet);
            $this->panel12->applyStyles($sheet);
            $this->panel13->applyStyles($sheet);
            $this->panel14->applyStyles($sheet);
            $this->panel15->applyStyles($sheet);
            $this->panel16->applyStyles($sheet);
        }

        return [];
    }

    public function charts()
    {
        $sheetTitle = $this->title();
        $charts = [];

        if ($c1 = $this->panel1->buildChart($sheetTitle)) {
            $charts[] = $c1;
        }
        if ($c2 = $this->panel2->buildChart($sheetTitle)) {
            $charts[] = $c2;
        }
        if ($c3 = $this->panel3->buildChart($sheetTitle)) {
            $charts[] = $c3;
        }
        if ($c6 = $this->panel6->buildChart($sheetTitle)) {
            $charts[] = $c6;
        }
        if ($c7 = $this->panel7->buildChart($sheetTitle)) {
            $charts[] = $c7;
        }
        if ($c8 = $this->panel8->buildChart($sheetTitle)) {
            $charts[] = $c8;
        }

        if (!$this->isRestrictedRole) {
            if ($c9 = $this->panel9->buildChart($sheetTitle)) {
                $charts[] = $c9;
            }
            if ($c10 = $this->panel10->buildChart($sheetTitle)) {
                $charts[] = $c10;
            }
            if ($c11 = $this->panel11->buildChart($sheetTitle)) {
                $charts[] = $c11;
            }
            if ($c12 = $this->panel12->buildChart($sheetTitle)) {
                $charts[] = $c12;
            }
            if ($c13 = $this->panel13->buildChart($sheetTitle)) {
                $charts[] = $c13;
            }
            if ($c14 = $this->panel14->buildChart($sheetTitle)) {
                $charts[] = $c14;
            }
            if ($c15 = $this->panel15->buildChart($sheetTitle)) {
                $charts[] = $c15;
            }
            if ($c16 = $this->panel16->buildChart($sheetTitle)) {
                $charts[] = $c16;
            }
        }

        return $charts;
    }
}

// ---------------------------------------------------------
// Component 1: Panel 1 - Meatprep Summary
// ---------------------------------------------------------
class Panel1MeatprepComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $legendLt80Row;
    protected $legendGe80Row;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    private function buildFilterClauses(): array
    {
        $where  = [];
        $where2 = [];
        $where3 = [];
        $where4 = [];
        $where5 = [];
        $where6 = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[]  = 'm.plan_id = :plan_id1';
            $where2[] = 'p.id_plan = :plan_id2';
            $where3[] = 'p.id_plan = :plan_id3';
            $where4[] = 'id_plan = :plan_id4';
            $where5[] = 'id_plan = :plan_id5';
            $where6[] = 'id_plan = :plan_id6';
            for ($i = 1; $i <= 6; $i++) {
                $bindings["plan_id{$i}"] = $this->filters['id_plan'];
            }
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $where[]  = 'nf.id_produk = :prod_id1';
            $where2[] = 'p.id_produk = :prod_id2';
            $where3[] = 'p.id_produk = :prod_id3';
            $where4[] = 'id_produk = :prod_id4';
            $where5[] = 'id_produk = :prod_id5';
            for ($i = 1; $i <= 5; $i++) {
                $bindings["prod_id{$i}"] = $this->filters['id_produk'];
            }
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[]  = 'm.tanggal BETWEEN :from1 AND :to1';
            $where2[] = 'p.tanggal BETWEEN :from2 AND :to2';
            $where3[] = 'p.tanggal BETWEEN :from3 AND :to3';
            $where4[] = 'tanggal BETWEEN :from4 AND :to4';
            $where5[] = 'tanggal BETWEEN :from5 AND :to5';
            $where6[] = 'tanggal BETWEEN :from6 AND :to6';
            for ($i = 1; $i <= 6; $i++) {
                $bindings["from{$i}"] = $this->filters['from_date'];
                $bindings["to{$i}"]   = $this->filters['to_date'];
            }
        }

        $buildWhere = fn(array $conds) => count($conds) ? ' AND ' . implode(' AND ', $conds) : '';

        return [
            'mixing'        => $buildWhere($where),
            'emulsi'        => $buildWhere($where2),
            'batter'        => $buildWhere($where3),
            'tumbling'      => $buildWhere($where4),
            'pemasak_nasi'  => $buildWhere($where5),
            'cooking_mixer' => $buildWhere($where6),
            'bindings'      => $bindings,
        ];
    }

    public function queryConformity(): array
    {
        $f = $this->buildFilterClauses();
        $b = $f['bindings'];

        $sql = "
        (SELECT
            'Mixing' AS metric,
            ROUND((SUM(CASE WHEN m.kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(m.kondisi)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN m.kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(m.kondisi)) * 100, 2) AS non_conformity
        FROM (
            SELECT kondisi, tanggal, plan_id, id_formula FROM persiapan_bahan_forming
            UNION ALL
            SELECT kondisi, tanggal, plan_id, id_no_formula_non_forming AS id_formula FROM persiapan_bahan_non_forming
        ) m
        LEFT JOIN nomor_formula nf ON m.id_formula = nf.id
        WHERE 1=1 {$f['mixing']})

        UNION ALL

        (SELECT
            'Emulsi' AS metric,
            ROUND((SUM(CASE WHEN s.suhu = '✔' THEN 1 ELSE 0 END) / COUNT(s.suhu)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN s.suhu = '✔' THEN 1 ELSE 0 END) / COUNT(s.suhu)) * 100, 2) AS non_conformity
        FROM suhu_emulsi s
        JOIN pembuatan_emulsi p ON s.pembuatan_emulsi_id = p.id
        WHERE 1=1 {$f['emulsi']})

        UNION ALL

        (SELECT
            'Batter' AS metric,
            ROUND((SUM(CASE WHEN CAST(REPLACE(a.aktual_vis, ',', '.') AS DECIMAL(10,2)) <= 50 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN CAST(REPLACE(a.aktual_vis, ',', '.') AS DECIMAL(10,2)) <= 50 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS non_conformity
        FROM aktual_better a
        JOIN persiapan_bahan_better p ON a.id_persiapan_bahan_better = p.id
        WHERE 1=1 {$f['batter']})

        UNION ALL

        (SELECT
            'Tumbling' AS metric,
            ROUND((SUM(CASE WHEN CAST(REPLACE(suhu_hasil_tumbling, ',', '.') AS DECIMAL(10,2)) <= 10 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN CAST(REPLACE(suhu_hasil_tumbling, ',', '.') AS DECIMAL(10,2)) <= 10 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS non_conformity
        FROM proses_tumbling
        WHERE 1=1 {$f['tumbling']})

        UNION ALL

        (SELECT
            'Pemasak Nasi' AS metric,
            ROUND((SUM(CASE WHEN sensori_kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN sensori_kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS non_conformity
        FROM pemasakan_nasi
        WHERE 1=1 {$f['pemasak_nasi']})

        UNION ALL

        (SELECT
            'Cooking Mixer' AS metric,
            ROUND((SUM(CASE WHEN sensori_kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,
            ROUND((1 - SUM(CASE WHEN sensori_kondisi = '✔' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS non_conformity
        FROM pemeriksaan_produk_cooking_mixer_fla
        WHERE 1=1 {$f['cooking_mixer']})
        ";

        try {
            $results = DB::select($sql, $b);
        } catch (\Exception $e) {
            $metrics = ['Mixing', 'Emulsi', 'Batter', 'Tumbling', 'Pemasak Nasi', 'Cooking Mixer'];
            return array_map(fn($m) => (object)['metric' => $m, 'conformity' => null, 'non_conformity' => null], $metrics);
        }

        return $results;
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryConformity();

        $rows = [];
        $rows[] = ['PANEL 1 - MEATPREP (% KESESUAIAN)', '', ''];
        $rows[] = ['Proses', 'Conformity (%)', 'Non Conformity (%)'];

        $this->dataStartRow = $this->startRow + 2;

        foreach ($this->data as $item) {
            $conf    = is_null($item->conformity)     ? 0 : (float) $item->conformity;
            $nonConf = is_null($item->non_conformity) ? 0 : (float) $item->non_conformity;
            $rows[]  = [$item->metric, $conf, $nonConf];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = [''];
        $rows[] = ['Legend:'];
        $rows[] = ['■ < 80%  (Merah - Non Conformity Tinggi)', '', ''];
        $this->legendLt80Row = $this->dataEndRow + 3;
        $rows[] = ['■ ≥ 80%  (Hijau  - Conformity Baik)', '', ''];
        $this->legendGe80Row = $this->dataEndRow + 4;
        $rows[] = ['']; // Blank separator row

        $currentRow = $this->legendGe80Row + 2;
        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $confCell = $sheet->getCell("B{$row}");
            $confVal  = (float) ($confCell->getValue() ?? 0);
            $barColor = $confVal >= 80 ? '27AE60' : 'E74C3C';
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $barColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Legend styles
        $sheet->getStyle("A{$this->legendLt80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => 'E74C3C'], 'bold' => true],
        ]);
        $sheet->getStyle("A{$this->legendGe80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => '27AE60'], 'bold' => true],
        ]);
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Conformity (%)']),
        ];
        $labelNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Non Conformity (%)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];
        $seriesNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STACKED,
            [0, 1],
            [$labelConformity[0], $labelNonConformity[0]],
            $categories,
            [$seriesConformity[0], $seriesNonConformity[0]]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Process: % Kesesuaian - Meatprep');

        $chart = new Chart(
            'meatprep_conformity_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow    = "G{$this->startRow}";
        $bottomRow = "O" . ($this->legendGe80Row + 1);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 2: Panel 2 - Cooking Summary
// ---------------------------------------------------------
class Panel2CookingComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $legendLt80Row;
    protected $legendGe80Row;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    private function buildFilterClauses(): array
    {
        $whereFrying   = [];
        $whereRoasting = [];
        $bindings      = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $whereFrying[]   = 'h.id_plan = :plan_id1';
            $whereRoasting[] = 'h.id_plan = :plan_id2';
            $bindings['plan_id1'] = $this->filters['id_plan'];
            $bindings['plan_id2'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $whereFrying[]   = 'h.id_produk = :prod_id1';
            $whereRoasting[] = 'h.id_produk = :prod_id2';
            $bindings['prod_id1'] = $this->filters['id_produk'];
            $bindings['prod_id2'] = $this->filters['id_produk'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $whereFrying[]   = 'h.tanggal BETWEEN :from1 AND :to1';
            $whereRoasting[] = 'h.tanggal BETWEEN :from2 AND :to2';
            $bindings['from1'] = $this->filters['from_date'];
            $bindings['to1']   = $this->filters['to_date'];
            $bindings['from2'] = $this->filters['from_date'];
            $bindings['to2']   = $this->filters['to_date'];
        }

        $buildWhere = fn(array $conds) => count($conds) ? ' AND ' . implode(' AND ', $conds) : '';

        return [
            'frying'   => $buildWhere($whereFrying),
            'roasting' => $buildWhere($whereRoasting),
            'bindings' => $bindings,
        ];
    }

    public function queryConformity(): array
    {
        $f = $this->buildFilterClauses();
        $b = $f['bindings'];

        $sql = "
        (SELECT 
            'Frying' AS metric,
            ROUND((SUM(CASE WHEN 
                CAST(REPLACE(SUBSTRING_INDEX(h.aktual_suhu_pusat, '/', 1), ',', '.') AS DECIMAL(10,2)) 
                BETWEEN 
                    CAST(SUBSTRING_INDEX(s.std_suhu_pusat, '-', 1) AS DECIMAL(10,2)) 
                    AND 
                    CAST(SUBSTRING_INDEX(s.std_suhu_pusat, '-', -1) AS DECIMAL(10,2))
                AND h.sensori_kematangan = '✔' AND h.sensori_kenampakan = '✔' AND h.sensori_warna = '✔'
                AND h.sensori_rasa = '✔' AND h.sensori_bau = '✔' AND h.sensori_tekstur = '✔'
            THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,
            
            ROUND((1 - (SUM(CASE WHEN 
                CAST(REPLACE(SUBSTRING_INDEX(h.aktual_suhu_pusat, '/', 1), ',', '.') AS DECIMAL(10,2)) 
                BETWEEN 
                    CAST(SUBSTRING_INDEX(s.std_suhu_pusat, '-', 1) AS DECIMAL(10,2)) 
                    AND 
                    CAST(SUBSTRING_INDEX(s.std_suhu_pusat, '-', -1) AS DECIMAL(10,2))
                AND h.sensori_kematangan = '✔' AND h.sensori_kenampakan = '✔' AND h.sensori_warna = '✔'
                AND h.sensori_rasa = '✔' AND h.sensori_bau = '✔' AND h.sensori_tekstur = '✔'
            THEN 1 ELSE 0 END) / COUNT(*))) * 100, 2) AS non_conformity

        FROM hasil_penggorengan h
        JOIN std_suhu_pusat s ON h.id_std_suhu_pusat = s.id
        WHERE 1=1 {$f['frying']})

        UNION ALL

        (SELECT 
            'Roasting' AS metric,
            ROUND((SUM(CASE WHEN 
                CAST(REPLACE(SUBSTRING_INDEX(JSON_UNQUOTE(h.aktual_suhu_pusat), '/', 1), ',', '.') AS DECIMAL(10,2)) 
                BETWEEN 
                    CAST(SUBSTRING_INDEX(sr.std_suhu_pusat_roasting, '-', 1) AS DECIMAL(10,2)) 
                    AND 
                    CAST(SUBSTRING_INDEX(sr.std_suhu_pusat_roasting, '-', -1) AS DECIMAL(10,2))
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_kematangan')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_kenampakan')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_warna')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_rasa')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_bau')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_tekstur')) = '✔'
            THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS conformity,

            ROUND((1 - (SUM(CASE WHEN 
                CAST(REPLACE(SUBSTRING_INDEX(JSON_UNQUOTE(h.aktual_suhu_pusat), '/', 1), ',', '.') AS DECIMAL(10,2)) 
                BETWEEN 
                    CAST(SUBSTRING_INDEX(sr.std_suhu_pusat_roasting, '-', 1) AS DECIMAL(10,2)) 
                    AND 
                    CAST(SUBSTRING_INDEX(sr.std_suhu_pusat_roasting, '-', -1) AS DECIMAL(10,2))
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_kematangan')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_kenampakan')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_warna')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_rasa')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_bau')) = '✔'
                AND JSON_UNQUOTE(JSON_EXTRACT(h.sensori, '$.sensori_tekstur')) = '✔'
            THEN 1 ELSE 0 END) / COUNT(*))) * 100, 2) AS non_conformity

        FROM hasil_proses_roasting h
        JOIN std_suhu_pusat_roasting sr ON h.id_std_suhu_pusat = sr.id
        WHERE 1=1 {$f['roasting']})
        ";

        try {
            $results = DB::select($sql, $b);
        } catch (\Exception $e) {
            $metrics = ['Frying', 'Roasting'];
            return array_map(fn($m) => (object)['metric' => $m, 'conformity' => null, 'non_conformity' => null], $metrics);
        }

        return $results;
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryConformity();

        $rows = [];
        $rows[] = ['PANEL 2 - COOKING (% KESESUAIAN)', '', ''];
        $rows[] = ['Proses', 'Conformity (%)', 'Non Conformity (%)'];

        $this->dataStartRow = $this->startRow + 2;

        foreach ($this->data as $item) {
            $conf    = is_null($item->conformity)     ? 0 : (float) $item->conformity;
            $nonConf = is_null($item->non_conformity) ? 0 : (float) $item->non_conformity;
            $rows[]  = [$item->metric, $conf, $nonConf];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = [''];
        $rows[] = ['Legend:'];
        $rows[] = ['■ < 80%  (Merah - Non Conformity Tinggi)', '', ''];
        $this->legendLt80Row = $this->dataEndRow + 3;
        $rows[] = ['■ ≥ 80%  (Hijau  - Conformity Baik)', '', ''];
        $this->legendGe80Row = $this->dataEndRow + 4;
        $rows[] = ['']; // Separator row

        $currentRow = $this->legendGe80Row + 2;
        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $confCell = $sheet->getCell("B{$row}");
            $confVal  = (float) ($confCell->getValue() ?? 0);
            $barColor = $confVal >= 80 ? '27AE60' : 'E74C3C';
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $barColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Legend styles
        $sheet->getStyle("A{$this->legendLt80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => 'E74C3C'], 'bold' => true],
        ]);
        $sheet->getStyle("A{$this->legendGe80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => '27AE60'], 'bold' => true],
        ]);
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Conformity (%)']),
        ];
        $labelNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Non Conformity (%)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];
        $seriesNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STACKED,
            [0, 1],
            [$labelConformity[0], $labelNonConformity[0]],
            $categories,
            [$seriesConformity[0], $seriesNonConformity[0]]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Cooking: % Kesesuaian');

        $chart = new Chart(
            'cooking_conformity_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow    = "G{$this->startRow}";
        $bottomRow = "O" . ($this->legendGe80Row + 1);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 3: Panel 3 - Packing & Cartoning Summary
// ---------------------------------------------------------
class Panel3PackingCartoningComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $legendLt80Row;
    protected $legendGe80Row;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    private function buildFilterClauses(): array
    {
        $wherePack = [];
        $whereBox  = [];
        $whereMD   = [];
        $bindings  = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $wherePack[] = 'pp.id_plan = :plan_id1';
            $whereBox[]  = 'pp.id_plan = :plan_id2';
            $whereMD[]   = 'id_plan = :plan_id3';
            $bindings['plan_id1'] = $this->filters['id_plan'];
            $bindings['plan_id2'] = $this->filters['id_plan'];
            $bindings['plan_id3'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $wherePack[] = 'pp.id_produk = :prod_id1';
            $whereBox[]  = 'pp.id_produk = :prod_id2';
            $whereMD[]   = 'id_produk = :prod_id3';
            $bindings['prod_id1'] = $this->filters['id_produk'];
            $bindings['prod_id2'] = $this->filters['id_produk'];
            $bindings['prod_id3'] = $this->filters['id_produk'];
        }

        $fromDate = !empty($this->filters['from_date']) 
            ? Carbon::parse($this->filters['from_date'])->format('Y-m-d H:i:s') 
            : Carbon::now()->subHours(24)->format('Y-m-d H:i:s');

        $toDate = !empty($this->filters['to_date']) 
            ? Carbon::parse($this->filters['to_date'])->format('Y-m-d H:i:s') 
            : Carbon::now()->format('Y-m-d H:i:s');

        $wherePack[] = 'p.tanggal BETWEEN DATE(:from1) AND DATE(:to1)';
        $whereBox[]  = 'k.tanggal BETWEEN DATE(:from2) AND DATE(:to2)';
        $whereMD[]   = 'tanggal BETWEEN DATE(:from3) AND DATE(:to3)';
        $bindings['from1'] = $fromDate;
        $bindings['to1']   = $toDate;
        $bindings['from2'] = $fromDate;
        $bindings['to2']   = $toDate;
        $bindings['from3'] = $fromDate;
        $bindings['to3']   = $toDate;

        $buildWhere = fn(array $conds) => count($conds) ? ' AND ' . implode(' AND ', $conds) : '';

        return [
            'per_pack' => $buildWhere($wherePack),
            'per_box'  => $buildWhere($whereBox),
            'md'       => $buildWhere($whereMD),
            'bindings' => $bindings,
        ];
    }

    private function buildDatetimeRange(): array
    {
        $fromDate = !empty($this->filters['from_date']) 
            ? Carbon::parse($this->filters['from_date'])->format('Y-m-d H:i:s') 
            : Carbon::now()->subHours(24)->format('Y-m-d H:i:s');

        $toDate = !empty($this->filters['to_date']) 
            ? Carbon::parse($this->filters['to_date'])->format('Y-m-d H:i:s') 
            : Carbon::now()->format('Y-m-d H:i:s');

        return ['from' => $fromDate, 'to' => $toDate];
    }

    private function getPlanId(): ?string
    {
        $plan = $this->filters['id_plan'] ?? null;
        if (empty($plan) || $plan === '$__all' || $plan === 'all') {
            return null;
        }
        return $plan;
    }

    public function queryConformity(): array
    {
        $dt     = $this->buildDatetimeRange();
        $planId = $this->getPlanId();
        $results = [];

        // --- Per Pack ---
        try {
            $packWhere = 'p.tanggal >= :from_pack AND p.tanggal <= :to_pack';
            $packBindings = ['from_pack' => $dt['from'], 'to_pack' => $dt['to']];
            if ($planId) {
                $packWhere .= ' AND pp.id_plan = :plan_pack';
                $packBindings['plan_pack'] = $planId;
            }
            $packSql = "
                SELECT 
                    'Per Pack' AS metric,
                    ROUND(
                        (SUM(CASE 
                            WHEN (p.kekuatan_seal LIKE '%✔%' AND p.kesesuaian_label_plastik = 'OK')
                            AND b.berat_aktual IS NOT NULL
                            AND b.berat_aktual != ''
                            AND JSON_VALID(b.berat_aktual) = 1
                            AND (SELECT AVG(CAST(j.val AS DECIMAL(10,2))) FROM JSON_TABLE(b.berat_aktual, '\$[*]' COLUMNS(val VARCHAR(255) PATH '\$')) AS j)
                                BETWEEN (CAST(SUBSTRING_INDEX(db.std_bag, '±', 1) AS DECIMAL(10,2)) - CAST(SUBSTRING_INDEX(db.std_bag, '±', -1) AS DECIMAL(10,2)))
                                    AND (CAST(SUBSTRING_INDEX(db.std_bag, '±', 1) AS DECIMAL(10,2)) + CAST(SUBSTRING_INDEX(db.std_bag, '±', -1) AS DECIMAL(10,2)))
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2
                    ) AS conformity,
                    ROUND(
                        (1 - (SUM(CASE 
                            WHEN (p.kekuatan_seal LIKE '%✔%' AND p.kesesuaian_label_plastik = 'OK')
                            AND b.berat_aktual IS NOT NULL
                            AND b.berat_aktual != ''
                            AND JSON_VALID(b.berat_aktual) = 1
                            AND (SELECT AVG(CAST(j.val AS DECIMAL(10,2))) FROM JSON_TABLE(b.berat_aktual, '\$[*]' COLUMNS(val VARCHAR(255) PATH '\$')) AS j)
                                BETWEEN (CAST(SUBSTRING_INDEX(db.std_bag, '±', 1) AS DECIMAL(10,2)) - CAST(SUBSTRING_INDEX(db.std_bag, '±', -1) AS DECIMAL(10,2)))
                                    AND (CAST(SUBSTRING_INDEX(db.std_bag, '±', 1) AS DECIMAL(10,2)) + CAST(SUBSTRING_INDEX(db.std_bag, '±', -1) AS DECIMAL(10,2)))
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0))) * 100, 2
                    ) AS non_conformity
                FROM pengemasan_plastik p
                LEFT JOIN pengemasan_produk pp ON p.id_pengemasan_produk = pp.id
                LEFT JOIN berat_produk_bag b ON p.id = b.id_pengemasan_plastik
                LEFT JOIN data_bag db ON pp.id_produk = db.id_produk
                WHERE {$packWhere}
            ";
            $packResult = DB::select($packSql, $packBindings);
            $results[] = $packResult[0] ?? (object)['metric' => 'Per Pack', 'conformity' => null, 'non_conformity' => null];
        } catch (\Exception $e) {
            $results[] = (object)['metric' => 'Per Pack', 'conformity' => null, 'non_conformity' => null];
        }

        // --- Per Box ---
        try {
            $boxWhere = 'k.tanggal >= :from_box AND k.tanggal <= :to_box';
            $boxBindings = ['from_box' => $dt['from'], 'to_box' => $dt['to']];
            if ($planId) {
                $boxWhere .= ' AND pp.id_plan = :plan_box';
                $boxBindings['plan_box'] = $planId;
            }
            $boxSql = "
                SELECT 
                    'Per Box' AS metric,
                    ROUND(
                        (SUM(CASE 
                            WHEN k.identitas_produk_pada_karton LIKE '%✔%'
                            AND bx.berat_aktual IS NOT NULL
                            AND bx.berat_aktual != ''
                            AND JSON_VALID(bx.berat_aktual) = 1
                            AND (SELECT AVG(CAST(j.val AS DECIMAL(10,2))) FROM JSON_TABLE(bx.berat_aktual, '\$[*]' COLUMNS(val VARCHAR(255) PATH '\$')) AS j)
                                BETWEEN (CAST(SUBSTRING_INDEX(dbx.std_box, '±', 1) AS DECIMAL(10,2)) - CAST(SUBSTRING_INDEX(dbx.std_box, '±', -1) AS DECIMAL(10,2)))
                                    AND (CAST(SUBSTRING_INDEX(dbx.std_box, '±', 1) AS DECIMAL(10,2)) + CAST(SUBSTRING_INDEX(dbx.std_box, '±', -1) AS DECIMAL(10,2)))
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2
                    ) AS conformity,
                    ROUND(
                        (1 - (SUM(CASE 
                            WHEN k.identitas_produk_pada_karton LIKE '%✔%'
                            AND bx.berat_aktual IS NOT NULL
                            AND bx.berat_aktual != ''
                            AND JSON_VALID(bx.berat_aktual) = 1
                            AND (SELECT AVG(CAST(j.val AS DECIMAL(10,2))) FROM JSON_TABLE(bx.berat_aktual, '\$[*]' COLUMNS(val VARCHAR(255) PATH '\$')) AS j)
                                BETWEEN (CAST(SUBSTRING_INDEX(dbx.std_box, '±', 1) AS DECIMAL(10,2)) - CAST(SUBSTRING_INDEX(dbx.std_box, '±', -1) AS DECIMAL(10,2)))
                                    AND (CAST(SUBSTRING_INDEX(dbx.std_box, '±', 1) AS DECIMAL(10,2)) + CAST(SUBSTRING_INDEX(dbx.std_box, '±', -1) AS DECIMAL(10,2)))
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0))) * 100, 2
                    ) AS non_conformity
                FROM pengemasan_karton k
                LEFT JOIN pengemasan_produk pp ON k.id_pengemasan_produk = pp.id
                LEFT JOIN berat_produk_box bx ON k.id_berat_produk_box = bx.id
                LEFT JOIN data_box dbx ON pp.id_produk = dbx.id_produk
                WHERE {$boxWhere}
            ";
            $boxResult = DB::select($boxSql, $boxBindings);
            $results[] = $boxResult[0] ?? (object)['metric' => 'Per Box', 'conformity' => null, 'non_conformity' => null];
        } catch (\Exception $e) {
            $results[] = (object)['metric' => 'Per Box', 'conformity' => null, 'non_conformity' => null];
        }

        // --- MD ---
        try {
            $mdWhere = 'tanggal >= :from_md AND tanggal <= :to_md';
            $mdBindings = ['from_md' => $dt['from'], 'to_md' => $dt['to']];
            if ($planId) {
                $mdWhere .= ' AND id_plan = :plan_md';
                $mdBindings['plan_md'] = $planId;
            }
            $mdSql = "
                SELECT 
                    'MD' AS metric,
                    ROUND(
                        (SUM(CASE 
                            WHEN JSON_EXTRACT(aktual_json_metal, '$.fe_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.fe_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.fe_belakang_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_belakang_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_belakang_aktual') LIKE '%✔%'
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2
                    ) AS conformity,
                    ROUND(
                        (1 - (SUM(CASE 
                            WHEN JSON_EXTRACT(aktual_json_metal, '$.fe_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.fe_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.fe_belakang_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.non_fe_belakang_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_depan_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_tengah_aktual') LIKE '%✔%'
                             AND JSON_EXTRACT(aktual_json_metal, '$.sus_belakang_aktual') LIKE '%✔%'
                            THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0))) * 100, 2
                    ) AS non_conformity
                FROM input_metal_detector
                WHERE {$mdWhere}
            ";
            $mdResult = DB::select($mdSql, $mdBindings);
            $results[] = $mdResult[0] ?? (object)['metric' => 'MD', 'conformity' => null, 'non_conformity' => null];
        } catch (\Exception $e) {
            $results[] = (object)['metric' => 'MD', 'conformity' => null, 'non_conformity' => null];
        }

        return $results;
    }




    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryConformity();

        $rows = [];
        $rows[] = ['PANEL 3 - PACKING & CARTONING (% KESESUAIAN PRODUK)', '', ''];
        $rows[] = ['Proses', 'Conformity (%)', 'Non Conformity (%)'];

        $this->dataStartRow = $this->startRow + 2;

        foreach ($this->data as $item) {
            $conf    = is_null($item->conformity)     ? 0 : (float) $item->conformity;
            $nonConf = is_null($item->non_conformity) ? 0 : (float) $item->non_conformity;
            $rows[]  = [$item->metric, $conf, $nonConf];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = [''];
        $rows[] = ['Legend:'];
        $rows[] = ['■ < 80%  (Merah - Non Conformity Tinggi)', '', ''];
        $this->legendLt80Row = $this->dataEndRow + 3;
        $rows[] = ['■ ≥ 80%  (Hijau  - Conformity Baik)', '', ''];
        $this->legendGe80Row = $this->dataEndRow + 4;
        $rows[] = ['']; // Separator row

        $currentRow = $this->legendGe80Row + 2;
        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $confCell = $sheet->getCell("B{$row}");
            $confVal  = (float) ($confCell->getValue() ?? 0);
            $barColor = $confVal >= 80 ? '27AE60' : 'E74C3C';
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $barColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Legend styles
        $sheet->getStyle("A{$this->legendLt80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => 'E74C3C'], 'bold' => true],
        ]);
        $sheet->getStyle("A{$this->legendGe80Row}")->applyFromArray([
            'font' => ['color' => ['rgb' => '27AE60'], 'bold' => true],
        ]);
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Conformity (%)']),
        ];
        $labelNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Non Conformity (%)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];
        $seriesNonConformity = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STACKED,
            [0, 1],
            [$labelConformity[0], $labelNonConformity[0]],
            $categories,
            [$seriesConformity[0], $seriesNonConformity[0]]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Process: % Kesesuaian Produk');

        $chart = new Chart(
            'packing_cartoning_conformity_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow    = "G{$this->startRow}";
        $bottomRow = "O" . ($this->legendGe80Row + 1);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 4: Panel 4 - Current Processing
// ---------------------------------------------------------
class Panel4CurrentProcessingComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = AllProcessExport::queryPanelData(0, $this->filters);

        $rows = [];
        $rows[] = ['PANEL 4 - CURRENT PROCESSING (STATUS TERKINI PRODUKSI)', '', '', '', '', ''];
        $rows[] = ['No', 'Bar Indicator', 'Nama Proses', 'Nama Produk', 'Kode Produksi', 'Jam'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                ' ',
                $item->process ?? '-',
                $item->product ?? '-',
                $item->code ?? '-',
                $item->jam ?? '-',
            ];
        }
        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:F{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row  = $this->dataStartRow + $i;
            $item = $this->data[$i];

            $rawColor = !empty($item->color) ? $item->color : '#3B82F6';
            $hexColor = strtoupper(ltrim($rawColor, '#'));

            $bgColor = ($i % 2 === 0) ? 'F8FAFC' : 'F1F5F9';
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hexColor]],
            ]);

            $sheet->getStyle("C{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $hexColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $sheet->getStyle("D{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        return null;
    }
}

// ---------------------------------------------------------
// Component 5: Panel 5 - Produksi Sebelumnya
// ---------------------------------------------------------
class Panel5ProduksiSebelumnyaComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = AllProcessExport::queryPanelData(1, $this->filters);

        $rows = [];
        $rows[] = ['PANEL 5 - PRODUKSI SEBELUMNYA (BATCH BATCH SEBELUMNYA)', '', '', '', '', ''];
        $rows[] = ['No', 'Bar Indicator', 'Nama Proses', 'Nama Produk', 'Kode Produksi', 'Jam'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                ' ',
                $item->process ?? '-',
                $item->product ?? '-',
                $item->code ?? '-',
                $item->jam ?? '-',
            ];
        }
        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:F{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row  = $this->dataStartRow + $i;
            $item = $this->data[$i];

            $rawColor = !empty($item->color) ? $item->color : '#3B82F6';
            $hexColor = strtoupper(ltrim($rawColor, '#'));

            $bgColor = ($i % 2 === 0) ? 'F8FAFC' : 'F1F5F9';
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hexColor]],
            ]);

            $sheet->getStyle("C{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $hexColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $sheet->getStyle("D{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        return null;
    }
}

// ---------------------------------------------------------
// Component 6: Panel 6 - Suhu Ruang (°C)
// ---------------------------------------------------------
class Panel6SuhuRuangComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function querySuhuRuang(): array
    {
        $whereSub  = ['ap.pemeriksaan_suhu_ruang IS NOT NULL', "ap.pemeriksaan_suhu_ruang != ''"];
        $bindings  = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $whereSub[] = 'ap.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $whereSub[] = 'ap.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $wSub = count($whereSub) ? implode(' AND ', $whereSub) : '1=1';

        $sql = "
        SELECT 
          metric,
          ROUND(
            (SUM(CASE WHEN is_sesuai = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2
          ) AS standar,
          ROUND(
            (SUM(CASE WHEN is_sesuai = 0 THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 2
          ) AS tidak_standar
        FROM (
          SELECT 
            CASE 
              WHEN LOWER(TRIM(ia.area)) LIKE '%chillroom%' AND LOWER(TRIM(ia.area)) NOT LIKE '%forming%' THEN 'Chillroom'
              WHEN LOWER(TRIM(ia.area)) LIKE '%seasoning%' THEN 'Seasoning'
              WHEN LOWER(TRIM(ia.area)) LIKE '%meatprep%' OR LOWER(TRIM(ia.area)) LIKE '%meat preparation%' OR LOWER(TRIM(ia.area)) LIKE '%meatpreparation%' THEN 'Meatprep'
              WHEN LOWER(TRIM(ia.area)) LIKE '%cooking%' THEN 'Cooking'
              WHEN LOWER(TRIM(ia.area)) LIKE '%packing primer%' OR LOWER(TRIM(ia.area)) LIKE '%packing primary%' OR LOWER(TRIM(ia.area)) LIKE '%packing dalam%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan primer%' THEN 'Packing Primer'
              WHEN LOWER(TRIM(ia.area)) LIKE '%packing sekunder%' OR LOWER(TRIM(ia.area)) LIKE '%packing secondary%' OR LOWER(TRIM(ia.area)) LIKE '%packing luar%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan sekunder%' THEN 'Packing Sekunder'
              ELSE ia.area
            END AS metric,
            CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) AS suhu,
            CASE 
              WHEN LOWER(TRIM(ia.area)) LIKE '%chillroom%' AND LOWER(TRIM(ia.area)) NOT LIKE '%forming%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) BETWEEN 0 AND 4 THEN 1 ELSE 0 END
              WHEN LOWER(TRIM(ia.area)) LIKE '%seasoning%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) BETWEEN 25 AND 35 THEN 1 ELSE 0 END
              WHEN LOWER(TRIM(ia.area)) LIKE '%meatprep%' OR LOWER(TRIM(ia.area)) LIKE '%meat preparation%' OR LOWER(TRIM(ia.area)) LIKE '%meatpreparation%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) <= 15 THEN 1 ELSE 0 END
              WHEN LOWER(TRIM(ia.area)) LIKE '%cooking%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) BETWEEN 20 AND 30 THEN 1 ELSE 0 END
              WHEN LOWER(TRIM(ia.area)) LIKE '%packing primer%' OR LOWER(TRIM(ia.area)) LIKE '%packing primary%' OR LOWER(TRIM(ia.area)) LIKE '%packing dalam%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan primer%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) BETWEEN 8 AND 12 THEN 1 ELSE 0 END
              WHEN LOWER(TRIM(ia.area)) LIKE '%packing sekunder%' OR LOWER(TRIM(ia.area)) LIKE '%packing secondary%' OR LOWER(TRIM(ia.area)) LIKE '%packing luar%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan sekunder%'
                THEN CASE WHEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) BETWEEN 8 AND 12 THEN 1 ELSE 0 END
              ELSE NULL
            END AS is_sesuai
          FROM area_proses ap
          INNER JOIN input_area ia ON ap.area_id = ia.id
          WHERE {$wSub}
            AND (
              LOWER(ia.area) LIKE '%chillroom%'
              OR LOWER(ia.area) LIKE '%seasoning%'
              OR LOWER(ia.area) LIKE '%meatprep%'
              OR LOWER(ia.area) LIKE '%meat preparation%'
              OR LOWER(ia.area) LIKE '%meatpreparation%'
              OR LOWER(ia.area) LIKE '%cooking%'
              OR LOWER(ia.area) LIKE '%packing%'
              OR LOWER(ia.area) LIKE '%pengemasan%'
            )
            AND LOWER(ia.area) NOT LIKE '%forming%'
        ) AS base
        WHERE is_sesuai IS NOT NULL
        GROUP BY metric
        ORDER BY FIELD(metric, 'Chillroom', 'Meatprep', 'Seasoning', 'Cooking', 'Packing Primer', 'Packing Sekunder')
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        $defaultAreas = ['Chillroom', 'Meatprep', 'Seasoning', 'Cooking', 'Packing Primer', 'Packing Sekunder'];
        return array_map(fn($a) => (object)['metric' => $a, 'standar' => 0, 'tidak_standar' => 0], $defaultAreas);
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->querySuhuRuang();

        $rows = [];
        $rows[] = ['PANEL 6 - SUHU RUANG (°C)', '', ''];
        $rows[] = ['Area Process', 'Standar (%)', 'Tidak Standar (%)'];

        $this->dataStartRow = $this->startRow + 2;
        foreach ($this->data as $item) {
            $standar      = is_null($item->standar)       ? 0 : (float) $item->standar;
            $tidakStandar = is_null($item->tidak_standar) ? 0 : (float) $item->tidak_standar;
            $rows[]  = [$item->metric, $standar, $tidakStandar];
        }
        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        // Legend rows
        $rows[] = [''];
        $rows[] = ['Legend:', '', ''];
        $rows[] = ['■ Standar (%)', '■ Tidak Standar (%)', ''];

        $currentRow = $this->dataEndRow + 4;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row     = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Standar (%) — green bar fill based on value
            $standarVal = (float) ($sheet->getCell("B{$row}")->getValue() ?? 0);
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Tidak Standar (%) — yellow/amber fill
            $tidakVal = (float) ($sheet->getCell("C{$row}")->getValue() ?? 0);
            $sheet->getStyle("C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1C40F']],
                'font'      => ['bold' => true, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Legend styling
        $legendRow = $this->dataEndRow + 2;
        $sheet->getStyle("A{$legendRow}")->applyFromArray([
            'font' => ['bold' => true],
        ]);
        $legendDataRow = $legendRow + 1;
        $sheet->getStyle("A{$legendDataRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
        ]);
        $sheet->getStyle("B{$legendDataRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1C40F']],
            'font' => ['color' => ['rgb' => '000000'], 'bold' => true],
        ]);
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);

        // Series 1: Standar (%) — Green
        $labelStandar = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Standar (%)']);
        $valStandar   = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}",
            null, $rowCount
        );
        $valStandar->setFillColor('27AE60');

        // Series 2: Tidak Standar (%) — Yellow
        $labelTidak = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Tidak Standar (%)']);
        $valTidak   = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}",
            null, $rowCount
        );
        $valTidak->setFillColor('F1C40F');

        // Categories (area labels)
        $categories = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}",
                null, $rowCount
            ),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STACKED,
            [0, 1],
            [$labelStandar, $labelTidak],
            $categories,
            [$valStandar, $valTidak]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Suhu Ruang (°C)');

        $chart = new Chart(
            'suhu_ruang_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow    = "E{$this->startRow}";
        $bottomRow = "N" . ($this->dataEndRow + 8);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}


// ---------------------------------------------------------
// Component 7: Panel 7 - Trend Suhu All Area
// ---------------------------------------------------------
class Panel7TrendSuhuComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryTrendSuhu(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'ap.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'ap.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT
            ap.tanggal,
            ap.jam,
            CONCAT('Suhu ', ia.area) AS metric,
            CASE 
              WHEN ap.pemeriksaan_suhu_ruang REGEXP '^[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(ap.pemeriksaan_suhu_ruang, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END AS value
        FROM area_proses ap
        JOIN input_area ia ON ap.area_id = ia.id
        WHERE ap.is_audit = 0 {$w}
          AND ap.pemeriksaan_suhu_ruang IS NOT NULL
          AND ap.pemeriksaan_suhu_ruang != ''
          AND ap.pemeriksaan_suhu_ruang REGEXP '^[0-9]+([.,][0-9]+)?$'
        ORDER BY ap.tanggal ASC, ap.jam ASC, ia.id ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryTrendSuhu();

        $rows = [];
        $rows[] = ['PANEL 7 - TREND SUHU ALL AREA', '', '', '', ''];
        $rows[] = ['No', 'Tanggal', 'Jam', 'Area / Metric', 'Suhu Ruang (°C)'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tgl = $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $jam = $item->jam ?: '-';
            $val = is_null($item->value) ? 0 : (float) $item->value;

            $rows[] = [
                $no++,
                $tgl,
                $jam,
                $item->metric ?? '-',
                $val,
            ];
        }

        if (empty($this->data)) {
            $rows[] = [1, '-', '-', '-', 0];
            $this->dataEndRow = $this->dataStartRow;
        } else {
            $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;
        }

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:E{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Tanggal, Jam centered
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Metric left align, bold
            $sheet->getStyle("D{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Suhu Ruang value cell (Col E) with green bar accent
            $sheet->getStyle("E{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelSeries = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu (°C)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$E\${$this->dataStartRow}:\$E\${$this->dataEndRow}", null, $rowCount),
        ];

        // Line Chart for Trend over time
        $dataSeries = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [$labelSeries[0]],
            $categories,
            [$seriesValues[0]]
        );

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Trend Suhu All Area');

        $chart = new Chart(
            'trend_suhu_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "G{$this->startRow}";
        $chartHeight = max(12, min(30, $rowCount + 4));
        $bottomRow   = "O" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 8: Panel 8 - Cleaning Area
// ---------------------------------------------------------
class Panel8CleaningAreaComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryCleaningArea(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'ap.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'ap.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT 
          CASE 
            WHEN LOWER(TRIM(ia.area)) LIKE '%chillroom%' AND LOWER(TRIM(ia.area)) NOT LIKE '%forming%' THEN 'Chillroom'
            WHEN LOWER(TRIM(ia.area)) LIKE '%seasoning%' THEN 'Seasoning'
            WHEN LOWER(TRIM(ia.area)) LIKE '%meatprep%' OR LOWER(TRIM(ia.area)) LIKE '%meat preparation%' OR LOWER(TRIM(ia.area)) LIKE '%meatpreparation%' THEN 'Meatprep'
            WHEN LOWER(TRIM(ia.area)) LIKE '%cooking%' THEN 'Cooking'
            WHEN LOWER(TRIM(ia.area)) LIKE '%packing primer%' OR LOWER(TRIM(ia.area)) LIKE '%packing primary%' OR LOWER(TRIM(ia.area)) LIKE '%packing dalam%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan primer%' THEN 'Packing Primer'
            WHEN LOWER(TRIM(ia.area)) LIKE '%packing sekunder%' OR LOWER(TRIM(ia.area)) LIKE '%packing secondary%' OR LOWER(TRIM(ia.area)) LIKE '%packing luar%' OR LOWER(TRIM(ia.area)) LIKE '%pengemasan sekunder%' THEN 'Packing Sekunder'
            ELSE ia.area
          END AS metric,
          
          SUM(CASE WHEN ap.kebersihan_ruangan = 'OK' THEN 1 ELSE 0 END) +
          SUM(CASE WHEN ap.kebersihan_karyawan = 'OK' THEN 1 ELSE 0 END) +
          SUM(CASE WHEN ap.kondisi_barang = 'Baik' THEN 1 ELSE 0 END) AS closed,
          
          SUM(CASE WHEN ap.kebersihan_ruangan = 'Kotor' THEN 1 ELSE 0 END) +
          SUM(CASE WHEN ap.kebersihan_karyawan = 'Kotor' THEN 1 ELSE 0 END) +
          SUM(CASE WHEN ap.kondisi_barang = 'Rusak' THEN 1 ELSE 0 END) AS open
        FROM area_proses ap
        INNER JOIN input_area ia ON ap.area_id = ia.id
        WHERE 1=1 {$w}
          AND (
            LOWER(ia.area) LIKE '%chillroom%'
            OR LOWER(ia.area) LIKE '%seasoning%'
            OR LOWER(ia.area) LIKE '%meatprep%'
            OR LOWER(ia.area) LIKE '%meat preparation%'
            OR LOWER(ia.area) LIKE '%meatpreparation%'
            OR LOWER(ia.area) LIKE '%cooking%'
            OR LOWER(ia.area) LIKE '%packing%'
            OR LOWER(ia.area) LIKE '%pengemasan%'
          )
          AND LOWER(ia.area) NOT LIKE '%forming%'
        GROUP BY ia.id, ia.area
        ORDER BY ia.id ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        $defaultAreas = ['Chillroom', 'Seasoning', 'Meatprep', 'Cooking', 'Packing Primer', 'Packing Sekunder'];
        return array_map(fn($a) => (object)['metric' => $a, 'closed' => 0, 'open' => 0], $defaultAreas);
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryCleaningArea();

        $rows = [];
        $rows[] = ['PANEL 8 - CLEANING AREA', '', ''];
        $rows[] = ['Area Process', 'Closed (OK/Baik)', 'Open (Kotor/Rusak)'];

        $this->dataStartRow = $this->startRow + 2;

        foreach ($this->data as $item) {
            $closed = is_null($item->closed) ? 0 : (int) $item->closed;
            $open   = is_null($item->open)   ? 0 : (int) $item->open;
            $rows[] = [$item->metric, $closed, $open];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';
            
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Closed count: Green fill (`27AE60`)
            $sheet->getStyle("B{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Open count: Orange fill (`F39C12`)
            $sheet->getStyle("C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F39C12']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelClosed = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Closed']),
        ];
        $labelOpen = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Open']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesClosed = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];
        $seriesOpen = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0, 1],
            [$labelClosed[0], $labelOpen[0]],
            $categories,
            [$seriesClosed[0], $seriesOpen[0]]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Cleaning Area');

        $chart = new Chart(
            'cleaning_area_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow    = "G{$this->startRow}";
        $bottomRow = "O" . ($this->startRow + 14);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 9: Panel 9 - Trend Frying
// ---------------------------------------------------------
class Panel9TrendFryingComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryTrendFrying(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'p.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $where[] = 'hp.id_produk = :prod_id';
            $bindings['prod_id'] = $this->filters['id_produk'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'p.created_at BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT
            DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i') AS time_group,
            COALESCE(prod.nama_produk, '-') AS nama_produk,
            COALESCE(pg.kode_produksi, '-') AS kode_produksi,
            ROUND(AVG(CASE 
              WHEN f1.aktual_suhu_penggorengan REGEXP '^[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(f1.aktual_suhu_penggorengan, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END), 2) AS fryer1,
            ROUND(AVG(CASE 
              WHEN f2.aktual_suhu_penggorengan REGEXP '^[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(f2.aktual_suhu_penggorengan, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END), 2) AS fryer2,
            ROUND(AVG(CASE 
              WHEN f3.aktual_suhu_penggorengan REGEXP '^[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(f3.aktual_suhu_penggorengan, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END), 2) AS fryer3,
            ROUND(AVG(CASE 
              WHEN f4.aktual_suhu_penggorengan REGEXP '^[0-9]+([.,][0-9]+)?\$' 
              THEN CAST(REPLACE(f4.aktual_suhu_penggorengan, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END), 2) AS fryer4,
            ROUND(AVG(CASE 
              WHEN f5.aktual_suhu_penggorengan REGEXP '^[0-9]+([.,][0-9]+)?\$' 
              THEN CAST(REPLACE(f5.aktual_suhu_penggorengan, ',', '.') AS DECIMAL(10,2)) 
              ELSE NULL 
            END), 2) AS fryer5,
            MAX(f1.aktual_penggorengan) AS waktu_fryer1,
            MAX(f2.aktual_penggorengan) AS waktu_fryer2,
            MAX(f3.aktual_penggorengan) AS waktu_fryer3,
            MAX(f4.aktual_penggorengan) AS waktu_fryer4,
            MAX(f5.aktual_penggorengan) AS waktu_fryer5,
            GROUP_CONCAT(DISTINCT CONCAT(hp.uuid, '::', hp.aktual_suhu_pusat) SEPARATOR '||') AS raw_suhu_pusat,
            MAX(ssp.std_suhu_pusat) AS std_suhu_pusat
        FROM pembekuan_iqf_penggorengan p
        LEFT JOIN proses_frayer f1 ON p.frayer_uuid = f1.uuid
        LEFT JOIN frayer_2 f2      ON p.frayer2_uuid = f2.uuid
        LEFT JOIN frayer_3 f3      ON p.frayer3_uuid = f3.uuid
        LEFT JOIN frayer_4 f4      ON p.frayer4_uuid = f4.uuid
        LEFT JOIN frayer_5 f5      ON p.frayer5_uuid = f5.uuid
        LEFT JOIN hasil_penggorengan hp ON p.hasil_penggorengan_uuid = hp.uuid
        LEFT JOIN std_suhu_pusat ssp    ON hp.id_std_suhu_pusat = ssp.id
        LEFT JOIN jenis_produk prod ON hp.id_produk = prod.id
        LEFT JOIN penggorengan pg ON hp.penggorengan_uuid = pg.uuid
        WHERE (p.is_audit = 0 OR p.is_audit IS NULL) {$w}
        GROUP BY DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i'), prod.nama_produk, pg.kode_produksi
        ORDER BY time_group ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) {
                // Suhu produk (aktual_suhu_pusat) dihitung di PHP karena format penyimpanan
                // berbeda: Wizard 1 = teks "75, 76", Wizard 2 = JSON array ["75","76"].
                foreach ($res as $row) {
                    $row->suhu_produk = $this->averageSuhuPusat($row->raw_suhu_pusat ?? null);
                }
                return $res;
            }
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)[
                'time_group'     => Carbon::now()->format('Y-m-d H:i'),
                'nama_produk'    => '-',
                'kode_produksi'  => '-',
                'fryer1'         => 0,
                'fryer2'         => 0,
                'fryer3'         => 0,
                'fryer4'         => 0,
                'fryer5'         => 0,
                'waktu_fryer1'   => '-',
                'waktu_fryer2'   => '-',
                'waktu_fryer3'   => '-',
                'waktu_fryer4'   => '-',
                'waktu_fryer5'   => '-',
                'suhu_produk'    => 0,
                'std_suhu_pusat' => '-',
            ],
        ];
    }

    /**
     * Ambil semua angka dari nilai aktual_suhu_pusat (teks, "a, b", "a/b", atau JSON array)
     * lalu kembalikan rata-ratanya. Regex sama dengan yang dipakai di show Wizard 1 & 2.
     */
    private function averageSuhuPusat($raw): ?float
    {
        if ($raw === null || $raw === '') return null;

        $values = [];
        foreach (explode('||', (string) $raw) as $entry) {
            // Buang prefix uuid ("uuid::nilai")
            $pos = strpos($entry, '::');
            $val = $pos !== false ? substr($entry, $pos + 2) : $entry;
            $val = trim($val);
            if ($val === '' || $val === 'null') continue;

            // Jika JSON array, ubah menjadi string agar bisa di-regex
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                $val = implode(' ', array_map(fn($v) => is_scalar($v) ? (string) $v : '', $decoded));
            }

            if (preg_match_all('/\d+[.,]\d+|\d+/', $val, $m)) {
                foreach ($m[0] as $num) {
                    $values[] = (float) str_replace(',', '.', $num);
                }
            }
        }

        return empty($values) ? null : round(array_sum($values) / count($values), 2);
    }

    private function formatStdSuhuPusat($val): string
    {
        if (empty($val) || $val === '-') return '-';

        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            $filtered = array_values(array_filter($decoded, function ($v) {
                $t = trim($v);
                return $t !== '' && $t !== '-';
            }));
            $filtered = array_unique($filtered);
            return empty($filtered) ? '-' : implode(' - ', $filtered);
        }

        return trim($val);
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryTrendFrying();

        $rows = [];
        $rows[] = ['PANEL 9 - TREND FRYING', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['No', 'Waktu / Jam', 'Nama Produk', 'Kode Produksi', 'Suhu Fryer 1 (°C)', 'Suhu Fryer 2 (°C)', 'Suhu Fryer 3 (°C)', 'Suhu Fryer 4 (°C)', 'Suhu Fryer 5 (°C)', 'Lama Pemasakan Fryer 1', 'Lama Pemasakan Fryer 2', 'Lama Pemasakan Fryer 3', 'Lama Pemasakan Fryer 4', 'Lama Pemasakan Fryer 5', 'Suhu Produk (°C)', 'Suhu Pusat Std'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tglJam     = $item->time_group ? Carbon::parse($item->time_group)->format('d-m-Y H:i') : '-';
            $namaProduk = !empty($item->nama_produk) ? $item->nama_produk : '-';
            $kodeProduksi = !empty($item->kode_produksi) ? $item->kode_produksi : '-';
            $f1  = is_null($item->fryer1) ? 0 : (float) $item->fryer1;
            $f2  = is_null($item->fryer2) ? 0 : (float) $item->fryer2;
            $f3  = is_null($item->fryer3) ? 0 : (float) $item->fryer3;
            $f4  = is_null($item->fryer4) ? 0 : (float) $item->fryer4;
            $f5  = is_null($item->fryer5) ? 0 : (float) $item->fryer5;
            $wf1 = !empty($item->waktu_fryer1) ? $item->waktu_fryer1 : '-';
            $wf2 = !empty($item->waktu_fryer2) ? $item->waktu_fryer2 : '-';
            $wf3 = !empty($item->waktu_fryer3) ? $item->waktu_fryer3 : '-';
            $wf4 = !empty($item->waktu_fryer4) ? $item->waktu_fryer4 : '-';
            $wf5 = !empty($item->waktu_fryer5) ? $item->waktu_fryer5 : '-';
            $sp  = is_null($item->suhu_produk) ? 0 : (float) $item->suhu_produk;
            $std = $this->formatStdSuhuPusat(!empty($item->std_suhu_pusat) ? $item->std_suhu_pusat : null);

            $rows[] = [$no++, $tglJam, $namaProduk, $kodeProduksi, $f1, $f2, $f3, $f4, $f5, $wf1, $wf2, $wf3, $wf4, $wf5, $sp, $std];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:P{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:P{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Adjust column dimensions
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(22);
        $sheet->getColumnDimension('L')->setWidth(22);
        $sheet->getColumnDimension('M')->setWidth(22);
        $sheet->getColumnDimension('N')->setWidth(22);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Waktu centered
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Nama Produk left-aligned
            $sheet->getStyle("C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Kode Produksi centered
            $sheet->getStyle("D{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Fryer 1-5 temperatures centered
            $sheet->getStyle("E{$row}:I{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Fryer 1-5 waktu centered
            $sheet->getStyle("J{$row}:N{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Suhu Produk cell (Col O) with purple accent fill (`8E44AD`)
            $sheet->getStyle("O{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8E44AD']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Suhu Pusat Std cell (Col P) with teal accent fill
            $sheet->getStyle("P{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A7A6E']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Fryer 1 (°C)']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Fryer 2 (°C)']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Fryer 3 (°C)']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Fryer 4 (°C)']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Fryer 5 (°C)']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu Produk (°C)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$E\${$this->dataStartRow}:\$E\${$this->dataEndRow}", null, $rowCount),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$F\${$this->dataStartRow}:\$F\${$this->dataEndRow}", null, $rowCount),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$G\${$this->dataStartRow}:\$G\${$this->dataEndRow}", null, $rowCount),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$H\${$this->dataStartRow}:\$H\${$this->dataEndRow}", null, $rowCount),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$I\${$this->dataStartRow}:\$I\${$this->dataEndRow}", null, $rowCount),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$O\${$this->dataStartRow}:\$O\${$this->dataEndRow}", null, $rowCount),
        ];

        $dataSeries = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0, 1, 2, 3, 4, 5],
            $labels,
            $categories,
            $seriesValues
        );
        // Note: Suhu Pusat Std (Col P) is a text reference column and is not included in the chart series.

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Trend Frying');

        $chart = new Chart(
            'trend_frying_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "K{$this->startRow}";
        $chartHeight = max(14, min(30, $rowCount + 4));
        $bottomRow   = "S" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 10: Panel 10 - Trend Berat/Pack
// ---------------------------------------------------------
class Panel10TrendBeratPackComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryTrendBeratPack(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'b.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $where[] = 'pp.id_produk = :prod_id';
            $bindings['prod_id'] = $this->filters['id_produk'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'b.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT 
          b.tanggal,
          b.jam,
          COALESCE(jp.nama_produk, '-') AS nama_produk,
          COALESCE(pp.kode_produksi, '-') AS kode_produksi,
          CASE 
            WHEN b.rata_rata_berat REGEXP '^[0-9]+([.,][0-9]+)?$' 
            THEN CAST(REPLACE(b.rata_rata_berat, ',', '.') AS DECIMAL(10,2)) 
            ELSE 0 
          END AS rata_rata_berat
        FROM berat_produk_bag b
        LEFT JOIN pengemasan_plastik pl ON b.id_pengemasan_plastik = pl.id
        LEFT JOIN pengemasan_produk pp ON COALESCE(b.id_pengemasan_produk, pl.id_pengemasan_produk) = pp.id
        LEFT JOIN jenis_produk jp ON pp.id_produk = jp.id
        WHERE b.rata_rata_berat IS NOT NULL 
          AND b.rata_rata_berat != ''
          {$w}
          AND EXISTS (
            SELECT 1 
            FROM dokumentasi d 
            WHERE d.id_berat_produk_bag = b.id 
              AND d.is_audit = 0
          )
        ORDER BY b.tanggal ASC, b.jam ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)['tanggal' => Carbon::now()->format('Y-m-d'), 'jam' => '00:00', 'nama_produk' => '-', 'kode_produksi' => '-', 'rata_rata_berat' => 0],
        ];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryTrendBeratPack();

        $rows = [];
        $rows[] = ['PANEL 10 - TREND BERAT/PACK', '', '', '', '', ''];
        $rows[] = ['No', 'Tanggal', 'Jam', 'Nama Produk', 'Kode Produksi', 'Rata-rata Berat (g)'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tgl = $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $jam = $item->jam ?: '-';
            $namaProduk = $item->nama_produk ?: '-';
            $kodeProduksi = $item->kode_produksi ?: '-';
            $val = is_null($item->rata_rata_berat) ? 0 : (float) $item->rata_rata_berat;

            $rows[] = [$no++, $tgl, $jam, $namaProduk, $kodeProduksi, $val];
        }

        if (empty($this->data)) {
            $rows[] = [1, '-', '-', '-', '-', 0];
            $this->dataEndRow = $this->dataStartRow;
        } else {
            $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;
        }

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:F{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Tanggal, Jam, Kode Produksi centered
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("E{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Nama Produk left aligned
            $sheet->getStyle("D{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Rata-rata Berat cell (Col F) with green fill (`27AE60`)
            $sheet->getStyle("F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelSeries = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Rata-rata Berat (g)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$F\${$this->dataStartRow}:\$F\${$this->dataEndRow}", null, $rowCount),
        ];

        // Line Chart for Trend over time
        $dataSeries = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [$labelSeries[0]],
            $categories,
            [$seriesValues[0]]
        );

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Trend Berat/Pack');

        $chart = new Chart(
            'trend_berat_pack_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "H{$this->startRow}";
        $chartHeight = max(12, min(30, $rowCount + 4));
        $bottomRow   = "P" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 11: Panel 11 - Trend Berat/Box
// ---------------------------------------------------------
class Panel11TrendBeratBoxComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryTrendBeratBox(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'bb.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $where[] = 'pp.id_produk = :prod_id';
            $bindings['prod_id'] = $this->filters['id_produk'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'bb.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT 
          bb.tanggal,
          bb.jam,
          COALESCE(jp.nama_produk, '-') AS nama_produk,
          COALESCE(pp.kode_produksi, '-') AS kode_produksi,
          CASE 
            WHEN bb.rata_rata_berat REGEXP '^[0-9]+([.,][0-9]+)?$' 
            THEN CAST(REPLACE(bb.rata_rata_berat, ',', '.') AS DECIMAL(10,2)) 
            ELSE 0 
          END AS rata_rata_berat
        FROM berat_produk_box bb
        LEFT JOIN berat_produk_bag b ON bb.id_berat_produk_bag = b.id
        LEFT JOIN pengemasan_plastik pl ON COALESCE(bb.id_pengemasan_plastik, b.id_pengemasan_plastik) = pl.id
        LEFT JOIN pengemasan_produk pp ON COALESCE(bb.id_pengemasan_produk, b.id_pengemasan_produk, pl.id_pengemasan_produk) = pp.id
        LEFT JOIN jenis_produk jp ON pp.id_produk = jp.id
        WHERE bb.rata_rata_berat IS NOT NULL 
          AND bb.rata_rata_berat != ''
          {$w}
          AND EXISTS (
            SELECT 1 
            FROM dokumentasi d 
            WHERE d.id_berat_produk_box = bb.id 
              AND d.is_audit = 0
          )
        ORDER BY bb.tanggal ASC, bb.jam ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)['tanggal' => Carbon::now()->format('Y-m-d'), 'jam' => '00:00', 'nama_produk' => '-', 'kode_produksi' => '-', 'rata_rata_berat' => 0],
        ];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryTrendBeratBox();

        $rows = [];
        $rows[] = ['PANEL 11 - TREND BERAT/BOX', '', '', '', '', ''];
        $rows[] = ['No', 'Tanggal', 'Jam', 'Nama Produk', 'Kode Produksi', 'Rata-rata Berat (g)'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tgl = $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $jam = $item->jam ?: '-';
            $namaProduk = $item->nama_produk ?: '-';
            $kodeProduksi = $item->kode_produksi ?: '-';
            $val = is_null($item->rata_rata_berat) ? 0 : (float) $item->rata_rata_berat;

            $rows[] = [$no++, $tgl, $jam, $namaProduk, $kodeProduksi, $val];
        }

        if (empty($this->data)) {
            $rows[] = [1, '-', '-', '-', '-', 0];
            $this->dataEndRow = $this->dataStartRow;
        } else {
            $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;
        }

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:F{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Tanggal, Jam, Kode Produksi centered
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("E{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Nama Produk left aligned
            $sheet->getStyle("D{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Rata-rata Berat cell (Col F) with green fill (`27AE60`)
            $sheet->getStyle("F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelSeries = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Rata-rata Berat (g)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$F\${$this->dataStartRow}:\$F\${$this->dataEndRow}", null, $rowCount),
        ];

        // Line Chart for Trend over time
        $dataSeries = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [$labelSeries[0]],
            $categories,
            [$seriesValues[0]]
        );

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Trend Berat/Box');

        $chart = new Chart(
            'trend_berat_box_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "H{$this->startRow}";
        $chartHeight = max(12, min(30, $rowCount + 4));
        $bottomRow   = "P" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 12: Panel 12 - Top Issue Pemeriksaan Proses Produksi
// ---------------------------------------------------------
class Panel12TopIssueComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryTopIssue(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'ppp.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'ppp.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT
            ppp.tanggal,
            ppp.jam,
            ia.area AS area,
            CASE 
                WHEN LOWER(TRIM(ppp.ketidaksesuaian)) = 'bahan' THEN 'Bahan'
                WHEN LOWER(TRIM(ppp.ketidaksesuaian)) = 'produk' THEN 'Produk'
                WHEN LOWER(TRIM(ppp.ketidaksesuaian)) = 'proses' THEN 'Proses'
                ELSE ppp.ketidaksesuaian
            END AS kategori,
            ppp.uraian_permasalahan AS masalah,
            ppp.tindakan_koreksi AS tindakan
        FROM pemeriksaan_proses_produksi ppp
        JOIN input_area ia ON ppp.id_area = ia.id
        WHERE ppp.is_audit = 0 {$w}
        ORDER BY ppp.tanggal DESC, ppp.jam DESC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)[
                'tanggal'  => Carbon::now()->format('Y-m-d'),
                'jam'      => '00:00',
                'area'     => '-',
                'kategori' => '-',
                'masalah'  => 'Tidak ada temuan masalah',
                'tindakan' => '-',
            ],
        ];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryTopIssue();

        $rows = [];
        $rows[] = ['PANEL 12 - TOP ISSUE PEMERIKSAAN PROSES PRODUKSI', '', '', '', '', ''];
        $rows[] = ['No', 'Waktu / Tanggal', 'Area', 'Kategori', 'Masalah', 'Tindakan'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tgl = $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $jam = $item->jam ?: '';
            $tglJam = trim("{$tgl} {$jam}");

            $rows[] = [
                $no++,
                $tglJam,
                $item->area ?? '-',
                $item->kategori ?? '-',
                $item->masalah ?? '-',
                $item->tindakan ?? '-',
            ];
        }

        if (empty($this->data)) {
            $rows[] = [1, '-', '-', '-', 'Tidak ada temuan masalah', '-'];
            $this->dataEndRow = $this->dataStartRow;
        } else {
            $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;
        }

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:F{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Adjust column widths for Masalah & Tindakan
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(35);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $item = $this->data[$i] ?? null;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Waktu, Area centered
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("C{$row}")->applyFromArray([
                'font' => ['bold' => true],
            ]);

            // Kategori badge styling
            $kat = strtolower(trim($item->kategori ?? ''));
            $katColor = '5D6D7E'; // default slate gray
            if (str_contains($kat, 'bahan')) {
                $katColor = 'E74C3C'; // Red
            } elseif (str_contains($kat, 'produk')) {
                $katColor = '3498DB'; // Blue
            } elseif (str_contains($kat, 'proses')) {
                $katColor = 'F39C12'; // Orange
            }

            $sheet->getStyle("D{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $katColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Masalah and Tindakan left aligned with wrap text
            $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        return null;
    }
}

// ---------------------------------------------------------
// Component 13: Panel 13 - Top Issue Suhu IQF
// ---------------------------------------------------------
class Panel13SuhuIqfComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function querySuhuIqf(): array
    {
        $whereFrying   = [];
        $whereRoasting = [];
        $bindings      = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $whereFrying[]   = 'id_plan = :plan_id1';
            $whereRoasting[] = 'id_plan = :plan_id2';
            $bindings['plan_id1'] = $this->filters['id_plan'];
            $bindings['plan_id2'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $whereFrying[]   = 'tanggal BETWEEN :from1 AND :to1';
            $whereRoasting[] = 'tanggal BETWEEN :from2 AND :to2';
            $bindings['from1'] = $this->filters['from_date'];
            $bindings['to1']   = $this->filters['to_date'];
            $bindings['from2'] = $this->filters['from_date'];
            $bindings['to2']   = $this->filters['to_date'];
        }

        $wFrying   = count($whereFrying)   ? ' AND ' . implode(' AND ', $whereFrying)   : '';
        $wRoasting = count($whereRoasting) ? ' AND ' . implode(' AND ', $whereRoasting) : '';

        $sql = "
        SELECT
            p.tanggal,
            'IQF Frying' AS proses,
            CASE 
              WHEN p.suhu_ruang_iqf REGEXP '^-?[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(p.suhu_ruang_iqf, ',', '.') AS DECIMAL(10,2)) 
              ELSE 0 
            END AS suhu_iqf,
            hp.aktual_suhu_pusat AS raw_suhu_pusat
        FROM pembekuan_iqf_penggorengan p
        LEFT JOIN hasil_penggorengan hp ON p.hasil_penggorengan_uuid = hp.uuid
        WHERE (p.is_audit = 0 OR p.is_audit IS NULL)
          AND p.suhu_ruang_iqf IS NOT NULL 
          AND p.suhu_ruang_iqf != ''
          {$wFrying}

        UNION ALL

        SELECT
            p.tanggal,
            'IQF Roasting' AS proses,
            CASE 
              WHEN p.suhu_ruang_iqf REGEXP '^-?[0-9]+([.,][0-9]+)?$' 
              THEN CAST(REPLACE(p.suhu_ruang_iqf, ',', '.') AS DECIMAL(10,2)) 
              ELSE 0 
            END AS suhu_iqf,
            hpr.aktual_suhu_pusat AS raw_suhu_pusat
        FROM pembekuan_iqf_roasting p
        LEFT JOIN hasil_proses_roasting hpr ON p.hasil_proses_roasting_uuid = hpr.uuid
        WHERE (p.is_audit = 0 OR p.is_audit IS NULL)
          AND p.suhu_ruang_iqf IS NOT NULL 
          AND p.suhu_ruang_iqf != ''
          {$wRoasting}

        ORDER BY tanggal ASC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)['tanggal' => Carbon::now()->format('Y-m-d'), 'proses' => 'IQF Frying', 'suhu_iqf' => 0, 'raw_suhu_pusat' => null],
        ];
    }

    private function formatSuhuPusat($val): string
    {
        if (empty($val) || $val === '-') return '-';

        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            $filtered = array_values(array_filter($decoded, function ($v) {
                $t = trim($v);
                return $t !== '' && $t !== '-';
            }));
            return empty($filtered) ? '-' : implode(', ', $filtered);
        }

        return trim($val);
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->querySuhuIqf();

        $rows = [];
        $rows[] = ['PANEL 13 - TOP ISSUE SUHU IQF', '', '', '', ''];
        $rows[] = ['No', 'Tanggal', 'Proses', 'Suhu IQF (°C)', 'Aktual Suhu Produk (°C)'];

        $this->dataStartRow = $this->startRow + 2;
        $no = 1;
        foreach ($this->data as $item) {
            $tgl = $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $val = is_null($item->suhu_iqf) ? 0 : (float) $item->suhu_iqf;
            $suhuPusat = $this->formatSuhuPusat($item->raw_suhu_pusat ?? null);

            $rows[] = [$no++, $tgl, $item->proses ?? '-', $val, $suhuPusat];
        }

        if (empty($this->data)) {
            $rows[] = [1, '-', '-', 0, '-'];
            $this->dataEndRow = $this->dataStartRow;
        } else {
            $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;
        }

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:E{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);
        
        $sheet->getColumnDimension('E')->setWidth(25);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No, Tanggal, Proses centered
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("C{$row}")->applyFromArray([
                'font' => ['bold' => true],
            ]);

            // Suhu IQF cell (Col D) with green fill (`27AE60`)
            $sheet->getStyle("D{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            
            // Aktual Suhu Produk cell (Col E)
            $sheet->getStyle("E{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelSeries = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Suhu IQF (°C)']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$C\${$this->dataStartRow}:\$C\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$D\${$this->dataStartRow}:\$D\${$this->dataEndRow}", null, $rowCount),
        ];

        // Column Chart for Suhu IQF
        $dataSeries = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            [0],
            [$labelSeries[0]],
            $categories,
            [$seriesValues[0]]
        );
        $dataSeries->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Top Issue Suhu IQF');

        $chart = new Chart(
            'top_issue_suhu_iqf_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "G{$this->startRow}";
        $chartHeight = max(12, min(30, $rowCount + 4));
        $bottomRow   = "O" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}

// ---------------------------------------------------------
// Component 14: Panel 14 - Overview GMP Karyawan
// ---------------------------------------------------------
class Panel14GmpKaryawanComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryGmpKaryawan(): array
    {
        $where = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[] = 'gk.id_plan = :plan_id';
            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[] = 'gk.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT
            CASE 
                WHEN JSON_UNQUOTE(JSON_EXTRACT(jt.entry, '$.verifikasi')) = 'tidak_ok' 
                     OR (
                         JSON_UNQUOTE(JSON_EXTRACT(jt.entry, '$.temuan_ketidaksesuaian')) IS NOT NULL 
                         AND JSON_UNQUOTE(JSON_EXTRACT(jt.entry, '$.temuan_ketidaksesuaian')) NOT IN ('sesuai', 'tidak_ada_temuan')
                     )
                THEN 'Tidak OK'
                ELSE 'OK'
            END AS metric,
            COUNT(*) AS value
        FROM gmp_karyawan gk,
        JSON_TABLE(gk.entries, '$[*]' COLUMNS (entry JSON PATH '$')) AS jt
        WHERE (gk.is_audit = 0 OR gk.is_audit IS NULL) {$w}
        GROUP BY metric
        ";

        $map = ['OK' => 0, 'Tidak OK' => 0];

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) {
                foreach ($res as $r) {
                    if (isset($map[$r->metric])) {
                        $map[$r->metric] = (int) $r->value;
                    }
                }
            }
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)['metric' => 'OK', 'value' => $map['OK']],
            (object)['metric' => 'Tidak OK', 'value' => $map['Tidak OK']],
        ];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data = $this->queryGmpKaryawan();

        $total = array_sum(array_column($this->data, 'value'));

        $rows = [];
        $rows[] = ['PANEL 14 - OVERVIEW GMP KARYAWAN', '', ''];
        $rows[] = ['Metric / Status', 'Jumlah Temuan', 'Persentase (%)'];

        $this->dataStartRow = $this->startRow + 2;
        foreach ($this->data as $item) {
            $val = (int) $item->value;
            $pct = $total > 0 ? round(($val / $total) * 100, 2) : 0;

            $rows[] = [$item->metric, $val, $pct];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:C{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:C{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = count($this->data);
        for ($i = 0; $i < $rowCount; $i++) {
            $row = $this->dataStartRow + $i;
            $item = $this->data[$i];
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Metric badge fill
            $metricColor = ($item->metric === 'OK') ? '27AE60' : 'E74C3C';
            $sheet->getStyle("A{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $metricColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Jumlah Temuan & Persentase centered
            $sheet->getStyle("B{$row}:C{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        if (empty($this->data) || $this->dataEndRow < $this->dataStartRow) {
            return null;
        }

        $rowCount = count($this->data);
        $labelSeries = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['Overview GMP Karyawan']),
        ];

        $categories = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheetTitle}'!\$A\${$this->dataStartRow}:\$A\${$this->dataEndRow}", null, $rowCount),
        ];

        $seriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}", null, $rowCount),
        ];

        // Pie Chart for Overview GMP Karyawan
        $dataSeries = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            [0],
            [$labelSeries[0]],
            $categories,
            [$seriesValues[0]]
        );

        $plotArea = new PlotArea(null, [$dataSeries]);
        $legend   = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title    = new ChartTitle('Overview GMP Karyawan');

        $chart = new Chart(
            'gmp_karyawan_overview_chart',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            null,
            null
        );

        $topRow      = "E{$this->startRow}";
        $chartHeight = 12;
        $bottomRow   = "M" . ($this->startRow + $chartHeight);
        $chart->setTopLeftPosition($topRow);
        $chart->setBottomRightPosition($bottomRow);

        return $chart;
    }
}
// Component 15: Panel 15 - Trend Roasting
// ---------------------------------------------------------

class Panel15RoastingComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // ---------------------------------------------------------
    // Query Data Roasting
    // ---------------------------------------------------------
    public function queryRoasting(): array
    {
        $where    = [];
        $bindings = [];

        // -----------------------------------------------------
        // Filter Plan
        // -----------------------------------------------------
        if (
            !empty($this->filters['id_plan']) &&
            $this->filters['id_plan'] !== '$__all' &&
            $this->filters['id_plan'] !== 'all'
        ) {
            $where[] = 'prf.id_plan = :plan_id';

            $bindings['plan_id'] = $this->filters['id_plan'];
        }

        // -----------------------------------------------------
        // Filter Produk
        // -----------------------------------------------------
        if (
            !empty($this->filters['id_produk']) &&
            $this->filters['id_produk'] !== '$__all' &&
            $this->filters['id_produk'] !== 'all'
        ) {
            $where[] = '
                COALESCE(
                    prf.id_produk,
                    hpr.id_produk
                ) = :prod_id
            ';

            $bindings['prod_id'] = $this->filters['id_produk'];
        }

        // -----------------------------------------------------
        // Filter Tanggal
        // -----------------------------------------------------
        if (
            !empty($this->filters['from_date']) &&
            !empty($this->filters['to_date'])
        ) {
            $where[] = '
                prf.tanggal BETWEEN :from_date AND :to_date
            ';

            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        // -----------------------------------------------------
        // Gabungkan WHERE tambahan
        // -----------------------------------------------------
        $w = '';

        if (!empty($where)) {
            $w = ' AND ' . implode(' AND ', $where);
        }

        // -----------------------------------------------------
        // Query
        //
        // Tidak menggunakan JSON_TABLE().
        //
        // Hal ini untuk kompatibilitas MariaDB versi yang
        // tidak mendukung JSON_TABLE() atau memiliki sintaks
        // JSON_TABLE yang berbeda.
        // -----------------------------------------------------
        $sql = "
            WITH RECURSIVE json_numbers AS (
                SELECT 0 AS n

                UNION ALL

                SELECT n + 1
                FROM json_numbers
                WHERE n < 1000
            )

            SELECT

                -- ---------------------------------------------
                -- Waktu
                -- ---------------------------------------------
                CONCAT(
                    DATE(prf.tanggal),
                    ' ',
                    COALESCE(
                        TIME(prf.jam),
                        TIME(prf.tanggal)
                    )
                ) AS time_group,

                -- ---------------------------------------------
                -- Nama Produk
                -- ---------------------------------------------
                COALESCE(
                    jp.nama_produk,
                    '-'
                ) AS nama_produk,

                -- ---------------------------------------------
                -- Kode Produksi
                -- ---------------------------------------------
                COALESCE(
                    ir.kode_produksi,
                    '-'
                ) AS kode_produksi,

                -- ---------------------------------------------
                -- Suhu Pemasakan
                --
                -- Contoh:
                -- suhu_roasting = '85/90'
                --
                -- Yang diambil:
                -- 85
                -- ---------------------------------------------
                CASE
                    WHEN
                        SUBSTRING_INDEX(
                            JSON_UNQUOTE(
                                JSON_EXTRACT(
                                    prf.blok_data,
                                    CONCAT(
                                        '\\$[',
                                        json_numbers.n,
                                        '].suhu_roasting'
                                    )
                                )
                            ),
                            '/',
                            1
                        ) REGEXP '^[0-9]+([.][0-9]+)?$'

                    THEN
                        CAST(
                            SUBSTRING_INDEX(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(
                                        prf.blok_data,
                                        CONCAT(
                                            '\\$[',
                                            json_numbers.n,
                                            '].suhu_roasting'
                                        )
                                    )
                                ),
                                '/',
                                1
                            ) AS DECIMAL(10,2)
                        )

                    ELSE NULL
                END AS suhu_pemasakan,

                -- ---------------------------------------------
                -- Steam / Humidity
                -- ---------------------------------------------
                CASE
                    WHEN
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                prf.blok_data,
                                CONCAT(
                                    '\\$[',
                                    json_numbers.n,
                                    '].aktual_humadity'
                                )
                            )
                        ) REGEXP '^[0-9]+([.][0-9]+)?$'

                    THEN
                        CAST(
                            JSON_UNQUOTE(
                                JSON_EXTRACT(
                                    prf.blok_data,
                                    CONCAT(
                                        '\\$[',
                                        json_numbers.n,
                                        '].aktual_humadity'
                                    )
                                )
                            ) AS DECIMAL(10,2)
                        )

                    ELSE NULL
                END AS steam_humidity

            FROM proses_roasting_fan prf

            -- ---------------------------------------------
            -- Relasi pembekuan IQF roasting
            -- ---------------------------------------------
            JOIN pembekuan_iqf_roasting piqr
                ON piqr.proses_roasting_fan_uuid = prf.uuid

            -- ---------------------------------------------
            -- Hasil proses roasting
            -- ---------------------------------------------
            LEFT JOIN hasil_proses_roasting hpr
                ON hpr.uuid = piqr.hasil_proses_roasting_uuid

            -- ---------------------------------------------
            -- Input roasting
            -- ---------------------------------------------
            LEFT JOIN input_roasting ir
                ON ir.uuid = COALESCE(
                    prf.input_roasting_uuid,
                    piqr.input_roasting_uuid,
                    hpr.input_roasting_uuid
                )

            -- ---------------------------------------------
            -- Jenis produk
            -- ---------------------------------------------
            LEFT JOIN jenis_produk jp
                ON jp.id = COALESCE(
                    prf.id_produk,
                    hpr.id_produk
                )

            -- ---------------------------------------------
            -- Generate index JSON
            -- ---------------------------------------------
            CROSS JOIN json_numbers

            WHERE piqr.is_audit = 0

                -- -----------------------------------------
                -- Pastikan blok_data valid JSON
                -- -----------------------------------------
                AND JSON_VALID(prf.blok_data)

                -- -----------------------------------------
                -- Pastikan index masih berada dalam array
                -- -----------------------------------------
                AND json_numbers.n < JSON_LENGTH(
                    prf.blok_data
                )

                -- -----------------------------------------
                -- Hanya ambil data yang mempunyai
                -- suhu atau humidity
                -- -----------------------------------------
                AND (
                    (
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                prf.blok_data,
                                CONCAT(
                                    '\\$[',
                                    json_numbers.n,
                                    '].suhu_roasting'
                                )
                            )
                        ) IS NOT NULL

                        AND

                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                prf.blok_data,
                                CONCAT(
                                    '\\$[',
                                    json_numbers.n,
                                    '].suhu_roasting'
                                )
                            )
                        ) != ''
                    )

                    OR

                    (
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                prf.blok_data,
                                CONCAT(
                                    '\\$[',
                                    json_numbers.n,
                                    '].aktual_humadity'
                                )
                            )
                        ) IS NOT NULL

                        AND

                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                prf.blok_data,
                                CONCAT(
                                    '\\$[',
                                    json_numbers.n,
                                    '].aktual_humadity'
                                )
                            )
                        ) != ''
                    )
                )

                {$w}

            ORDER BY
                DATE(prf.tanggal) ASC,

                COALESCE(
                    TIME(prf.jam),
                    TIME(prf.tanggal)
                ) ASC,

                prf.id ASC,

                json_numbers.n ASC
        ";

        // -----------------------------------------------------
        // Execute Query
        // -----------------------------------------------------
        try {

            $res = DB::select(
                $sql,
                $bindings
            );

            if (!empty($res)) {
                return $res;
            }

        } catch (\Throwable $e) {

            \Illuminate\Support\Facades\Log::error(
                'Panel15 Roasting query error: ' . $e->getMessage(),
                [
                    'filters' => $this->filters,
                    'sql'     => $sql,
                    'bindings'=> $bindings,
                    'trace'   => $e->getTraceAsString(),
                ]
            );
        }

        // -----------------------------------------------------
        // Fallback jika query gagal / tidak ada data
        // -----------------------------------------------------
        return [
            (object)[
                'time_group'     => Carbon::now()->format(
                    'Y-m-d H:i:s'
                ),
                'nama_produk'    => '-',
                'kode_produksi'  => '-',
                'suhu_pemasakan' => 0,
                'steam_humidity' => 0,
            ],
        ];
    }

    // ---------------------------------------------------------
    // Build Rows
    // ---------------------------------------------------------
    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;

        $this->data = $this->queryRoasting();

        $rows = [];

        // -----------------------------------------------------
        // Title
        // -----------------------------------------------------
        $rows[] = [
            'PANEL 15 - TREND ROASTING',
            '',
            '',
            '',
            '',
            ''
        ];

        // -----------------------------------------------------
        // Header
        // -----------------------------------------------------
        $rows[] = [
            'No',
            'Waktu / Jam',
            'Nama Produk',
            'Kode Produksi',
            'Suhu Pemasakan (°C)',
            'Steam / Humidity (%)'
        ];

        $this->dataStartRow = $this->startRow + 2;

        $no = 1;

        // -----------------------------------------------------
        // Data
        // -----------------------------------------------------
        foreach ($this->data as $item) {

            // ---------------------------------------------
            // Waktu
            // ---------------------------------------------
            $tglJam = '-';

            if (!empty($item->time_group)) {

                try {

                    $tglJam = Carbon::parse(
                        $item->time_group
                    )->format(
                        'd-m-Y H:i'
                    );

                } catch (\Throwable $e) {

                    $tglJam = (string)$item->time_group;
                }
            }

            // ---------------------------------------------
            // Nama produk
            // ---------------------------------------------
            $namaProduk = !empty(
                $item->nama_produk
            )
                ? $item->nama_produk
                : '-';

            // ---------------------------------------------
            // Kode produksi
            // ---------------------------------------------
            $kodeProduksi = !empty(
                $item->kode_produksi
            )
                ? $item->kode_produksi
                : '-';

            // ---------------------------------------------
            // Suhu
            // ---------------------------------------------
            $suhu = is_null(
                $item->suhu_pemasakan
            )
                ? 0
                : (float)$item->suhu_pemasakan;

            // ---------------------------------------------
            // Humidity
            // ---------------------------------------------
            $steam = is_null(
                $item->steam_humidity
            )
                ? 0
                : (float)$item->steam_humidity;

            // ---------------------------------------------
            // Tambahkan row
            // ---------------------------------------------
            $rows[] = [
                $no++,
                $tglJam,
                $namaProduk,
                $kodeProduksi,
                $suhu,
                $steam
            ];
        }

        // -----------------------------------------------------
        // Data kosong
        // -----------------------------------------------------
        if (empty($this->data)) {

            $rows[] = [
                1,
                '-',
                '-',
                '-',
                0,
                0
            ];

            $this->dataEndRow =
                $this->dataStartRow;

        } else {

            $this->dataEndRow =
                $this->dataStartRow
                + count($this->data)
                - 1;
        }

        // -----------------------------------------------------
        // Separator
        // -----------------------------------------------------
        $rows[] = [''];

        $currentRow =
            $this->dataEndRow + 2;

        return $rows;
    }

    // ---------------------------------------------------------
    // Apply Styles
    // ---------------------------------------------------------
    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) {
            return;
        }

        // -----------------------------------------------------
        // Title Row
        // -----------------------------------------------------
        $sheet
            ->getStyle(
                "A{$this->startRow}"
            )
            ->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'size'  => 12,
                    'color' => [
                        'rgb' => '6B3A6B'
                    ],
                ],
            ]);

        $sheet->mergeCells(
            "A{$this->startRow}:F{$this->startRow}"
        );

        // -----------------------------------------------------
        // Header Row
        // -----------------------------------------------------
        $headerRow =
            $this->startRow + 1;

        $sheet
            ->getStyle(
                "A{$headerRow}:F{$headerRow}"
            )
            ->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ],
                ],

                'fill' => [
                    'fillType' => Fill::FILL_SOLID,

                    'startColor' => [
                        'rgb' => '6B3A6B'
                    ],
                ],

                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER
                ],

                'borders' => [
                    'allBorders' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'rgb' => 'CCCCCC'
                        ],
                    ],
                ],
            ]);

        // -----------------------------------------------------
        // Data Rows
        // -----------------------------------------------------
        $rowCount = max(
            1,
            count($this->data)
        );

        for (
            $i = 0;
            $i < $rowCount;
            $i++
        ) {

            $row =
                $this->dataStartRow + $i;

            // ---------------------------------------------
            // Warna selang-seling
            // ---------------------------------------------
            $bgColor =
                ($i % 2 === 0)
                    ? 'F9F0F9'
                    : 'F0E0F0';

            $sheet
                ->getStyle(
                    "A{$row}:F{$row}"
                )
                ->applyFromArray([

                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,

                        'startColor' => [
                            'rgb' => $bgColor
                        ],
                    ],

                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,

                            'color' => [
                                'rgb' => 'DDDDDD'
                            ],
                        ],
                    ],

                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER
                    ],
                ]);

            // ---------------------------------------------
            // No & waktu
            // ---------------------------------------------
            $sheet
                ->getStyle(
                    "A{$row}:B{$row}"
                )
                ->applyFromArray([
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER
                    ],
                ]);

            // ---------------------------------------------
            // Nama produk
            // ---------------------------------------------
            $sheet
                ->getStyle(
                    "C{$row}"
                )
                ->applyFromArray([
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_LEFT
                    ],
                ]);

            // ---------------------------------------------
            // Kode produksi
            // ---------------------------------------------
            $sheet
                ->getStyle(
                    "D{$row}"
                )
                ->applyFromArray([
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER
                    ],
                ]);

            // ---------------------------------------------
            // Suhu
            // ---------------------------------------------
            $sheet
                ->getStyle(
                    "E{$row}"
                )
                ->applyFromArray([

                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,

                        'startColor' => [
                            'rgb' => 'D35400'
                        ],
                    ],

                    'font' => [
                        'bold' => true,

                        'color' => [
                            'rgb' => 'FFFFFF'
                        ],
                    ],

                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER
                    ],
                ]);

            // ---------------------------------------------
            // Humidity
            // ---------------------------------------------
            $sheet
                ->getStyle(
                    "F{$row}"
                )
                ->applyFromArray([

                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,

                        'startColor' => [
                            'rgb' => '148F77'
                        ],
                    ],

                    'font' => [
                        'bold' => true,

                        'color' => [
                            'rgb' => 'FFFFFF'
                        ],
                    ],

                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER
                    ],
                ]);
        }
    }

    // ---------------------------------------------------------
    // Build Chart
    // ---------------------------------------------------------
    public function buildChart(
        string $sheetTitle
    ): ?Chart {

        if (
            empty($this->data) ||
            $this->dataEndRow <
            $this->dataStartRow
        ) {
            return null;
        }

        $rowCount =
            count($this->data);

        // -----------------------------------------------------
        // Label Series
        // -----------------------------------------------------
        $labels = [

            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                null,
                null,
                1,
                [
                    'Suhu Pemasakan (°C)'
                ]
            ),

            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                null,
                null,
                1,
                [
                    'Steam / Humidity (%)'
                ]
            ),
        ];

        // -----------------------------------------------------
        // Categories
        // -----------------------------------------------------
        $categories = [

            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,

                "'{$sheetTitle}'!\$B\${$this->dataStartRow}:\$B\${$this->dataEndRow}",

                null,

                $rowCount
            ),
        ];

        // -----------------------------------------------------
        // Series Values
        // -----------------------------------------------------
        $seriesValues = [

            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,

                "'{$sheetTitle}'!\$E\${$this->dataStartRow}:\$E\${$this->dataEndRow}",

                null,

                $rowCount
            ),

            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,

                "'{$sheetTitle}'!\$F\${$this->dataStartRow}:\$F\${$this->dataEndRow}",

                null,

                $rowCount
            ),
        ];

        // -----------------------------------------------------
        // Data Series
        // -----------------------------------------------------
        $dataSeries = new DataSeries(

            DataSeries::TYPE_LINECHART,

            DataSeries::GROUPING_STANDARD,

            [0, 1],

            $labels,

            $categories,

            $seriesValues
        );

        // -----------------------------------------------------
        // Plot Area
        // -----------------------------------------------------
        $plotArea = new PlotArea(
            null,
            [$dataSeries]
        );

        // -----------------------------------------------------
        // Legend
        // -----------------------------------------------------
        $legend = new Legend(
            Legend::POSITION_BOTTOM,
            null,
            false
        );

        // -----------------------------------------------------
        // Chart Title
        // -----------------------------------------------------
        $title = new ChartTitle(
            'Trend Roasting - Suhu Pemasakan & Steam'
        );

        // -----------------------------------------------------
        // Chart
        // -----------------------------------------------------
        $chart = new Chart(

            'trend_roasting_chart',

            $title,

            $legend,

            $plotArea,

            true,

            DataSeries::EMPTY_AS_GAP,

            null,

            null
        );

        // -----------------------------------------------------
        // Posisi Chart
        // -----------------------------------------------------
        $topRow =
            "H{$this->startRow}";

        $chartHeight =
            max(
                12,
                min(
                    30,
                    $rowCount + 4
                )
            );

        $bottomRow =
            "P" .
            (
                $this->startRow
                + $chartHeight
            );

        $chart->setTopLeftPosition(
            $topRow
        );

        $chart->setBottomRightPosition(
            $bottomRow
        );

        return $chart;
    }
}


// ---------------------------------------------------------
// Component 16: Panel 16 - Hasil MD (Metal Detector)
// ---------------------------------------------------------
class Panel16HasilMdComponent
{
    protected $filters;
    protected $startRow;
    protected $dataStartRow;
    protected $dataEndRow;
    protected $data = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function queryHasilMd(): array
    {
        $where    = [];
        $bindings = [];

        if (!empty($this->filters['id_plan']) && $this->filters['id_plan'] !== '$__all' && $this->filters['id_plan'] !== 'all') {
            $where[]              = 'imd.id_plan = :plan_id';
            $bindings['plan_id']  = $this->filters['id_plan'];
        }

        if (!empty($this->filters['id_produk']) && $this->filters['id_produk'] !== '$__all' && $this->filters['id_produk'] !== 'all') {
            $where[]              = 'imd.id_produk = :prod_id';
            $bindings['prod_id']  = $this->filters['id_produk'];
        }

        if (!empty($this->filters['from_date']) && !empty($this->filters['to_date'])) {
            $where[]              = 'imd.tanggal BETWEEN :from_date AND :to_date';
            $bindings['from_date'] = $this->filters['from_date'];
            $bindings['to_date']   = $this->filters['to_date'];
        }

        $w = count($where) ? ' AND ' . implode(' AND ', $where) : '';

        $sql = "
        SELECT
            imd.id,
            imd.tanggal,
            imd.kode_produksi,
            jp.nama_produk,
            -- Evaluate all 9 fields; if every one contains ✔ → OK, otherwise Tidak OK
            CASE
                WHEN JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.fe_depan_aktual'))     LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.fe_tengah_aktual'))    LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.fe_belakang_aktual'))  LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.non_fe_depan_aktual'))    LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.non_fe_tengah_aktual'))   LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.non_fe_belakang_aktual')) LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.sus_depan_aktual'))    LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.sus_tengah_aktual'))   LIKE '%✔%'
                 AND JSON_UNQUOTE(JSON_EXTRACT(imd.aktual_json_metal, '$.sus_belakang_aktual')) LIKE '%✔%'
                THEN 'OK'
                ELSE 'Tidak OK'
            END AS hasil_md
        FROM input_metal_detector imd
        LEFT JOIN jenis_produk jp ON imd.id_produk = jp.id
        WHERE imd.deleted_at IS NULL {$w}
        ORDER BY imd.tanggal DESC, imd.id DESC
        ";

        try {
            $res = DB::select($sql, $bindings);
            if (!empty($res)) return $res;
        } catch (\Exception $e) {
            // fallback
        }

        return [
            (object)[
                'id'           => '-',
                'tanggal'      => Carbon::now()->format('Y-m-d'),
                'kode_produksi'=> '-',
                'nama_produk'  => '-',
                'hasil_md'     => '-',
            ],
        ];
    }

    public function buildRows(&$currentRow): array
    {
        $this->startRow = $currentRow;
        $this->data     = $this->queryHasilMd();

        $rows   = [];
        $rows[] = ['PANEL 16 - HASIL MD (METAL DETECTOR)', '', '', '', ''];
        $rows[] = ['No', 'Tanggal', 'Nama Produk', 'Kode Produksi', 'Hasil MD'];

        $this->dataStartRow = $this->startRow + 2;

        $no = 1;
        foreach ($this->data as $item) {
            $tgl    = !empty($item->tanggal)       ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
            $produk = !empty($item->nama_produk)   ? $item->nama_produk   : '-';
            $kode   = !empty($item->kode_produksi) ? $item->kode_produksi : '-';
            $hasil  = !empty($item->hasil_md)      ? $item->hasil_md      : '-';

            $rows[] = [$no++, $tgl, $produk, $kode, $hasil];
        }

        $this->dataEndRow = $this->dataStartRow + count($this->data) - 1;

        $rows[] = ['']; // Separator row
        $currentRow = $this->dataEndRow + 2;

        return $rows;
    }

    public function applyStyles(Worksheet $sheet)
    {
        if (!$this->startRow) return;

        // Title row
        $sheet->getStyle("A{$this->startRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A5276']],
        ]);
        $sheet->mergeCells("A{$this->startRow}:E{$this->startRow}");

        // Header row
        $headerRow = $this->startRow + 1;
        $sheet->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);

        // Data rows
        $rowCount = max(1, count($this->data));
        for ($i = 0; $i < $rowCount; $i++) {
            $row     = $this->dataStartRow + $i;
            $bgColor = ($i % 2 === 0) ? 'F4F6F7' : 'EBF5FB';

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // No & Tanggal centered
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Nama Produk left-aligned
            $sheet->getStyle("C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);

            // Kode Produksi centered
            $sheet->getStyle("D{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Hasil MD — green for OK, red for Tidak OK
            $hasilVal  = $sheet->getCell("E{$row}")->getValue();
            $hasilColor = (trim((string) $hasilVal) === 'OK') ? '27AE60' : 'E74C3C';
            $sheet->getStyle("E{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hasilColor]],
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }

    public function buildChart(string $sheetTitle): ?Chart
    {
        // No chart needed for this panel — it's a detail list
        return null;
    }
}
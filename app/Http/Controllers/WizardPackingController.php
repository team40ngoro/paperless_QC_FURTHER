<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Models\JenisProduk;
use App\Models\DataShift;
use App\Models\DataBag;
use App\Models\DataBox;
use App\Models\PengemasanProduk;
use App\Models\PengemasanPlastik;
use App\Models\BeratProdukBag;
use App\Models\BeratProdukBox;
use App\Models\PengemasanKarton;
use App\Models\Dokumentasi;
use Barryvdh\DomPDF\Facade\Pdf;

class WizardPackingController extends Controller
{
    use \App\Http\Controllers\Traits\HasAuditController;

    protected function auditModel(): string
    {
        return Dokumentasi::class;
    }

    protected function auditRoutePrefix(): string
    {
        return 'wizards.packing';
    }

    protected function auditViewPrefix(): string
    {
        return 'qc-sistem.wizard_packing';
    }

    protected function auditDateColumn(): string
    {
        return 'tanggal';
    }

    protected function auditTimeColumn(): string
    {
        return 'tanggal';
    }

    protected function auditRelations(): array
    {
        return [
            'pengemasanKarton',
            'beratProdukBox',
            'beratProdukBag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift'
        ];
    }

    /**
     * Override copyToAudit to use correct permission string 'create-wizard-packing'
     * instead of the default 'create-wizards.packing' from auditRoutePrefix.
     */
        public function copyToAudit($uuid)
        {
            $model = $this->auditModel();
            $item = $model::where('uuid', $uuid)->firstOrFail();

            // Check correct permission
            if (!Auth::user()->hasPermissionTo('create-wizard-packing')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membuat data audit.');
            }

            // Jika item yang diklik sudah merupakan record audit → langsung ke show audit
            if ($item->is_audit) {
                return redirect()->route("{$this->auditRoutePrefix()}.edit", $item->uuid);
            }

            // Jika data operasional ini sudah memiliki versi audit sebelumnya → ke show audit
            if ($item->auditVersion) {
                return redirect()->route("{$this->auditRoutePrefix()}.edit", $item->auditVersion->uuid);
            }

            $clone = $item->copyToAudit();

            return redirect()->route("{$this->auditRoutePrefix()}.edit", $clone->uuid)
                ->with('success', 'Data berhasil dicopy ke Audit. Silakan periksa dan edit data audit.');
        }

    public function index(Request $request)
    {
        $user = Auth::user();
         if ($user->id_role == 6) {
            return redirect()->route('wizards.packing.audit');
        }
        $search = $request->input('search');

        // We use Dokumentasi as the root header
        $query = Dokumentasi::operasional()->with([
            'pengemasanKarton',
            'beratProdukBox',
            'beratProdukBag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift'
        ]);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('pengemasanProduk.produk', function($q) use ($search) {
                    $q->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('pengemasanProduk', function($q) use ($search) {
                    $q->where('kode_produksi', 'LIKE', "%{$search}%");
                });
            });
        }

        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('id_shift', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $shiftQuery = \App\Models\DataShift::query();
        if ($user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $user->id_plan);
        }
        $cachedShifts = $shiftQuery->get();

        return view('qc-sistem.wizard_packing.index', compact('records', 'cachedShifts', 'search'));
    }

    public function auditIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $auditedSourceUuids = Dokumentasi::where('is_audit', true)->whereNotNull('source_uuid')->pluck('source_uuid')->toArray();

        // We use Dokumentasi as the root header
        $query = Dokumentasi::where(function ($q) use ($auditedSourceUuids) {
            $q->where('is_audit', true)
              ->orWhere(function ($subQ) use ($auditedSourceUuids) {
                  $subQ->where('is_audit', false);
                  if (!empty($auditedSourceUuids)) {
                      $subQ->whereNotIn('uuid', $auditedSourceUuids);
                  }
              });
        })->with([
            'pengemasanKarton',
            'beratProdukBox',
            'beratProdukBag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift'
        ]);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('pengemasanProduk.produk', function($q) use ($search) {
                    $q->where('nama_produk', 'LIKE', "%{$search}%");
                })->orWhereHas('pengemasanProduk', function($q) use ($search) {
                    $q->where('kode_produksi', 'LIKE', "%{$search}%");
                });
            });
        }

        $records = $query->orderBy('tanggal', 'desc')
            ->orderBy('id_shift', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $shiftQuery = \App\Models\DataShift::query();
        if ($user->role !== 'superadmin') {
            $shiftQuery->where('id_plan', $user->id_plan);
        }
        $cachedShifts = $shiftQuery->get();

        return view('qc-sistem.wizard_packing.index_audit', compact('records', 'cachedShifts', 'search'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            $shifts  = DataShift::all();
            $produks = JenisProduk::all();
            $dataBags = DataBag::all();
            $dataBoxes = DataBox::all();
        } else {
            $shifts  = DataShift::where('id_plan', $user->id_plan)->get();
            $produks = JenisProduk::where('id_plan', $user->id_plan)->get();
            $dataBags = DataBag::where('id_plan', $user->id_plan)->get();
            $dataBoxes = DataBox::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.wizard_packing.create', compact('shifts', 'produks', 'dataBags', 'dataBoxes'));
    }

    public function storePengemasanProduk(Request $request)
    {
        try {
            $user = Auth::user();
            $record = PengemasanProduk::create([
                'uuid'             => Str::uuid(),
                'id_plan'          => $user->id_plan,
                'user_id'          => $user->id,
                'id_produk'        => $request->id_produk,
                'id_shift'         => $request->id_shift,
                'tanggal'          => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam'              => $request->jam,
                'kode_form'        => $request->kode_form,
                'berat'            => $request->has('berat_produk') ? $request->berat_produk : $request->berat,
                'tanggal_expired'  => $request->tanggal_expired,
                'kode_produksi'    => $request->kode_produksi,
                'std_suhu_produk_iqf' => $request->std_suhu_produk_iqf,
                'aktual_suhu_produk'  => is_array($request->aktual_suhu_produk) ? implode(', ', array_filter(array_map('trim', $request->aktual_suhu_produk), fn($v) => $v !== '')) : $request->aktual_suhu_produk,
                'waktu_awal_packing'  => $request->waktu_awal_packing,
                'is_kombinasi'        => ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? 1 : 0,
                'id_produk_2'         => ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->id_produk_2 : null,
                'kode_produksi_2'     => ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->kode_produksi_2 : null,
                'berat_produk_2'      => ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->berat_produk_2 : null,
                'tanggal_expired_2'   => ($request->has('is_kombinasi') && $request->is_kombinasi == 1) ? $request->tanggal_expired_2 : null,
            ]);

            return response()->json(['success' => true, 'id' => $record->id, 'uuid' => $record->uuid]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function storePengemasanPlastik(Request $request)
    {
        try {
            $user = Auth::user();
            
            // nomor_md is now optional

            // Handle file upload for nomor_md
            $nomor_md_path = null;
            if ($request->hasFile('nomor_md')) {
                $file = $request->file('nomor_md');
                $filename = time() . '_' . uniqid() . '_nomormd.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/nomor_md_dokumentasi/' . $filename, $image);
                $nomor_md_path = 'uploads/nomor_md_dokumentasi/' . $filename;
            }

            // Fetch pengemasan produk to get the berat and shift
            $pengemasanProduk = PengemasanProduk::find($request->id_pengemasan_produk);
            $berat = $pengemasanProduk ? $pengemasanProduk->berat : null;
            $id_shift = $pengemasanProduk ? $pengemasanProduk->id_shift : null;

            $record = PengemasanPlastik::create([
                'uuid' => Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'id_pengemasan_produk' => $request->id_pengemasan_produk,
                'id_shift' => $id_shift,
                'tanggal' => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
                'berat' => $berat,
                'proses_penimbangan' => $request->proses_penimbangan,
                'proses_sealing' => $request->proses_sealing,
                'identitas_produk' => $request->identitas_produk,
                'nomor_md' => $nomor_md_path,
                'kode_kemasan_plastik' => $request->kode_kemasan_plastik,
                'line' => is_array($request->line) ? implode(', ', array_filter(array_map('trim', $request->line), fn($v) => $v !== '')) : $request->line,
                'waktu_pengemasan' => $request->waktu_pengemasan,
                'kesesuaian_label_plastik' => $request->kesesuaian_label_plastik,
                'kekuatan_seal' => $request->kekuatan_seal,
            ]);
            return response()->json(['success' => true, 'id' => $record->id, 'uuid' => $record->uuid]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function storeBeratProdukBag(Request $request)
    {
        try {
            $user = Auth::user();
            $pengemasanProduk = PengemasanProduk::find($request->id_pengemasan_produk);
            $id_shift = $pengemasanProduk ? $pengemasanProduk->id_shift : null;
            
            $tanggal = \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s');
            $firstId = null;
            $uuid = null;

            foreach ($request->line as $index => $line) {
                $record = BeratProdukBag::create([
                    'id_plan' => $user->id_plan,
                    'user_id' => $user->id,
                    'id_pengemasan_produk' => $request->id_pengemasan_produk,
                    'id_pengemasan_plastik' => $request->id_pengemasan_plastik,
                    'id_shift' => $id_shift,
                    'id_data_bag' => $request->id_data_bag[$index] ?? $request->id_data_bag[0],
                    'line' => $line,
                    'tanggal' => $tanggal,
                    'jam' => $request->jam,
                    'berat_aktual' => $request->berat_aktual[$index] ?? [],
                    'rata_rata_berat' => $request->rata_rata_berat[$index],
                ]);

                if ($index === 0) {
                    $firstId = $record->id;
                    $uuid = $record->uuid;
                }
            }

            return response()->json(['success' => true, 'id' => $firstId, 'uuid' => $uuid]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function storeBeratProdukBox(Request $request)
    {
        try {
            $user = Auth::user();
            $pengemasanProduk = PengemasanProduk::find($request->id_pengemasan_produk);
            $id_shift = $pengemasanProduk ? $pengemasanProduk->id_shift : null;

            // Handle berat_aktual as array
            $beratAktualRaw = $request->input('berat_aktual', []);
            if (!is_array($beratAktualRaw)) {
                $beratAktualRaw = [$beratAktualRaw];
            }
            $beratAktualFiltered = array_values(array_filter(array_map('floatval', $beratAktualRaw)));

            // Auto-calculate rata_rata if not provided
            $rataRata = $request->rata_rata_berat;
            if (!$rataRata && count($beratAktualFiltered) > 0) {
                $rataRata = round(array_sum($beratAktualFiltered) / count($beratAktualFiltered), 2);
            }

            $record = BeratProdukBox::create([
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'id_pengemasan_produk' => $request->id_pengemasan_produk,
                'id_pengemasan_plastik' => $request->id_pengemasan_plastik,
                'id_berat_produk_bag' => $request->id_berat_produk_bag,
                'id_shift' => $id_shift,
                'id_data_box' => $request->id_data_box,
                'tanggal' => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
                'berat_aktual' => $beratAktualFiltered,
                'rata_rata_berat' => $rataRata,
            ]);
            return response()->json(['success' => true, 'id' => $record->id, 'uuid' => $record->uuid]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function storePengemasanKarton(Request $request)
    {
        try {
            $user = Auth::user();
            $pengemasanProduk = PengemasanProduk::find($request->id_pengemasan_produk);
            $id_shift = $pengemasanProduk ? $pengemasanProduk->id_shift : null;

            $record = PengemasanKarton::create([
                'uuid' => Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'id_pengemasan_produk' => $request->id_pengemasan_produk,
                'id_pengemasan_plastik' => $request->id_pengemasan_plastik,
                'id_berat_produk_bag' => $request->id_berat_produk_bag,
                'id_berat_produk_box' => $request->id_berat_produk_box,
                'shift_id' => $id_shift,
                'tanggal' => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
                'identitas_produk_pada_karton' => $request->identitas_produk_pada_karton,
                'standar_jumlah_karton' => $request->standar_jumlah_karton,
                'aktual_jumlah_karton' => $request->aktual_jumlah_karton,
                'kesesuaian_isi_pack' => $request->kesesuaian_isi_pack ? json_encode(array_values($request->kesesuaian_isi_pack)) : null,
            ]);
            return response()->json(['success' => true, 'id' => $record->id, 'uuid' => $record->uuid]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function storeDokumentasi(Request $request)
    {
        try {
            $user = Auth::user();
            $pengemasanProduk = PengemasanProduk::find($request->id_pengemasan_produk);
            $id_shift = $pengemasanProduk ? $pengemasanProduk->id_shift : null;

            $foto_kode_produksi_path = $request->input('foto_kode_produksi') === '-' ? '-' : null;
            $foto_kode_produksi_sekunder_path = null;
            // Jika mode status (OK/Not OK), simpan nilainya langsung
            if ($request->filled('foto_kode_produksi_sekunder_status')) {
                $foto_kode_produksi_sekunder_path = $request->input('foto_kode_produksi_sekunder_status');
            }
            $qr_code_path = $request->input('qr_code') === '-' ? null : null;
            $label_polyroll_path = $request->input('label_polyroll') === '-' ? null : null;
            $label_supplier_path = $request->input('label_supplier') === '-' ? null : null;

            // Pastikan folder ada
            if (!Storage::disk('public')->exists('uploads/foto_kode_produksi')) {
                Storage::disk('public')->makeDirectory('uploads/foto_kode_produksi');
            }
            if (!Storage::disk('public')->exists('uploads/foto_kode_produksi_sekunder')) {
                Storage::disk('public')->makeDirectory('uploads/foto_kode_produksi_sekunder');
            }
            if (!Storage::disk('public')->exists('uploads/qr_code')) {
                Storage::disk('public')->makeDirectory('uploads/qr_code');
            }
            if (!Storage::disk('public')->exists('uploads/label_polyroll')) {
                Storage::disk('public')->makeDirectory('uploads/label_polyroll');
            }
            if (!Storage::disk('public')->exists('uploads/label_supplier')) {
                Storage::disk('public')->makeDirectory('uploads/label_supplier');
            }

            // Foto Kode Produksi
            if ($request->hasFile('foto_kode_produksi')) {
                $file = $request->file('foto_kode_produksi');
                $filename = time() . '_' . uniqid() . '_kode.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/foto_kode_produksi/' . $filename, $image);
                $foto_kode_produksi_path = 'uploads/foto_kode_produksi/' . $filename;
            }

            // Foto Kode Produksi Sekunder
            if ($request->hasFile('foto_kode_produksi_sekunder')) {
                $file = $request->file('foto_kode_produksi_sekunder');
                $filename = time() . '_' . uniqid() . '_kode_sekunder.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/foto_kode_produksi_sekunder/' . $filename, $image);
                $foto_kode_produksi_sekunder_path = 'uploads/foto_kode_produksi_sekunder/' . $filename;
            }
            // QR Code
            if ($request->hasFile('qr_code')) {
                $file = $request->file('qr_code');
                $filename = time() . '_' . uniqid() . '_qr.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/qr_code/' . $filename, $image);
                $qr_code_path = 'uploads/qr_code/' . $filename;
            }

            // Label Polyroll
            if ($request->hasFile('label_polyroll')) {
                $file = $request->file('label_polyroll');
                $filename = time() . '_' . uniqid() . '_polyroll.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/label_polyroll/' . $filename, $image);
                $label_polyroll_path = 'uploads/label_polyroll/' . $filename;
            }
            
            // Label Supplier
            if ($request->hasFile('label_supplier')) {
                $file = $request->file('label_supplier');
                $filename = time() . '_' . uniqid() . '_supplier.jpg';
                $image = Image::make($file)->encode('jpg', 70);
                Storage::disk('public')->put('uploads/label_supplier/' . $filename, $image);
                $label_supplier_path = 'uploads/label_supplier/' . $filename;
            }

            $record = Dokumentasi::create([
                'uuid' => Str::uuid(),
                'id_plan' => $user->id_plan,
                'user_id' => $user->id,
                'id_pengemasan_produk' => $request->id_pengemasan_produk,
                'id_pengemasan_plastik' => $request->id_pengemasan_plastik,
                'id_berat_produk_bag' => $request->id_berat_produk_bag,
                'id_berat_produk_box' => $request->id_berat_produk_box,
                'id_pengemasan_karton' => $request->id_pengemasan_karton,
                'id_shift' => $id_shift,
                'tanggal' => \Carbon\Carbon::parse($request->tanggal)->setTimeFrom(now())->format('Y-m-d H:i:s'),
                'jam' => $request->jam,
                'foto_kode_produksi' => $foto_kode_produksi_path,
                'foto_kode_produksi_sekunder' => $foto_kode_produksi_sekunder_path,
                'qr_code' => $qr_code_path,
                'label_polyroll' => $label_polyroll_path,
                'label_supplier' => $label_supplier_path,
                'keterangan' => $request->keterangan,
            ]);

            if ($pengemasanProduk) {
                $pengemasanProduk->update([
                    'status_release_produk' => $request->status_release_produk,
                    'tindakan_koreksi'      => $request->tindakan_koreksi,
                ]);
            }
            
            session()->flash('success', 'Proses Packing selesai! Semua data berhasil disimpan.');

            return response()->json([
                'success' => true,
                'uuid' => $record->uuid,
                'redirect_url' => route('wizards.packing.index'),
            ]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function show($uuid) {
        $pembekuan = Dokumentasi::with([
            'pengemasanKarton',
            'beratProdukBox',
            'beratProdukBag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift'
        ])->where('uuid', $uuid)->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizards.packing.show', $pembekuan)) {
            return $redirect;
        }

        $semuaBeratProdukBag = BeratProdukBag::with('data_bag')->where('id_pengemasan_plastik', $pembekuan->id_pengemasan_plastik)->get();

        return view('qc-sistem.wizard_packing.show', compact('pembekuan', 'semuaBeratProdukBag'));
    }
    
    public function exportPdf($uuid) {
        $pembekuan = Dokumentasi::with([
            'pengemasanKarton',
            'beratProdukBox.data_box',
            'beratProdukBag.data_bag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift',
            'plan',
        ])->where('uuid', $uuid)->firstOrFail();

        $semuaBeratProdukBag = BeratProdukBag::with('data_bag')->where('id_pengemasan_plastik', $pembekuan->id_pengemasan_plastik)->get();
        $data = [
            ['pembekuan' => $pembekuan, 'semuaBeratProdukBag' => $semuaBeratProdukBag]
        ];

        $kode_form = 'QF 15/00';

        $pdf = Pdf::loadView('qc-sistem.wizard_packing.export_pdf', compact('data', 'kode_form'))
                  ->setPaper('a4', 'portrait');

        $filename = 'WizardPacking_' . $pembekuan->uuid . '.pdf';
        return $pdf->stream($filename);
    }

    public function bulkExportPdf(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_awal',
            'is_audit' => 'nullable|boolean',
            'id_shift' => 'nullable|exists:data_shift,id',
            'kode_form' => 'nullable|string',
        ]);

        $user = Auth::user();
        $query = Dokumentasi::with([
            'pengemasanKarton',
            'beratProdukBox.data_box',
            'beratProdukBag.data_bag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift',
            'plan',
            'qcApprover',
            'produksiApprover',
            'spvApprover'
        ]);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        if ($request->has('is_audit') && $request->is_audit == 1) {
            $query->where('is_audit', true);
        } else {
            $query->operasional();
        }

        if ($request->filled('id_shift')) {
            $query->where('id_shift', $request->id_shift);
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        } elseif ($request->filled('tanggal_awal')) {
            $tanggalAwal = \Carbon\Carbon::parse($request->tanggal_awal)->startOfDay();
            $query->where('tanggal', '>=', $tanggalAwal);
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();
        $kode_form = $request->kode_form ?? 'QF 15/00';

        // Build data array with bag data per record
        $allData = [];
        foreach ($records as $rec) {
            $bags = BeratProdukBag::with('data_bag')
                ->where('id_pengemasan_plastik', $rec->id_pengemasan_plastik)
                ->get();
            $allData[] = [
                'pembekuan'          => $rec,
                'semuaBeratProdukBag' => $bags,
            ];
        }

        // If only one record or total fits in one PDF, download directly
        $chunkSize = 25;
        if (count($allData) <= $chunkSize) {
            $pdf = Pdf::loadView('qc-sistem.wizard_packing.export_pdf', [
                'data'      => $allData,
                'kode_form' => $kode_form,
            ])->setPaper('a4', 'portrait');

            $dateLabel = $request->tanggal_awal
                ? \Carbon\Carbon::parse($request->tanggal_awal)->format('Y-m-d')
                : 'All';
            $filename = 'WizardPacking_' . $dateLabel . '_' . time() . '.pdf';
            return $pdf->download($filename);
        }

        // Multiple records: split into chunks of 25 and zip them
        $chunks  = array_chunk($allData, $chunkSize);
        $tmpDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wp_pdf_' . uniqid();
        mkdir($tmpDir, 0755, true);

        $dateLabel  = $request->tanggal_awal
            ? \Carbon\Carbon::parse($request->tanggal_awal)->format('Y-m-d')
            : 'All';
        $zipPath    = $tmpDir . DIRECTORY_SEPARATOR . 'WizardPacking_' . $dateLabel . '.zip';
        $zip        = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($chunks as $index => $chunk) {
            $partNum    = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $totalParts = str_pad(count($chunks), 2, '0', STR_PAD_LEFT);
            $pdfName    = "WizardPacking_{$dateLabel}_part{$partNum}of{$totalParts}.pdf";
            $pdfPath    = $tmpDir . DIRECTORY_SEPARATOR . $pdfName;

            $pdf = Pdf::loadView('qc-sistem.wizard_packing.export_pdf', [
                'data'      => $chunk,
                'kode_form' => $kode_form,
            ])->setPaper('a4', 'portrait');

            // Write PDF to temp file so images are loaded from disk (not memory)
            file_put_contents($pdfPath, $pdf->output());
            $zip->addFile($pdfPath, $pdfName);
        }

        $zip->close();

        $zipFilename = 'WizardPacking_' . $dateLabel . '_' . time() . '.zip';

        // Clean up temp directory after the response is sent
        $tmpDirToClean = $tmpDir;
        register_shutdown_function(function () use ($tmpDirToClean) {
            if (is_dir($tmpDirToClean)) {
                foreach (glob($tmpDirToClean . DIRECTORY_SEPARATOR . '*') as $f) {
                    @unlink($f);
                }
                @rmdir($tmpDirToClean);
            }
        });

        return response()->download($zipPath, $zipFilename, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
        ]);
    }

    public function edit($uuid) {
        $pembekuan = Dokumentasi::with([
            'pengemasanKarton',
            'beratProdukBox',
            'beratProdukBag',
            'pengemasanPlastik',
            'pengemasanProduk',
            'pengemasanProduk.produk',
            'shift'
        ])->where('uuid', $uuid)->firstOrFail();

        if ($redirect = $this->redirectToCanonicalAuditUrl('wizards.packing.edit', $pembekuan)) {
            return $redirect;
        }
        
        $semuaBeratProdukBag = BeratProdukBag::with('data_bag')->where('id_pengemasan_plastik', $pembekuan->id_pengemasan_plastik)->get();
        $semuaBeratProdukBox = BeratProdukBox::where('id_berat_produk_bag', $pembekuan->id_berat_produk_bag)->get(); // assuming this relation

        $user = Auth::user();
        if ($user->role === 'superadmin') {
            $produks = \App\Models\JenisProduk::all();
            $shifts = \App\Models\DataShift::all();
            $dataBags = \App\Models\DataBag::all();
            $dataBoxes = \App\Models\DataBox::all();
        } else {
            $produks = \App\Models\JenisProduk::where('id_plan', $user->id_plan)->get();
            $shifts = \App\Models\DataShift::where('id_plan', $user->id_plan)->get();
            $dataBags = \App\Models\DataBag::where('id_plan', $user->id_plan)->get();
            $dataBoxes = \App\Models\DataBox::where('id_plan', $user->id_plan)->get();
        }

        return view('qc-sistem.wizard_packing.edit', compact('pembekuan', 'semuaBeratProdukBag', 'semuaBeratProdukBox', 'produks', 'shifts', 'dataBags', 'dataBoxes'));
    }

    public function update(Request $request, $uuid) {
        try {
            $pembekuan = Dokumentasi::where('uuid', $uuid)->firstOrFail();
            
            // Update Step 1
            if ($pembekuan->pengemasanProduk && $request->has('step1')) {
                $step1 = $request->step1;
                // handle aktual_suhu_produk array
                if (isset($step1['aktual_suhu_produk'])) {
                    if (is_array($step1['aktual_suhu_produk'])) {
                        $suhuArray = array_filter(array_map('trim', $step1['aktual_suhu_produk']), fn($v) => $v !== '');
                        $step1['aktual_suhu_produk'] = implode(', ', $suhuArray);
                    } elseif (is_string($step1['aktual_suhu_produk'])) {
                        $suhuArray = array_filter(array_map('trim', explode(',', $step1['aktual_suhu_produk'])), fn($v) => $v !== '');
                        $step1['aktual_suhu_produk'] = implode(', ', $suhuArray);
                    }
                }
                $step1['is_kombinasi'] = (isset($step1['is_kombinasi']) && $step1['is_kombinasi'] == 1) ? 1 : 0;
                if (!$step1['is_kombinasi']) {
                    $step1['id_produk_2'] = null;
                    $step1['kode_produksi_2'] = null;
                    $step1['berat_produk_2'] = null;
                    $step1['tanggal_expired_2'] = null;
                } else {
                    $step1['tanggal_expired_2'] = !empty($step1['tanggal_expired_2']) ? \Carbon\Carbon::parse($step1['tanggal_expired_2'])->format('Y-m-d') : null;
                }
                $pembekuan->pengemasanProduk->update($step1);

                // Update shift and tanggal in Dokumentasi and all related tables
                $globalUpdates = [];
                if (isset($step1['id_shift'])) $globalUpdates['id_shift'] = $step1['id_shift'];
                if (isset($step1['tanggal']))  $globalUpdates['tanggal']  = \Carbon\Carbon::parse($step1['tanggal'])->setTimeFrom(now())->format('Y-m-d H:i:s');
                if (isset($step1['jam']))      $globalUpdates['jam']      = $step1['jam'];
                
                if (!empty($globalUpdates)) {
                    $pembekuan->update($globalUpdates);
                    if ($pembekuan->pengemasanPlastik) $pembekuan->pengemasanPlastik->update($globalUpdates);
                    if ($pembekuan->beratProdukBox) $pembekuan->beratProdukBox->update($globalUpdates);
                    if ($pembekuan->pengemasanKarton) $pembekuan->pengemasanKarton->update($globalUpdates);
                    BeratProdukBag::where('id_pengemasan_plastik', $pembekuan->id_pengemasan_plastik)->update($globalUpdates);
                }
            }

            // Update Step 2
            if ($pembekuan->pengemasanPlastik && $request->has('step2')) {
                $step2 = $request->step2;
                if (isset($step2['line']) && is_array($step2['line'])) {
                    $step2['line'] = implode(', ', array_filter(array_map('trim', $step2['line']), fn($v) => $v !== ''));
                }

                if ($request->hasFile('step2.nomor_md')) {
                    if ($pembekuan->pengemasanPlastik->nomor_md && Storage::disk('public')->exists($pembekuan->pengemasanPlastik->nomor_md)) {
                        Storage::disk('public')->delete($pembekuan->pengemasanPlastik->nomor_md);
                    }
                    $file = $request->file('step2.nomor_md');
                    $filename = time() . '_' . uniqid() . '_nomormd.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/nomor_md_dokumentasi/' . $filename, $image);
                    $step2['nomor_md'] = 'uploads/nomor_md_dokumentasi/' . $filename;
                } else {
                    unset($step2['nomor_md']);
                }

                $pembekuan->pengemasanPlastik->update($step2);
            }

            // Update Step 3
            if ($request->has('step3')) {
                foreach ($request->step3 as $bag_id => $bag_data) {
                    $bag = BeratProdukBag::find($bag_id);
                    if ($bag) {
                        if (isset($bag_data['berat_aktual']) && is_array($bag_data['berat_aktual'])) {
                            $bag_data['berat_aktual'] = array_values(array_filter(array_map('floatval', $bag_data['berat_aktual'])));
                        }
                        $bag->update($bag_data);
                    }
                }
            }

            // Update Step 4
            if ($pembekuan->beratProdukBox && $request->has('step4')) {
                $step4 = $request->step4;
                if (isset($step4['berat_aktual']) && is_array($step4['berat_aktual'])) {
                    $step4['berat_aktual'] = array_values(array_filter(array_map('floatval', $step4['berat_aktual'])));
                }
                $pembekuan->beratProdukBox->update($step4);
            }

            // Update Step 5
            if ($pembekuan->pengemasanKarton && $request->has('step5')) {
                $step5 = $request->step5;
                if (isset($step5['kesesuaian_isi_pack']) && is_array($step5['kesesuaian_isi_pack'])) {
                    $step5['kesesuaian_isi_pack'] = json_encode(array_values($step5['kesesuaian_isi_pack']));
                }
                $pembekuan->pengemasanKarton->update($step5);
            }

            // Update Step 6 (Dokumentasi Files)
            if ($request->has('step6')) {
                $step6 = $request->step6;
                $updateDokumentasi = [];

 if (isset($step6['foto_kode_produksi']) && $request->hasFile('step6.foto_kode_produksi')) {
                    $file = $request->file('step6.foto_kode_produksi');
                    $filename = time() . '_' . uniqid() . '_kode_produksi.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/foto_kode_produksi/' . $filename, $image);
                    $updateDokumentasi['foto_kode_produksi'] = 'uploads/foto_kode_produksi/' . $filename;
                }

                if (isset($step6['foto_kode_produksi_sekunder']) && $request->hasFile('step6.foto_kode_produksi_sekunder')) {
                    $file = $request->file('step6.foto_kode_produksi_sekunder');
                    $filename = time() . '_' . uniqid() . '_kode_sekunder.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/foto_kode_produksi_sekunder/' . $filename, $image);
                    $updateDokumentasi['foto_kode_produksi_sekunder'] = 'uploads/foto_kode_produksi_sekunder/' . $filename;
                } elseif (!empty($step6['foto_kode_produksi_sekunder_status'])) {
                    // Mode OK / Not OK — simpan nilai status langsung
                    $updateDokumentasi['foto_kode_produksi_sekunder'] = $step6['foto_kode_produksi_sekunder_status'];
                }

                if (isset($step6['qr_code']) && $request->hasFile('step6.qr_code')) {
                    $file = $request->file('step6.qr_code');
                    $filename = time() . '_' . uniqid() . '_qr.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/qr_code/' . $filename, $image);
                    $updateDokumentasi['qr_code'] = 'uploads/qr_code/' . $filename;
                }

                if (isset($step6['label_polyroll']) && $request->hasFile('step6.label_polyroll')) {
                    $file = $request->file('step6.label_polyroll');
                    $filename = time() . '_' . uniqid() . '_polyroll.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/label_polyroll/' . $filename, $image);
                    $updateDokumentasi['label_polyroll'] = 'uploads/label_polyroll/' . $filename;
                }

                if (isset($step6['label_supplier']) && $request->hasFile('step6.label_supplier')) {
                    $file = $request->file('step6.label_supplier');
                    $filename = time() . '_' . uniqid() . '_supplier.jpg';
                    $image = Image::make($file)->encode('jpg', 70);
                    Storage::disk('public')->put('uploads/label_supplier/' . $filename, $image);
                    $updateDokumentasi['label_supplier'] = 'uploads/label_supplier/' . $filename;
                }

                if (isset($step6['keterangan'])) {
                    $updateDokumentasi['keterangan'] = $step6['keterangan'];
                }
                if ($pembekuan->pengemasanProduk) {
                    $ppUpdates = [];
                    if (array_key_exists('status_release_produk', $step6)) {
                        $ppUpdates['status_release_produk'] = $step6['status_release_produk'];
                    }
                    if (array_key_exists('tindakan_koreksi', $step6)) {
                        $ppUpdates['tindakan_koreksi'] = $step6['tindakan_koreksi'];
                    }
                    if (!empty($ppUpdates)) {
                        $pembekuan->pengemasanProduk->update($ppUpdates);
                    }
                }

                if (!empty($updateDokumentasi)) {
                    $pembekuan->update($updateDokumentasi);
                }
            }

            return redirect()->route($pembekuan->is_audit ? 'wizards.packing.audit' : 'wizards.packing.index')->with('success', 'Data Packing berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan perubahan: ' . $e->getMessage());
        }
    }

    public function destroy($uuid) {
        $record = Dokumentasi::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();
        
        if ($user->role !== 'superadmin' && !$record->is_audit) {
            if ($record->id_plan !== $user->id_plan) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }
        
        $is_audit = $record->is_audit;
        $record->delete();
        
        return redirect()->route($is_audit ? 'wizards.packing.audit' : 'wizards.packing.index')->with('success', 'Data berhasil dihapus.');
    }

    public function approve(Request $request, $uuid) {
        try {
            $user = Auth::user();
            $type = $request->input('type');
            
            if (!in_array($type, ['qc', 'produksi', 'spv'])) {
                return response()->json(['success' => false, 'message' => 'Tipe approval tidak valid'], 400);
            }
            
            $pembekuan = Dokumentasi::where('uuid', $uuid)->firstOrFail();

            // Sequential approval validation
            if ($type === 'produksi' && !$pembekuan->approved_by_qc) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh QC terlebih dahulu.'], 400);
            }
            if ($type === 'spv' && !$pembekuan->approved_by_produksi) {
                return response()->json(['success' => false, 'message' => 'Data harus disetujui oleh Produksi terlebih dahulu.'], 400);
            }
            
            switch ($type) {
                case 'qc':
                    $pembekuan->approved_by_qc = true;
                    $pembekuan->qc_approved_by = $user->id;
                    $pembekuan->qc_approved_at = now();
                    break;
                case 'produksi':
                    $pembekuan->approved_by_produksi = true;
                    $pembekuan->produksi_approved_by = $user->id;
                    $pembekuan->produksi_approved_at = now();
                    break;
                case 'spv':
                    $pembekuan->approved_by_spv = true;
                    $pembekuan->spv_approved_by = $user->id;
                    $pembekuan->spv_approved_at = now();
                    break;
            }
            
            $pembekuan->save();
            return response()->json(['success' => true, 'message' => 'Data berhasil di-approve oleh ' . ucfirst($type)]);
        } catch (\Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    /**
     * Bulk Approve data wizard packing per shift
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:data_shift,id'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $tanggal = $request->tanggal;
        $shift_id = $request->shift_id;

        // Role-based access control
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5]
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan approval ini.'
            ], 403);
        }

        // Ambil semua data pada tanggal dan shift tersebut
        $query = Dokumentasi::whereDate('tanggal', $tanggal)
                          ->operasional()
                          ->where('id_shift', $shift_id);

        if ($user->role !== 'superadmin') {
            $query->where('id_plan', $user->id_plan);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ditemukan untuk shift ini.'
            ], 404);
        }

        // Sequential approval validation
        if ($type === 'produksi') {
            $unapprovedByQc = $items->where('approved_by_qc', false)->count();
            if ($unapprovedByQc > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh QC terlebih dahulu.'
                ], 400);
            }
        }

        if ($type === 'spv') {
            $unapprovedByProduksi = $items->where('approved_by_produksi', false)->count();
            if ($unapprovedByProduksi > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seluruh data pada shift ini harus disetujui oleh Produksi terlebih dahulu.'
                ], 400);
            }
        }

        $approvalField = "approved_by_{$type}";
        $approvedByField = "{$type}_approved_by";
        $approvedAtField = "{$type}_approved_at";
        
        // Update all records
        $query->update([
            $approvalField => true,
            $approvedByField => $user->id,
            $approvedAtField => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data pada shift ini berhasil disetujui.'
        ]);
    }

    public function bulkApproveSelected(Request $request)
    {
        $request->validate([
            'type' => 'required|in:qc,produksi,spv',
            'uuids' => 'required|array|min:1|max:100',
            'uuids.*' => 'required|uuid',
        ]);

        $user = Auth::user();
        $type = $request->input('type');
        $allowedRoles = [
            'qc' => [1, 3, 5],
            'produksi' => [1, 2, 5],
            'spv' => [1, 4, 5],
        ];

        if (!in_array($user->id_role, $allowedRoles[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki otoritas untuk verifikasi ini.',
            ], 403);
        }

        $query = Dokumentasi::with('auditVersion')
            ->whereIn('uuid', $request->input('uuids'))
            ->where("approved_by_{$type}", false)
            ->when($user->role !== 'superadmin', function ($query) use ($user) {
                $query->where('id_plan', $user->id_plan);
            });

        $itemsToUpdate = $query->get();
        if ($itemsToUpdate->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua data sudah diverifikasi sebelumnya.',
            ]);
        }

        $updateData = [
            "approved_by_{$type}" => true,
            "{$type}_approved_by" => $user->id,
            "{$type}_approved_at" => now(),
        ];

        \Illuminate\Support\Facades\DB::transaction(function () use ($itemsToUpdate, $updateData) {
            foreach ($itemsToUpdate as $item) {
                $item->update($updateData);

                if (!$item->is_audit && $item->auditVersion) {
                    $item->auditVersion->update($updateData);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil verifikasi ' . $itemsToUpdate->count() . ' data ' . strtoupper($type) . '.',
        ]);
    }

    public function bulkExportExcel(Request $request, \App\Services\BulkExport\WizardPackingBulkExportService $exportService)
    {
        return $exportService->exportExcel($request);
    }
}

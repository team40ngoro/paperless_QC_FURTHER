<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class AccessControlController extends Controller
{
    /**
     * Display a listing of roles and their permissions for qc-sistem modules
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('role', 'asc')->get();
        $selectedRole = null;
        $modules = $this->getQcModules();
        $rolePermissions = [];

        if ($request->has('role_id')) {
            $selectedRole = Role::find($request->role_id);
            if ($selectedRole) {
                // Get permissions associated directly with this specific Role mapping
                $rolePermissions = $selectedRole->permissions()->pluck('name')->toArray();
            }
        }

        // Make sure all modules have their corresponding permissions exist in DB
        $this->ensurePermissionsExist($modules);

        return view('akses-control.index', compact('roles', 'selectedRole', 'modules', 'rolePermissions'));
    }

    /**
     * Update the role's permissions
     */
    public function update(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($request->role_id);
        $submittedPermissions = $request->permissions ?? [];
        
        $permissionIds = [];
        foreach ($submittedPermissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $permissionIds[] = $permission->id;
        }

        // Sync local mapping
        $role->permissions()->sync($permissionIds);

        return redirect()->back()->with('success', 'Akses kontrol untuk role ' . $role->role . ' berhasil diperbarui!');
    }

    /**
     * Get all available QC modules with official form labels (e.g. [QF-01/00] Chillroom)
     */
    private function getQcModules()
    {
        $modulesMap = [
            // Pemeriksaan Kedatangan
            'chillroom'                            => '[QF-01/00] Chillroom',
            'seasoning'                            => '[QF-01/00] Seasoning',
            'shoestring'                           => '[QF-02/00] Shoestring',
            'pemeriksaan_bahan_kemas'              => '[QF-03/00] Kemasan',
            'rebox'                                => '[QF-34/00] Rebox',

            // Meat Preparation
            'persiapan_bahan_forming'              => '[QF-04/00] Cold Mixing',
            'persiapan_bahan_emulsi'               => '[QF-05/00] Bahan Emulsi',
            'persiapan_bahan_better'               => '[QF-06/00] Bahan Better',
            'wizard_proses4'                       => '[QF-07/00] Wizard Proses 4 (Tumbling & Aging)',

            // Cooking QC (Wizard)
            'wizard_proses1'                       => '[QF-09/00] Wizard Proses 1 (Frying -> IQF)',
            'wizard_proses2'                       => '[QF-10/00] Wizard Proses 2 (Frying -> Roasted -> IQF)',
            'wizard_proses3'                       => '[QF-10/00] Wizard Proses 3 (Roasted -> IQF)',
            'verifikasi_berat_produk'              => '[QF-11/00] Verifikasi Berat Produk',

            // Pemeriksaan Thawing
            'proses_twahing'                       => '[QF-33/00] Proses Thawing',

            // Packing
            'wizard_packing'                       => '[QF-15/00] Wizard Packing',
            'pembuatan_sample'                     => '[QF-17/00] Pembuatan Sample Produk',
            'input_metal_detector'                 => '[QF-18/00] Metal Detector',
            'label_pemeriksaan'                    => '[QF-36/00] Label Pemeriksaan',

            // KPI Berat Produk YUM
            'produk_yum'                           => '[QF-16/00] KPI Produk YUM',

            // Produk Fla
            'pemeriksaan-produk-cooking-mixer-fla' => '[QF-12/00] Cooking Mixer Fla',
            'pemeriksaan-rheon-machine'            => '[QF-19/00] Rheon Machine Fla',

            // Produk Rice Bites
            'pemasakan_nasi'                       => '[QF-13/00] Pemasakan Nasi',
            'pemeriksaan_rice_bites'               => '[QF-14/00] Pemeriksaan Produk Rice Bites',

            // Non Proses Produksi
            'produk_forming'                       => '[QF-20/00] Produk Forming',
            'produk_non_forming'                   => '[QF-21/00] Produk Non Forming',
            'ketidaksesuaian_plastik'              => '[QF-22/00] Ketidaksesuaian Plastik',
            'pemeriksaan_benda_asing'              => '[QF-23/00] Kontaminasi Benda Asing',
            'pemeriksaan_proses_produksi'          => '[QF-24/00] Ketidaksesuaian Proses Produksi',
            'area_proses'                          => '[QF-25/00] Pemeriksaan Kondisi Area Kerja',
            'verif_peralatan'                      => '[QF-26/00] Verifikasi Kebersihan Mesin & Peralatan',
            'verif_cip'                            => '[QF-27/00] Pemeriksaan CIP',
            'gmp_karyawan'                         => '[QF-28/00] GMP Karyawan',
            'kontrol_sanitasi'                     => '[QF-29/00] Kontrol Sanitasi',
            'barang_mudah_pecah'                   => '[QF-30/00] Barang Mudah Pecah',
            'timbangan'                            => '[QF-31/00] Timbangan',
            'thermometer'                          => '[QF-32/00] Thermometer',
            'inventaris_qc'                        => '[QF-39/00] Checklist Inventaris Peralatan QC',
        ];

        $modules = [];
        foreach ($modulesMap as $dirName => $label) {
            $moduleKebab = str_replace('_', '-', $dirName);
            $modules[] = [
                'id'          => $dirName,
                'label'       => $label,
                'permissions' => [
                    'view'   => 'view-' . $moduleKebab,
                    'create' => 'create-' . $moduleKebab,
                    'edit'   => 'edit-' . $moduleKebab,
                    'delete' => 'delete-' . $moduleKebab,
                ]
            ];
        }

        // Sort alphabetically by label to maintain uniform order
        usort($modules, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $modules;
    }

    /**
     * Seed any new permissions on layout load
     */
    private function ensurePermissionsExist($modules)
    {
        foreach ($modules as $module) {
            foreach ($module['permissions'] as $action => $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }
    }
}

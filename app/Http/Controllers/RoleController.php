<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::withCount('users')
            ->orderBy('role', 'asc')
            ->paginate(10);

        return view('super-admin.role.index', compact('roles'));
    }

    /**
     * Menampilkan form untuk membuat role baru
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('super-admin.role.create');
    }

    /**
     * Menyimpan role baru ke database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|max:255|unique:roles,role',
        ], [
            'role.required' => 'Nama role wajib diisi',
            'role.string' => 'Nama role harus berupa teks',
            'role.max' => 'Nama role maksimal 255 karakter',
            'role.unique' => 'Nama role sudah ada, gunakan nama lain',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['uuid'] = Str::uuid();

        Role::create($data);

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail role
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $role = Role::with('users')
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('super-admin.roles.show', compact('role'));
    }

    /**
     * Menampilkan form untuk edit role
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        return view('super-admin.role.edit', compact('role'));
    }

    /**
     * Update role di database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|max:255|unique:roles,role,' . $role->id,
        ], [
            'role.required' => 'Nama role wajib diisi',
            'role.string' => 'Nama role harus berupa teks',
            'role.max' => 'Nama role maksimal 255 karakter',
            'role.unique' => 'Nama role sudah ada, gunakan nama lain',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = $request->except(['uuid']);
        $role->update($updateData);

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diupdate.');
    }

    /**
     * Hapus role dari database
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();

        // Cek apakah role masih digunakan oleh user
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' user.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}

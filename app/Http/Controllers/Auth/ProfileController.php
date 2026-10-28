<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        return view('auth.edit', compact('user'));
    }

    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $request->validate([
            // Ignore uniqueness check by matching on uuid column (not id)
            'email' => 'required|email|unique:users,email,' . $user->uuid . ',uuid',
            'password' => 'nullable|min:6'
        ]);
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return back()->with('success', 'Akun berhasil diupdate!');
    }

    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->delete();
        return redirect()->back()->with('success', 'Akun berhasil dihapus!');
    }
}

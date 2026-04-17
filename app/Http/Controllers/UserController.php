<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['pegawai', 'unitKerja']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        $users = $query->orderBy('username')->paginate(10)->withQueryString();
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $pegawai = Pegawai::whereDoesntHave('user')
                          ->where('is_active', true)
                          ->orderBy('nama')
                          ->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();
        
        return view('users.create', compact('pegawai', 'unitKerja'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,sub_admin,pegawai',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'unit_kerja_id' => 'required_if:role,sub_admin|nullable|exists:unit_kerja,id',
        ]);

        User::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'pegawai_id' => $request->pegawai_id,
            'unit_kerja_id' => $request->role === 'sub_admin' ? $request->unit_kerja_id : null,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $pegawai = Pegawai::where(function($q) use ($user) {
                            $q->whereDoesntHave('user')
                              ->orWhere('id', $user->pegawai_id);
                        })
                        ->where('is_active', true)
                        ->orderBy('nama')
                        ->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();
        
        return view('users.edit', compact('user', 'pegawai', 'unitKerja'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'nama' => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,sub_admin,pegawai',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'unit_kerja_id' => 'required_if:role,sub_admin|nullable|exists:unit_kerja,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'username' => $request->username,
            'nama' => $request->nama,
            'role' => $request->role,
            'pegawai_id' => $request->pegawai_id,
            'unit_kerja_id' => $request->role === 'sub_admin' ? $request->unit_kerja_id : null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus user yang sedang login.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        $temporaryPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);
        
        return redirect()->route('users.index')->with('success', 'Password berhasil direset. Password sementara: ' . $temporaryPassword . '. User wajib ganti password saat login berikutnya.');
    }

    public function changePassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah.');
    }
}

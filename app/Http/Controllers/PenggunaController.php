<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PenggunaStoreRequest;
use App\Http\Requests\PenggunaUpdateRequest;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar pengguna
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Filter berdasarkan pencarian
        if ($request->has('cari') && $request->cari != '') {
            $search = $request->cari;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asal_sekolah', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nomor_hp', 'like', "%{$search}%")
                    ->orWhere('default_role', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->has('role') && $request->role != '') {
            $query->role($request->role);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $pengguna = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('sistem.pengguna.index', [
            'title' => 'Manajemen Pengguna',
            'pengguna' => $pengguna,
            'roles' => $roles,
        ]);
    }

    /**
     * Menampilkan form tambah pengguna
     */
    public function create(): View
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();
        return view('sistem.pengguna.create', [
            'title' => 'Tambah Pengguna Baru',
            'roles' => $roles,
        ]);
    }

    /**
     * Menyimpan pengguna baru
     */
    public function store(PenggunaStoreRequest $request): RedirectResponse
    {
        try {
            $pengguna = User::create([
                'name'          => $request->nama,
                'asal_sekolah'  => $request->asal_sekolah,
                'email'         => $request->email,
                'username'      => $request->username,
                'nomor_hp'      => $request->nomor_hp,
                'nomor_hp2'     => $request->nomor_hp2,
                'default_role'  => $request->role,
                'status'        => $request->status ?? 'active',
                'password'      => bcrypt($request->password),
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $pengguna->uploadAvatar($request->file('avatar'));
            }

            // Assign role ke pengguna
            $pengguna->syncRoles([$request->role]);

            return redirect()->route('pengguna.index')
                ->with('success', 'Pengguna ' . $pengguna->name . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pengguna
     */
    public function show(User $pengguna): View
    {
        return view('sistem.pengguna.show', [
            'title' => 'Detail Pengguna - ' . $pengguna->name,
            'pengguna' => $pengguna,
        ]);
    }

    /**
     * Menampilkan form edit pengguna
     */
    public function edit(User $pengguna): View
    {
        $roles = Role::where('name', '!=', 'superadmin')->get();

        return view('sistem.pengguna.edit', [
            'title' => 'Edit Pengguna - ' . $pengguna->name,
            'pengguna' => $pengguna,
            'roles' => $roles,
        ]);
    }

    /**
     * Update data pengguna
     */
    public function update(PenggunaUpdateRequest $request, User $pengguna): RedirectResponse
    {
        // dd($request->file('avatar'));

        try {
            $data = [
                'name'          => $request->nama,
                'asal_sekolah'  => $request->asal_sekolah,
                'email'         => $request->email,
                'username'      => $request->username,
                'nomor_hp'      => $request->nomor_hp,
                'nomor_hp2'     => $request->nomor_hp2,
                'default_role'  => $request->role,
                'status'        => $request->status,
            ];

            // Jika password diisi, update password
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $pengguna->update($data);

            // Handle avatar upload
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $pengguna->uploadAvatar($request->file('avatar'));
            }

            // Update role (kecuali superadmin tidak bisa diubah)
            if (!$pengguna->hasRole('superadmin')) {
                $pengguna->syncRoles([$request->role]);
            }

            return redirect()->route('pengguna.index')
                ->with('success', 'Data ' . $pengguna->name . ' berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Hapus avatar pengguna
     */
    public function deleteAvatar(User $pengguna): RedirectResponse
    {
        try {
            $pengguna->clearMediaCollection('avatar');

            return back()->with('success', 'Foto profil berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus foto profil: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pengguna
     */
    public function destroy(User $pengguna): RedirectResponse
    {
        // Cegah hapus superadmin
        if ($pengguna->hasRole('superadmin')) {
            return back()->with('error', 'Tidak dapat menghapus pengguna dengan role Superadmin.');
        }

        // Cegah hapus diri sendiri
        if ($pengguna->user_id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        try {
            // Hapus media terlebih dahulu
            $pengguna->clearMediaCollection('avatar');
            $pengguna->delete();

            return redirect()->route('pengguna.index')
                ->with('success', 'Pengguna ' . $pengguna->name . ' berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Reset password pengguna
     */
    public function resetPassword(User $pengguna): RedirectResponse
    {
        try {
            $defaultPassword = $pengguna->username;
            $pengguna->update([
                'password' => bcrypt($defaultPassword)
            ]);

            return back()->with('success', 'Berhasil direset. Username: (' . $pengguna->username . ') Password baru: (' . $defaultPassword . ')');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;
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
     * Menampilkan halaman register mitra pmb
     */
    public function createPublic(): View
    {
        return view('sistem.daftar', [
            'title' => 'Daftar ' . konfigs('NAMA_SISTEM_ALIAS'),
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
     * Menyimpan pengguna baru (via register)
     */
    public function storePublic(PenggunaStoreRequest $request): RedirectResponse // Gunakan PenggunaStoreRequest
    {
        // Validasi sudah dilakukan oleh PenggunaStoreRequest
        // Tapi kita hanya perlu menangani logika register di sini

        // Ambil data dari request, sesuaikan dengan field di User model
        // Kita gunakan 'nama' dari request dan mapping ke 'name' di model
        // Kita set role default ke 'agen' dan status default ke 'pending'

        // dd($request->syarat_dan_ketentuan); // null - "on"
        if ($request->syarat_dan_ketentuan == null) {
            return back()->withInput()->withErrors(['info' => 'Anda belum menyetujui syarat dan ketentuan yang berlaku.']);
        }

        // Verifikasi Turnstile untuk create mitra pmb
        if (!$this->handleTurnstileValidation($request)) {
            // return back()->withInput()->with('turnstile_notvalid', 'Verifikasi keamanan gagal');
            return back()->withInput()->withErrors(['turnstile_notvalid' => 'Verifikasi keamanan gagal']);
        }

        $data = [
            'name'          => $request->nama, // Sesuaikan dengan nama field di form dan request
            'asal_sekolah'  => $request->asal_sekolah,
            'email'         => $request->email,
            'username'      => $request->username,
            'nomor_hp'      => $request->nomor_hp, // Bisa null jika opsional di rules register
            'nomor_hp2'     => $request->nomor_hp2,
            'default_role'  => 'agen', // Role default untuk pendaftar
            'status'        => 'pending', // Status default untuk pendaftar, bisa diaktifkan oleh admin
            'password'      => bcrypt($request->password)
        ];

        try {
            $user = User::create($data);

            // Assign role default 'agen'
            // $user->assignRole('agen');
            $user->assignRole($data['default_role'] ?? 'agen');

            // Opsional: Kirim email verifikasi
            // $user->sendEmailVerificationNotification();

            // Opsional: Login otomatis setelah register
            // Auth::login($user);

            return redirect()->route('login')->with('info', 'Terimakasih telah mendaftar sebagai Mitra. Permohonan akun Anda akan segera diproses oleh TIM PMB.');
        } catch (Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['general' => 'Gagal mendaftar. Silakan coba lagi.'])
                ->withInput();
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

    /**
     * Handle Turnstile validation logic.
     * Returns true if validation passes, false otherwise.
     */
    private function handleTurnstileValidation(PenggunaStoreRequest $request)
    {
        if (!env('USING_TURNSTILE', true)) {
            return true; // Bypass if Turnstile is disabled
        }

        $turnstileResponse = $request->input('cf-turnstile-response');
        if (!$turnstileResponse) {
            return false; // Tidak ada respons turnstile
        }

        return $this->validateTurnstile($turnstileResponse, $request->ip());
    }

    /**
     * Fungsi untuk memverifikasi Turnstile.
     */
    protected function validateTurnstile(string $response, string $ip): bool
    {
        $apiResponse = Http::asForm()->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify', // Perhatikan spasi di akhir URL sebelumnya
            [
                'secret'   => env('TURNSTILE_SECRET_KEY'),
                'response' => $response,
                'remoteip' => $ip,
            ]
        );

        return $apiResponse->json('success', false);
    }
}

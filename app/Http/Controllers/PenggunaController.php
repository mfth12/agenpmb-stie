<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use App\Models\UserAfiliasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
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
        // Gunakan query builder untuk join
        $query = User::select('users.*', 'user_afiliasis.nama as afiliasi_nama') // Ambil kolom dari users dan nama afiliasi
            ->leftJoin('user_afiliasis', 'users.afiliasi', '=', 'user_afiliasis.afiliasi_id'); // LEFT JOIN

        // Filter berdasarkan pencarian
        if ($request->has('cari') && $request->cari != '') {
            $search = $request->cari;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.asal_sekolah', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.username', 'like', "%{$search}%")
                    ->orWhere('users.nomor_hp', 'like', "%{$search}%")
                    ->orWhere('users.default_role', 'like', "%{$search}%")
                    ->orWhere('user_afiliasis.nama', 'like', "%{$search}%"); // Tambahkan filter ke nama afiliasi
            });
        }

        // Filter berdasarkan role
        if ($request->has('role') && $request->role != '') {
            $query->role($request->role);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('users.status', $request->status);
        }

        // Sekarang $query sudah termasuk data afiliasi (jika ada)
        // Gunakan with() untuk relasi lain yang diakses di view untuk mencegah N+1
        $pengguna = $query->with(['media', 'roles']) // Tambahkan ini untuk mencegah N+1 pada media dan roles
            ->latest('users.created_at')->paginate(10);
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
        $afiliasis_root = UserAfiliasi::root()->get(); // Ambil afiliasi root

        return view('sistem.daftar', [
            'title' => 'Daftar ' . konfigs('NAMA_SISTEM_ALIAS'),
            'afiliasis_root' => $afiliasis_root, // Kirim ke view
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
    public function storePublic(PenggunaStoreRequest $request): RedirectResponse
    {
        // Validasi sudah dilakukan oleh PenggunaStoreRequest

        // Verifikasi Turnstile untuk create mitra pmb
        if (env('USING_TURNSTILE', false)) {
            $turnstileResponse = $request->input('cf-turnstile-response');
            if (!$turnstileResponse) {
                return back()->withInput()->with('error', 'Verifikasi keamanan wajib dilakukan.');
            }

            $turnstileValidationResult = $this->validateTurnstile($turnstileResponse, $request->ip());

            if (!$turnstileValidationResult) {
                return back()->withInput()->with('error', 'Verifikasi keamanan gagal. Silakan coba lagi.');
            }
        }

        if ($request->syarat_dan_ketentuan == null) {
            return back()->withInput()->with('error', 'Anda belum menyetujui syarat dan ketentuan yang berlaku.');
        }

        // --- LOGIKA AFILIASI ---
        $afiliasiId = $request->afiliasi; // Bisa null, 1 (Alumni), 2 (Civitas), 3 (Mitra), atau ID child dari Civitas
        $afiliasiChildId = $request->afiliasi_child_civitas; // ini adalah nilai child
        $asalSekolah = $request->asal_sekolah; // Bisa null atau string

        // Validasi logis tambahan
        $afiliasiModel = null;
        if ($afiliasiId) {
            $afiliasiModel = \App\Models\UserAfiliasi::find($afiliasiId);
            if (!$afiliasiModel) {
                return back()->withInput()->with('error', 'Afiliasi tidak valid.');
            }
        }

        if ($request->afiliasi == null) {
            return back()->withInput()->with('error', 'Silakan dicek kembali Jenis Mitra');
        }

        $afiliasiChildModel = null;
        if ($afiliasiChildId) {
            $afiliasiChildModel = \App\Models\UserAfiliasi::find($afiliasiChildId);
            if (!$afiliasiChildModel) {
                return back()->withInput()->with('error', 'Afiliasi child tidak valid.');
            }
        }

        // Jika afiliasi adalah Mitra (ID 3) atau Child dari Mitra, asal_sekolah wajib
        $isMitra = $afiliasiModel && ($afiliasiModel->afiliasi_id == 3 || $afiliasiModel->parent_id == 3);
        if ($isMitra && empty($asalSekolah)) {
            return back()->withInput()->with('error', 'Nama Instansi/Sekolah Asal wajib diisi untuk afiliasi Mitra.');
        }

        // Jika afiliasi adalah Alumni (ID 1), mungkin asal_sekolah opsional atau wajib, sesuaikan kebijakan
        // Untuk contoh, kita anggap opsional untuk Alumni
        // Jika afiliasi adalah Civitas (ID 2) atau childnya (dosen, staff, lainnya), mungkin asal_sekolah opsional atau diganti dengan info lain
        // Untuk contoh, kita anggap opsional untuk Civitas
        // Jika tidak ada afiliasi, asal_sekolah opsional

        // --- END LOGIKA AFILIASI ---

        $data = [
            'name' => $request->nama,
            'asal_sekolah' => $asalSekolah, // Gunakan nilai yang telah divalidasi
            'afiliasi' => $afiliasiChildId ?? $afiliasiId, // Gunakan nilai ID afiliasi
            'email' => $request->email,
            'username' => $request->username,
            'nomor_hp' => $request->nomor_hp,
            'nomor_hp2' => $request->nomor_hp2,
            'default_role' => 'mitra', // Role default untuk pendaftar
            'status' => 'pending', // Status default untuk pendaftar
            'password' => bcrypt($request->password)
        ];

        try {
            $user = User::create($data);

            // Assign role default 'mitra'
            // $user->assignRole('mitra');
            $user->assignRole($data['default_role'] ?? 'mitra');

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
                'about'        => $request->about,
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
     * Approve user by setting status to 'active'.
     */
    public function approve(User $pengguna): RedirectResponse
    {
        // Authorization check
        if (!auth()->user()->can('user_view')) {
            abort(403, 'Unauthorized');
        }

        // Validasi: hanya pengguna dengan status 'pending' yang bisa disetujui
        if ($pengguna->status !== 'pending') {
            return back()->with('error', 'Hanya pengguna dengan status pending yang bisa disetujui.');
        }

        try {
            $pengguna->update(['status' => 'active']);

            return redirect()->route('pengguna.index')
                ->with('success', 'Pengguna ' . $pengguna->name . ' berhasil disetujui (status menjadi aktif).');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui pengguna: ' . $e->getMessage());
        }
    }


    /**
     * Reject user by setting status to 'inactive'.
     */
    public function reject(User $pengguna): RedirectResponse
    {
        // Authorization check
        if (!auth()->user()->can('user_view')) {
            abort(403, 'Unauthorized');
        }

        // Validasi: hanya pengguna dengan status 'pending' yang bisa disetujui
        if ($pengguna->status !== 'pending') {
            return back()->with('error', 'Hanya pengguna dengan status pending yang bisa disetujui.');
        }

        try {
            $pengguna->update(['status' => 'inactive']);

            return redirect()->route('pengguna.index')
                ->with('success', 'Penolakan atas nama pengguna (' . $pengguna->name . ') telah berhasil.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui pengguna: ' . $e->getMessage());
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

    public function getChildren($parentId): JsonResponse
    {
        $children = UserAfiliasi::childrenOf($parentId)->get();

        return response()->json([
            'success' => true,
            'afiliasis' => $children
        ]);
    }
}

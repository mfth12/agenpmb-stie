<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Jobs\NotifWhatsappJob;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AntrianNotifWhatsappModel;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MasukController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return view('sistem.masuk', [
            'title' => 'Masuk ' . konfigs('NAMA_SISTEM_ALIAS'),
        ]);
    }

    /**
     * Proses login menggunakan API SIAKAD atau lokal
     */
    public function masuk(LoginRequest $request): RedirectResponse
    {
        // Rate limit dulu berdasarkan username + IP
        $throttleKey  = Str::transliterate(Str::lower($request->string('username')) . '|' . $request->ip());
        $maxAttempts  = (int) env('LOGIN_MAX_ATTEMPTS', 3);
        $decaySeconds = (int) env('LOGIN_DECAY_SECONDS', 120);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            event(new Lockout($request));
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'masuk' => 'Silakan coba lagi dalam <span id="countdown" style="margin: -10px">' . $seconds . '</span> detik.',
            ]);
        }

        // Ambil kredensial
        $credentials = $request->only(['username', 'password', 'via_siakad']);

        // Cek status via_siakad
        return $credentials['via_siakad'] == 1
            ? $this->loginViaSiakad($request, $credentials, $throttleKey, $decaySeconds)
            : $this->loginLocal($request, $credentials, $throttleKey, $decaySeconds);
    }

    /**
     * Login via API SIAKAD
     */
    protected function loginViaSiakad(LoginRequest $request, array $credentials, string $throttleKey, int $decaySeconds): RedirectResponse
    {
        // Lakukan try catch ke endpoint API SIAKAD
        try {
            $response = Http::timeout(10)->post(
                rtrim(env('URL_API_SIAKAD'), '/') . '/api/v2/auth/login',
                [
                    'username' => $credentials['username'],
                    'password' => $credentials['password']
                ]
            );
        } catch (Exception $e) {
            RateLimiter::hit($throttleKey, $decaySeconds);
            return back()->withErrors(['koneksi' => 'Gagal menghubungi layanan Siakad.']);
        }

        $data = $response->json();

        // Verifikasi Turnstile
        if (!$this->handleTurnstileValidation($request)) {
            return back()->withErrors(['turnstile_notvalid' => 'Verifikasi keamanan gagal']);
        }

        // Jika login berhasil
        if ($response->successful() && isset($data['access_token'], $data['user'])) {
            RateLimiter::clear($throttleKey);

            $access_token = $data['access_token'];
            $userData     = $data['user'];

            // Validasi, status user harus aktif
            if (!in_array($userData['status'], ['active', 1, '1'], true)) {
                return back()->withErrors(['masuk' => 'Akun Siakad Anda tidak aktif.']);
            }

            // Cari atau buat user di sistem
            $user = User::updateOrCreate(
                ['siakad_id' => $userData['id']],
                [
                    'siakad_id'         => $userData['id'],
                    'username'          => $userData['username'],
                    'password'          => null, // Jangan simpan password dari Siakad
                    'email'             => $userData['email'] ?? null,
                    'name'              => $userData['name'] ?? '',
                    'nomor_hp'          => $userData['nomor_hp'] ?? '',
                    'nomor_hp2'         => $userData['nomor_hp2'] ?? null,
                    'email_verified_at' => $userData['email_verified_at'] ?? null,
                    'about'             => $userData['about'] ?? null,
                    'default_role'      => $userData['default_role'] ?? 'mitra',
                    'theme'             => $userData['theme'] ?? 'default',
                    'avatar'            => $userData['avatar'] ?? null,
                    'status'            => $userData['status'] ?? 'active',
                    'status_login'      => 'online',
                    'isdeleted'         => $userData['isdeleted'] ?? false,
                    'last_logged_in'    => Carbon::now(),
                    'last_synced_at'    => Carbon::now(),
                ]
            );

            // Untuk user lain, assign role berdasarkan default_role dari Siakad
            $mitra_role = $userData['default_role'] ?? 'mahasiswa';
            $mitra_role = is_array($mitra_role) ? $mitra_role : [$mitra_role];
            $user->syncRoles($mitra_role);

            // Simpan access_token ke session
            Session::put('api_access_token', $access_token);
            Session::put('api_userroles', $userData['roles'] ?? []);

            Auth::login($user);

            // Kirim notifikasi WhatsApp setelah login berhasil
            $this->sendLoginNotification($user, $request, $from = 'siakad');

            return redirect()->intended(route('dashboard.index'));
        }

        // Hit jika gagal login
        RateLimiter::hit($throttleKey, $decaySeconds);

        // Tampilkan error dari API
        $errorMessage = 'Tidak dapat melakukan otentikasi';
        if (!empty($data['message'])) {
            $errorMessage = is_array($data['message'])
                ? implode('. ', array_map(fn($msg) => implode(' ', (array) $msg), $data['message']))
                : $data['message'];
        }

        return back()->withErrors(['masuk' => $errorMessage . '.']);
    }

    /**
     * Login lokal ke database user
     */
    protected function loginLocal(LoginRequest $request, array $credentials, string $throttleKey, int $decaySeconds): RedirectResponse
    {
        // Verifikasi Turnstile untuk login lokal juga
        if (!$this->handleTurnstileValidation($request)) {
            return back()->withErrors(['turnstile_notvalid' => 'Verifikasi keamanan gagal']);
        }

        // Coba login dengan kredensial lokal
        if (
            Auth::attempt([
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ], $request->boolean('remember'))
        ) {
            RateLimiter::clear($throttleKey);

            // Validasi, jika user pending
            if (in_array(Auth::user()->status, ['pending'], true)) {
                Auth::logout();
                return back()->withErrors(['masuk' => 'Akun Anda belum diverifikasi TIM PMB.']);
            }
            // Validasi, jika tidak aktif
            if (!in_array(Auth::user()->status, ['active', 1, '1'], true)) {
                Auth::logout();
                return back()->withErrors(['masuk' => 'Akun Anda tidak aktif. Silakan hubungi administrator.']);
            }

            // Update last logged in
            $user = Auth::user();
            $user->update(['last_logged_in' => Carbon::now()]);

            // Kirim notifikasi WhatsApp setelah login berhasil
            $this->sendLoginNotification($user, $request, $from = 'local');

            return redirect()->intended(route('dashboard.index'));
        }

        // Hit jika gagal login
        RateLimiter::hit($throttleKey, $decaySeconds);

        return back()->withErrors([
            'masuk' => 'Username atau password salah.',
        ]);
    }

    /**
     * Menambahkan notifikasi whatsapp ke daftar antrian
     */
    protected function notifikasiWhatsapp($user, $pesan)
    {
        $nomor_wa = $user->nomor_hp2 ?? $user->nomor_hp;
        // Jika nomor_hp dan nomor_hp2 sama, gunakan salah satunya
        if ($user->nomor_hp == $user->nomor_hp2) {
            $nomor_wa = $user->nomor_hp2; // $user->nomor_hp2
        }
        $antrian = AntrianNotifWhatsappModel::create([
            'user_id'   => $user->user_id,
            'sesi'      => konfigs('wa.session', env('WA_GATEWAY_SESSION')),
            'target'    => $nomor_wa,
            'tipe'      => 'text',
            'isi_pesan' => $pesan,
            'status'    => 0,
        ]);

        // dispatch ke queue whatsapp
        NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');
        // log whatsapp
        Log::channel('whatsapp')->info("NotifWhatsapp (login {$user->name}) berhasil masuk antrean", [
            'user_id'     => $user->user_id,
            'to'          => $nomor_wa, // Log nomor yang digunakan
            'antrian_id'  => $antrian->antrian_id,
        ]);
    }

    /**
     * Proses logout
     */
    public function keluar(): RedirectResponse
    {
        // Update status login user
        if (Auth::check()) {
            Auth::user()->update(['status_login' => 'offline']);
        }

        // Hapus session
        Session::forget(['api_access_token', 'api_userroles']);
        Auth::logout();

        return redirect()->route('login')->with('keluar', 'Anda telah keluar sistem');
    }

/////// --- FUNGSI PEMBANTU BARU UNTUK MENGURANGI DUPLIKASI --->
/////// --- FUNGSI PEMBANTU BARU UNTUK MENGURANGI DUPLIKASI --->

    /**
     * Handle Turnstile validation logic.
     * Returns true if validation passes, false otherwise.
     */
    private function handleTurnstileValidation(LoginRequest $request)
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

    /**
     * Send login notification via WhatsApp with cache guard.
     */
    private function sendLoginNotification($user, $request, $from): void
    {
        $sessionLifetime = (int) env('SESSION_LIFETIME', 120); // menit
        $ip = $request->ip(); // Ambil IP user
        $cacheKey = 'notif_wa_login_' . $user->user_id . '_' . $ip; // Gunakan $user->user_id bukan $user->user_id

        // Jika cache ada, langsung SKIP pengiriman notifikasi
        if (Cache::has($cacheKey)) {
            return; // Keluar dari fungsi jika sudah pernah dikirim dalam periode ini
        }

        try {
            // Pesan WhatsApp
            $greeting = now()->hour < 11 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam'));
            $waktu = Carbon::now()->locale('id')->translatedFormat('l, d M Y H:i:s');
            $pesan = "🔸Selamat " . $greeting . ", {$user->name}.\n"
                . "Akun" . ($from == 'siakad' ? ' SIAKAD' : '') . " Anda telah digunakan untuk akses masuk *"
                . konfigs('NAMA_SISTEM') . "* pada:\n" . "{$waktu}\n" . "\n"
                . "Jika aktivitas ini mencurigakan, segera lakukan langkah pengamanan pada Akun Anda.";
            $this->notifikasiWhatsapp($user, $pesan);
        } catch (Throwable $e) {
            Log::channel('whatsapp')->warning('Gagal masuk antrean notif whatsapp (login)', [
                'err'     => $e->getMessage(),
                'user_id' => $user->user_id ?? null
            ]);
        }

        // Simpan flag cache per user per IP, agar tidak kirim lagi dalam masa SESSION_LIFETIME
        Cache::put($cacheKey, true, now()->addMinutes($sessionLifetime));
    }
}

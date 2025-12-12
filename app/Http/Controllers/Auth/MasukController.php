<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
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
use Symfony\Component\HttpFoundation\Request;
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
      'title' => konfigs('NAMA_SISTEM'),
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

    // --- LOGIKA RATE LIMITING DENGAN LOGGING ---
    if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
      event(new Lockout($request));
      $seconds = RateLimiter::availableIn($throttleKey);

      // Log kejadian rate limit
      $this->logAction(
        'warning',
        'Rate limiter aktif "' . $request->username . '"',
        [
          'username'      => $request->username ?? 'Tidak diketahui',
          'waiting_time'  => $seconds . ' detik',
        ]
      );

      throw ValidationException::withMessages([
        'masuk' => 'Silakan coba lagi dalam <span id="countdown" style="margin: -10px">' . $seconds . '</span> detik.',
      ]);
    }
    // --- END LOGIKA RATE LIMITING ---

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
      // Hit rate limiter jika gagal koneksi
      RateLimiter::hit($throttleKey, $decaySeconds);

      // Log error koneksi
      $this->logAction(
        'error',
        'Gagal menghubungi layanan Siakad untuk login via API Siakad',
        [
          'username' => $credentials['username'],
          'error' => $e->getMessage(),
        ]
      );

      return back()->withErrors(['koneksi' => 'Gagal menghubungi layanan Siakad.']);
    }

    $data = $response->json();

    // Verifikasi Turnstile
    if (!$this->handleTurnstileValidation($request)) {
      // Log kegagalan turnstile
      $this->logAction(
        'warning',
        'Verifikasi keamanan Turnstile gagal untuk login via API Siakad',
        [
          'username' => $credentials['username'],
        ]
      );
      return back()->withErrors(['turnstile_notvalid' => 'Verifikasi keamanan gagal']);
    }

    // Jika login berhasil
    if ($response->successful() && isset($data['access_token'], $data['user'])) {
      RateLimiter::clear($throttleKey);

      $access_token = $data['access_token'];
      $userData     = $data['user'];

      // Validasi, status user harus aktif
      if (!in_array($userData['status'], ['active', 1, '1'], true)) {
        // Log login gagal karena status tidak aktif
        $this->logAction(
          'warning',
          'Login via API gagal, akun tidak aktif',
          [
            'username' => $userData['username'] ?? $credentials['username'],
            'siakad_id' => $userData['id'],
          ]
        );
        return back()->withErrors(['masuk' => 'Akun Siakad Anda tidak aktif.']);
      }

      // Cari atau buat user di sistem
      try {
        $user = User::updateOrCreate(
          ['siakad_id' => $userData['id']],
          [
            'siakad_id'         => $userData['id'],
            'username'          => $userData['username'],
            'password'          => null, // Jangan simpan password dari Siakad
            'email'             => $userData['email'] ?? null,
            'name'              => $userData['name'] ?? '',
            'nomor_hp'          => $userData['nomor_hp'] ?? '',
            'nomor_hp2'         => $userData['nomor_hp2'] ?? '',
            'email_verified_at' => $userData['email_verified_at'] ?? null,
            'about'             => $userData['about'] ?? null,
            'default_role'      => $userData['default_role'] ?? 'mitra', // Ganti 'mitra' sesuai kebutuhan
            'theme'             => $userData['theme'] ?? 'default',
            'avatar'            => $userData['avatar'] ?? null,
            'status'            => $userData['status'] ?? 'active',
            'status_login'      => 'online',
            'isdeleted'         => $userData['isdeleted'] ?? false,
            'last_logged_in'    => Carbon::now(),
            'last_synced_at'    => Carbon::now(),
          ]
        );
      } catch (Exception $e) {
        // Hit rate limiter jika gagal karena konflik data unik
        RateLimiter::hit($throttleKey, $decaySeconds);

        // Log error detail ke file log
        $this->logAction(
          'error',
          'Login via Siakad gagal - Konflik Data Unik: ' . $e->getMessage(),
          [
            'siakad_user_id' => $userData['id'] ?? null,
            'siakad_username' => $userData['username'] ?? null,
            'siakad_nomor_hp' => $userData['nomor_hp'] ?? null,
            'siakad_email' => $userData['email'] ?? null,
            'request_ip' => $request->ip(),
            'request_user_agent' => $request->userAgent(),
          ]
        );

        // Tangani spesifik error Duplicate Entry untuk memberikan pesan yang jelas ke user
        $pesanError = "Terjadi kesalahan saat sinkronisasi data akun dari Siakad. ";
        $pesanLog = "Konflik data unik: " . $e->getMessage(); // Pesan untuk log

        if (str_contains($e->getMessage(), 'Duplicate entry')) {
          if (str_contains($e->getMessage(), 'users_username_unique')) {
            $pesanError .= "Username '" . $userData['username'] . "' dari Siakad sudah digunakan oleh akun lain.";
          } elseif (str_contains($e->getMessage(), 'users_email_unique')) {
            $pesanError .= "Email '" . ($userData['email'] ?? 'N/A') . "' dari Siakad sudah digunakan oleh akun lain.";
          } elseif (str_contains($e->getMessage(), 'users_nomor_hp_unique')) {
            $pesanError .= "Nomor HP '" . $userData['nomor_hp'] . "' dari Siakad sudah digunakan oleh akun lain.";
          } elseif (str_contains($e->getMessage(), 'users_nomor_hp2_unique')) {
            $pesanError .= "Nomor Whatsapp '" . ($userData['nomor_hp2'] ?? 'N/A') . "' dari Siakad sudah digunakan oleh akun lain.";
          } else {
            // Jika Duplicate entry tapi kolomnya tidak dikenali
            $pesanError .= "Data dari akun Siakad Anda menyebabkan konflik (mungkin dengan username, email, atau nomor HP).";
          }
        } else {
          // Jika error bukan Duplicate Entry, tampilkan pesan umum
          $pesanError .= "Silakan hubungi administrator.";
        }

        // Kembalikan ke halaman login dengan pesan error yang jelas
        return back()->withErrors(['masuk' => $pesanError]);
      }

      // Assign role berdasarkan default_role dari Siakad
      $mitra_role = $userData['default_role'] ?? 'mitra'; // Ganti 'mitra' sesuai kebutuhan default
      $mitra_role = is_array($mitra_role) ? $mitra_role : [$mitra_role];
      $user->syncRoles($mitra_role);

      // Simpan access_token ke session
      Session::put('api_access_token', $access_token);
      Session::put('api_userroles', $userData['roles'] ?? []);

      Auth::login($user);

      // Log login sukses via API
      $this->logAction(
        'info',
        'Login via API Siakad berhasil',
        [
          'user_id' => $user->user_id,
          'username' => $user->username,
          'name' => $user->name,
          'siakad_id' => $user->siakad_id,
        ]
      );

      // Kirim notifikasi WhatsApp setelah login berhasil
      $this->sendLoginNotification($user, $request, $from = 'siakad');

      return redirect()->intended(route('dashboard.index'));
    }

    // Hit jika gagal login (gagal auth API)
    RateLimiter::hit($throttleKey, $decaySeconds);

    // Log login gagal via API
    $this->logAction(
      'alert',
      'Login via API Siakad gagal',
      [
        'username' => $credentials['username'],
        'api_response' => $data, // Log respons API jika relevan
      ]
    );

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
      // Log kegagalan turnstile untuk login lokal
      $this->logAction(
        'warning',
        'Verifikasi keamanan Turnstile gagal untuk login lokal',
        [
          'username' => $credentials['username'],
        ]
      );
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

      $user = Auth::user();

      // Validasi ulang (antisipasi jika status berubah setelah attempt)
      if (!in_array($user->status, ['active', 1, '1'], true)) {
        Auth::logout();
        // Log logout karena status tidak aktif setelah login
        $this->logAction(
          'warning',
          'Login lokal sukses tetapi akun tidak aktif, logout otomatis',
          [
            'user_id' => $user->user_id,
            'username' => $user->username,
            'name' => $user->name,
            'status' => $user->status,
          ]
        );
        return back()->withErrors(['masuk' => 'Akun Anda tidak aktif. Silakan hubungi administrator.']);
      }

      // Update last logged in
      $user->update(['last_logged_in' => Carbon::now()]);

      // Log login sukses lokal
      $this->logAction(
        'info',
        'Login lokal berhasil',
        [
          'user_id' => $user->user_id,
          'username' => $user->username,
          'name' => $user->name,
        ]
      );

      // Kirim notifikasi WhatsApp setelah login berhasil
      $this->sendLoginNotification($user, $request, $from = 'local');

      return redirect()->intended(route('dashboard.index'));
    }

    // Hit jika gagal login (gagal auth lokal)
    RateLimiter::hit($throttleKey, $decaySeconds);

    // Log login gagal lokal
    $this->logAction(
      'alert',
      'Login lokal gagal (username/password salah)',
      [
        'username' => $credentials['username'],
      ]
    );

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
    Log::channel('whatsapp')->info("NotifWhatsapp (login {$user->name}) berhasil masuk antrian", [
      'user_id'     => $user->user_id,
      'to'          => $nomor_wa, // Log nomor yang digunakan
      'antrian_id'  => $antrian->antrian_id,
    ]);
  }

  /**
   * Proses logout
   */
  public function keluar(Request $request): RedirectResponse
  {
    // Update status login user
    if (Auth::check()) {
      Auth::user()->update(['status_login' => 'offline']);
    }

    $user = Auth::user();

    // Hapus session
    Session::forget(['api_access_token', 'api_userroles']);
    Auth::logout();

    // Log logout 
    $this->logAction(
      'info',
      'Pengguna berhasil logout',
      [
        'user_id' => $user->user_id,
        'username' => $user->username,
        'name' => $user->name,
      ]
    );
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
      $internalMap = [
        '127.0.0.1'     => 'Local testing host',
        '192.168.17.1'  => 'Kampus STIE Pembangunan',
      ];
      $location = 'Tidak diketahui';
      // Jika IP ada di internal map, gunakan itu
      if (isset($internalMap[$ip])) {
        $location = $internalMap[$ip];
      } else {
        // Cek apakah IP publik -> hanya jika publik maka sistem memanggil ip-api
        $isPublicIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($isPublicIp) {
          $geo = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,query");
          if ($geo->successful() && data_get($geo->json(), 'status') === 'success') {
            $location = data_get($geo->json(), 'city') . ', ' . data_get($geo->json(), 'regionName') . ', ' . data_get($geo->json(), 'country');
          }
        } else {
          $location = 'Tidak diketahui';
        }
      }
      // Pesan WhatsApp
      $greeting = now()->hour < 11 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam'));
      $waktu = Carbon::now()->locale('id')->translatedFormat('l, d M Y H:i:s');
      $pesan = "🔸Selamat " . $greeting . ", {$user->name}.\n"
        . "Akun" . ($from == 'siakad' ? ' SIAKAD' : '') . " Anda telah digunakan untuk akses masuk *" . konfigs('NAMA_SISTEM') . "* pada:\n"
        . "Waktu: {$waktu}\n"
        . "IP: {$ip}\n"
        . "Lokasi: {$location}";
      // Lakukan Pengiriman
      $this->notifikasiWhatsapp($user, $pesan);
    } catch (Throwable $e) {
      Log::channel('whatsapp')->warning('Gagal masuk antrian notif whatsapp (login)', [
        'err'     => $e->getMessage(),
        'user_id' => $user->user_id ?? null
      ]);
    }

    // Simpan flag cache per user per IP, agar tidak kirim lagi dalam masa SESSION_LIFETIME
    Cache::put($cacheKey, true, now()->addMinutes($sessionLifetime));
  }

  /**
   * Fungsi untuk mencatat log aksi pengguna ke channel 'masuk'.
   * Menambahkan informasi lokasi berdasarkan IP.
   */
  protected function logAction($level, $message, $context = [])
  {
    // Tambahkan detail user agent
    $agent = new Agent();
    $deviceType = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');
    $platform = $agent->platform() ?? 'Unknown';
    $browser  = $agent->browser() ?? 'Unknown';

    // Tambahkan detail ip
    $ip = request()->ip();
    $internalMap = [
      '127.0.0.1'     => 'Local testing host',
      '192.168.17.1'  => 'Kampus STIE Pembangunan',
    ];

    // Tambahkan detail lokasi
    $location = 'Tidak diketahui';
    if (isset($internalMap[$ip])) {
      $location = $internalMap[$ip];
    } else {
      $isPublicIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
      if ($isPublicIp) {
        $geo = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,query"); // Tambahkan timeout
        if ($geo->successful() && data_get($geo->json(), 'status') === 'success') {
          $location = data_get($geo->json(), 'city', 'N/A') . ', ' .
            data_get($geo->json(), 'regionName', 'N/A') . ', ' .
            data_get($geo->json(), 'country', 'N/A');
        }
      } else {
        $location = 'Jaringan Internal';
      }
    }

    // Apply konteks
    $context['user_agent'] = [
      'raw'       => request()->userAgent(),
      'device'    => $deviceType,
      'platform'  => $platform,
      'browser'   => $browser,
    ];
    $context['location'] = $location;
    $context['time'] = now()->toDateTimeString();

    // Catat log ke channel 'masuk'
    Log::channel('masuk')->$level($message, $context);
  }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class LogAksesPengguna
{
  public function handle($request, Closure $next)
  {
    // PERSIAPAN DATA LEBIH AWAL UNTUK MENGHINDARI ERROR FILE HILANG
    $agent = new Agent();
    $deviceType = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');
    $platform = $agent->platform();   // Windows, macOS, Android, iOS, Linux
    $browser  = $agent->browser();    // Chrome, Firefox, Safari, dll

    $method = $request->method();
    $body = null;

    if (auth()->check()) {
      // Filter isi body hanya untuk method tertentu
      // (dipindahkan ke sebelum $next untuk menghindari FileNotFoundException)
      if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        // Hati-hati: bisa filter field sensitif di sini
        $body = $request->except(['password', 'password_confirmation', '_token', '_method']);

        // Menghindari error file hilang: sanitasi semua UploadedFile
        foreach ($body as $key => $value) {
          if ($value instanceof UploadedFile) {
            $body[$key] = '[uploaded file omitted]';
          }
        }
      }

      // Mempersiapkan body ALL request untuk log (disanitasi)
      $allBody = $request->all();
      foreach ($allBody as $key => $value) {
        if ($value instanceof UploadedFile) {
          $allBody[$key] = '[uploaded file omitted]';
        }
      }

      $logData = [
        'username'      => auth()->user()->username,
        'full_name'     => auth()->user()->name,
        'ip_address'    => $request->ip(),
        'method'        => $request->method(),
        'url'           => $request->fullUrl(),
        'user_agent'    => [
          'raw'       => $request->userAgent(),
          'device'    => $deviceType,
          'platform'  => $platform,
          'browser'   => $browser,
        ],
        'time'          => now()->toDateTimeString(),
        'body'          => $allBody, // body aman yang sudah disanitasi
      ];

      // Jika user superadmin dan env diset false, maka abaikan log
      $isSuperadmin = auth()->user()->hasRole(['superadmin', 'developer']);
      $logForSuperadmin = filter_var(env('LOG_RECORDING_FOR_SUPERADMIN_DEV', false), FILTER_VALIDATE_BOOLEAN);

      if (!$isSuperadmin || $logForSuperadmin) {
        Log::channel('aksespengguna')->info(
          auth()->user()->name . ' melakukan ' . $method . ' pada resource: ' . $request->fullUrl(),
          $logData
        );
      }
    }

    $response = $next($request);
    return $response;
  }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;

class LogAksesPengguna
{
  public function handle($request, Closure $next)
  {
    $response = $next($request);
    $agent = new Agent();
    $deviceType = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');
    $platform = $agent->platform();   // Windows, macOS, Android, iOS, Linux
    $browser  = $agent->browser();    // Chrome, Firefox, Safari, dll

    if (auth()->check()) {
      // Filter isi body hanya untuk method tertentu
      $method = $request->method();
      $body = null;

      if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        // Hati-hati: bisa filter field sensitif di sini
        $body = $request->except(['password', 'password_confirmation', '_token', '_method']);
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
        'body'          => $request->all(),
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

    return $response;
  }
}

<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\Request;

class SpamController implements \Spatie\Honeypot\SpamResponder\SpamResponder
{
  public function respond(Request $request, Closure $next)
  {
    return response()->view('errors.spam');
  }
}

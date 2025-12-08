<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProfilUpdateRequest;
use App\Http\Requests\ProfilUpdatePasswordRequest;

class ProfilController extends Controller
{
  /**
   * Menampilkan profil pribadi
   */
  public function show(): View
  {
    $user = auth()->user();

    return view('sistem.profil.show', [
      'title' => 'Profil Saya',
      'user' => $user,
    ]);
  }

  /**
   * Menampilkan form edit profil
   */
  public function edit(): View
  {
    $user = auth()->user();

    return view('sistem.profil.edit', [
      'title' => 'Edit Profil',
      'user' => $user,
    ]);
  }

  /**
   * Update data profil
   */
  public function update(ProfilUpdateRequest $request): RedirectResponse
  {
    try {
      $user = auth()->user();

      $data = [
        'name' => $request->nama,
        'asal_sekolah' => $request->asal_sekolah,
        'email' => $request->email,
        'nomor_hp' => $request->nomor_hp,
        'nomor_hp2' => $request->nomor_hp2,
        'about' => $request->about,
      ];

      // Handle avatar upload
      if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
        $user->uploadAvatar($request->file('avatar'));
      }

      $user->update($data);

      return redirect()->route('profil.show')
        ->with('success', 'Profil berhasil diperbarui.');
    } catch (Exception $e) {
      return back()->withInput()
        ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
    }
  }

  /**
   * Update password
   */
  public function updatePassword(ProfilUpdatePasswordRequest $request): RedirectResponse
  {
    try {
      $user = auth()->user();

      $user->update([
        'password' => bcrypt($request->password_baru)
      ]);

      return redirect()->route('profil.show')
        ->with('success', 'Password berhasil diperbarui.');
    } catch (Exception $e) {
      return back()->withInput()
        ->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
    }
  }

  /**
   * Hapus avatar profil
   */
  public function deleteAvatar(): RedirectResponse
  {
    try {
      $user = auth()->user();
      $user->clearMediaCollection('avatar');

      return back()->with('success', 'Foto profil berhasil dihapus.');
    } catch (Exception $e) {
      return back()->with('error', 'Gagal menghapus foto profil: ' . $e->getMessage());
    }
  }
}

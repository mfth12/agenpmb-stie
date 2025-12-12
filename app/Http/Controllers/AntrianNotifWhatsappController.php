<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Jobs\NotifWhatsappJob;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\AntrianNotifWhatsappModel;
use Illuminate\Support\Facades\Validator;

class AntrianNotifWhatsappController extends Controller
{
  protected $waService;

  public function __construct(WhatsappService $waService)
  {
    $this->waService = $waService;
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): View
  {
    $query = AntrianNotifWhatsappModel::with(['user']); // Eager load user jika diperlukan

    // Filter berdasarkan pencarian
    if ($request->has('cari') && $request->cari != '') {
      $searchTerm = $request->cari;
      $query->where(function ($q) use ($searchTerm) {
        $q->where('target', 'like', "%{$searchTerm}%")
          ->orWhere('isi_pesan', 'like', "%{$searchTerm}%")
          ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
            $userQuery->where('name', 'like', "%{$searchTerm}%");
          });
      });
    }

    // Filter berdasarkan status
    if ($request->has('status') && $request->status != '') {
      $query->where('status', $request->status);
    }

    // Filter berdasarkan user_id (opsional, jika ingin melihat notif untuk user tertentu)
    if ($request->has('user_id') && $request->user_id != '') {
      $query->where('user_id', $request->user_id);
    }

    // Filter jumlah data per halaman
    $perPageOptions = [10, 25, 50, 100, 'all'];
    $perPage = $request->get('per_page', 10); // Default 10

    if (!in_array($perPage, $perPageOptions) || $perPage === 'all') {
      $perPage = $query->count(); // Jika 'all', tampilkan semua
    }

    $antrians = $query->orderBy('created_at', 'desc')->paginate($perPage);

    // Ambil daftar user untuk filter dropdown (opsional)
    $users = User::select('user_id', 'name')->get();

    return view('sistem.antrian-notif-whatsapp.index', [
      'title' => 'Notifikasi WhatsApp',
      'antrians' => $antrians,
      'users' => $users, // Kirim ke view untuk filter
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): View
  {
    $users = User::select('user_id', 'name', 'nomor_hp', 'nomor_hp2')->get();
    return view('sistem.antrian-notif-whatsapp.create', [
      'title' => 'Kirim Notifikasi WhatsApp',
      'users' => $users,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): RedirectResponse
  {
    $validator = Validator::make($request->all(), [
      'user_id' => 'nullable|exists:users,user_id',
      'target' => 'required_without:user_id|nullable|string|max:20', // Wajib jika user_id tidak dipilih
      'isi_pesan' => 'required|string|max:4096', // Batasi panjang pesan
      'tipe' => 'required|in:text,image,video,document', // Sesuaikan tipe yang didukung gateway
    ], [
      // Pesan kustom untuk user_id
      'user_id.exists' => 'User yang dipilih tidak ditemukan.',
      'user_id.nullable' => 'Input user_id tidak valid.',

      // Pesan kustom untuk target
      'target.required_without' => 'Target wajib diisi jika user tidak dipilih.',
      'target.string' => 'Target harus berupa teks.',
      'target.max' => 'Target maksimal :max karakter.',
      'target.nullable' => 'Input target tidak valid.',

      // Pesan kustom untuk isi_pesan
      'isi_pesan.required' => 'Isi pesan wajib diisi.',
      'isi_pesan.string' => 'Isi pesan harus berupa teks.',
      'isi_pesan.max' => 'Isi pesan maksimal :max karakter.',

      // Pesan kustom untuk tipe
      'tipe.required' => 'Jenis pesan wajib dipilih.',
      'tipe.in' => 'Jenis pesan yang dipilih tidak valid.',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    $validatedData = $validator->validated();

    // Jika user_id dipilih, ambil nomor dari user
    if (!empty($validatedData['user_id'])) {
      $user = User::findOrFail($validatedData['user_id']);
      // Prioritas: nomor_hp2, lalu nomor_hp
      $target = $user->nomor_hp2 ?? $user->nomor_hp;
      if (!$target) {
        return redirect()->back()
          ->withErrors(['user_id' => 'User yang dipilih tidak memiliki nomor HP yang valid.'])
          ->withInput();
      }
      $validatedData['target'] = $target;
    }
    // Jika user_id tidak dipilih, target diambil dari input form

    $validatedData['sesi'] = konfigs('WA_SESSION', 'sesiwhatsapp');
    $validatedData['status'] = AntrianNotifWhatsappModel::PENDING; // Default status adalah pending
    $validatedData['retry_count'] = 0; // Default retry count adalah 0

    try {
      $antrian = AntrianNotifWhatsappModel::create($validatedData);

      // Dispatch job ke queue
      NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

      Log::channel('whatsapp')->info('NotifWhatsapp (input baru) berhasil masuk antrian', [
        'antrian_id' => $antrian->antrian_id,
        'user_id' => $antrian->user_id,
        'target' => $antrian->target,
      ]);

      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Pesan WhatsApp berhasil ditambahkan ke antrian dan akan segera diproses.');
    } catch (Exception $e) {
      Log::channel('whatsapp')->error('Gagal menambahkan pesan whatsapp ke antrian: ' . $e->getMessage(), [
        'user_id' => $validatedData['user_id'] ?? null,
        'target' => $validatedData['target'] ?? null,
      ]);
      return redirect()->back()
        ->withErrors(['general' => 'Gagal menambahkan pesan ke antrian. Silakan coba lagi.'])
        ->withInput();
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(AntrianNotifWhatsappModel $antrian): View
  {
    return view('sistem.antrian-notif-whatsapp.show', [
      'title' => 'Detail Notifikasi WhatsApp',
      'antrian' => $antrian,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   * Hanya digunakan untuk mengedit isi pesan jika diperlukan.
   */
  public function edit(AntrianNotifWhatsappModel $antrian): View
  {
    return view('sistem.antrian-notif-whatsapp.edit', [
      'title' => 'Edit Notifikasi WhatsApp',
      'antrian' => $antrian,
    ]);
  }

  /**
   * Update the specified resource in storage.
   * Hanya mengizinkan update isi pesan jika statusnya masih pending.
   */
  public function update(Request $request, AntrianNotifWhatsappModel $antrian): RedirectResponse
  {
    // Hanya boleh diedit jika statusnya pending
    if ($antrian->status !== AntrianNotifWhatsappModel::PENDING) {
      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('error', 'Tidak dapat mengedit antrian yang statusnya bukan pending.');
    }

    $validator = Validator::make($request->all(), [
      'isi_pesan' => 'required|string|max:4096',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    try {
      $antrian->update(['isi_pesan' => $request->isi_pesan]);

      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Antrian notifikasi WhatsApp berhasil diperbarui.');
    } catch (Exception $e) {
      Log::channel('whatsapp')->error('Gagal memperbarui notif whatsapp: ' . $e->getMessage(), [
        'antrian_id' => $antrian->antrian_id,
      ]);
      return redirect()->back()
        ->withErrors(['general' => 'Gagal memperbarui antrian. Silakan coba lagi.']);
    }
  }

  /**
   * Remove the specified resource from storage.
   * Hanya mengizinkan hapus jika statusnya pending.
   */
  public function destroy(AntrianNotifWhatsappModel $antrian): RedirectResponse
  {
    // Hanya boleh dihapus jika statusnya pending
    if ($antrian->status !== AntrianNotifWhatsappModel::PENDING) {
      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('error', 'Tidak dapat menghapus antrian yang statusnya bukan pending.');
    }

    try {
      $antrian->delete();

      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Antrian notifikasi WhatsApp berhasil dihapus.');
    } catch (Exception $e) {
      Log::channel('whatsapp')->error('Gagal menghapus notif whatsapp: ' . $e->getMessage(), [
        'antrian_id' => $antrian->antrian_id,
      ]);
      return redirect()->back()
        ->withErrors(['general' => 'Gagal menghapus antrian. Silakan coba lagi.']);
    }
  }

  /**
   * Remove all notifications from the queue.
   */
  public function destroyAll(Request $request): RedirectResponse
  {
    // Authorization check - gunakan permission check
    if (!auth()->user()->can('antrian_whatsapp_delete')) {
      abort(403, 'Unauthorized action.');
    }

    try {
      // Hitung jumlah data sebelum melakukan truncate (truncate tidak mengembalikan jumlah)
      $countBefore = AntrianNotifWhatsappModel::count();

      // Jika tabel memiliki foreign key constraints, truncate bisa gagal.
      // Nonaktifkan sementara foreign key checks untuk MySQL/MariaDB supaya truncate bisa berjalan.
      // Jika Anda yakin tidak ada FK, Anda boleh menghilangkan dua statement berikut.
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');

      // Hapus semua data dari tabel antrian_notif_whatsapps
      // truncate() lebih cepat untuk menghapus semua data
      AntrianNotifWhatsappModel::truncate();

      // Set ulang auto increment mulai dari 3210
      DB::statement('ALTER TABLE antrian_whatsapps AUTO_INCREMENT = 61231;');

      // Kembalikan foreign key checks agar database kembali normal
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');

      // Log jumlah data yang dihapus
      Log::channel('whatsapp')->info("Semua antrian notifikasi WhatsApp berhasil dihapus.", [
        'jumlah_dihapus' => $countBefore,
        'user_id'        => auth()->id(),
        'username'       => auth()->user()->username ?? null,
      ]);

      // Kembalikan RedirectResponse
      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Semua notifikasi WhatsApp berhasil dihapus.');
    } catch (Throwable $e) {
      // Pastikan foreign key checks diaktifkan kembali jika terjadi error
      try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      } catch (Throwable $inner) {
        // jika gagal mengembalikan FK checks, catat juga (tidak melempar lagi)
        Log::channel('whatsapp')->warning('Gagal mengembalikan FOREIGN_KEY_CHECKS setelah error: ' . $inner->getMessage(), [
          'user_id' => auth()->id(),
          'username' => auth()->user()->username ?? null,
        ]);
      }

      // Log error lengkap termasuk pesan exception supaya mudah diagnosa
      Log::channel('whatsapp')->error('Gagal menghapus semua data notifikasi WhatsApp: ' . $e->getMessage(), [
        'user_id'  => auth()->id(),
        'username' => auth()->user()->username ?? null,
        'error'    => $e->getMessage(),
        'trace'    => $e->getTraceAsString(),
      ]);

      // Kembalikan RedirectResponse dengan error
      return redirect()->back()
        ->withErrors(['general' => 'Gagal menghapus semua data. Silakan coba lagi.']);
    }
  }


  /**
   * Retry sending the message for a specific queue item.
   * Only retry if status is 'failed' or 'dead'.
   */
  public function retry(AntrianNotifWhatsappModel $antrian): RedirectResponse
  {
    // Hanya retry jika statusnya 'failed' (2) atau 'dead' (3)
    if (!in_array($antrian->status, [AntrianNotifWhatsappModel::GAGAL, AntrianNotifWhatsappModel::DEAD])) {
      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('error', 'Hanya antrian dengan status Gagal atau Dead yang bisa diulang.');
    }

    try {
      // Reset status ke pending
      $antrian->update([
        'status' => AntrianNotifWhatsappModel::PENDING,
        // 'retry_count' => 0,
      ]);

      // Dispatch ulang job ke queue
      NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

      Log::channel('whatsapp')->info('Retry notif whatsapp dipicu', [
        'antrian_id' => $antrian->antrian_id,
        'user_id' => $antrian->user_id,
        'target' => $antrian->target,
      ]);

      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Proses pengiriman untuk antrian ini sedang diulang.');
    } catch (Exception $e) {
      Log::channel('whatsapp')->error('Gagal memicu retry notif whatsapp: ' . $e->getMessage(), [
        'antrian_id' => $antrian->antrian_id,
      ]);
      return redirect()->back()
        ->withErrors(['general' => 'Gagal memicu retry. Silakan coba lagi.']);
    }
  }

  /**
   * Force retry sending the message for a specific queue item, regardless of status.
   * Useful for debugging or re-sending successful messages.
   */
  public function forceRetry(AntrianNotifWhatsappModel $antrian): RedirectResponse
  {
    try {
      // Reset status ke pending
      $antrian->update([
        'status' => AntrianNotifWhatsappModel::PENDING,
        // 'retry_count' => 0,
      ]);

      // Dispatch ulang job ke queue
      NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

      Log::channel('whatsapp')->info('Force retry notif whatsapp dipicu', [
        'antrian_id' => $antrian->antrian_id,
        'user_id' => $antrian->user_id,
        'target' => $antrian->target,
      ]);

      return redirect()->route('antrian-notif-whatsapp.index')
        ->with('success', 'Proses pengiriman untuk antrian ini sedang diulang (force).');
    } catch (Exception $e) {
      Log::channel('whatsapp')->error('Gagal memicu force retry notif whatsapp: ' . $e->getMessage(), [
        'antrian_id' => $antrian->antrian_id,
      ]);
      return redirect()->back()
        ->withErrors(['general' => 'Gagal memicu force retry. Silakan coba lagi.']);
    }
  }
}

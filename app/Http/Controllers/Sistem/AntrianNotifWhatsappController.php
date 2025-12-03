<?php

namespace App\Http\Controllers\Sistem;

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

        $antrians = $query->orderBy('created_at', 'desc')->paginate(15);

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
            'title' => 'Kirim Notifikasi WhatsApp Baru',
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
            'target' => 'required_without:user_id|string|max:20', // Wajib jika user_id tidak dipilih
            'isi_pesan' => 'required|string|max:4096', // Batasi panjang pesan
            'tipe' => 'required|in:text,image,video,document', // Sesuaikan tipe yang didukung gateway
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

        $validatedData['sesi'] = konfigs('wa.session', env('WA_GATEWAY_SESSION'));
        $validatedData['status'] = AntrianNotifWhatsappModel::PENDING; // Default status adalah pending
        $validatedData['retry_count'] = 0; // Default retry count adalah 0

        try {
            $antrian = AntrianNotifWhatsappModel::create($validatedData);

            // Dispatch job ke queue
            NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

            Log::channel('whatsapp')->info('Notif whatsapp baru berhasil masuk antrean', [
                'antrian_id' => $antrian->antrian_id,
                'user_id' => $antrian->user_id,
                'target' => $antrian->target,
            ]);

            return redirect()->route('antrian-notif-whatsapp.index')
                ->with('success', 'Pesan WhatsApp berhasil ditambahkan ke antrean dan akan segera diproses.');
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Gagal menambahkan notif whatsapp ke antrean: ' . $e->getMessage(), [
                'user_id' => $validatedData['user_id'] ?? null,
                'target' => $validatedData['target'] ?? null,
            ]);
            return redirect()->back()
                ->withErrors(['general' => 'Gagal menambahkan pesan ke antrean. Silakan coba lagi.'])
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
                ->with('error', 'Tidak dapat mengedit antrean yang statusnya bukan pending.');
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
                ->with('success', 'Antrean notifikasi WhatsApp berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Gagal memperbarui notif whatsapp: ' . $e->getMessage(), [
                'antrian_id' => $antrian->antrian_id,
            ]);
            return redirect()->back()
                ->withErrors(['general' => 'Gagal memperbarui antrean. Silakan coba lagi.']);
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
                ->with('error', 'Tidak dapat menghapus antrean yang statusnya bukan pending.');
        }

        try {
            $antrian->delete();

            return redirect()->route('antrian-notif-whatsapp.index')
                ->with('success', 'Antrean notifikasi WhatsApp berhasil dihapus.');
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Gagal menghapus notif whatsapp: ' . $e->getMessage(), [
                'antrian_id' => $antrian->antrian_id,
            ]);
            return redirect()->back()
                ->withErrors(['general' => 'Gagal menghapus antrean. Silakan coba lagi.']);
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
                ->with('error', 'Hanya antrean dengan status Gagal atau Dead yang bisa diulang.');
        }

        try {
            // Reset status ke pending dan reset retry count
            $antrian->update([
                'status' => AntrianNotifWhatsappModel::PENDING,
                'retry_count' => 0,
            ]);

            // Dispatch ulang job ke queue
            NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

            Log::channel('whatsapp')->info('Retry notif whatsapp dipicu', [
                'antrian_id' => $antrian->antrian_id,
                'user_id' => $antrian->user_id,
                'target' => $antrian->target,
            ]);

            return redirect()->route('antrian-notif-whatsapp.index')
                ->with('success', 'Proses pengiriman untuk antrean ini sedang diulang.');
        } catch (\Exception $e) {
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
            // Reset status ke pending dan reset retry count
            $antrian->update([
                'status' => AntrianNotifWhatsappModel::PENDING,
                'retry_count' => 0,
            ]);

            // Dispatch ulang job ke queue
            NotifWhatsappJob::dispatch($antrian)->onQueue('whatsapp');

            Log::channel('whatsapp')->info('Force retry notif whatsapp dipicu', [
                'antrian_id' => $antrian->antrian_id,
                'user_id' => $antrian->user_id,
                'target' => $antrian->target,
            ]);

            return redirect()->route('antrian-notif-whatsapp.index')
                ->with('success', 'Proses pengiriman untuk antrean ini sedang diulang (force).');
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Gagal memicu force retry notif whatsapp: ' . $e->getMessage(), [
                'antrian_id' => $antrian->antrian_id,
            ]);
            return redirect()->back()
                ->withErrors(['general' => 'Gagal memicu force retry. Silakan coba lagi.']);
        }
    }

    // Jika Anda ingin menambahkan relasi ke model User di AntrianNotifWhatsappModel
    // Anda bisa menambahkan method ini ke model AntrianNotifWhatsappModel
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'user_id');
    // }
}

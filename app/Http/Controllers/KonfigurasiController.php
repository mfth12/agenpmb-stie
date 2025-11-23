<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Sistem\KonfigurasiModel;
use App\Http\Requests\KonfigurasiStoreRequest; // Buat request ini
use App\Http\Requests\KonfigurasiUpdateRequest; // Buat request ini

class KonfigurasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = KonfigurasiModel::query();

        // Filter berdasarkan config_group
        if ($request->has('group') && $request->group != '') {
            $query->where('config_group', $request->group);
        }

        // Filter berdasarkan pencarian
        if ($request->has('cari') && $request->cari != '') {
            $search = $request->cari;
            $query->where(function ($q) use ($search) {
                $q->where('config_key', 'like', "%{$search}%")
                    ->orWhere('config_group', 'like', "%{$search}%");
            });
        }

        $konfigurasis = $query->orderBy('config_group')->orderBy('config_key')->paginate(20);

        // Ambil daftar group unik untuk filter
        $groups = KonfigurasiModel::distinct('config_group')->pluck('config_group');

        return view('sistem.konfigurasi.index', [
            'title' => 'Konfigurasi Sistem',
            'konfigurasis' => $konfigurasis,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('sistem.konfigurasi.create', [
            'title' => 'Tambah Konfigurasi Baru',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KonfigurasiStoreRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            KonfigurasiModel::create($data);

            // Refresh cache setelah update
            KonfigurasiModel::refreshCache();

            return redirect()->route('konfigurasi.index')
                ->with('success', 'Konfigurasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan konfigurasi: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KonfigurasiModel $konfigurasi): View
    {
        return view('sistem.konfigurasi.edit', [
            'title' => 'Edit Konfigurasi - ' . $konfigurasi->config_key,
            'konfigurasi' => $konfigurasi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KonfigurasiUpdateRequest $request, KonfigurasiModel $konfigurasi): RedirectResponse
    {
        try {
            $data = $request->validated();
            $konfigurasi->update($data);

            // Refresh cache setelah update
            KonfigurasiModel::refreshCache();

            return redirect()->route('konfigurasi.index')
                ->with('success', 'Konfigurasi ' . $konfigurasi->config_key . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui konfigurasi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KonfigurasiModel $konfigurasi): RedirectResponse
    {
        try {
            $key = $konfigurasi->config_key;
            $konfigurasi->delete();

            // Refresh cache setelah update
            KonfigurasiModel::refreshCache();

            return redirect()->route('konfigurasi.index')
                ->with('success', 'Konfigurasi "' . $key . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus konfigurasi: ' . $e->getMessage());
        }
    }
}

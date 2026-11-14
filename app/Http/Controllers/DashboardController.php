<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalMataKuliah = MataKuliah::count();
        $totalKrs = Krs::count();
        $krsAktif = Krs::whereIn('status', ['diajukan', 'disetujui'])->count();

        $rataSks = $totalKrs > 0
            ? (int) round(Krs::with('mataKuliah')->get()
                ->avg(fn (Krs $krs) => $krs->mataKuliah->sum('sks')))
            : 0;

        $statistik = [
            ['judul' => 'Mahasiswa', 'jumlah' => $totalMahasiswa,
                'ikon' => 'bi-mortarboard-fill', 'warna' => 'primary'],
            ['judul' => 'Dosen', 'jumlah' => $totalDosen,
                'ikon' => 'bi-person-badge-fill', 'warna' => 'success'],
            ['judul' => 'Mata Kuliah', 'jumlah' => $totalMataKuliah,
                'ikon' => 'bi-book-fill', 'warna' => 'warning'],
            ['judul' => 'KRS Aktif', 'jumlah' => $krsAktif,
                'ikon' => 'bi-journal-check', 'warna' => 'info'],
            ['judul' => 'Rata-rata SKS per KRS', 'jumlah' => $rataSks,
                'ikon' => 'bi-bar-chart-line-fill', 'warna' => 'danger'],
        ];

        $kolomKrs = [
            'nim' => 'NIM', 'nama' => 'Nama Mahasiswa',
            'sks' => 'SKS', 'status' => 'Status',
        ];

        $krsTerbaru = Krs::with('mahasiswa', 'mataKuliah')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Krs $krs) => [
                'nim' => $krs->mahasiswa?->nim ?? '-',
                'nama' => $krs->mahasiswa?->nama ?? '-',
                'sks' => $krs->mataKuliah->sum('sks'),
                'status' => ucfirst($krs->status),
            ])
            ->all();

        $jumlahStatus = Krs::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $konfigurasiStatus = [
            'draft' => ['label' => 'Draft', 'warna' => 'warning'],
            'diajukan' => ['label' => 'Diajukan', 'warna' => 'info'],
            'disetujui' => ['label' => 'Disetujui', 'warna' => 'success'],
            'ditolak' => ['label' => 'Ditolak', 'warna' => 'danger'],
        ];

        $statusKrs = [];
        foreach ($konfigurasiStatus as $status => $info) {
            $jumlah = (int) ($jumlahStatus[$status] ?? 0);
            $statusKrs[] = [
                'label' => $info['label'],
                'persen' => $totalKrs > 0 ? (int) round($jumlah / $totalKrs * 100) : 0,
                'warna' => $info['warna'],
            ];
        }

        return view('dashboard', compact(
            'statistik', 'kolomKrs', 'krsTerbaru', 'statusKrs'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    private const BATAS_SKS = 24;

    public function index(): View
    {
        return view('krs.index', [
            'daftarTahun' => Krs::query()->distinct()
                ->orderBy('tahun_akademik')->pluck('tahun_akademik'),
            'daftarSemester' => ['Ganjil', 'Genap'],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Krs::query()
            ->leftJoin('mahasiswa', 'mahasiswa.id', '=', 'krs.mahasiswa_id')
            ->select([
                'krs.*',
                'mahasiswa.nim as nim_mahasiswa',
                'mahasiswa.nama as nama_mahasiswa',
            ])
            ->with('mataKuliah')
            ->when($request->filled('tahun_akademik'), function ($q) use ($request) {
                $q->where('krs.tahun_akademik', $request->input('tahun_akademik'));
            })
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->where('krs.semester', $request->input('semester'));
            });

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('total_sks', fn (Krs $krs) => $krs->mataKuliah->sum('sks'))
            ->addColumn('aksi', function (Krs $krs) {
                return view('krs._aksi', compact('krs'))->render();
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function create(): View
    {
        return view('krs.create', [
            'krs' => new Krs(),
            'daftarMahasiswa' => Mahasiswa::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $krs = Krs::create($this->validated($request) + ['status' => 'draft']);

        return redirect()
            ->route('krs.show', $krs)
            ->with('success', 'KRS berhasil dibuat. Silakan tambahkan mata kuliah.');
    }

    public function show(Krs $krs): View
    {
        $krs->load('mahasiswa', 'krsDetail.mataKuliah');

        $terpakai = $krs->krsDetail->sum(
            fn (KrsDetail $detail) => $detail->mataKuliah?->sks ?? 0
        );

        return view('krs.show', [
            'krs' => $krs,
            'terpakai' => $terpakai,
            'batasSks' => self::BATAS_SKS,
            'daftarMataKuliah' => MataKuliah::orderBy('kode_mk')->get(),
        ]);
    }

    public function edit(Krs $krs): View
    {
        return view('krs.edit', [
            'krs' => $krs,
            'daftarMahasiswa' => Mahasiswa::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Krs $krs): RedirectResponse
    {
        $krs->update($this->validated($request, $krs));

        return redirect()
            ->route('krs.index')
            ->with('success', 'Data KRS berhasil diperbarui.');
    }

    public function destroy(Krs $krs): JsonResponse
    {
        $krs->delete();

        return response()->json([
            'message' => 'Data KRS berhasil dihapus.',
        ]);
    }

    public function storeDetail(Request $request, Krs $krs): RedirectResponse
    {
        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
        ], [
            'mata_kuliah_id.required' => 'Mata kuliah wajib dipilih.',
            'mata_kuliah_id.exists' => 'Mata kuliah tidak ditemukan.',
        ]);

        if (in_array($krs->status, ['disetujui', 'ditolak'], true)) {
            throw ValidationException::withMessages([
                'mata_kuliah_id' => 'KRS yang sudah diproses tidak dapat diubah.',
            ]);
        }

        $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);

        if ($krs->krsDetail()->where('mata_kuliah_id', $mataKuliah->id)->exists()) {
            throw ValidationException::withMessages([
                'mata_kuliah_id' => 'Mata kuliah ini sudah ada di KRS.',
            ]);
        }

        $terpakai = $krs->krsDetail()->with('mataKuliah')->get()
            ->sum(fn (KrsDetail $detail) => $detail->mataKuliah?->sks ?? 0);

        if ($terpakai + $mataKuliah->sks > self::BATAS_SKS) {
            throw ValidationException::withMessages([
                'mata_kuliah_id' => 'Batas ' . self::BATAS_SKS . ' SKS terlampaui. '
                    . 'Sisa ' . max(0, self::BATAS_SKS - $terpakai) . ' SKS.',
            ]);
        }

        try {
            $krs->krsDetail()->create(['mata_kuliah_id' => $mataKuliah->id]);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'mata_kuliah_id' => 'Mata kuliah sudah terdaftar di KRS ini.',
            ]);
        }

        return redirect()->route('krs.show', $krs)
            ->with('success', 'Mata kuliah ' . $mataKuliah->nama_mk . ' ditambahkan ke KRS.');
    }

    public function destroyDetail(Krs $krs, KrsDetail $detail): RedirectResponse
    {
        abort_unless((int) $detail->krs_id === (int) $krs->id, 404);

        if (in_array($krs->status, ['disetujui', 'ditolak'], true)) {
            return back()->with('error', 'KRS yang sudah diproses tidak dapat diubah.');
        }

        $detail->delete();

        return back()->with('success', 'Mata kuliah dihapus dari KRS.');
    }

    public function updateStatus(Request $request, Krs $krs): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'diajukan', 'disetujui', 'ditolak'])],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $krs->update(['status' => $validated['status']]);

        return back()->with('success', 'Status KRS diubah menjadi ' . $validated['status'] . '.');
    }

    private function validated(Request $request, ?Krs $krs = null): array
    {
        $rules = [
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'tahun_akademik' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('krs', 'tahun_akademik')
                    ->where(fn ($q) => $q->where('mahasiswa_id', $request->input('mahasiswa_id'))
                        ->where('semester', $request->input('semester')))
                    ->ignore($krs?->id),
            ],
            'semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ];

        if ($krs !== null) {
            $rules['status'] = ['required', Rule::in(['draft', 'diajukan', 'disetujui', 'ditolak'])];
        }

        return $request->validate($rules, [
            'mahasiswa_id.required' => 'Mahasiswa wajib dipilih.',
            'mahasiswa_id.exists' => 'Mahasiswa tidak ditemukan.',
            'tahun_akademik.required' => 'Tahun akademik wajib diisi.',
            'tahun_akademik.regex' => 'Format tahun akademik harus YYYY/YYYY, contoh 2026/2027.',
            'tahun_akademik.unique' => 'Mahasiswa sudah punya KRS untuk tahun dan semester itu.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);
    }
}

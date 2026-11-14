<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MahasiswaController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.index');
    }

    public function data(): JsonResponse
    {
        $query = Mahasiswa::query()->select([
            'id', 'nim', 'nama', 'jenis_kelamin', 'email', 'angkatan', 'program_studi',
        ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('aksi', function (Mahasiswa $mahasiswa) {
                return view('mahasiswa._aksi', compact('mahasiswa'))->render();
            })
            ->rawColumns(['aksi'])
            ->toJson();
    }

    public function create(): View
    {
        return view('mahasiswa.create', ['mahasiswa' => new Mahasiswa()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = Mahasiswa::create($this->validated($request));

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa ' . $mahasiswa->nama . ' berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load('krs.mataKuliah');

        return view('mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->update($this->validated($request, $mahasiswa));

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Data mahasiswa ' . $mahasiswa->nama . ' berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): JsonResponse
    {
        if ($mahasiswa->krs()->exists()) {
            return response()->json([
                'message' => 'Mahasiswa tidak dapat dihapus: masih memiliki data KRS.',
            ], 409);
        }

        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return response()->json([
            'message' => 'Data mahasiswa ' . $nama . ' berhasil dihapus.',
        ]);
    }

    private function validated(Request $request, ?Mahasiswa $mahasiswa = null): array
    {
        return $request->validate([
            'nim' => ['required', 'string', 'max:15',
                Rule::unique(Mahasiswa::class, 'nim')->ignore($mahasiswa?->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'email' => ['required', 'email', 'max:100',
                Rule::unique(Mahasiswa::class, 'email')->ignore($mahasiswa?->id),
            ],
            'angkatan' => ['required', 'integer', 'digits:4'],
            'program_studi' => ['required', 'string', 'max:100'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.max' => 'NIM maksimal 15 karakter.',
            'nim.unique' => 'NIM sudah dipakai mahasiswa lain.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah dipakai mahasiswa lain.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'angkatan.integer' => 'Angkatan harus berupa angka.',
            'angkatan.digits' => 'Angkatan harus 4 digit.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'program_studi.max' => 'Program studi maksimal 100 karakter.',
        ]);
    }
}

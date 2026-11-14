<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="mahasiswa_id" class="form-label">Mahasiswa</label>
        <select id="mahasiswa_id" name="mahasiswa_id"
            @class(['form-select', 'is-invalid' => $errors->has('mahasiswa_id')])>
            <option value="">-- Pilih Mahasiswa --</option>
            @foreach ($daftarMahasiswa as $mhs)
                <option value="{{ $mhs->id }}"
                    @selected((int) old('mahasiswa_id', $krs->mahasiswa_id) === (int) $mhs->id)>
                    {{ $mhs->nim }} - {{ $mhs->nama }}
                </option>
            @endforeach
        </select>
        @error('mahasiswa_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <x-form-input label="Tahun Akademik" name="tahun_akademik"
            :value="$krs->tahun_akademik" placeholder="2026/2027"
            maxlength="9" required />
    </div>

    <div class="col-md-3">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester"
            @class(['form-select', 'is-invalid' => $errors->has('semester')])>
            <option value="">-- Pilih --</option>
            <option value="Ganjil" @selected(old('semester', $krs->semester) === 'Ganjil')>
                Ganjil
            </option>
            <option value="Genap" @selected(old('semester', $krs->semester) === 'Genap')>
                Genap
            </option>
        </select>
        @error('semester')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($krs->exists)
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status"
                @class(['form-select', 'is-invalid' => $errors->has('status')])>
                @foreach (['draft' => 'Draft', 'diajukan' => 'Diajukan',
                    'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $nilai => $label)
                    <option value="{{ $nilai }}"
                        @selected(old('status', $krs->status) === $nilai)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif
</div>

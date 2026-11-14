<div class="row g-3 mb-3">
    <div class="col-md-4">
        <x-form-input label="NIM" name="nim" :value="$mahasiswa->nim"
            maxlength="15" required autofocus />
    </div>
    <div class="col-md-8">
        <x-form-input label="Nama Lengkap" name="nama" :value="$mahasiswa->nama"
            maxlength="100" required />
    </div>

    <div class="col-md-4">
        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
        <select id="jenis_kelamin" name="jenis_kelamin"
            @class(['form-select', 'is-invalid' => $errors->has('jenis_kelamin')])>
            <option value="">-- Pilih --</option>
            <option value="L" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'L')>
                Laki-laki
            </option>
            <option value="P" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'P')>
                Perempuan
            </option>
        </select>
        @error('jenis_kelamin')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <x-form-input label="Email" name="email" type="email"
            :value="$mahasiswa->email" maxlength="100" required />
    </div>
    <div class="col-md-4">
        <x-form-input label="Angkatan" name="angkatan" type="number"
            :value="$mahasiswa->angkatan" min="2000" max="2100" required />
    </div>

    <div class="col-12">
        <x-form-input label="Program Studi" name="program_studi"
            :value="$mahasiswa->program_studi ?: 'D3 Teknik Informatika'"
            maxlength="100" required />
    </div>
</div>

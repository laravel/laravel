@extends('layouts.app')

@section('title', 'Atur Kebutuhan Pegawai')
@section('page-title', 'Atur Kebutuhan Pegawai per Jabatan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-gear me-2"></i>Atur Kebutuhan Pegawai</span>
        <a href="{{ route('peta-jabatan.index', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <!-- Filter Unit Kerja -->
        <form method="GET" action="{{ route('peta-jabatan.set-kebutuhan') }}" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Pilih Unit Kerja</label>
                    <select name="unit_kerja_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach($unitKerjas as $uk)
                        <option value="{{ \App\Helpers\IdEncoder::encode($uk->id) }}" {{ $selectedUnitKerja == $uk->id ? 'selected' : '' }}>
                            {{ $uk->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if($selectedUnitKerja)
        <form method="POST" action="{{ route('peta-jabatan.store-kebutuhan') }}">
            @csrf
            <input type="hidden" name="unit_kerja_id" value="{{ $selectedUnitKerja }}">
            
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Kode</th>
                            <th>Nama Jabatan</th>
                            <th class="text-center" width="150">Jumlah Kebutuhan</th>
                            <th width="250">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jabatans as $i => $jabatan)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td><strong>{{ $jabatan->kode }}</strong></td>
                            <td>{{ $jabatan->nama }}</td>
                            <td>
                                <input type="number" 
                                       name="kebutuhan[{{ $jabatan->id }}][jumlah]" 
                                       class="form-control form-control-sm text-center" 
                                       value="{{ $kebutuhanData[$jabatan->id]['jumlah'] ?? 0 }}"
                                       min="0"
                                       placeholder="0">
                            </td>
                            <td>
                                <input type="text" 
                                       name="kebutuhan[{{ $jabatan->id }}][keterangan]" 
                                       class="form-control form-control-sm"
                                       value="{{ $kebutuhanData[$jabatan->id]['keterangan'] ?? '' }}"
                                       placeholder="Keterangan (opsional)">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('peta-jabatan.index', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-x me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
        @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-gear fs-1 d-block mb-3"></i>
            <h5>Pilih Unit Kerja</h5>
            <p>Silakan pilih unit kerja untuk mengatur kebutuhan pegawai.</p>
        </div>
        @endif
    </div>
</div>

<!-- Info -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
        <ul class="mb-0">
            <li>Masukkan jumlah kebutuhan pegawai untuk setiap jabatan di unit kerja yang dipilih.</li>
            <li>Jabatan dengan jumlah kebutuhan 0 tidak akan ditampilkan di peta jabatan (kecuali sudah ada pegawai yang mengisi jabatan tersebut).</li>
            <li>Keterangan bersifat opsional dan dapat digunakan untuk memberikan informasi tambahan.</li>
        </ul>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Pegawai')
@section('page-title', 'Edit Pegawai')

@section('content')
<form action="{{ route('pegawai.update', $pegawai) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person me-2"></i>Data Pribadi</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $pegawai->nip) }}" required>
                                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pegawai->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $pegawai->nik) }}" maxlength="16" placeholder="16 digit NIK">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NPWP</label>
                                <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp', $pegawai->npwp) }}" maxlength="20" placeholder="Nomor NPWP">
                                @error('npwp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Rekening</label>
                                <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening', $pegawai->no_rekening) }}" maxlength="50" placeholder="Nomor Rekening Bank">
                                @error('no_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Agama <span class="text-danger">*</span></label>
                                <select name="agama" class="form-select" required>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $pegawai->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status Perkawinan <span class="text-danger">*</span></label>
                                <select name="status_perkawinan" class="form-select" required>
                                    @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status)
                                    <option value="{{ $status }}" {{ old('status_perkawinan', $pegawai->status_perkawinan) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pendidikan Terakhir</label>
                                @php
                                    $selectedPendidikan = old('pendidikan_terakhir', $pegawai->pendidikan_terakhir);
                                    $pendidikanOptions = [
                                        'Strata III' => ['Strata III', 'S3'],
                                        'Strata II' => ['Strata II', 'S2'],
                                        'Strata I' => ['Strata I', 'S1'],
                                        'Diploma IV' => ['Diploma IV', 'D4'],
                                        'Diploma III' => ['Diploma III', 'D3'],
                                        'Sekolah Lanjutan Tingkat Atas' => ['Sekolah Lanjutan Tingkat Atas', 'SLTA', 'SMA', 'SMK', 'MA'],
                                        'Sekolah Lanjutan Tingkat Pertama' => ['Sekolah Lanjutan Tingkat Pertama', 'SLTP', 'SMP', 'MTS'],
                                        'Sekolah Tingkat Dasar' => ['Sekolah Tingkat Dasar', 'SD', 'MI'],
                                    ];

                                    $normalize = function ($value) {
                                        return preg_replace('/[^a-z0-9]/', '', strtolower((string) $value));
                                    };

                                    $selectedPendidikanNorm = $normalize($selectedPendidikan);
                                    $hasMappedPendidikan = false;
                                @endphp
                                <select name="pendidikan_terakhir" class="form-select">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikanOptions as $pendidikan => $aliases)
                                    @php
                                        $aliasNorms = array_map($normalize, $aliases);
                                        $isSelected = $selectedPendidikanNorm !== '' && in_array($selectedPendidikanNorm, $aliasNorms, true);
                                        if ($isSelected) {
                                            $hasMappedPendidikan = true;
                                        }
                                    @endphp
                                    <option value="{{ $pendidikan }}" {{ $isSelected ? 'selected' : '' }}>{{ $pendidikan }}</option>
                                    @endforeach
                                    @if(!empty($selectedPendidikan) && !$hasMappedPendidikan)
                                    <option value="{{ $selectedPendidikan }}" selected>{{ $selectedPendidikan }} (Data Lama)</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan Pendidikan</label>
                        <input type="text" name="jurusan_pendidikan" class="form-control" value="{{ old('jurusan_pendidikan', $pegawai->jurusan_pendidikan) }}" placeholder="Contoh: Teknik Informatika, Manajemen, dll.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $pegawai->alamat) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten_kota_id" class="form-select select2" data-placeholder="-- Pilih Kabupaten/Kota --">
                            <option value=""></option>
                            @foreach($kabupatenKota as $kk)
                            <option value="{{ $kk->id }}" {{ old('kabupaten_kota_id', $pegawai->kabupaten_kota_id) == $kk->id ? 'selected' : '' }}>{{ $kk->tipe }} {{ $kk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $pegawai->telepon) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $pegawai->email) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-briefcase me-2"></i>Data Kepegawaian</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                                <select name="status_pegawai" class="form-select" required>
                                    @foreach(['CPNS', 'PNS', 'PPPK', 'PPPK Paruh Waktu', 'Non ASN', 'Berhenti/Keluar', 'Pensiun'] as $status)
                                    <option value="{{ $status }}" {{ old('status_pegawai', $pegawai->status_pegawai) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Golongan</label>
                                <select name="golongan_id" class="form-select select2" data-placeholder="-- Pilih Golongan --">
                                    <option value=""></option>
                                    @foreach($golongan as $g)
                                    <option value="{{ $g->id }}" {{ old('golongan_id', $pegawai->golongan_id) == $g->id ? 'selected' : '' }}>{{ $g->kode }} - {{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Eselon</label>
                                <select name="eselon" id="eselon" class="form-select">
                                    <option value="">-- Semua Eselon --</option>
                                    @foreach($eselonList as $eselon)
                                    <option value="{{ $eselon }}" {{ old('eselon', $selectedEselon) == $eselon ? 'selected' : '' }}>{{ $eselon }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih eselon untuk memfilter jabatan</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <select name="jabatan_id" id="jabatan_id" class="form-select select2" data-placeholder="-- Pilih Jabatan --">
                                    <option value=""></option>
                                    @foreach($jabatan as $j)
                                    <option value="{{ $j->id }}" data-eselon="{{ $j->eselon }}" {{ old('jabatan_id', $pegawai->jabatan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit Kerja</label>
                                <select name="unit_kerja_id" class="form-select select2" data-placeholder="-- Pilih Unit Kerja --">
                                    <option value=""></option>
                                    @foreach($unitKerja as $uk)
                                    <option value="{{ $uk->id }}" {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT CPNS</label>
                                <input type="date" name="tmt_cpns" class="form-control" value="{{ old('tmt_cpns', $pegawai->tmt_cpns?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT PNS</label>
                                <input type="date" name="tmt_pns" class="form-control" value="{{ old('tmt_pns', $pegawai->tmt_pns?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT Golongan</label>
                                <input type="date" name="tmt_golongan" class="form-control" value="{{ old('tmt_golongan', $pegawai->tmt_golongan?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT Jabatan</label>
                                <input type="date" name="tmt_jabatan" class="form-control" value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $pegawai->keterangan) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-image me-2"></i>Foto</div>
                <div class="card-body text-center">
                    @if($pegawai->foto)
                    <img id="preview" src="{{ Storage::url($pegawai->foto) }}" class="img-fluid mb-3 rounded" style="max-height: 250px;">
                    @else
                    <img id="preview" src="https://via.placeholder.com/200x250?text=Foto" class="img-fluid mb-3 rounded" style="max-height: 250px;">
                    @endif
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                    <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('preview');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

// Store all jabatan options for filtering
var allJabatanOptions = [];
var currentJabatanId = '{{ old('jabatan_id', $pegawai->jabatan_id) }}';

$(document).ready(function() {
    // Store original jabatan options
    $('#jabatan_id option').each(function() {
        allJabatanOptions.push({
            value: $(this).val(),
            text: $(this).text(),
            eselon: $(this).data('eselon') || ''
        });
    });
    
    // Handle eselon change
    $('#eselon').on('change', function() {
        var selectedEselon = $(this).val();
        var $jabatanSelect = $('#jabatan_id');
        
        // Clear current options
        $jabatanSelect.empty();
        $jabatanSelect.append('<option value=""></option>');
        
        // Add filtered options
        allJabatanOptions.forEach(function(option) {
            if (option.value === '') return;
            
            // Show all if no eselon selected, otherwise filter by eselon
            if (selectedEselon === '' || option.eselon === selectedEselon) {
                var $option = $('<option></option>')
                    .val(option.value)
                    .text(option.text)
                    .attr('data-eselon', option.eselon);
                    
                // Re-select the current jabatan if it matches
                if (option.value == currentJabatanId) {
                    $option.attr('selected', 'selected');
                }
                $jabatanSelect.append($option);
            }
        });
        
        // Trigger change for select2 to update
        if ($.fn.select2 && $jabatanSelect.hasClass('select2-hidden-accessible')) {
            $jabatanSelect.trigger('change');
        }
    });
    
    // Trigger initial filter if eselon has value
    var initialEselon = $('#eselon').val();
    if (initialEselon) {
        $('#eselon').trigger('change');
    }
});
</script>
@endpush
@endsection

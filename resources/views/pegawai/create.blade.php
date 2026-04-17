@extends('layouts.app')

@section('title', 'Tambah Pegawai')
@section('page-title', 'Tambah Pegawai')

@section('content')
<form action="{{ route('pegawai.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person me-2"></i>Data Pribadi</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" required>
                                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK">
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">NPWP</label>
                                <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp') }}" maxlength="20" placeholder="Nomor NPWP">
                                @error('npwp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">No. Rekening</label>
                                <input type="text" name="no_rekening" class="form-control @error('no_rekening') is-invalid @enderror" value="{{ old('no_rekening') }}" maxlength="50" placeholder="Nomor Rekening Bank">
                                @error('no_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Agama <span class="text-danger">*</span></label>
                                <select name="agama" class="form-select" required>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
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
                                    <option value="{{ $status }}" {{ old('status_perkawinan') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="form-select">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach(['Strata III', 'Strata II', 'Strata I', 'Diploma IV', 'Diploma III', 'Sekolah Lanjutan Tingkat Atas', 'Sekolah Lanjutan Tingkat Pertama', 'Sekolah Tingkat Dasar'] as $pendidikan)
                                    <option value="{{ $pendidikan }}" {{ old('pendidikan_terakhir') == $pendidikan ? 'selected' : '' }}>{{ $pendidikan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan Pendidikan</label>
                        <input type="text" name="jurusan_pendidikan" class="form-control" value="{{ old('jurusan_pendidikan') }}" placeholder="Contoh: Teknik Informatika, Manajemen, dll.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten_kota_id" class="form-select select2" data-placeholder="-- Pilih Kabupaten/Kota --">
                            <option value=""></option>
                            @foreach($kabupatenKota as $kk)
                            <option value="{{ $kk->id }}" {{ old('kabupaten_kota_id') == $kk->id ? 'selected' : '' }}>{{ $kk->tipe }} {{ $kk->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
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
                                    <option value="{{ $status }}" {{ old('status_pegawai', 'PNS') == $status ? 'selected' : '' }}>{{ $status }}</option>
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
                                    <option value="{{ $g->id }}" {{ old('golongan_id') == $g->id ? 'selected' : '' }}>{{ $g->kode }} - {{ $g->nama }}</option>
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
                                    <option value="{{ $eselon }}" {{ old('eselon') == $eselon ? 'selected' : '' }}>{{ $eselon }}</option>
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
                                    <option value="{{ $j->id }}" data-eselon="{{ $j->eselon }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
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
                                    <option value="{{ $uk->id }}" {{ old('unit_kerja_id') == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT CPNS</label>
                                <input type="date" name="tmt_cpns" class="form-control" value="{{ old('tmt_cpns') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT PNS</label>
                                <input type="date" name="tmt_pns" class="form-control" value="{{ old('tmt_pns') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT Golongan</label>
                                <input type="date" name="tmt_golongan" class="form-control" value="{{ old('tmt_golongan') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">TMT Jabatan</label>
                                <input type="date" name="tmt_jabatan" class="form-control" value="{{ old('tmt_jabatan') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-image me-2"></i>Foto</div>
                <div class="card-body text-center">
                    <img id="preview" src="https://via.placeholder.com/200x250?text=Foto" class="img-fluid mb-3 rounded" style="max-height: 250px;">
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                    <small class="text-muted">Format: JPG, PNG. Max: 2MB</small>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
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
        var currentValue = $jabatanSelect.val();
        
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
                $jabatanSelect.append($option);
            }
        });
        
        // Trigger change for select2 to update
        if ($.fn.select2 && $jabatanSelect.hasClass('select2-hidden-accessible')) {
            $jabatanSelect.trigger('change');
        }
    });
    
    // Trigger initial filter if eselon has old value
    var initialEselon = $('#eselon').val();
    if (initialEselon) {
        $('#eselon').trigger('change');
    }
});
</script>
@endpush
@endsection

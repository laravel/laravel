@extends('layouts.app')

@section('title', 'Pengaturan Jadwal Update')
@section('page-title', 'Pengaturan Jadwal Update Data')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-range me-2"></i>Jadwal Update Data
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-schedule.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" value="1" {{ old('is_enabled', $schedule?->is_enabled) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_enabled">
                            Aktifkan pengaturan jadwal update
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="starts_at" class="form-label">Mulai Update</label>
                            <input
                                type="datetime-local"
                                class="form-control @error('starts_at') is-invalid @enderror"
                                id="starts_at"
                                name="starts_at"
                                value="{{ old('starts_at', $schedule?->starts_at?->format('Y-m-d\\TH:i')) }}"
                            >
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ends_at" class="form-label">Berakhir Update</label>
                            <input
                                type="datetime-local"
                                class="form-control @error('ends_at') is-invalid @enderror"
                                id="ends_at"
                                name="ends_at"
                                value="{{ old('ends_at', $schedule?->ends_at?->format('Y-m-d\\TH:i')) }}"
                            >
                            @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Contoh: Periode pemutakhiran semester 1"
                        >{{ old('notes', $schedule?->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-3 mt-lg-0">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Status Saat Ini
            </div>
            <div class="card-body">
                @if(!$schedule || !$schedule->is_enabled)
                    <span class="badge bg-secondary mb-2">Tidak Aktif</span>
                    <p class="text-muted mb-0">Jadwal update belum diaktifkan. Semua user bisa update data sesuai hak akses.</p>
                @elseif($schedule->hasEnded())
                    <span class="badge bg-danger mb-2">Read-Only Aktif</span>
                    <p class="mb-1">Berakhir: <strong>{{ $schedule->formattedEndsAt() ?? '-' }}</strong></p>
                    <p class="text-muted mb-0">Sub admin dan pegawai tidak bisa melakukan perubahan data.</p>
                @else
                    <span class="badge bg-success mb-2">Update Dibuka</span>
                    <p class="mb-1">Mulai: <strong>{{ $schedule->formattedStartsAt() ?? '-' }}</strong></p>
                    <p class="mb-1">Berakhir: <strong>{{ $schedule->formattedEndsAt() ?? '-' }}</strong></p>
                    <p class="text-muted mb-0">Sub admin dan pegawai masih dapat melakukan update data.</p>
                @endif

                @if(!empty($schedule?->notes))
                    <hr>
                    <p class="mb-1"><strong>Catatan:</strong></p>
                    <p class="text-muted mb-0">{{ $schedule->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

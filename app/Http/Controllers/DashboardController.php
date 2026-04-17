<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\PerjalananDinas;
use App\Models\Cuti;
use App\Models\Kgb;
use App\Models\UpdateSchedule;

class DashboardController extends Controller
{
    private const EXCLUDED_STATUS_FOR_COUNT = ['Berhenti/Keluar', 'Pensiun'];

    public function index()
    {
        $user = auth()->user();
        $schedule = UpdateSchedule::current();
        $scheduleReadOnly = $schedule ? ($schedule->is_enabled && $schedule->hasEnded()) : false;
        $scheduleCountdownIso = null;

        if ($schedule && $schedule->is_enabled && $schedule->ends_at) {
            $scheduleCountdownIso = $schedule->ends_at->toIso8601String();
        }
        
        if ($user->isAdmin()) {
            $data = [
                'total_pegawai' => Pegawai::where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count(),
                'total_perjalanan_dinas' => PerjalananDinas::whereIn('status', ['Diajukan', 'Disetujui'])->count(),
                'total_cuti' => Cuti::whereIn('status', ['Diajukan', 'Disetujui'])->count(),
                'total_kgb' => Kgb::where('status', 'Diproses')->count(),
                'perjalanan_pending' => PerjalananDinas::where('status', 'Diajukan')->with('pegawai')->latest()->take(5)->get(),
                'cuti_pending' => Cuti::where('status', 'Diajukan')->with('pegawai', 'jenisCuti')->latest()->take(5)->get(),
                'kgb_pending' => Kgb::where('status', 'Diproses')->with('pegawai')->latest()->take(5)->get(),
            ];
        } elseif ($user->isSubAdmin()) {
            // Sub Admin - hanya melihat data dari unit kerja yang ditugaskan
            $unitKerjaId = $user->unit_kerja_id;
            $pegawaiIds = Pegawai::where('unit_kerja_id', $unitKerjaId)->pluck('id');
            
            $data = [
                'total_pegawai' => Pegawai::where('unit_kerja_id', $unitKerjaId)
                    ->where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count(),
                'total_perjalanan_dinas' => PerjalananDinas::whereIn('pegawai_id', $pegawaiIds)->whereIn('status', ['Diajukan', 'Disetujui'])->count(),
                'total_cuti' => Cuti::whereIn('pegawai_id', $pegawaiIds)->whereIn('status', ['Diajukan', 'Disetujui'])->count(),
                'total_kgb' => Kgb::whereIn('pegawai_id', $pegawaiIds)->where('status', 'Diproses')->count(),
                'perjalanan_pending' => PerjalananDinas::whereIn('pegawai_id', $pegawaiIds)->where('status', 'Diajukan')->with('pegawai')->latest()->take(5)->get(),
                'cuti_pending' => Cuti::whereIn('pegawai_id', $pegawaiIds)->where('status', 'Diajukan')->with('pegawai', 'jenisCuti')->latest()->take(5)->get(),
                'kgb_pending' => Kgb::whereIn('pegawai_id', $pegawaiIds)->where('status', 'Diproses')->with('pegawai')->latest()->take(5)->get(),
                'unit_kerja' => $user->unitKerja,
                'is_sub_admin' => true,
                'update_schedule' => $schedule,
                'update_schedule_read_only' => $scheduleReadOnly,
                'update_schedule_countdown_iso' => $scheduleCountdownIso,
            ];
        } else {
            $pegawai = $user->pegawai;
            $data = [
                'pegawai' => $pegawai,
                'perjalanan_dinas' => $pegawai ? $pegawai->perjalananDinas()->latest()->take(5)->get() : collect(),
                'cuti' => $pegawai ? $pegawai->cuti()->with('jenisCuti')->latest()->take(5)->get() : collect(),
                'kgb' => $pegawai ? $pegawai->kgb()->latest()->take(5)->get() : collect(),
                'update_schedule' => $schedule,
                'update_schedule_read_only' => $scheduleReadOnly,
                'update_schedule_countdown_iso' => $scheduleCountdownIso,
            ];
        }
        
        return view('dashboard', $data);
    }
}

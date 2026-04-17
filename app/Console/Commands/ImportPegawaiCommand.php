<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Pegawai;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportPegawaiCommand extends Command
{
    protected $signature = 'import:pegawai {file?}';
    protected $description = 'Import data pegawai dari file Excel';

    public function handle()
    {
        $file = $this->argument('file') ?? 'D:\laragon\www\daftar pegawai bahan aplikasi.xlsx';
        
        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: $file");
            return 1;
        }

        $this->info("Membaca file: $file");
        
        try {
            $spreadsheet = IOFactory::load($file);
            $sheets = $spreadsheet->getAllSheets();
            
            $this->info("Ditemukan " . count($sheets) . " sheet(s)");
            
            foreach ($sheets as $index => $sheet) {
                $sheetName = $sheet->getTitle();
                $this->info("\n--- Sheet " . ($index + 1) . ": $sheetName ---");
                
                $data = $sheet->toArray(null, true, true, true);
                
                // Show first few rows to understand structure
                $this->info("Total baris: " . count($data));
                
                if (count($data) > 0) {
                    // Show header row
                    $this->info("Header: " . json_encode($data[1] ?? [], JSON_UNESCAPED_UNICODE));
                    
                    // Process based on sheet name
                    $this->processSheet($sheetName, $data);
                }
            }
            
            $this->info("\n=== Import selesai! ===");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    protected function processSheet($sheetName, $data)
    {
        $sheetNameLower = strtolower($sheetName);
        
        // Skip header row, get rows starting from row 2
        $rows = array_slice($data, 1, null, true);
        
        if (str_contains($sheetNameLower, 'pegawai') || str_contains($sheetNameLower, 'data')) {
            $this->importPegawai($data);
        } elseif (str_contains($sheetNameLower, 'golongan')) {
            $this->importGolongan($rows);
        } elseif (str_contains($sheetNameLower, 'jabatan')) {
            $this->importJabatan($rows);
        } elseif (str_contains($sheetNameLower, 'unit')) {
            $this->importUnitKerja($rows);
        } else {
            // Try to auto-detect and import pegawai
            $this->importPegawai($data);
        }
    }

    protected function importPegawai($data)
    {
        $this->info("Memproses data pegawai...");
        
        // Detect header row
        $header = $data[1] ?? [];
        $headerMap = $this->mapHeaders($header);
        
        $this->info("Mapped headers: " . json_encode($headerMap, JSON_UNESCAPED_UNICODE));
        
        $imported = 0;
        $skipped = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($data as $rowNum => $row) {
                if ($rowNum <= 1) continue; // Skip header
                
                // Skip empty rows
                if (empty(array_filter($row))) continue;
                
                $pegawaiData = $this->extractPegawaiData($row, $headerMap);
                
                if (empty($pegawaiData['nip']) && empty($pegawaiData['nama'])) {
                    $skipped++;
                    continue;
                }
                
                // Create or update pegawai
                $pegawai = $this->createOrUpdatePegawai($pegawaiData);
                
                if ($pegawai) {
                    $imported++;
                    $this->line("  Imported: {$pegawai->nip} - {$pegawai->nama}");
                } else {
                    $skipped++;
                }
            }
            
            DB::commit();
            $this->info("Berhasil import: $imported pegawai, Dilewati: $skipped");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error importing: " . $e->getMessage());
        }
    }

    protected function mapHeaders($header)
    {
        $map = [];
        
        foreach ($header as $col => $value) {
            if (empty($value)) continue;
            
            $valueLower = strtolower(trim($value));
            
            // No urut
            if ($valueLower == 'no' || $valueLower == 'no.') {
                $map['no'] = $col;
            }
            // NIP
            elseif ($valueLower == 'nip') {
                $map['nip'] = $col;
            }
            // NIK
            elseif ($valueLower == 'nik' || str_contains($valueLower, 'nik')) {
                $map['nik'] = $col;
            }
            // NPWP
            elseif ($valueLower == 'npwp' || str_contains($valueLower, 'npwp')) {
                $map['npwp'] = $col;
            }
            // Nama (harus exact match atau mengandung nama tapi bukan nama jabatan/unit)
            elseif ($valueLower == 'nama' || ($valueLower == 'nama lengkap')) {
                $map['nama'] = $col;
            }
            // Tempat lahir
            elseif (str_contains($valueLower, 'tempat') && str_contains($valueLower, 'lahir')) {
                $map['tempat_lahir'] = $col;
            }
            // Tanggal lahir
            elseif ((str_contains($valueLower, 'tanggal') && str_contains($valueLower, 'lahir')) || str_contains($valueLower, 'tgl lahir') || $valueLower == 'tgl lahir') {
                $map['tanggal_lahir'] = $col;
            }
            // Jenis kelamin
            elseif ((str_contains($valueLower, 'jenis') && str_contains($valueLower, 'kelamin')) || $valueLower == 'jk' || $valueLower == 'l/p') {
                $map['jenis_kelamin'] = $col;
            }
            // Agama
            elseif ($valueLower == 'agama') {
                $map['agama'] = $col;
            }
            // Pangkat/Golongan
            elseif (str_contains($valueLower, 'pangkat') || ($valueLower == 'golongan') || str_contains($valueLower, 'gol/ruang')) {
                $map['golongan'] = $col;
            }
            // Jabatan (exact atau mengandung jabatan, bukan TMT jabatan)
            elseif ($valueLower == 'jabatan' || $valueLower == 'nama jabatan') {
                $map['jabatan'] = $col;
            }
            // OPD / Unit kerja
            elseif ($valueLower == 'opd' || $valueLower == 'skpd' || $valueLower == 'instansi') {
                $map['unit_kerja'] = $col;
            }
            // Nama Unit Kerja (secondary)
            elseif ($valueLower == 'nama unit kerja' && !isset($map['unit_kerja'])) {
                $map['unit_kerja'] = $col;
            }
            // Status pegawai
            elseif ($valueLower == 'status' || (str_contains($valueLower, 'status') && str_contains($valueLower, 'pegawai'))) {
                $map['status_pegawai'] = $col;
            }
            // Status perkawinan
            elseif (str_contains($valueLower, 'status') && (str_contains($valueLower, 'kawin') || str_contains($valueLower, 'perkawinan'))) {
                $map['status_perkawinan'] = $col;
            }
            // Pendidikan / Tingkat
            elseif ($valueLower == 'tingkat' || str_contains($valueLower, 'pendidikan') || str_contains($valueLower, 'pend.')) {
                $map['pendidikan_terakhir'] = $col;
            }
            // Jurusan Pendidikan
            elseif (str_contains($valueLower, 'jurusan') || str_contains($valueLower, 'program studi') || str_contains($valueLower, 'prodi')) {
                $map['jurusan_pendidikan'] = $col;
            }
            // Alamat
            elseif (str_contains($valueLower, 'alamat')) {
                $map['alamat'] = $col;
            }
            // Telepon/HP
            elseif (str_contains($valueLower, 'telepon') || str_contains($valueLower, 'telp') || str_contains($valueLower, 'hp') || str_contains($valueLower, 'no. hp') || str_contains($valueLower, 'no hp')) {
                $map['telepon'] = $col;
            }
            // Email
            elseif (str_contains($valueLower, 'email')) {
                $map['email'] = $col;
            }
            // TMT CPNS
            elseif (str_contains($valueLower, 'tmt') && str_contains($valueLower, 'cpns')) {
                $map['tmt_cpns'] = $col;
            }
            // TMT PNS
            elseif (str_contains($valueLower, 'tmt') && str_contains($valueLower, 'pns') && !str_contains($valueLower, 'cpns')) {
                $map['tmt_pns'] = $col;
            }
            // TMT Golongan / TMT Pangkat
            elseif (str_contains($valueLower, 'tmt') && (str_contains($valueLower, 'gol') || str_contains($valueLower, 'pangkat'))) {
                $map['tmt_golongan'] = $col;
            }
            // TMT Jabatan
            elseif (str_contains($valueLower, 'tmt') && str_contains($valueLower, 'jab')) {
                $map['tmt_jabatan'] = $col;
            }
            // TMT KGB
            elseif (str_contains($valueLower, 'tmt') && str_contains($valueLower, 'kgb')) {
                $map['tmt_kgb'] = $col;
            }
        }
        
        return $map;
    }

    protected function extractPegawaiData($row, $headerMap)
    {
        $data = [];
        
        // NIP
        $data['nip'] = isset($headerMap['nip']) ? $this->cleanValue($row[$headerMap['nip']] ?? '') : '';
        
        // NIK
        $data['nik'] = isset($headerMap['nik']) ? $this->cleanValue($row[$headerMap['nik']] ?? '') : null;
        
        // NPWP
        $data['npwp'] = isset($headerMap['npwp']) ? $this->cleanValue($row[$headerMap['npwp']] ?? '') : null;
        
        // Nama
        $data['nama'] = isset($headerMap['nama']) ? $this->cleanValue($row[$headerMap['nama']] ?? '') : '';
        
        // Tempat lahir
        $data['tempat_lahir'] = isset($headerMap['tempat_lahir']) ? $this->cleanValue($row[$headerMap['tempat_lahir']] ?? '') : null;
        
        // Tanggal lahir
        $data['tanggal_lahir'] = isset($headerMap['tanggal_lahir']) ? $this->parseDate($row[$headerMap['tanggal_lahir']] ?? '') : null;
        
        // Jenis kelamin
        $jk = isset($headerMap['jenis_kelamin']) ? $this->cleanValue($row[$headerMap['jenis_kelamin']] ?? '') : '';
        $data['jenis_kelamin'] = $this->parseJenisKelamin($jk);
        
        // Agama
        $data['agama'] = isset($headerMap['agama']) ? $this->parseAgama($row[$headerMap['agama']] ?? '') : 'Islam';
        
        // Status perkawinan
        $data['status_perkawinan'] = isset($headerMap['status_perkawinan']) ? $this->cleanValue($row[$headerMap['status_perkawinan']] ?? '') : 'Belum Kawin';
        
        // Pendidikan
        $data['pendidikan_terakhir'] = isset($headerMap['pendidikan_terakhir']) ? $this->cleanValue($row[$headerMap['pendidikan_terakhir']] ?? '') : null;
        
        // Jurusan Pendidikan
        $data['jurusan_pendidikan'] = isset($headerMap['jurusan_pendidikan']) ? $this->cleanValue($row[$headerMap['jurusan_pendidikan']] ?? '') : null;
        
        // Alamat
        $data['alamat'] = isset($headerMap['alamat']) ? $this->cleanValue($row[$headerMap['alamat']] ?? '') : null;
        
        // Telepon
        $data['telepon'] = isset($headerMap['telepon']) ? $this->cleanValue($row[$headerMap['telepon']] ?? '') : null;
        
        // Email
        $data['email'] = isset($headerMap['email']) ? $this->cleanValue($row[$headerMap['email']] ?? '') : null;
        
        // Status pegawai
        $data['status_pegawai'] = isset($headerMap['status_pegawai']) ? $this->cleanValue($row[$headerMap['status_pegawai']] ?? '') : 'PNS';
        if (empty($data['status_pegawai'])) $data['status_pegawai'] = 'PNS';
        
        // Golongan
        $data['golongan'] = isset($headerMap['golongan']) ? $this->cleanValue($row[$headerMap['golongan']] ?? '') : '';
        
        // Jabatan
        $data['jabatan'] = isset($headerMap['jabatan']) ? $this->cleanValue($row[$headerMap['jabatan']] ?? '') : '';
        
        // Unit kerja
        $data['unit_kerja'] = isset($headerMap['unit_kerja']) ? $this->cleanValue($row[$headerMap['unit_kerja']] ?? '') : '';
        
        // TMT dates
        $data['tmt_cpns'] = isset($headerMap['tmt_cpns']) ? $this->parseDate($row[$headerMap['tmt_cpns']] ?? '') : null;
        $data['tmt_pns'] = isset($headerMap['tmt_pns']) ? $this->parseDate($row[$headerMap['tmt_pns']] ?? '') : null;
        $data['tmt_golongan'] = isset($headerMap['tmt_golongan']) ? $this->parseDate($row[$headerMap['tmt_golongan']] ?? '') : null;
        $data['tmt_jabatan'] = isset($headerMap['tmt_jabatan']) ? $this->parseDate($row[$headerMap['tmt_jabatan']] ?? '') : null;
        
        return $data;
    }

    protected function createOrUpdatePegawai($data)
    {
        if (empty($data['nip']) && empty($data['nama'])) {
            return null;
        }
        
        // Generate NIP if empty
        if (empty($data['nip'])) {
            $data['nip'] = 'TMP' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
        
        // Get or create golongan
        $golonganId = null;
        if (!empty($data['golongan'])) {
            // Skip if it looks like a date or is too long
            $golonganValue = trim($data['golongan']);
            $monthPatternId = 'januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember';
            $monthPatternEn = 'january|february|march|april|may|june|july|august|september|october|november|december';
            $datePattern = '/^\d{1,2}\s+(' . $monthPatternId . '|' . $monthPatternEn . ')\s+\d{4}$/i';
            
            if (strlen($golonganValue) > 20 || preg_match($datePattern, $golonganValue)) {
                // This looks like a date, skip it
                $golonganId = null;
            } else {
                $golongan = Golongan::where('kode', 'like', '%' . $golonganValue . '%')
                    ->orWhere('nama', 'like', '%' . $golonganValue . '%')
                    ->first();
                
                if (!$golongan) {
                    $golongan = Golongan::create([
                        'kode' => substr($golonganValue, 0, 20),
                        'nama' => $golonganValue,
                    ]);
                }
                $golonganId = $golongan->id;
            }
        }
        
        // Get or create jabatan
        $jabatanId = null;
        if (!empty($data['jabatan'])) {
            $jabatan = Jabatan::where('nama', $data['jabatan'])->first();
            
            if (!$jabatan) {
                // Generate kode from jabatan nama
                $baseKode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $data['jabatan']), 0, 10));
                if (empty($baseKode)) $baseKode = 'JAB';
                
                // Make sure kode is unique
                $kode = $baseKode;
                $counter = 1;
                while (Jabatan::where('kode', $kode)->exists()) {
                    $kode = $baseKode . $counter;
                    $counter++;
                }
                
                $jabatan = Jabatan::create([
                    'kode' => $kode,
                    'nama' => $data['jabatan'],
                ]);
            }
            $jabatanId = $jabatan->id;
        }
        
        // Get or create unit kerja
        $unitKerjaId = null;
        if (!empty($data['unit_kerja'])) {
            $unitKerja = UnitKerja::where('nama', $data['unit_kerja'])->first();
            
            if (!$unitKerja) {
                // Generate kode from unit kerja nama
                $baseKode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $data['unit_kerja']), 0, 15));
                if (empty($baseKode)) $baseKode = 'UK';
                
                // Make sure kode is unique
                $kode = $baseKode;
                $counter = 1;
                while (UnitKerja::where('kode', $kode)->exists()) {
                    $kode = $baseKode . $counter;
                    $counter++;
                }
                
                $unitKerja = UnitKerja::create([
                    'kode' => $kode,
                    'nama' => $data['unit_kerja'],
                ]);
            }
            $unitKerjaId = $unitKerja->id;
        }
        
        // Create or update pegawai
        $pegawai = Pegawai::updateOrCreate(
            ['nip' => $data['nip']],
            [
                'nik' => $data['nik'] ?? null,
                'npwp' => $data['npwp'] ?? null,
                'nama' => $data['nama'] ?? 'Unknown',
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'agama' => $data['agama'] ?? 'Islam',
                'status_perkawinan' => $this->parseStatusPerkawinan($data['status_perkawinan']),
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'jurusan_pendidikan' => $data['jurusan_pendidikan'] ?? null,
                'alamat' => $data['alamat'],
                'telepon' => $data['telepon'],
                'email' => $data['email'],
                'status_pegawai' => $this->parseStatusPegawai($data['status_pegawai']),
                'golongan_id' => $golonganId,
                'jabatan_id' => $jabatanId,
                'unit_kerja_id' => $unitKerjaId,
                'tmt_cpns' => $data['tmt_cpns'],
                'tmt_pns' => $data['tmt_pns'],
                'tmt_golongan' => $data['tmt_golongan'],
                'tmt_jabatan' => $data['tmt_jabatan'],
                'is_active' => true,
            ]
        );
        
        // Create user account for pegawai
        if ($pegawai->wasRecentlyCreated) {
            $temporaryPassword = Str::random(12);

            User::updateOrCreate(
                ['username' => $pegawai->nip],
                [
                    'nama' => $pegawai->nama,
                    'password' => Hash::make($temporaryPassword),
                    'role' => 'pegawai',
                    'pegawai_id' => $pegawai->id,
                    'is_active' => $pegawai->is_active,
                    'must_change_password' => true,
                ]
            );

            $this->line("    User: {$pegawai->nip} | Password sementara: {$temporaryPassword}");
        }
        
        return $pegawai;
    }

    protected function cleanValue($value)
    {
        if (is_null($value)) return null;
        return trim((string) $value);
    }

    protected function parseDate($value)
    {
        if (empty($value)) return null;
        
        try {
            // Excel serial date
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
            }
            
            // Try various formats
            $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'd M Y', 'd F Y', 'Y/m/d'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseJenisKelamin($value)
    {
        $valueLower = strtolower(trim($value));
        
        if (in_array($valueLower, ['l', 'laki-laki', 'laki', 'pria', 'male', '1'])) {
            return 'L';
        }
        
        if (in_array($valueLower, ['p', 'perempuan', 'wanita', 'female', '2'])) {
            return 'P';
        }
        
        return 'L'; // Default
    }

    protected function parseAgama($value)
    {
        $valueLower = strtolower(trim($value ?? ''));
        
        if (str_contains($valueLower, 'islam') || str_contains($valueLower, 'muslim')) return 'Islam';
        if (str_contains($valueLower, 'protestan') || str_contains($valueLower, 'kristen')) return 'Kristen';
        if (str_contains($valueLower, 'katolik') || str_contains($valueLower, 'catholic')) return 'Katolik';
        if (str_contains($valueLower, 'hindu')) return 'Hindu';
        if (str_contains($valueLower, 'buddha') || str_contains($valueLower, 'budha')) return 'Buddha';
        if (str_contains($valueLower, 'konghucu') || str_contains($valueLower, 'khonghucu')) return 'Konghucu';
        
        return 'Islam'; // Default
    }

    protected function parseStatusPerkawinan($value)
    {
        $valueLower = strtolower(trim($value ?? ''));
        
        if (str_contains($valueLower, 'belum')) return 'Belum Kawin';
        if (str_contains($valueLower, 'kawin') || str_contains($valueLower, 'menikah')) return 'Kawin';
        if (str_contains($valueLower, 'cerai') && str_contains($valueLower, 'hidup')) return 'Cerai Hidup';
        if (str_contains($valueLower, 'cerai') && str_contains($valueLower, 'mati')) return 'Cerai Mati';
        if (str_contains($valueLower, 'cerai')) return 'Cerai Hidup';
        
        return 'Belum Kawin';
    }

    protected function parseStatusPegawai($value)
    {
        $valueLower = strtolower(trim($value ?? ''));
        
        if (str_contains($valueLower, 'cpns')) return 'CPNS';
        if (str_contains($valueLower, 'pns')) return 'PNS';
        if (str_contains($valueLower, 'pppk') && str_contains($valueLower, 'paruh')) return 'PPPK Paruh Waktu';
        if (str_contains($valueLower, 'pppk') || str_contains($valueLower, 'p3k')) return 'PPPK';
        if (str_contains($valueLower, 'honor') || str_contains($valueLower, 'non asn')) return 'Non ASN';
        if (str_contains($valueLower, 'berhenti') || str_contains($valueLower, 'keluar')) return 'Berhenti/Keluar';
        if (str_contains($valueLower, 'pensiun')) return 'Pensiun';
        
        return 'PNS';
    }

    protected function importGolongan($rows)
    {
        $this->info("Memproses data golongan...");
        
        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue;
            
            $kode = $row['A'] ?? $row['B'] ?? '';
            $nama = $row['B'] ?? $row['C'] ?? $kode;
            
            if (empty($kode)) continue;
            
            Golongan::updateOrCreate(
                ['kode' => $kode],
                ['nama' => $nama]
            );
            
            $this->line("  Golongan: $kode - $nama");
        }
    }

    protected function importJabatan($rows)
    {
        $this->info("Memproses data jabatan...");
        
        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue;
            
            $nama = $row['A'] ?? $row['B'] ?? '';
            
            if (empty($nama)) continue;
            
            Jabatan::updateOrCreate(
                ['nama' => $nama],
                []
            );
            
            $this->line("  Jabatan: $nama");
        }
    }

    protected function importUnitKerja($rows)
    {
        $this->info("Memproses data unit kerja...");
        
        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue;
            
            $nama = $row['A'] ?? $row['B'] ?? '';
            
            if (empty($nama)) continue;
            
            UnitKerja::updateOrCreate(
                ['nama' => $nama],
                []
            );
            
            $this->line("  Unit Kerja: $nama");
        }
    }
}

<?php

namespace Faker\Provider\id_ID;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyPrefix}} {{lastName}}',
        '{{companyPrefix}} {{lastName}} {{lastName}}',
        '{{companyPrefix}} {{lastName}} {{companySuffix}}',
        '{{companyPrefix}} {{lastName}} {{lastName}} {{companySuffix}}',
    ];

    /**
     * @see http://id.wikipedia.org/wiki/Jenis_badan_usaha
     */
    protected static $companyPrefix = ['PT', 'Fa', 'CV', 'UD', 'PJ', 'PD', 'Perum', 'Yayasan'];

    /**
     * @see https://www.amesbostonhotel.com/macam-macam-profesi-pekerjaan/
     * @see https://dukcapil.kedirikab.go.id/artikel/Jenis-Pekerjaan_ID10.html
     */
    protected static $jobTitleFormat = [
        'Akuntan', 'Apoteker', 'Arsitek', 'Atlet', 'Belum / Tidak Bekerja', 'Biarawati', 'Bidan', 'Buruh Harian Lepas',
        'Buruh Nelayan / Perikanan', 'Buruh Peternakan', 'Buruh Tani / Perkebunan', 'Desainer', 'Dokter', 'Dosen',
        'Guru', 'Hakim', 'Imam Masjid', 'Industri', 'Jaksa', 'Juru Masak', 'Karyawan BUMD', 'Karyawan BUMN',
        'Karyawan Honorer', 'Karyawan Swasta', 'Kepala Desa', 'Kepolisian RI (POLRI)', 'Kondektur', 'Konstruksi',
        'Konsultan', 'Masinis', 'Mekanik', 'Mengurus Rumah Tangga', 'Montir', 'Nahkoda', 'Nelayan / Perikanan',
        'Notaris', 'Paraji', 'Pastor', 'Pedagang', 'Pegawai Negeri Sipil (PNS)', 'Pelajar / Mahasiswa', 'Pelaut',
        'Pemandu Wisata', 'Pembantu Rumah Tangga', 'Penambang', 'Penata Busana', 'Penata Rambut', 'Penata Rias',
        'Pendeta', 'Peneliti', 'Penerjemah', 'Pengacara', 'Pensiunan', 'Penulis', 'Penyelam', 'Penyiar Radio',
        'Penyiar Televisi', 'Perancang Busana', 'Perangkat Desa', 'Perawat', 'Perdagangan', 'Petani / Pekebun',
        'Peternak', 'Pialang', 'Pilot', 'Pramugari', 'Pramusaji', 'Presiden', 'Programmer', 'Promotor Acara',
        'Psikiater / Psikolog', 'Satpam', 'Seniman', 'Sopir', 'Tabib', 'Tentara Nasional Indonesia (TNI)',
        'Transportasi', 'Tukang Batu', 'Tukang Cukur', 'Tukang Gigi', 'Tukang Jahit', 'Tukang Kayu',
        'Tukang Las / Pandai Besi', 'Tukang Listrik', 'Tukang Sol Sepatu', 'Ustaz / Mubaligh', 'Wakil Presiden',
        'Wartawan', 'Wiraswasta',
    ];

    /**
     * @see http://id.wikipedia.org/wiki/Jenis_badan_usaha
     */
    protected static $companySuffix = ['(Persero) Tbk', 'Tbk'];

    /**
     * Get company prefix
     *
     * @return string company prefix
     */
    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }

    /**
     * Get company suffix
     *
     * @return string company suffix
     */
    public static function companySuffix()
    {
        return static::randomElement(static::$companySuffix);
    }
}

<?php

namespace Faker\Provider\ms_MY;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyName}} {{companySuffix}}',
        '{{industry}} {{lastNameMalay}} {{companySuffix}}',
        '{{industry}} {{firstNameMaleChinese}} {{companySuffix}}',
        '{{industry}} {{firstNameMaleIndian}} {{companySuffix}}',
    ];

    /**
     * There are more Private Limited Companies(Sdn Bhd) than Public Listed Companies(Berhad)
     *
     * @see http://www.risscorporateservices.com/types-of-business-entities.html
     */
    protected static $companySuffix = [
        'Berhad',
        'Bhd',
        'Bhd.',
        'Enterprise',
        'Sdn Bhd', 'Sdn Bhd', 'Sdn Bhd', 'Sdn Bhd',
        'Sdn. Bhd.', 'Sdn. Bhd.', 'Sdn. Bhd.', 'Sdn. Bhd.',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/List_of_companies_of_Malaysia
     */
    protected static $companies = [
        'Adventa', 'AirAsia', 'AmBank', 'Astro Malaysia Holdings', 'Astro Radio', 'Axiata',
        'Berjaya Group', 'Bonia', 'Boustead Holdings', 'BSA Manufacturing', 'Bufori', 'Bumiputra-Commerce Holdings', 'Bursa Malaysia',
        'Capital Dynamics', 'Celcom', 'CIMB',
        'Digi Telecommunications', 'DRB-HICOM',
        'Edaran Otomobil Nasional (EON)',
        'Friendster',
        'Gamuda', 'Genting Group', 'Golden Hope', 'Golden Screen Cinemas', 'Guthrie',
        'HELP International Corporation',
        'iMoney.my', 'IOI Group', 'Iskandar Investment', 'The Italian Baker',
        'Jaring', 'JobStreet.com', 'Johor Corporation', 'Johor Land',
        'Khazanah Nasional', 'Khind Holdings', 'KLCC Properties', 'Keretapi Tanah Melayu (KTM)', 'Konsortium Transnasional (KTB)', 'Kulim (Malaysia)',
        'Lam Eng Rubber', 'Lion Group',
        'Magnum Corporation', 'Maybank', 'Malaysia Airlines', 'Malaysia Airports', 'Marrybrown', 'Maxis Communications', 'MBO Cinemas', 'Media Prima', 'MIMOS', 'MISC', 'Modenas', 'MUI Group', 'Mydin',
        'NAZA Group', 'New Straits Times Press',
        'OYL Industries',
        'Parkson', 'Pensonic', 'Permodalan Nasional', 'Perodua', 'Petronas', 'PLUS', 'Pos Malaysia', 'Prasarana Malaysia', 'Proton Holdings', 'Public Bank',
        'Ramly Group', 'Ranhill Holdings', 'Resort World', 'RHB Bank', 'Royal Selangor',
        'Scientex Incorporated', 'Scomi', 'Sime Darby', 'SIRIM', 'Sunway Group', 'Supermax',
        'Tan Chong Motor', 'Tanjong', 'Tenaga Nasional', 'Telekom Malaysia(TM)', 'TGV Cinemas', 'Top Glove',
        'U Mobile', 'UEM Group', 'UMW Holdings',
        'VADS', 'ViTrox',
        'Wasco Energy',
        'YTL Corporation',
    ];

    /**
     * @see http://www.daftarsyarikat.biz/perkhidmatan-dan-konsultasi/pendaftaran-lesen-kementerian-kewangan/senarai-kod-bidang/
     */
    protected static $industry = [
        'Agen Pengembaraan', 'Agen Penghantaran', 'Agen Perkapalan', 'Agensi Kredit Dan Pemfaktoran', 'Air', 'Akseso Kenderaan', 'Aksesori', 'Aksesori Jentera Berat', 'Aksesori Penghubung Dan Telekomunikasi', 'Aksesori Senjata Api', 'Akuatik', 'Akustik Dan Gelombang', 'Alat Forensik Dan Aksesori', 'Alat Gani', 'Alat Ganti', 'Alat Ganti Dan Kelengkapan Bot', 'Alat Hawa Dingin', 'Alat Hawa Dingin Kenderaan', 'Alat Kebombaan', 'Alat Kelengkapan Perubatan', 'Alat Keselamatan, Perlindungan Dan Kawalan Perlindungan Dan Kawalan', 'Alat Muzik Dan Aksesori', 'Alat Muzik, Kesenian dan Aksesori', 'Alat Penghasil Nyalaan', 'Alat penyelamat', 'Alat Penyimpan Tenaga Dan Aksesori', 'Alat Perhubungan', 'Alat Semboyan', 'Alat-Alat Marin', 'Alatganti Dan Kelengkapan Pesawat', 'Alatulis', 'Animation', 'Anti Kakis', 'Artis Dan Penghibur Profesional', 'Audio Visual',
        'Bagasi Dan Beg dari kulit', 'Bahan Api Nuklear', 'Bahan Bacaan', 'Bahan Bacaan Terbitan Luar Negara', 'Bahan Bakar', 'Bahan Binaan', 'Bahan dan Peralatan Solekan dan Andaman', 'Bahan Letupan', 'Bahan Peledak', 'Bahan Pelincir', 'Bahan pembungkusan', 'Bahan Pencuci Dan Pembersihan', 'Bahan Pendidikan', 'Bahan Penerbitan Elektronik Dan Muzik', 'Bahan Surih, Drafting Dan Alat Lukis', 'Bahan Tambah', 'Bahan Tarpaulin Dan Kanvas', 'Baik Pulih Kasut Dan Barangan Kulit', 'Baikpulih Barang-Barang Logam', 'Baja Dan Nutrien Tumbuhan', 'Baka', 'Bangunan', 'Bantuan Kecemasan DanAmbulan', 'Bantuan Kemanusiaan', 'Barangan Hiasan Dalaman Dan Aksesori', 'Barangan PVC', 'Barge', 'Bas', 'Basah', 'Basikal', 'Bekalan Pejabat Dan Alatulis', 'Bekas', 'Belon Panas', 'Benih Semaian', 'Bill Board', 'Bioteknologi', 'Bot', 'Bot Malim', 'Bot Tunda', 'Brangan Logam', 'Broker Insuran', 'Broker Perkapalan', 'Bunga Api Dan Mercun', 'Butang Dan Bekalan Jahitan',
        'Cat', 'Cenderamata Dan Hadiah', 'Cetakan Hologram', 'Cetakan Keselamatan', 'Chalet', 'Cloud Seeding', 'Complete Rounds', 'Customization and maintenance including data',
        'Dadah Berjadual', 'Dan Aksesori', 'Darat', 'Dasar Dan Peraturan', 'Data management –Provide services including Disaster', 'Dll', 'DNA', 'Dobi', 'Dokumentasi Dan Panduarah',
        'Elektronik', 'Empangan', 'Enjin Kenderaan', 'Enjin, Komponen Enjin Dan Aksesori', 'Entry, data processing',
        'Fabrik', 'Faksimili', 'Feri', 'Filem dan Mikrofilem', 'Filem Siap Untuk Tayangan', 'Fotografi',
        'Gas', 'Gas Turbine', 'Geographic Information System', 'Geologi', 'Graphic Design',
        'Habitat Dan Tempat Kurungan Haiwan', 'Haiwan Ternakan, Bukan Ternakan dan Akuatik', 'Hak Harta Intelek', 'Hardware', 'Hardware', 'Hardware and Software leasing', 'Hasil Sampingan Dan Sisa Perladangan', 'Helikopter', 'Hiasan Dalaman', 'Hiasan Jalan', 'Hidrografi', 'Homestay', 'Hortikultur', 'Hotel', 'Hubungan Antarabangsa', 'Hutan Dan Ladang Hutan',
        'ICT security and firewall, Encryption, PKI, Anti Virus', 'Industri', 'Infrastructure', 'Internet',
        'Jentera', 'Jentera Berat', 'Jentera Berat', 'Jet Ski',
        'Kabel Elektrik Dan Aksesori', 'Kain', 'Kajian Telekomunikasi', 'Kakitangan Iktisas', 'Kakitangan Separa Iktisas', 'Kamera dan Aksesori', 'Kanvas', 'Kapal', 'Kapal Angkasa Dan Alatganti', 'Kapal Laut', 'Kapal Selam', 'Kapal Selam', 'Kapal Terbang', 'Kawalan Keselamatan', 'Kawalan Serangga Perosak, Anti Termite', 'Kawasan', 'Kayu', 'Kediaman', 'Kelengkapan', 'Kelengkapan Dan Aksesori', 'Kelengkapan Hospital Dan Makmal', 'Kelengkapan Pakaian', 'Kelengkapan Sasaran', 'Kemudahan Awam', 'Kemudahan Awam', 'Kenderaan', 'Kenderaan Bawah 3 Ton', 'Kenderaan Ber Rel Dan kereta Kabel', 'Kenderaan Jenazah', 'Kenderaan Kegunaan Khusus', 'Kenderaan Kegunaan Khusus', 'Kenderaan Melebihi 3Ton', 'Kenderaan Rekreasi', 'Kenderaan Udara', 'Kereta', 'Kerja Pembaikan Kapal Angkasa', 'Kerja-Kerja Khusus', 'Kerja-kerja Mengetuk dan Mengecat', 'Kerja-Kerja Pembaikan Kenderaan Ber Rel Dan Kereta Kabel', 'Kerja-Kerja Penyelenggaraan Sistem Kenderaan', 'Kertas', 'Kertas Komputer', 'Khidmat Guaman', 'Khidmat Latihan, Tenaga Pengajar dan Moderator', 'Khidmat Udara', 'Kit Pendidikan', 'Kodifikasi', 'Kolam Kumbahan', 'Komponen Dan Aksesori Elektrik', 'Komponen Enjin Pembakaran Dalaman', 'Kontena', 'Kotak', 'Kren', 'Kunci, Perkakasan Perlindungan Dan Aksesori', 'Kusyen dan Bumbung',
        'Label', 'Ladang', 'Lagu', 'Lain-lain Media Pengiklanan', 'Laminating', 'Lampu, Komponen Lampu Dan Aksesori', 'Laut', 'Lesen', 'LIDAR', 'Lilin', 'Logam', 'Lokomotif Dan Troli Elektrik', 'Lori',
        'Maintenance', 'Makanan', 'Makanan Bermasak', 'Makanan Bermasak', 'Makanan Dan Bahan Mentah Kering', 'Makanan dan Minuman', 'Makanan Haiwan', 'Makmal', 'Malim Kapal', 'Marker', 'Mechanisation System', 'Media Cetak', 'Media Elektronik', 'Medium Penyimpanan', 'Membaik Pulih Bateri', 'Membaik Pulih Tayar', 'Membaik Pulih TempatDuduk', 'Membaiki Buff Fuel Tank', 'Membaikpulih BahanTerbitan Dan Manuskrip', 'Membekal Air', 'Membeli Barang Lusuh Perlu Permit', 'Membeli Barang Lusuh Tanpa Permit', 'Membersih Kawasan', 'Membersih Kenderaan', 'Membersih Pantai', 'Memproses Air', 'Memproses Filem', 'Menangkap', 'Mencetak Borang', 'Mencetak Buku, Majalah, Laporan Akhbar', 'Mencetak Continuous Stationery Forms', 'Mencetak Fail, Kad Perniagaan Dan Kad Ucapan', 'Mencetak Label, Poster dan Pelekat', 'Mencetak Label, Poster, Pelekat dan Iron On', 'Mencuci Kolam Renang', 'Menembak Haiwan', 'Mengangkat Sampah', 'Mengangkut Mayat', 'Mengikat Dan Melepas Tali Kapal', 'Menjahit Bukan Pakaian', 'Menjahit Pakaian Dan Kelengkapan', 'Menjilid Kulit Keras', 'Menjilid Kulit Lembut', 'Menyelam', 'Mereka-Cipta Dan Seni Halus', 'Mesin Dan Kelengkapan Bengkel', 'Mesin dan Kelengkapan Khusus', 'Mesin dan peralatan makmal', 'Mesin dan Peralatan Pejabat', 'Mesin dan Peralatan Woksyop', 'Mesin Pengimbas', 'Mesin-Mesin Pejabat', 'Mesin-Mesin Pejabat Dan Aksesori', 'Minuman Tambahan', 'Motel', 'Motor Dan Alat Ubah', 'Motosikal', 'Multimedia-products services and maintenance', 'Multimodal Transport Operator',
        'Negotiator', 'Networking-supply', 'Nylon',
        'Oceanografi',
        'P.A Sistem Dan Alat Muzik', 'Paip Air Dan Komponen', 'Paip Dan Kelengkapan', 'Pakaian', 'Pakaian Keselamatan, Kelengkapan Dan Aksesori', 'Pakaian Sukan Dan Aksesori', 'Palet', 'Pameran pertunjukan, taman hiburan dan karnival', 'Papan Tanda dan Aksesori', 'Pejabat', 'Pekakas Perubatan Pakai Buang', 'Pelancar Misil Dan Roket', 'Pelupusan Dan Perawatan Sisa berbahaya', 'Pelupusan Dan Perawatan Sisa tidak berbahaya', 'Pelupusan dan Rawatan Sisa Radio Aktif dan Nuklear', 'Peluru Berpandu', 'Peluru Dan Bom', 'Pemadam Api', 'Pembaikan Alat Keselamatan', 'Pembaikan Kenderaan Yang Tidak Berenjin', 'Pembajaan', 'Pembersihan Bangunan Dan Pejabat', 'Pembersihan Tumpahan Minyak', 'Pembuat', 'Pembuat', 'Pembuat Keselamatan', 'Pembungkusan', 'Pembungkusan Dan Penyimpanan', 'Pemeliharaan Bahan Bahan Sejarah Dan Tempat Bersejarah', 'Pemetaan', 'Pemetaan Utiliti Bawah Tanah', 'Pemilik Kapal', 'Pemungut Hutang', 'Pencahayaan', 'Pencelup', 'Pencucuh', 'Penerbitan Elektronik Atas Talian', 'Pengangkutan Lori', 'Pengatur Huruf', 'Pengeluaran Filem', 'Pengenalan Dan Pas Keselamatan Bersalut', 'Penghantar Notis', 'Penghantaran Dokumen', 'Pengkomersilan', 'Pengurusan Jenazah Dan Kelengkapan', 'Pengurusan Kewangan Dan Korporat', 'Pengurusan Pelabuhan', 'Penjana Kuasa', 'Pensijilan dan Pengiktirafan', 'Penterjemahan', 'Penulisan – Semua Jenis Penulisan', 'Penyediaan Akaun dan Pengauditan', 'Penyediaan Pentas', 'Penyelenggaraan', 'Penyelenggaraan Kapal Terbang', 'Penyelenggaraan Misil', 'Penyelenggaraan Simulator Helikopter', 'Penyelenggaraan Simulator Kapal', 'Penyelenggaraan Simulator Kapal Terbang', 'PenyelenggaraanHelikopter', 'Penyelenggaran Dan Pembaikan Senjata', 'Penyiaran', 'Penyiasat Persendirian', 'Penyimpanan Rekod',
        'Perabot', 'Perabot Jalan Raya', 'Perabot Pejabat', 'Perabot, Perabot Makmal dan Kelengkapan Berasaskan', 'Peralatan', 'Peralatan Dan Kelengkapan Hospital', 'Peralatan Dan Kelengkapan Pertanian', 'Peralatan Dan Kelengkapan Perubatan', 'Peralatan Dan Perkakas Domestik', 'Peralatan Kawalan Api', 'Peralatan Kawalan Keselamatan', 'Peralatan Keselamatan', 'Peralatan Keselamatan dan Senjata', 'Peralatan Makmal Pengukuran, Pencerapan Dan Sukat', 'Peralatan Makmal serta Aksesori', 'Peralatan Marin', 'Peralatan Memancing', 'Peralatan Memburu', 'Peralatan Pemantauan Dan Pengesanan', 'Peralatan Pemprosesan Fotografi, Mikrofilem', 'Peralatan Pengawalan Perosak Tanaman', 'Peralatan Percetakan Serta Aksesori', 'Peralatan Perindustrian Hiliran', 'Peralatan Perindustrian Huluan', 'Peralatan Perkhemahan Dan Aktiviti Luar', 'Peralatan Servis Dan Selenggara', 'Peralatan Sistem Bunyi, Pembesar Suara dan Projektor', 'Peralatan Sistem Kumbahan Dan Aksesori', 'Peralatan Sukan', 'Peralatan Untuk Orang Kurang Upaya Dan Pemulihan', 'Perhubungan', 'Perikanan Dan Akuakultur', 'Perkakas', 'Perkakas Elektrik Dan Aksesori', 'Perkakas Elektronik Dan Aksesori', 'Perkakasan Dan Bahan Kebersihan Diri Dan Mandian, Kelengkapan Bilik Air', 'Perkakasan Penyuntingan', 'Perkhidmatan Fotostat', 'Perkhidmatan Mel Pukal', 'Permainan', 'Perosak, Rumpai', 'Persembahan', 'Pertanian', 'Perundingan', 'Pesakit', 'Pesawat', 'Pesawat Udara', 'Pest Control', 'Pestaria', 'Pewarna', 'Pisah Warna', 'Plastik', 'Plastik', 'Printers, storage area network', 'Production Testing, Surface Well Testing and Wire Line Services', 'Pump', 'Pusat Latihan', 'Pvc',
        'Racun Berjadual', 'Racun Serangga', 'Radar Dan Alatganti', 'Rakaman', 'Rawatan Hutan', 'Reaktor dan Instrumen Nuklear', 'Rekabentuk Percetakan', 'Renting', 'Resort', 'Roket Dan Sub Sistem, Pelancar', 'Rotan', 'Ruang Niaga', 'Rumah Kediaman', 'Rumah Tumpangan',
        'Salvage Boat', 'Sampan', 'Sampel dan Sampel Awetan Haiwan', 'Sand Blasting Dan Mengecat Untuk Kapal', 'Satelit', 'Satelit Dan Alatganti', 'Semua Peralatan Sukatan', 'Senjata Api', 'Serangga', 'Sesalur', 'Shelf packages including maintenance', 'Ship Chandling', 'Ship Trimming', 'Simulator', 'Simulator Bot', 'Simulator serta lain-lain', 'Sisa Perawatan', 'Sistem Elektrik', 'Sistem Elektronik', 'Sistem Pencegah Kebakaran', 'Sistem Perhubungan', 'Sistem, Peralatan, Alat Ganti Keretapi Dan Aksesori', 'Software', 'Solekan', 'Split', 'Stesen Janakuasa, Peralatan', 'Stevedor', 'Stor', 'Sub Sistem Roket', 'Sukan', 'Sumber Air', 'Sungai', 'Syarikat Insuran', 'Syarikat pelelong awam', 'System development',
        'Tag', 'Talian Paip', 'Taman', 'Tanaman', 'Tanda Dan Stiker', 'Tangki', 'Tasik', 'Tatahias Haiwan', 'Teknologi Hijau', 'Teknologi Maklumat Dan Komunikasi', 'Tekstil', 'Tekstil Guna Semula Kakitangan', 'Tekstil Pakai Buang Kakitangan', 'Telecommunication', 'Telekomunikasi', 'Telly Clerk', 'Tempat Letak Kereta', 'Tenaga Buruh', 'Ternakan', 'Terusan', 'Topografi', 'Trailer Dan Aksesori', 'Tukun Tiruan', 'Tumbuhan',
        'Ubat Haiwan', 'Ubat Tidak Berjadual', 'Ujian Makmal', 'Ukuran',
        'Varnishing',
        'WAN', 'Wayar Elektrik Dan Aksesori', 'Wireless',
    ];

    /**
     * Return a random company name
     *
     * @example 'AirAsia'
     */
    public static function companyName()
    {
        return static::randomElement(static::$companies);
    }

    /**
     * Return a random industry
     *
     * @example 'Automobil'
     */
    public static function industry()
    {
        return static::randomElement(static::$industry);
    }
}

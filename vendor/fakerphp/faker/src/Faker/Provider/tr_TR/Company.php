<?php

namespace Faker\Provider\tr_TR;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}}oğlu {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companySuffix}}',
        '{{lastName}} {{companyField}} {{companySuffix}}',
        '{{lastName}} {{companyField}} {{companySuffix}}',
        '{{lastName}} {{companyField}} {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companyField}} {{companySuffix}}',
    ];

    protected static $companySuffix = ['A.Ş.', 'Ltd. Şti.'];

    protected static $companyField = [
        'Akaryakıt', 'Beyaz Eşya', 'Bilgi İşlem', 'Bilgisayar', 'Bilişim Hizmetleri',
        'Biracılık ve Malt Sanayii', 'Cam Sanayii', 'Çimento', 'Demir ve Çelik',
        'Dış Ticaret', 'Eczacılık', 'Elektrik İletim', 'Elektrik Üretim', 'Elektronik',
        'Emlak', 'Enerji', 'Giyim', 'Gıda', 'Holding', 'Isıtma ve Soğutma Sistemleri',
        'İletişim Hizmetleri', 'İnşaat ve Sanayi', 'İthalat ve İhracat', 'Kimya',
        'Kurumsal Hizmetler', 'Lojistik', 'Madencilik', 'Makina', 'Mağazalar', 'Nakliyat',
        'Otomotiv', 'Pazarlama', 'Perakende Ticaret', 'Petrol', 'Petrolcülük', 'Sanayi',
        'Sağlık Hizmetleri', 'Servis ve Ticaret', 'Süt Ürünleri', 'Tarım Sanayi',
        'Tavukçuluk', 'Tekstil', 'Telekomünikasyon', 'Tersane ve Ulaşım Sanayi',
        'Ticaret', 'Ticaret ve Sanayi', 'Ticaret ve Taahhüt', 'Turizm', 'Yatırım',
    ];

    /**
     * @see https://tr.wikipedia.org/wiki/Meslekler_listesi
     *
     * @note Randomly took 300 from this list
     */
    protected static $jobTitleFormat = [
        'Acil tıp teknisyeni', 'Agronomist', 'Aile hekimi', 'Aktar', 'Aktör', 'Aktüer',
        'Akustikçi', 'Albay', 'Ambarcı', 'Ambulans şoförü', 'Amiral', 'Analist',
        'Antika satıcısı', 'Araba tamircisi', 'Arabacı', 'Araştırmacı', 'Armatör', 'Artist',
        'Asker', 'Astrofizikçi', 'Astrolog', 'Astronom', 'Astronot', 'Atlet', 'Avukat',
        'Ayakkabı boyacısı', 'Ayakkabı tamircisi', 'Ayakçı', 'Ağ yöneticisi', 'Aşçıbaşı',
        'Bacacı', 'Badanacı', 'Baharatçı', 'Bahçe bitkileri uzmanı', 'Bakkal', 'Bakteriyolog',
        'Balon pilotu', 'Bankacı', 'Banker', 'Barmeyd', 'Başdümenci', 'Başpiskopos',
        'Başçavuş', 'Bebek Bakıcısı', 'Belediye başkanı', 'Belediye meclisi üyesi', 'Besteci',
        'Biletçi', 'Bilgi İşlemci', 'Bilgisayar mühendisi', 'Binicilik', 'Biyografi yazarı',
        'Bobinajcı', 'Borsacı', 'Boyacı', 'Bulaşıkçı', 'Börekçi', 'Çamaşırcı', 'Çantacı',
        'Çevik Kuvvet', 'Çevirmen', 'Çevre Mühendisi', 'Çevrebilimci', 'Çeyizci',
        'Çiftlik işletici', 'Çiftçi', 'Çinici', 'Çoban', 'Çırak', 'Dadı', 'Daktilograf',
        'Dalgıç', 'Dansöz', 'Dedektif', 'Derici', 'Değirmen işçisi', 'Değirmenci', 'Dilci',
        'Diplomat', 'Doktor', 'Dokumacı', 'Dondurmacı', 'Doğramacı', 'Dövizci', 'Döşemeci',
        'Elektrik mühendisi', 'Elektronik mühendisi', 'Elektronik ve Haberleşme mühendisi',
        'Embriyolog', 'Emniyet amiri', 'Emniyet genel müdürü', 'Ergonomist', 'Eskici', 'Falcı',
        'Fizikçi', 'Fizyoterapist', 'Fotoğrafçı', 'Fıçıcı', 'Galerici', 'Garson',
        'Gazete dağıtıcısı', 'Gazete satıcısı', 'Gazeteci', 'Gelir uzman yardımcısı', 'General',
        'Genetik mühendisi', 'Gezici vaiz', 'Gondolcu', 'Guru', 'Gökbilimci', 'Gözlükçü',
        'Güfteci', 'Gümrük uzmanı', 'Haham', 'Hakem', 'Halkbilimci', 'Hamal', 'Hamurkâr',
        'Hareket memuru', 'Hava trafikçisi', 'Havacı', 'Hayvan terbiyecisi', 'Hesap uzmanı',
        'Heykeltıraş', 'Hokkabaz', 'Irgat', 'İcra memuru', 'İllüzyonist', 'İmam',
        'İnsan kaynakları uzmanı', 'İplikçi', 'İthalatçı', 'İş ve uğraşı terapisti', 'İşaretçi',
        'Jimnastikçi', 'Jokey', 'Kabin görevlisi', 'Kabuk soyucusu', 'Kadın berberi', 'Kahveci',
        'Kalaycı', 'Kaplamacı', 'Kapı satıcısı', 'Kardinal', 'Kardiyolog', 'Karikatürist',
        'Kat görevlisi', 'Kaymakam', 'Kayıkçı', 'Kazıcı', 'Klarnetçi', 'Konserveci',
        'Konveyör operatörü', 'Koramiral', 'Korgeneral', 'Kozmolog', 'Kuaför', 'Kumaşçı', 'Kumcu',
        'Kuruyemişçi', 'Kurye', 'Kuyumcu', 'Kâğıtçı', 'Köpek eğiticisi', 'Köşe yazarı', 'Kürkçü',
        'Kırtasiyeci', 'Laborant', 'Laboratuar işçisi', 'Lahmacuncu', 'Lehimci', 'Levazımcı',
        'Lobici', 'Lokantacı', 'Lokman', 'Lostracı', 'Madenci', 'Makastar', 'Makine mühendisi',
        'Makine zabiti', 'Makyajcı', 'Mali hizmetler uzmanı', 'Manastır baş rahibesi',
        'Manifaturacı', 'Manikürcü', 'Masör', 'Matematikçi', 'Memur', 'Mermerci',
        'Meteoroloji uzmanı', 'Misyoner', 'Model', 'Modelci', 'Modelist', 'Montajcı', 'Montör',
        'Muallim', 'Muhafız', 'Mumyalayıcı', 'Müzik yönetmeni', 'Müşavir', 'Nalbant', 'Nalbur',
        'Oduncu', 'Orgcu', 'Ornitolog', 'Oto elektrikçisi', 'Oto lastik tamircisi', 'Oyuncakçı',
        'Oyuncu', 'Ön muhasebe yardımcı elemanı', 'Ön muhasebeci', 'Öğretim elemanı',
        'Öğretim görevlisi', 'Öğretim üyesi', 'Papaz', 'Paramedik', 'Pastörizör', 'Pencereci',
        'Perukçu', 'Peyzaj teknikeri', 'Peçeteci', 'Pideci', 'Pilot', 'Piyanist', 'Politikacı',
        'Pompacı', 'Psikolog', 'Radyolog', 'Radyoloji teknisyeni/teknikeri', 'Rejisör',
        'Reklamcı', 'Rektör', 'Rot balansçı', 'Saat tamircisi', 'Sanat yönetmeni', 'Saraç', 'Savcı',
        'Saz şairi', 'Sekreter', 'Ses teknisyeni', 'Sicil memuru', 'Sihirbaz', 'Sistem mühendisi',
        'Sosyal hizmet uzmanı', 'Sosyolog', 'Soğuk demirci', 'Stenograf', 'Stilist',
        'Sucu', 'Sunucu', 'Susuz araç yıkama', 'Sünnetçi', 'Sürveyan', 'Şapel papazı',
        'Şarkı sözü yazarı', 'Şehir Plancısı', 'Şekerci', 'Şimşirci', 'Şoför', 'Tahsildar',
        'Tarihçi', 'Tasarımcı', 'Taşlayıcı', 'Taşçı', 'Tekniker', 'Teknisyen', 'Teknoloji uzmanı',
        'Televizyon tamircisi', 'Terapist', 'Tesisatçı', 'Teşrifatçı', 'Tornacı', 'Tuğgeneral',
        'Ulaşım sorumlusu', 'Ustabaşı', 'Uydu antenci', 'Üst Düzey Yönetici', 'Ütücü',
        'Uzay bilimcisi', 'Vali', 'Veri hazırlama ve kontrol işletmeni', 'Veteriner hekim',
        'Veteriner sağlık teknikeri', 'Veznedar', 'Vinç operatörü', 'Vitrinci', 'Yarbay',
        'Yardımcı pilot', 'Yargıç', 'Yazar', 'Yazı işleri müdürü', 'Yazılım mühendisi',
        'Yer gösterici', 'Yol bekçisi', 'Yorgancı', 'Yoğurtçu', 'Yıkıcı', 'Zabıta', 'Zoolog',
    ];

    /**
     * Returns a random company field.
     *
     * @return string
     */
    public static function companyField()
    {
        return static::randomElement(static::$companyField);
    }
}

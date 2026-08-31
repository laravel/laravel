<?php

namespace Faker\Provider\ms_MY;

class Address extends \Faker\Provider\Address
{
    /**
     * @see https://en.wikipedia.org/wiki/Addresses_in_Malaysia
     */
    protected static $addressFormats = [
        '{{streetAddress}}, {{township}}, {{townState}}',
    ];

    protected static $streetAddressFormats = [
        '{{buildingPrefix}}{{buildingNumber}}, {{streetName}}',
    ];

    /**
     * Most of the time 'No.' is not needed, and 'Lot' is less used.
     */
    protected static $buildingPrefix = [
        '', '', '', '', '', '',
        'No. ', 'No. ', 'No. ',
        'Lot ',
    ];

    protected static $buildingNumber = [
        '%', '%', '%',
        '%#', '%#', '%#', '%#',
        '%##',
        '%-%',
        '?-##-##',
        '%?-##',
    ];

    protected static $streetNameFormats = [
        '{{streetPrefix}} %',
        '{{streetPrefix}} %/%',
        '{{streetPrefix}} %/%#',
        '{{streetPrefix}} %/%?',
        '{{streetPrefix}} %/%#?',
        '{{streetPrefix}} %?',
        '{{streetPrefix}} %#?',
        '{{streetPrefix}} {{streetSuffix}}',
        '{{streetPrefix}} {{streetSuffix}} %',
        '{{streetPrefix}} {{streetSuffix}} %/%',
        '{{streetPrefix}} {{streetSuffix}} %/%#',
        '{{streetPrefix}} {{streetSuffix}} %/%?',
        '{{streetPrefix}} {{streetSuffix}} %/%#?',
        '{{streetPrefix}} {{streetSuffix}} %?',
        '{{streetPrefix}} {{streetSuffix}} %#?',
    ];

    protected static $townshipFormats = [
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefix}} {{townshipSuffix}}',
        '{{townshipPrefixAbbr}}%',
        '{{townshipPrefixAbbr}}%#',
        '{{townshipPrefixAbbr}}%#?',
    ];

    /**
     * 'Jalan' & 'Jln' are more frequently used than 'Lorong'
     *
     * @see https://en.wikipedia.org/wiki/List_of_roads_in_Kuala_Lumpur#Standard_translations
     */
    protected static $streetPrefix = [
        'Jln', 'Jln',
        'Jalan', 'Jalan', 'Jalan',
        'Lorong',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/List_of_roads_in_Kuala_Lumpur
     * @see https://en.wikipedia.org/wiki/List_of_roads_in_Ipoh
     * @see https://en.wikipedia.org/wiki/Transportation_in_Seremban#Inner_city_roads
     * @see https://en.wikipedia.org/wiki/List_of_streets_in_George_Town,_Penang
     */
    protected static $streetSuffix = [
        'Air Itam', 'Alor', 'Ampang', 'Ampang Hilir', 'Anson', 'Ariffin',
        'Bangsar', 'Baru', 'Bellamy', 'Birch', 'Bijih Timah', 'Bukit Aman', 'Bukit Bintang', 'Bukit Petaling', 'Bukit Tunku',
        'Cantonment', 'Cenderawasih', 'Chan Sow Lin', 'Chow Kit', 'Cinta', 'Cochrane', 'Conlay',
        'D. S. Ramanathan', 'Damansara', 'Dang Wangi', 'Davis', 'Dewan Bahasa', 'Dato Abdul Rahman', 'Dato\'Keramat', 'Dato\' Maharaja Lela', 'Doraisamy',
        'Eaton',
        'Faraday',
        'Galloway', 'Genting Klang', 'Gereja',
        'Hang Jebat', 'Hang Kasturi', 'Hang Lekir', 'Hang Lekiu', 'Hang Tuah', 'Hospital',
        'Imbi', 'Istana',
        'Jelutong',
        'Kampung Attap', 'Kebun Bunga', 'Kedah', 'Keliling', 'Kia Peng', 'Kinabalu', 'Kuala Kangsar', 'Kuching',
        'Ledang', 'Lembah Permai', 'Loke Yew', 'Lt. Adnan', 'Lumba Kuda',
        'Madras', 'Magazine', 'Maharajalela', 'Masjid', 'Maxwell', 'Mohana Chandran', 'Muda',
        'P. Ramlee', 'Padang Kota Lama', 'Pahang', 'Pantai Baharu', 'Parlimen', 'Pasar', 'Pasar Besar', 'Perak', 'Perdana', 'Petaling', 'Prangin', 'Pudu', 'Pudu Lama',
        'Raja', 'Raja Abdullah', 'Raja Chulan', 'Raja Laut', 'Rakyat', 'Residensi', 'Robson',
        'S.P. Seenivasagam', 'Samarahan 1', 'Selamat', 'Sempadan', 'Sentul', 'Serian 1', 'Sasaran', 'Sin Chee', 'Sultan Abdul Samad', 'Sultan Azlan Shah', 'Sultan Iskandar', 'Sultan Ismail', 'Sultan Sulaiman', 'Sungai Besi', 'Syed Putra',
        'Tan Cheng Lock', 'Thambipillay', 'Tugu', 'Tuanku Abdul Halim', 'Tuanku Abdul Rahman', 'Tun Abdul Razak', 'Tun Dr Ismail', 'Tun H S Lee', 'Tun Ismail', 'Tun Perak', 'Tun Razak', 'Tun Sambanthan',
        'U-Thant', 'Utama',
        'Vermont', 'Vivekananda',
        'Wan Kadir', 'Wesley', 'Wisma Putra',
        'Yaacob Latif', 'Yap Ah Loy', 'Yap Ah Shak', 'Yap Kwan Seng', 'Yew',
        'Zaaba', 'Zainal Abidin',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/List_of_Petaling_Jaya_city_sections
     * @see https://en.wikipedia.org/wiki/UEP_Subang_Jaya#History
     */
    protected static $townshipPrefixAbbr = [
        'SS', 'Seksyen ', 'PJS', 'PJU', 'USJ ',
    ];

    /**
     * 'Bandar' and 'Taman' are the most common township prefix
     *
     * @see https://en.wikipedia.org/wiki/Template:Greater_Kuala_Lumpur > Townships
     * @see https://en.wikipedia.org/wiki/Template:Johor > Townships
     * @see https://en.wikipedia.org/wiki/Template:Kedah > Townships
     * @see https://en.wikipedia.org/wiki/Template:Kelantan > Townships
     * @see https://en.wikipedia.org/wiki/Template:Melaka > Townships
     * @see https://en.wikipedia.org/wiki/Template:Negeri_Sembilan > Townships
     * @see https://en.wikipedia.org/wiki/Template:Perak > Townships
     * @see https://en.wikipedia.org/wiki/Template:Penang > Townships
     * @see https://en.wikipedia.org/wiki/Template:Selangor > Townships
     * @see https://en.wikipedia.org/wiki/Template:Terengganu > Townships
     */
    protected static $townshipPrefix = [
        'Alam', 'Apartment', 'Ara',
        'Bandar', 'Bandar', 'Bandar', 'Bandar', 'Bandar', 'Bandar',
        'Bandar Bukit', 'Bandar Seri', 'Bandar Sri', 'Bandar Baru', 'Batu', 'Bukit',
        'Desa', 'Damansara',
        'Kampung', 'Kampung Baru', 'Kampung Baru', 'Kondominium', 'Kota',
        'Laman', 'Lembah',
        'Medan',
        'Pandan', 'Pangsapuri', 'Petaling', 'Puncak',
        'Seri', 'Sri',
        'Taman', 'Taman', 'Taman', 'Taman', 'Taman', 'Taman',
        'Taman Desa',
    ];
    protected static $townshipSuffix = [
        'Aman', 'Amanjaya', 'Anggerik', 'Angkasa', 'Antarabangsa', 'Awan',
        'Bahagia', 'Bangsar', 'Baru', 'Belakong', 'Bendahara', 'Bestari', 'Bintang', 'Brickfields',
        'Casa', 'Changkat', 'Country Heights',
        'Damansara', 'Damai', 'Dato Harun', 'Delima', 'Duta',
        'Flora',
        'Gembira', 'Genting',
        'Harmoni', 'Hartamas',
        'Impian', 'Indah', 'Intan',
        'Jasa', 'Jaya',
        'Keramat', 'Kerinchi', 'Kiara', 'Kinrara', 'Kuchai',
        'Laksamana',
        'Mahkota', 'Maluri', 'Manggis', 'Maxwell', 'Medan', 'Melawati', 'Menjalara', 'Meru', 'Mulia', 'Mutiara',
        'Pahlawan', 'Perdana', 'Pertama', 'Permai', 'Pelangi', 'Petaling', 'Pinang', 'Puchong', 'Puteri', 'Putra',
        'Rahman', 'Rahmat', 'Raya', 'Razak', 'Ria',
        'Saujana', 'Segambut', 'Selamat', 'Selatan', 'Semarak', 'Sentosa', 'Seputeh', 'Setapak', 'Setia Jaya', 'Sinar', 'Sungai Besi', 'Sungai Buaya', 'Sungai Long', 'Suria',
        'Tasik Puteri', 'Tengah', 'Timur', 'Tinggi', 'Tropika', 'Tun Hussein Onn', 'Tun Perak', 'Tunku',
        'Ulu', 'Utama', 'Utara',
        'Wangi',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Template:Greater_Kuala_Lumpur
     * @see https://en.wikipedia.org/wiki/Template:Johor
     * @see https://en.wikipedia.org/wiki/Template:Kedah
     * @see https://en.wikipedia.org/wiki/Template:Kelantan
     * @see https://en.wikipedia.org/wiki/Template:Labuan
     * @see https://en.wikipedia.org/wiki/Template:Melaka
     * @see https://en.wikipedia.org/wiki/Template:Negeri_Sembilan
     * @see https://en.wikipedia.org/wiki/Template:Pahang
     * @see https://en.wikipedia.org/wiki/Template:Perak
     * @see https://en.wikipedia.org/wiki/Template:Perlis
     * @see https://en.wikipedia.org/wiki/Template:Penang
     * @see https://en.wikipedia.org/wiki/Template:Sabah
     * @see https://en.wikipedia.org/wiki/Template:Sarawak
     * @see https://en.wikipedia.org/wiki/Template:Selangor
     * @see https://en.wikipedia.org/wiki/Template:Terengganu
     */
    protected static $towns = [
        'johor' => [
            'Ayer Hitam',
            'Batu Pahat', 'Bukit Gambir', 'Bukit Kepong', 'Bukit Naning',
            'Desaru',
            'Endau',
            'Gelang Patah', 'Gemas Baharu',
            'Iskandar Puteri',
            'Jementah', 'Johor Lama', 'Johor Bahru',
            'Kempas', 'Kluang', 'Kota Iskandar', 'Kota Tinggi', 'Kukup', 'Kulai',
            'Labis ', 'Larkin', 'Layang-Layang',
            'Mersing', 'Muar',
            'Pagoh', 'Paloh', 'Parit Jawa', 'Pasir Gudang', 'Pekan Nanas', 'Permas Jaya', 'Pontian Kechil',
            'Renggam',
            'Segamat', 'Senai', 'Simpang Renggam', 'Skudai', 'Sri Gading',
            'Tangkak', 'Tebrau',
            'Ulu Tiram',
            'Yong Peng',
        ],
        'kedah' => [
            'Alor Setar',
            'Baling', 'Bukit Kayu Hitam',
            'Changlun',
            'Durian Burung',
            'Gurun',
            'Jitra',
            'Kepala Batas', 'Kuah', 'Kuala Kedah', 'Kuala Ketil', 'Kulim',
            'Langgar', 'Lunas',
            'Merbok',
            'Padang Serai', 'Pendang',
            'Serdang', 'Sintok', 'Sungai Petani',
            'Tawar, Baling',
            'Yan',
        ],
        'kelantan' => [
            'Bachok', 'Bunut Payong',
            'Dabong',
            'Gua Musang',
            'Jeli',
            'Ketereh', 'Kota Bharu', 'Kuala Krai',
            'Lojing',
            'Machang',
            'Pasir Mas', 'Pasir Puteh',
            'Rantau Panjang',
            'Salor',
            'Tok Bali',
            'Wakaf Bharu', 'Wakaf Che Yeh',
        ],
        'kl' => [
            'Ampang',
            'Bandar Tasik Selatan', 'Bandar Tun Razak', 'Bangsar', 'Batu', 'Brickfields', 'Bukit Bintang', 'Bukit Jalil', 'Bukit Tunku',
            'Cheras', 'Chow Kit',
            'Damansara Town Centre', 'Dang Wangi', 'Desa Petaling', 'Desa Tun Hussein Onn',
            'Jinjang',
            'Kampung Baru', 'Kampung Kasipillay', 'Kampung Pandan', 'Kampung Sungai Penchala', 'Kepong', 'KLCC', 'Kuchai Lama',
            'Lake Gardens', 'Lembah Pantai',
            'Medan Tuanku', 'Mid Valley City', 'Mont Kiara',
            'Pantai Dalam', 'Pudu',
            'Salak South', 'Segambut', 'Semarak', 'Sentul', 'Setapak', 'Setiawangsa', 'Seputeh', 'Sri Hartamas', 'Sri Petaling', 'Sungai Besi',
            'Taman Desa', 'Taman Melawati', 'Taman OUG', 'Taman Tun Dr Ismail', 'Taman U-Thant', 'Taman Wahyu', 'Titiwangsa', 'Tun Razak Exchange',
            'Wangsa Maju',
        ],
        'labuan' => [
            'Batu Manikar',
            'Kiamsam',
            'Layang-Layang',
            'Rancha-Rancha',
        ],
        'melaka' => [
            'Alor Gajah',
            'Bandaraya Melaka', 'Batu Berendam', 'Bukit Beruang', 'Bukit Katil',
            'Cheng',
            'Durian Tunggal',
            'Hang Tuah Jaya',
            'Jasin',
            'Klebang',
            'Lubuk China',
            'Masjid Tanah',
            'Naning',
            'Pekan Asahan',
            'Ramuan China',
            'Simpang Ampat',
            'Tanjung Bidara', 'Telok Mas',
            'Umbai',
        ],
        'nsembilan' => [
            'Ayer Kuning', 'Ampangan',
            'Bahau', 'Batang Benar',
            'Chembong',
            'Dangi',
            'Gemas',
            'Juasseh',
            'Kuala Pilah',
            'Labu', 'Lenggeng', 'Linggi',
            'Mantin',
            'Nilai',
            'Pajam', 'Pedas', 'Pengkalan Kempas', 'Port Dickson',
            'Rantau', 'Rompin',
            'Senawang', 'Seremban', 'Sungai Gadut',
            'Tampin', 'Tiroi',
        ],
        'pahang' => [
            'Bandar Tun Razak', 'Bentong', 'Brinchang', 'Bukit Fraser', 'Bukit Tinggi',
            'Chendor',
            'Gambang', 'Genting Highlands', 'Genting Sempah',
            'Jerantut',
            'Karak', 'Kemayan', 'Kota Shahbandar', 'Kuala Lipis', 'Kuala Pahang', 'Kuala Rompin', 'Kuantan',
            'Lanchang', 'Lubuk Paku',
            'Maran', 'Mengkuang', 'Mentakab',
            'Nenasi',
            'Panching',
            'Pekan', 'Penor',
            'Raub',
            'Sebertak', 'Sungai Lembing',
            'Tanah Rata', 'Tanjung Sepat', 'Tasik Chini', 'Temerloh', 'Teriang', 'Tringkap',
        ],
        'penang' => [
            'Air Itam',
            'Balik Pulau', 'Batu Ferringhi', 'Batu Kawan', 'Bayan Lepas', 'Bukit Mertajam', 'Butterworth',
            'Gelugor', 'George Town',
            'Jelutong',
            'Kepala Batas',
            'Nibong Tebal',
            'Permatang Pauh', 'Pulau Tikus',
            'Simpang Ampat',
            'Tanjung Bungah', 'Tanjung Tokong',
        ],
        'perak' => [
            'Ayer Tawar',
            'Bagan Serai', 'Batu Gajah', 'Behrang', 'Bidor', 'Bukit Gantang', 'Bukit Merah',
            'Changkat Jering', 'Chemor', 'Chenderiang',
            'Damar Laut',
            'Gerik', 'Gopeng', 'Gua Tempurung',
            'Hutan Melintang',
            'Ipoh',
            'Jelapang',
            'Kamunting', 'Kampar', 'Kuala Kangsar',
            'Lekir', 'Lenggong', 'Lumut',
            'Malim Nawar', 'Manong', 'Menglembu',
            'Pantai Remis', 'Parit', 'Parit Buntar', 'Pasir Salak', 'Proton City',
            'Simpang Pulai', 'Sitiawan', 'Slim River', 'Sungai Siput', 'Sungkai',
            'Taiping', 'Tambun', 'Tanjung Malim', 'Tanjung Rambutan', 'Tapah', 'Teluk Intan',
            'Ulu Bernam',
        ],
        'perlis' => [
            'Arau',
            'Beseri',
            'Chuping',
            'Kaki Bukit', 'Kangar', 'Kuala Perlis',
            'Mata Ayer',
            'Padang Besar',
            'Sanglang', 'Simpang Empat',
            'Wang Kelian',
        ],
        'putrajaya' => [
            'Precinct 1', 'Precinct 4', 'Precinct 5',
            'Precinct 6', 'Precinct 8', 'Precinct 10',
            'Precinct 11', 'Precinct 12', 'Precinct 13',
            'Precinct 16', 'Precinct 18', 'Precinct 19',
        ],
        'sabah' => [
            'Beaufort', 'Bingkor',
            'Donggongon',
            'Inanam',
            'Kinabatangan', 'Kota Belud', 'Kota Kinabalu', 'Kuala Penyu', 'Kimanis', 'Kundasang',
            'Lahad Datu', 'Likas', 'Lok Kawi',
            'Manggatal',
            'Nabawan',
            'Papar', 'Pitas',
            'Ranau',
            'Sandakan', 'Sapulut', 'Semporna', 'Sepanggar',
            'Tambunan', 'Tanjung Aru', 'Tawau', 'Tenom', 'Tuaran',
            'Weston',
        ],
        'sarawak' => [
            'Asajaya',
            'Ba\'kelalan', 'Bario', 'Batu Kawa', 'Batu Niah', 'Betong', 'Bintulu',
            'Dalat', 'Daro',
            'Engkilili',
            'Julau',
            'Kapit', 'Kota Samarahan', 'Kuching',
            'Lawas', 'Limbang', 'Lubok Antu',
            'Marudi', 'Matu', 'Miri',
            'Oya',
            'Pakan',
            'Sadong Jaya', 'Sematan', 'Sibu', 'Siburan', 'Song', 'Sri Aman', 'Sungai Tujoh',
            'Tanjung Kidurong', 'Tanjung Manis', 'Tatau',
        ],
        'selangor' => [
            'Ampang', 'Assam Jawa',
            'Balakong', 'Bandar Baru Bangi', 'Bandar Baru Selayang', 'Bandar Sunway', 'Bangi', 'Banting', 'Batang Kali', 'Batu Caves', 'Bestari Jaya', 'Bukit Lanjan',
            'Cheras', 'Cyberjaya',
            'Damansara', 'Dengkil',
            'Ijok',
            'Jenjarom',
            'Kajang', 'Kelana Jaya', 'Klang', 'Kuala Kubu Bharu', 'Kuala Selangor', 'Kuang',
            'Lagong',
            'Morib',
            'Pandamaran', 'Paya Jaras', 'Petaling Jaya', 'Port Klang', 'Puchong',
            'Rasa', 'Rawang',
            'Salak Tinggi', 'Sekinchan', 'Selayang', 'Semenyih', 'Sepang', 'Serendah', 'Seri Kembangan', 'Shah Alam', 'Subang', 'Subang Jaya', 'Sungai Buloh',
            'Tanjung Karang', 'Tanjung Sepat',
            'Ulu Klang', 'Ulu Yam',
        ],
        'terengganu' => [
            'Ajil',
            'Bandar Ketengah Jaya', 'Bandar Permaisuri', 'Bukit Besi', 'Bukit Payong',
            'Chukai',
            'Jerteh',
            'Kampung Raja', 'Kerteh', 'Kijal', 'Kuala Besut', 'Kuala Berang', 'Kuala Dungun', 'Kuala Terengganu',
            'Marang', 'Merchang',
            'Pasir Raja',
            'Rantau Abang',
            'Teluk Kalung',
            'Wakaf Tapai',
        ],
    ];

    /**
     * @see https://en.wikipedia.org/wiki/States_and_federal_territories_of_Malaysia
     */
    protected static $states = [
        'johor' => [
            'Johor Darul Ta\'zim',
            'Johor',
        ],
        'kedah' => [
            'Kedah Darul Aman',
            'Kedah',
        ],
        'kelantan' => [
            'Kelantan Darul Naim',
            'Kelantan',
        ],
        'kl' => [
            'KL',
            'Kuala Lumpur',
            'WP Kuala Lumpur',
        ],
        'labuan' => [
            'Labuan',
        ],
        'melaka' => [
            'Malacca',
            'Melaka',
        ],
        'nsembilan' => [
            'Negeri Sembilan Darul Khusus',
            'Negeri Sembilan',
        ],
        'pahang' => [
            'Pahang Darul Makmur',
            'Pahang',
        ],
        'penang' => [
            'Penang',
            'Pulau Pinang',
        ],
        'perak' => [
            'Perak Darul Ridzuan',
            'Perak',
        ],
        'perlis' => [
            'Perlis Indera Kayangan',
            'Perlis',
        ],
        'putrajaya' => [
            'Putrajaya',
        ],
        'sabah' => [
            'Sabah',
        ],
        'sarawak' => [
            'Sarawak',
        ],
        'selangor' => [
            'Selangor Darul Ehsan',
            'Selangor',
        ],
        'terengganu' => [
            'Terengganu Darul Iman',
            'Terengganu',
        ],
    ];

    /**
     * @see https://ms.wikipedia.org/wiki/Senarai_negara_berdaulat
     */
    protected static $country = [
        'Abkhazia', 'Afghanistan', 'Afrika Selatan', 'Republik Afrika Tengah', 'Akrotiri dan Dhekelia', 'Albania', 'Algeria', 'Amerika Syarikat', 'Andorra', 'Angola', 'Antigua dan Barbuda', 'Arab Saudi', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belanda', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bonaire', 'Bosnia dan Herzegovina', 'Botswana', 'Brazil', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Cameroon', 'Chad', 'Chile', 'Republik Rakyat China', 'Republik China di Taiwan', 'Colombia', 'Comoros', 'Republik Demokratik Congo', 'Republik Congo', 'Kepulauan Cook', 'Costa Rica', 'Côte d\'Ivoire (Ivory Coast)', 'Croatia', 'Cuba', 'Curaçao', 'Cyprus', 'Republik Turki Cyprus Utara', 'Republik Czech',
        'Denmark', 'Djibouti', 'Dominika', 'Republik Dominika',
        'Ecuador', 'El Salvador', 'Emiriah Arab Bersatu', 'Eritrea', 'Estonia',
        'Kepulauan Faroe', 'Fiji', 'Filipina', 'Finland',
        'Gabon', 'Gambia', 'Georgia', 'Ghana', 'Grenada', 'Greece (Yunani)', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guinea Khatulistiwa', 'Guiana Perancis', 'Guyana',
        'Habsyah (Etiopia)', 'Haiti', 'Honduras', 'Hungary',
        'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Itali',
        'Jamaika', 'Jepun', 'Jerman', 'Jordan',
        'Kanada', 'Kazakhstan', 'Kemboja', 'Kenya', 'Kiribati', 'Korea Selatan', 'Korea Utara', 'Kosovo', 'Kuwait', 'Kyrgyzstan',
        'Laos', 'Latvia', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Lubnan', 'Luxembourg',
        'Macedonia', 'Madagaskar', 'Maghribi', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Kepulauan Marshall', 'Mauritania', 'Mauritius', 'Mesir', 'Mexico', 'Persekutuan Micronesia', 'Moldova', 'Monaco', 'Montenegro', 'Mongolia', 'Mozambique', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norway',
        'Oman', 'Ossetia Selatan',
        'Pakistan', 'Palau', 'Palestin', 'Panama', 'Papua New Guinea', 'Paraguay', 'Perancis', 'Peru', 'Poland', 'Portugal',
        'Qatar',
        'Romania', 'Russia', 'Rwanda',
        'Sahara Barat', 'Saint Kitts dan Nevis', 'Saint Lucia', 'Saint Vincent dan Grenadines', 'Samoa', 'San Marino', 'São Tomé dan Príncipe', 'Scotland', 'Senegal', 'Sepanyol', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapura', 'Slovakia', 'Slovenia', 'Kepulauan Solomon', 'Somalia', 'Somaliland', 'Sri Lanka', 'Sudan', 'Sudan Selatan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syria',
        'Tajikistan', 'Tanjung Verde', 'Tanzania', 'Thailand', 'Timor Leste', 'Togo', 'Tonga', 'Transnistria', 'Trinidad dan Tobago', 'Tunisia', 'Turki', 'Turkmenistan', 'Tuvalu',
        'Uganda', 'Ukraine', 'United Kingdom', 'Uruguay', 'Uzbekistan',
        'Vanuatu', 'Kota Vatican', 'Venezuela', 'Vietnam',
        'Yaman',
        'Zambia', 'Zimbabwe',
    ];

    /**
     * Return a building prefix
     *
     * @example 'No.'
     *
     * @return string
     */
    public static function buildingPrefix()
    {
        return static::randomElement(static::$buildingPrefix);
    }

    /**
     * Return a building number
     *
     * @example '123'
     *
     * @return string
     */
    public static function buildingNumber()
    {
        return static::toUpper(static::lexify(static::numerify(static::randomElement(static::$buildingNumber))));
    }

    /**
     * Return a street prefix
     *
     * @example 'Jalan'
     */
    public function streetPrefix()
    {
        $format = static::randomElement(static::$streetPrefix);

        return $this->generator->parse($format);
    }

    /**
     * Return a complete streename
     *
     * @example 'Jalan Utama 7'
     *
     * @return string
     */
    public function streetName()
    {
        $format = static::toUpper(static::lexify(static::numerify(static::randomElement(static::$streetNameFormats))));

        return $this->generator->parse($format);
    }

    /**
     * Return a randown township
     *
     * @example Taman Bahagia
     *
     * @return string
     */
    public function township()
    {
        $format = static::toUpper(static::lexify(static::numerify(static::randomElement(static::$townshipFormats))));

        return $this->generator->parse($format);
    }

    /**
     * Return a township prefix abbreviation
     *
     * @example 'USJ'
     *
     * @return string
     */
    public function townshipPrefixAbbr()
    {
        return static::randomElement(static::$townshipPrefixAbbr);
    }

    /**
     * Return a township prefix
     *
     * @example 'Taman'
     *
     * @return string
     */
    public function townshipPrefix()
    {
        return static::randomElement(static::$townshipPrefix);
    }

    /**
     * Return a township suffix
     *
     * @example 'Bahagia'
     */
    public function townshipSuffix()
    {
        return static::randomElement(static::$townshipSuffix);
    }

    /**
     * Return a postcode based on state
     *
     * @example '55100'
     *
     * @see https://en.wikipedia.org/wiki/Postal_codes_in_Malaysia#States
     *
     * @param string|null $state 'state' or null
     *
     * @return string
     */
    public static function postcode($state = null)
    {
        $format = [
            'perlis' => [ // (01000 - 02800)
                '0' . self::numberBetween(1000, 2800),
            ],
            'kedah' => [ // (05000 - 09810)
                '0' . self::numberBetween(5000, 9810),
            ],
            'penang' => [ // (10000 - 14400)
                self::numberBetween(10000, 14400),
            ],
            'kelantan' => [ // (15000 - 18500)
                self::numberBetween(15000, 18500),
            ],
            'terengganu' => [ // (20000 - 24300)
                self::numberBetween(20000, 24300),
            ],
            'pahang' => [ // (25000 - 28800 | 39000 - 39200 | 49000, 69000)
                self::numberBetween(25000, 28800),
                self::numberBetween(39000, 39200),
                self::numberBetween(49000, 69000),
            ],
            'perak' => [ // (30000 - 36810)
                self::numberBetween(30000, 36810),
            ],
            'selangor' => [ // (40000 - 48300 | 63000 - 68100)
                self::numberBetween(40000, 48300),
                self::numberBetween(63000, 68100),
            ],
            'kl' => [ // (50000 - 60000)
                self::numberBetween(50000, 60000),
            ],
            'putrajaya' => [ // (62000 - 62988)
                self::numberBetween(62000, 62988),
            ],
            'nsembilan' => [ // (70000 - 73509)
                self::numberBetween(70000, 73509),
            ],
            'melaka' => [ // (75000 - 78309)
                self::numberBetween(75000, 78309),
            ],
            'johor' => [ // (79000 - 86900)
                self::numberBetween(79000, 86900),
            ],
            'labuan' => [ // (87000 - 87033)
                self::numberBetween(87000, 87033),
            ],
            'sabah' => [ // (88000 - 91309)
                self::numberBetween(88000, 91309),
            ],
            'sarawak' => [ // (93000 - 98859)
                self::numberBetween(93000, 98859),
            ],
        ];

        $postcode = null === $state ? static::randomElement($format) : $format[$state];

        return (string) static::randomElement($postcode);
    }

    /**
     * Return the complete town address with matching postcode and state
     *
     * @example 55100 Bukit Bintang, Kuala Lumpur
     *
     * @return string
     */
    public function townState()
    {
        $state = static::randomElement(array_keys(static::$states));
        $postcode = static::postcode($state);
        $town = static::randomElement(static::$towns[$state]);
        $state = static::randomElement(static::$states[$state]);

        return $postcode . ' ' . $town . ', ' . $state;
    }

    /**
     * Return a random city (town)
     *
     * @example 'Ampang'
     *
     * @return string
     */
    public function city()
    {
        $state = static::randomElement(array_keys(static::$towns));

        return static::randomElement(static::$towns[$state]);
    }

    /**
     * Return a random state
     *
     * @example 'Johor'
     *
     * @return string
     */
    public function state()
    {
        $state = static::randomElement(array_keys(static::$states));

        return static::randomElement(static::$states[$state]);
    }
}

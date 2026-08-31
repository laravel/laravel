<?php

namespace Faker\Provider\id_ID;

class Person extends \Faker\Provider\Person
{
    protected static $lastNameFormat = [
        '{{lastNameMale}}',
        '{{lastNameFemale}}',
    ];

    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}} {{suffix}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastNameMale}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}} {{suffix}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastNameFemale}} {{suffix}}',
    ];

    /**
     * @see http://www.nama.web.id/search.php?gender=male&origin=Indonesia+-+Jawa&letter=&submit=Search
     */
    protected static $firstNameMale = [
        'Abyasa', 'Ade', 'Adhiarja', 'Adiarja', 'Adika', 'Adikara', 'Adinata',
        'Aditya', 'Agus', 'Ajiman', 'Ajimat', 'Ajimin', 'Ajiono', 'Akarsana',
        'Alambana', 'Among', 'Anggabaya', 'Anom', 'Argono', 'Aris', 'Arta',
        'Artanto', 'Artawan', 'Arsipatra', 'Asirwada', 'Asirwanda', 'Aslijan',
        'Asmadi', 'Asman', 'Asmianto', 'Asmuni', 'Aswani', 'Atma', 'Atmaja',
        'Bagas', 'Bagiya', 'Bagus', 'Bagya', 'Bahuraksa', 'Bahuwarna',
        'Bahuwirya', 'Bajragin', 'Bakda', 'Bakiadi', 'Bakianto', 'Bakidin',
        'Bakijan', 'Bakiman', 'Bakiono', 'Bakti', 'Baktiadi', 'Baktianto',
        'Baktiono', 'Bala', 'Balamantri', 'Balangga', 'Balapati', 'Balidin',
        'Balijan', 'Bambang', 'Banara', 'Banawa', 'Banawi', 'Bancar', 'Budi',
        'Cagak', 'Cager', 'Cahyadi', 'Cahyanto', 'Cahya', 'Cahyo', 'Cahyono',
        'Caket', 'Cakrabirawa', 'Cakrabuana', 'Cakrajiya', 'Cakrawala',
        'Cakrawangsa', 'Candra', 'Chandra', 'Candrakanta', 'Capa', 'Caraka',
        'Carub', 'Catur', 'Caturangga', 'Cawisadi', 'Cawisono', 'Cawuk',
        'Cayadi', 'Cecep', 'Cemani', 'Cemeti', 'Cemplunk', 'Cengkal', 'Cengkir',
        'Dacin', 'Dadap', 'Dadi', 'Dagel', 'Daliman', 'Dalimin', 'Daliono',
        'Damar', 'Damu', 'Danang', 'Daniswara', 'Danu', 'Danuja', 'Dariati',
        'Darijan', 'Darimin', 'Darmaji', 'Darman', 'Darmana', 'Darmanto',
        'Darsirah', 'Dartono', 'Daru', 'Daruna', 'Daryani', 'Dasa', 'Digdaya',
        'Dimas', 'Dimaz', 'Dipa', 'Dirja', 'Drajat', 'Dwi', 'Dono', 'Dodo',
        'Edi', 'Eka', 'Elon', 'Eluh', 'Eman', 'Emas', 'Embuh', 'Emong',
        'Empluk', 'Endra', 'Enteng', 'Estiawan', 'Estiono', 'Eko', 'Edi',
        'Edison', 'Edward', 'Elvin', 'Erik', 'Emil', 'Ega', 'Emin', 'Eja',
        'Gada', 'Gadang', 'Gaduh', 'Gaiman', 'Galak', 'Galang', 'Galar',
        'Galih', 'Galiono', 'Galuh', 'Galur', 'Gaman', 'Gamani', 'Gamanto',
        'Gambira', 'Gamblang', 'Ganda', 'Gandewa', 'Gandi', 'Gandi', 'Ganep',
        'Gangsa', 'Gangsar', 'Ganjaran', 'Gantar', 'Gara', 'Garan', 'Garang',
        'Garda', 'Gatot', 'Gatra', 'Gilang', 'Galih', 'Ghani', 'Gading',
        'Hairyanto', 'Hardana', 'Hardi', 'Harimurti', 'Harja', 'Harjasa',
        'Harjaya', 'Harjo', 'Harsana', 'Harsanto', 'Harsaya', 'Hartaka',
        'Hartana', 'Harto', 'Hasta', 'Heru', 'Himawan', 'Hadi', 'Halim',
        'Hasim', 'Hasan', 'Hendra', 'Hendri', 'Heryanto', 'Hamzah', 'Hari',
        'Imam', 'Indra', 'Irwan', 'Irsad', 'Ikhsan', 'Irfan', 'Ian', 'Ibrahim',
        'Ibrani', 'Ismail', 'Irnanto', 'Ilyas', 'Ibun', 'Ivan', 'Ikin', 'Ihsan',
        'Jabal', 'Jaeman', 'Jaga', 'Jagapati', 'Jagaraga', 'Jail', 'Jaiman',
        'Jaka', 'Jarwa', 'Jarwadi', 'Jarwi', 'Jasmani', 'Jaswadi', 'Jati',
        'Jatmiko', 'Jaya', 'Jayadi', 'Jayeng', 'Jinawi', 'Jindra', 'Joko',
        'Jumadi', 'Jumari', 'Jamal', 'Jamil', 'Jais', 'Jefri', 'Johan', 'Jono',
        'Kacung', 'Kajen', 'Kambali', 'Kamidin', 'Kariman', 'Karja', 'Karma',
        'Karman', 'Karna', 'Karsa', 'Karsana', 'Karta', 'Kasiran', 'Kasusra',
        'Kawaca', 'Kawaya', 'Kayun', 'Kemba', 'Kenari', 'Kenes', 'Kuncara',
        'Kunthara', 'Kusuma', 'Kadir', 'Kala', 'Kalim', 'Kurnia', 'Kanda',
        'Kardi', 'Karya', 'Kasim', 'Kairav', 'Kenzie', 'Kemal', 'Kamal', 'Koko',
        'Labuh', 'Laksana', 'Lamar', 'Lanang', 'Langgeng', 'Lanjar', 'Lantar',
        'Lega', 'Legawa', 'Lembah', 'Liman', 'Limar', 'Luhung', 'Lukita',
        'Luluh', 'Lulut', 'Lurhur', 'Luwar', 'Luwes', 'Latif', 'Lasmanto',
        'Lukman', 'Luthfi', 'Leo', 'Luis', 'Lutfan', 'Lasmono', 'Laswi',
        'Mahesa', 'Makara', 'Makuta', 'Manah', 'Maras', 'Margana', 'Mariadi',
        'Marsudi', 'Martaka', 'Martana', 'Martani', 'Marwata', 'Maryadi',
        'Maryanto', 'Mitra', 'Mujur', 'Mulya', 'Mulyanto', 'Mulyono', 'Mumpuni',
        'Muni', 'Mursita', 'Murti', 'Mustika', 'Maman', 'Mahmud', 'Mahdi',
        'Mahfud', 'Malik', 'Muhammad', 'Mustofa', 'Marsito', 'Mursinin',
        'Nalar', 'Naradi', 'Nardi', 'Niyaga', 'Nrima', 'Nugraha', 'Nyana',
        'Narji', 'Nasab', 'Nasrullah', 'Nasim', 'Najib', 'Najam', 'Nyoman',
        'Olga', 'Ozy', 'Omar', 'Opan', 'Oskar', 'Oman', 'Okto', 'Okta', 'Opung',
        'Paiman', 'Panca', 'Pangeran', 'Pangestu', 'Pardi', 'Parman', 'Perkasa',
        'Praba', 'Prabu', 'Prabawa', 'Prabowo', 'Prakosa', 'Pranata', 'Pranawa',
        'Prasetya', 'Prasetyo', 'Prayitna', 'Prayoga', 'Prayogo', 'Purwadi',
        'Purwa', 'Purwanto', 'Panji', 'Pandu', 'Paiman', 'Prima', 'Putu',
        'Raden', 'Raditya', 'Raharja', 'Rama', 'Rangga', 'Reksa', 'Respati',
        'Rusman', 'Rosman', 'Rahmat', 'Rahman', 'Rendy', 'Reza', 'Rizki',
        'Ridwan', 'Rudi', 'Raden', 'Radit', 'Radika', 'Rafi', 'Rafid', 'Raihan',
        'Salman', 'Saadat', 'Saiful', 'Surya', 'Slamet', 'Samsul', 'Soleh',
        'Simon', 'Sabar', 'Sabri', 'Sidiq', 'Satya', 'Setya', 'Saka', 'Sakti',
        'Taswir', 'Tedi', 'Teddy', 'Taufan', 'Taufik', 'Tomi', 'Tasnim',
        'Teguh', 'Tasdik', 'Timbul', 'Tirta', 'Tirtayasa', 'Tri', 'Tugiman',
        'Umar', 'Usman', 'Uda', 'Umay', 'Unggul', 'Utama', 'Umaya', 'Upik',
        'Viktor', 'Vino', 'Vinsen', 'Vero', 'Vega', 'Viman', 'Virman',
        'Wahyu', 'Wira', 'Wisnu', 'Wadi', 'Wardi', 'Warji', 'Waluyo', 'Wakiman',
        'Wage', 'Wardaya', 'Warsa', 'Warsita', 'Warta', 'Wasis', 'Wawan',
        'Xanana', 'Yahya', 'Yusuf', 'Yosef', 'Yono', 'Yoga',
    ];

    /**
     * @see http://namafb.com/2010/08/12/top-1000-nama-populer-indonesia/
     */
    protected static $firstNameFemale = [
        'Ade', 'Agnes', 'Ajeng', 'Amalia', 'Anita', 'Ayu', 'Aisyah', 'Ana',
        'Ami', 'Ani', 'Azalea', 'Aurora', 'Alika', 'Anastasia', 'Amelia',
        'Almira', 'Bella', 'Betania', 'Belinda', 'Citra', 'Cindy', 'Chelsea',
        'Clara', 'Cornelia', 'Cinta', 'Cinthia', 'Ciaobella', 'Cici', 'Carla',
        'Calista', 'Devi', 'Dewi', 'Dian', 'Diah', 'Diana', 'Dina', 'Dinda',
        'Dalima', 'Eka', 'Eva', 'Endah', 'Elisa', 'Eli', 'Ella', 'Ellis',
        'Elma', 'Elvina', 'Fitria', 'Fitriani', 'Febi', 'Faizah', 'Farah',
        'Farhunnisa', 'Fathonah', 'Gabriella', 'Gasti', 'Gawati', 'Genta',
        'Ghaliyati', 'Gina', 'Gilda', 'Halima', 'Hesti', 'Hilda', 'Hafshah',
        'Hamima', 'Hana', 'Hani', 'Hasna', 'Humaira', 'Ika', 'Indah', 'Intan',
        'Irma', 'Icha', 'Ida', 'Ifa', 'Ilsa', 'Ina', 'Ira', 'Iriana', 'Jamalia',
        'Janet', 'Jane', 'Julia', 'Juli', 'Jessica', 'Jasmin', 'Jelita',
        'Kamaria', 'Kamila', 'Kani', 'Karen', 'Karimah', 'Kartika', 'Kasiyah',
        'Keisha', 'Kezia', 'Kiandra', 'Kayla', 'Kania', 'Lala', 'Lalita',
        'Latika', 'Laila', 'Laras', 'Lidya', 'Lili', 'Lintang', 'Maria', 'Mala',
        'Maya', 'Maida', 'Maimunah', 'Melinda', 'Mila', 'Mutia', 'Michelle',
        'Malika', 'Nadia', 'Nadine', 'Nabila', 'Natalia', 'Novi', 'Nova',
        'Nurul', 'Nilam', 'Najwa', 'Olivia', 'Ophelia', 'Oni', 'Oliva', 'Padma',
        'Putri', 'Paramita', 'Paris', 'Patricia', 'Paulin', 'Puput', 'Puji',
        'Pia', 'Puspa', 'Puti', 'Putri', 'Padmi', 'Qori', 'Queen', 'Ratih',
        'Ratna', 'Restu', 'Rini', 'Rika', 'Rina', 'Rahayu', 'Rahmi', 'Rachel',
        'Rahmi', 'Raisa', 'Raina', 'Sarah', 'Sari', 'Siti', 'Siska', 'Suci',
        'Syahrini', 'Septi', 'Sadina', 'Safina', 'Sakura', 'Salimah', 'Salwa',
        'Salsabila', 'Samiah', 'Shania', 'Sabrina', 'Silvia', 'Shakila',
        'Talia', 'Tami', 'Tira', 'Tiara', 'Titin', 'Tania', 'Tina', 'Tantri',
        'Tari', 'Titi', 'Uchita', 'Unjani', 'Ulya', 'Uli', 'Ulva', 'Umi',
        'Usyi', 'Vanya', 'Vanesa', 'Vivi', 'Vera', 'Vicky', 'Victoria',
        'Violet', 'Winda', 'Widya', 'Wulan', 'Wirda', 'Wani', 'Yani', 'Yessi',
        'Yulia', 'Yuliana', 'Yuni', 'Yunita', 'Yance', 'Zahra', 'Zalindra',
        'Zaenab', 'Zulfa', 'Zizi', 'Zulaikha', 'Zamira', 'Zelda', 'Zelaya',
    ];

    /**
     * @see http://namafb.com/2010/08/12/top-1000-nama-populer-indonesia/
     * @see http://id.wikipedia.org/wiki/Daftar_marga_suku_Batak_di_Toba
     */
    protected static $lastNameMale = [
        'Adriansyah', 'Ardianto', 'Anggriawan', 'Budiman', 'Budiyanto',
        'Damanik', 'Dongoran', 'Dabukke', 'Firmansyah', 'Firgantoro',
        'Gunarto', 'Gunawan', 'Hardiansyah', 'Habibi', 'Hakim', 'Halim',
        'Haryanto', 'Hidayat', 'Hidayanto', 'Hutagalung', 'Hutapea', 'Hutasoit',
        'Irawan', 'Iswahyudi', 'Kuswoyo', 'Januar', 'Jailani', 'Kurniawan',
        'Kusumo', 'Latupono', 'Lazuardi', 'Maheswara', 'Mahendra', 'Mustofa',
        'Mansur', 'Mandala', 'Megantara', 'Maulana', 'Maryadi', 'Mangunsong',
        'Manullang', 'Marpaung', 'Marbun', 'Narpati', 'Natsir', 'Nugroho',
        'Najmudin', 'Nashiruddin', 'Nainggolan', 'Nababan', 'Napitupulu',
        'Pangestu', 'Putra', 'Pranowo', 'Prabowo', 'Pratama', 'Prasetya',
        'Prasetyo', 'Pradana', 'Pradipta', 'Prakasa', 'Permadi', 'Prasasta',
        'Prayoga', 'Ramadan', 'Rajasa', 'Rajata', 'Saptono', 'Santoso',
        'Saputra', 'Saefullah', 'Setiawan', 'Suryono', 'Suwarno', 'Siregar',
        'Sihombing', 'Salahudin', 'Sihombing', 'Samosir', 'Saragih', 'Sihotang',
        'Simanjuntak', 'Sinaga', 'Simbolon', 'Sitompul', 'Sitorus', 'Sirait',
        'Siregar', 'Situmorang', 'Tampubolon', 'Thamrin', 'Tamba', 'Tarihoran',
        'Utama', 'Uwais', 'Wahyudin', 'Waluyo', 'Wibowo', 'Winarno', 'Wibisono',
        'Wijaya', 'Widodo', 'Wacana', 'Waskita', 'Wasita', 'Zulkarnain',
    ];

    /**
     * @see http://namafb.com/2010/08/12/top-1000-nama-populer-indonesia/
     */
    protected static $lastNameFemale = [
        'Agustina', 'Andriani', 'Anggraini', 'Aryani', 'Astuti',
        'Fujiati', 'Farida', 'Handayani', 'Hassanah', 'Hartati', 'Hasanah',
        'Haryanti', 'Hariyah', 'Hastuti', 'Halimah', 'Kusmawati', 'Kuswandari',
        'Laksmiwati', 'Laksita', 'Lestari', 'Lailasari', 'Mandasari',
        'Mardhiyah', 'Mayasari', 'Melani', 'Mulyani', 'Maryati', 'Nurdiyanti',
        'Novitasari', 'Nuraini', 'Nasyidah', 'Nasyiah', 'Namaga', 'Palastri',
        'Pudjiastuti', 'Puspasari', 'Puspita', 'Purwanti', 'Pratiwi',
        'Purnawati', 'Pertiwi', 'Permata', 'Prastuti', 'Padmasari', 'Rahmawati',
        'Rahayu', 'Riyanti', 'Rahimah', 'Suartini', 'Sudiati', 'Suryatmi',
        'Susanti', 'Safitri', 'Oktaviani', 'Utami', 'Usamah', 'Usada',
        'Uyainah', 'Yuniar', 'Yuliarti', 'Yulianti', 'Yolanda', 'Wahyuni',
        'Wijayanti', 'Widiastuti', 'Winarsih', 'Wulandari', 'Wastuti', 'Zulaika',
    ];

    /**
     * @see http://id.wikipedia.org/wiki/Gelar_akademik
     */
    protected static $titleMale = ['dr.', 'drg.', 'Dr.', 'Drs.', 'Ir.', 'H.'];

    /**
     * @see http://id.wikipedia.org/wiki/Gelar_akademik
     */
    protected static $titleFemale = ['dr.', 'drg.', 'Dr.', 'Hj.'];

    /**
     * @see http://informasipedia.com/wilayah-indonesia/daftar-kabupaten-kota-di-indonesia/
     */
    protected static $birthPlaceCode = [
        '1101', '1102', '1103', '1104', '1105', '1106', '1107', '1108', '1109', '1110', '1111', '1112', '1113', '1114', '1115', '1116',
        '1117', '1118', '1171', '1172', '1173', '1174', '1175', '1201', '1202', '1203', '1204', '1205', '1206', '1207', '1208', '1209',
        '1210', '1211', '1212', '1213', '1214', '1215', '1216', '1217', '1218', '1219', '1220', '1221', '1222', '1223', '1224', '1225',
        '1271', '1272', '1273', '1274', '1275', '1276', '1277', '1278', '1301', '1302', '1303', '1304', '1305', '1306', '1307', '1308',
        '1309', '1310', '1311', '1312', '1371', '1372', '1373', '1374', '1375', '1376', '1377', '1401', '1402', '1403', '1404', '1405',
        '1406', '1407', '1408', '1409', '1410', '1471', '1472', '1501', '1502', '1503', '1504', '1505', '1506', '1507', '1508', '1509',
        '1571', '1572', '1601', '1602', '1603', '1604', '1605', '1606', '1607', '1608', '1609', '1610', '1611', '1612', '1613', '1671',
        '1672', '1673', '1674', '1701', '1702', '1703', '1704', '1705', '1706', '1707', '1708', '1709', '1771', '1801', '1802', '1803',
        '1804', '1805', '1806', '1807', '1808', '1809', '1810', '1811', '1812', '1813', '1871', '1872', '1901', '1902', '1903', '1904',
        '1905', '1906', '1971', '2101', '2102', '2103', '2104', '2105', '2171', '2172', '3101', '3171', '3172', '3173', '3174', '3175',
        '3201', '3202', '3203', '3204', '3205', '3206', '3207', '3208', '3209', '3210', '3211', '3212', '3213', '3214', '3215', '3216',
        '3217', '3218', '3271', '3272', '3273', '3274', '3275', '3276', '3277', '3278', '3279', '3301', '3302', '3303', '3304', '3305',
        '3306', '3307', '3308', '3309', '3310', '3311', '3312', '3313', '3314', '3315', '3316', '3317', '3318', '3319', '3320', '3321',
        '3322', '3323', '3324', '3325', '3326', '3327', '3328', '3329', '3371', '3372', '3373', '3374', '3375', '3376', '3401', '3402',
        '3403', '3404', '3471', '3501', '3502', '3503', '3504', '3505', '3506', '3507', '3508', '3509', '3510', '3511', '3512', '3513',
        '3514', '3515', '3516', '3517', '3518', '3519', '3520', '3521', '3522', '3523', '3524', '3525', '3526', '3527', '3528', '3529',
        '3571', '3572', '3573', '3574', '3575', '3576', '3577', '3578', '3579', '3601', '3602', '3603', '3604', '3671', '3672', '3673',
        '3674', '5101', '5102', '5103', '5104', '5105', '5106', '5107', '5108', '5171', '5201', '5202', '5203', '5204', '5205', '5206',
        '5207', '5208', '5271', '5272', '5301', '5302', '5303', '5304', '5305', '5306', '5307', '5308', '5309', '5310', '5311', '5312',
        '5313', '5314', '5315', '5316', '5317', '5318', '5319', '5320', '5321', '5371', '6101', '6102', '6103', '6104', '6105', '6106',
        '6107', '6108', '6109', '6110', '6111', '6112', '6171', '6172', '6201', '6202', '6203', '6204', '6205', '6206', '6207', '6208',
        '6209', '6210', '6211', '6212', '6213', '6271', '6301', '6302', '6303', '6304', '6305', '6306', '6307', '6308', '6309', '6310',
        '6311', '6371', '6401', '6402', '6403', '6407', '6408', '6409', '6411', '6471', '6472', '6474', '6501', '6502', '6503', '6504',
        '6571', '7101', '7102', '7103', '7104', '7105', '7106', '7107', '7108', '7109', '7110', '7111', '7171', '7201', '7202', '7203',
        '7204', '7205', '7206', '7207', '7208', '7209', '7210', '7211', '7212', '7271', '7301', '7302', '7303', '7304', '7305', '7306',
        '7307', '7308', '7309', '7310', '7311', '7312', '7313', '7314', '7315', '7316', '7317', '7318', '7322', '7324', '7326', '7371',
        '7372', '7373', '7401', '7402', '7403', '7404', '7405', '7406', '7407', '7408', '7409', '7410', '7411', '7412', '7413', '7414',
        '7415', '7471', '7472', '7501', '7502', '7503', '7504', '7505', '7571', '7601', '7602', '7603', '7604', '7605', '7606', '8101',
        '8102', '8103', '8104', '8105', '8106', '8107', '8108', '8109', '8171', '8172', '8201', '8202', '8203', '8204', '8205', '8206',
        '8207', '8208', '8271', '8272', '9101', '9102', '9103', '9104', '9105', '9106', '9107', '9108', '9109', '9110', '9111', '9112',
        '9113', '9114', '9115', '9116', '9117', '9118', '9119', '9120', '9121', '9122', '9123', '9124', '9125', '9126', '9127', '9128',
        '9171', '9201', '9202', '9203', '9204', '9205', '9206', '9207', '9208', '9209', '9210', '9211', '9212', '9271',
    ];

    /**
     * For academic title
     *
     * @see http://id.wikipedia.org/wiki/Gelar_akademik
     */
    private static $suffix = ['S.Ked', 'S.Gz', 'S.Pt', 'S.IP', 'S.E.I',
        'S.E.', 'S.Kom', 'S.H.', 'S.T.', 'S.Pd', 'S.Psi', 'S.I.Kom',
        'S.Sos', 'S.Farm', 'M.M.', 'M.Kom.', 'M.TI.', 'M.Pd', 'M.Farm', 'M.Ak', ];

    /**
     * Return last name
     *
     * @param string|null $gender male or female or null for any
     *
     * @return string last name
     */
    public function lastName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::lastNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::lastNameFemale();
        }
        $lastNameRandomElement = static::randomElement(static::$lastNameFormat);

        return $this->generator->parse($lastNameRandomElement);
    }

    /**
     * Return last name for male
     *
     * @return string last name
     */
    public static function lastNameMale()
    {
        return static::randomElement(static::$lastNameMale);
    }

    /**
     * Return last name for female
     *
     * @return string last name
     */
    public static function lastNameFemale()
    {
        return static::randomElement(static::$lastNameFemale);
    }

    /**
     * For academic title
     *
     * @return string suffix
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * Generates Nomor Induk Kependudukan (NIK)
     *
     * @see https://en.wikipedia.org/wiki/National_identification_number#Indonesia
     *
     * @param string|null    $gender
     * @param \DateTime|null $birthDate
     *
     * @return string
     */
    public function nik($gender = null, $birthDate = null)
    {
        // generate first numbers (region data)
        $nik = $this->birthPlaceCode();
        $nik .= $this->generator->numerify('##');

        if (!$birthDate) {
            $birthDate = $this->generator->dateTimeBetween();
        }

        if (!$gender) {
            $gender = $this->generator->randomElement([self::GENDER_MALE, self::GENDER_FEMALE]);
        }

        // if gender is female, add 40 to days
        if ($gender == self::GENDER_FEMALE) {
            $nik .= $birthDate->format('d') + 40;
        } else {
            $nik .= $birthDate->format('d');
        }

        $nik .= $birthDate->format('my');

        // add last random digits
        $nik .= $this->generator->numerify('####');

        return $nik;
    }

    /**
     * Generates birth place code for NIK
     *
     * @see https://id.wikipedia.org/wiki/Nomor_Induk_Kependudukan
     * @see http://informasipedia.com/wilayah-indonesia/daftar-kabupaten-kota-di-indonesia/
     */
    protected function birthPlaceCode()
    {
        return static::randomElement(static::$birthPlaceCode);
    }
}

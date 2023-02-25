<?php

namespace Faker\Provider\id_ID;

class Color extends \Faker\Provider\Color
{
    /**
     * @see https://id.wikipedia.org/wiki/Warna
     * @see https://id.wikipedia.org/wiki/Kategori:Warna
     * @see https://id.wikipedia.org/wiki/Warna_tersier
     */
    protected static $safeColorNames = ['abu-abu', 'biru', 'biru dongker', 'biru laut', 'cokelat',
        'emas', 'hijau', 'hitam', 'jingga', 'krem', 'kuning', 'magenta', 'mawar', 'merah', 'merah jambu',
        'merah marun', 'nila', 'perak', 'putih', 'sepia', 'teal', 'toska', 'ungu', 'violet', 'zaitun',
    ];

    /**
     * @see https://id.wikipedia.org/wiki/Daftar_warna
     */
    protected static $allColorNames = [
        'Abu-Abu', 'Abu-Abu Tua', 'Abu-Abu Muda', 'Abu-Abu Kecokelatan', 'Almond',
        'Biru', 'Biru Baja', 'Biru Dongker', 'Biru Keabu-abuan', 'Biru Kehijauan', 'Biru Keunguan', 'Biru Laut', 'Biru Laut Gelap', 'Biru Laut Terang', 'Biru Langit', 'Biru Langit Muda', 'Biru Langit Tua', 'Biru Malam', 'Biru Muda', 'Biru Nilam', 'Biru Pucat', 'Biru Terang', 'Biru Tua',
        'Chiffon', 'Cokelat', 'Cokelat Gandum', 'Cokelat Keemasan', 'Cokelat Kekuningan', 'Cokelat Kemerahan', 'Cokelat Tua',
        'Delima',
        'Emas',
        'Hijau Abu-Abu', 'Hijau Botol', 'Hijau Cerah', 'Hijah Gelap', 'Hijau Hutan', 'Hijau Kebiruan', 'Hijau Kekuningan', 'Hijau Laut', 'Hijau Laut Gelap', 'Hijau Laut Terang', 'Hijau Lemon', 'Hijau Lumut', 'Hijau Muda', 'Hijau Muda Kekuningan', 'Hijau Neon', 'Hijau Pucat', 'Hijau Rumput', 'Hijau Tua', 'Hijau Zamrud', 'Hitam', 'Hitam Arang', 'Hitam Pekat',
        'Jingga', 'Jingga Labu', 'Jingga Muda', 'Jingga Tua',
        'Khaki', 'Khaki Tua', 'Koral', 'Koral Terang', 'Krem', 'Krimson', 'Kuning', 'Kuning Aprikot', 'Kuning Gelap', 'Kuning Jingga', 'Kuning Kehijauan', 'Kuning Kehijauan Pucat', 'Kuning Kecokelatan Tua', 'Kuning Lemon', 'Kuning Muda', 'Kuning Neon', 'Kuning Pucat', 'Kuning Terang', 'Kuning Sawo',
        'Lavender', 'Lemon', 'Lemon Chiffon',
        'Magenta', 'Magenta Gelap', 'Mawar', 'Merah', 'Merah Bata', 'Merah Indian', 'Merah Kekuning-Kuningan', 'Merah Keungu-Unguan', 'Merah Muda', 'Merah Muda Dakam', 'Merah Muda Kekuningan', 'Merah Muda Keunguan', 'Merah Muda Keunguan Pudar', 'Merah Muda Panas', 'Merah Muda Terang', 'Merah Oranye', 'Merah Tomat', 'Merah Tua', 'Merah Tua Terang', 'Moka',
        'Nila',
        'Oranye', 'Oranye Pepaya',
        'Pastel', 'Peach', 'Pelangi', 'Perak', 'Plum', 'Putih', 'Putih Gading', 'Putih Gandum', 'Putih Salju', 'Putih Terang',
        'Rambut Jagung',
        'Salmon', 'Salmon Gelap', 'Salmon Terang', 'Sawo', 'Sawo Matang',
        'Tembaga', 'Tomat',
        'Ungu', 'Ungu Gelap', 'Ungu Kebiruan', 'Ungu Kecokelatan', 'Ungu Lembayung', 'Ungu Lembayung Muda', 'Ungu Muda', 'Ungu Terong',
        'Zaitun', 'Zaitun Hijau Gelap',
    ];
}

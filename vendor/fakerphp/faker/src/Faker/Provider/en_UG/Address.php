<?php

namespace Faker\Provider\en_UG;

class Address extends \Faker\Provider\en_US\Address
{
    protected static $cityFormats = [
        '{{cityName}}',
    ];

    /**
     * @see http://en.wikipedia.org/wiki/List_of_cities_and_towns_in_Uganda
     */
    protected static $cityName = [
        'Alebtong', 'Abim', 'Adjumani', 'Amolatar', 'Amuria', 'Amuru', 'Apac', 'Arua',
        'Bombo', 'Budaka', 'Bugembe', 'Bugiri', 'Bukedea', 'Bulisa', 'Buikwe', 'Bundibugyo',
        'Busembatya', 'Bushenyi', 'Busia', 'Busolwe', 'Butaleja', 'Buwenge',
        'Dokolo',
        'Entebbe',
        'Fort Portal',
        'Gulu',
        'Hima', 'Hoima',
        'Ibanda', 'Iganga', 'Isingiro',
        'Jinja',
        'Kaabong', 'Kabale', 'Kaberamaido', 'Kabwohe', 'Kagadi', 'Kakinga', 'Kakiri', 'Kalangala', 'Kaliro',
        'Kalisizo', 'Kalongo', 'Kalungu', 'Kampala', 'Kamuli', 'Kanoni', 'Kamwenge', 'Kanungu', 'Kapchorwa',
        'Kasese', 'Katakwi', 'Kayunga', 'Kibaale', 'Kiboga', 'Kihiihi', 'Kiira', 'Kiruhura', 'Kiryandongo',
        'Kisoro', 'Kitgum', 'Koboko', 'Kotido', 'Kumi', 'Kyenjojo', 'Kyotera',
        'Lira', 'Lugazi', 'Lukaya', 'Luwero', 'Lwakhakha', 'Lwengo', 'Lyantonde',
        'Malaba', 'Manafwa', 'Masaka', 'Masindi', 'Masindi Port', 'Matugga', 'Mayuge', 'Mbale', 'Mbarara',
        'Mitooma', 'Mityana', 'Mpigi', 'Mpondwe', 'Moroto', 'Moyo', 'Mubende', 'Mukono', 'Mutukula',
        'Nagongera', 'Nakaseke', 'Nakasongola', 'Nakapiripirit', 'Namutumba', 'Nansana', 'Nebbi', 'Ngora',
        'Njeru', 'Nkokonjeru', 'Ntungamo',
        'Oyam',
        'Pader', 'Paidha', 'Pakwach', 'Pallisa',
        'Rakai', 'Rukungiri',
        'Sembabule', 'Sironko', 'Soroti',
        'Tororo',
        'Wakiso', 'Wobulenzi',
        'Yumbe',
    ];

    /**
     * @see http://en.wikipedia.org/wiki/Category:Districts_of_Uganda
     */
    protected static $district = [
        'Abim', 'Adjumani', 'Agago', 'Alebtong', 'Amolatar', 'Amudat', 'Amuria', 'Amuru', 'Apac', 'Arua',
        'Budaka', 'Bududa', 'Bugiri', 'Buhweju', 'Buikwe', 'Bukedea', 'Bukomansimbi', 'Bukwo', 'Bulambuli',
        'Buliisa', 'Bundibugyo', 'Bushenyi', 'Busia', 'Butaleja', 'Butambala', 'Buvuma', 'Buyende',
        'Dokolo',
        'Gomba', 'Gulu',
        'Hoima',
        'Ibanda', 'Iganga', 'Isingiro',
        'Jinja',
        'Kaabong', 'Kabale', 'Kabarole', 'Kaberamaido', 'Kalangala', 'Kaliro', 'Kalungu', 'Kampala', 'Kamuli',
        'Kamwenge', 'Kanungu', 'Kapchorwa', 'Kasese', 'Katakwi', 'Kayunga', 'Kibaale', 'Kiboga', 'Kibuku',
        'Kigezi', 'Kiruhura', 'Kiryandongo', 'Kisoro', 'Kitgum', 'Koboko', 'Kole', 'Kotido', 'Kumi', 'Kween',
        'Kyankwanzi', 'Kyegegwa', 'Kyenjojo',
        'Lamwo', 'Lira', 'Luuka', 'Luweero', 'Lwengo', 'Lyantonde',
        'Manafwa', 'Maracha', 'Maracha-Terego', 'Masaka', 'Masindi', 'Mayuge', 'Mbale', 'Mbarara', 'Mitooma',
        'Mityana', 'Moroto', 'Moyo', 'Mpigi', 'Mubende', 'Mukono',
        'Nakapiripirit', 'Nakaseke', 'Nakasongola', 'Namayingo', 'Namutumba', 'Napak', 'Nebbi', 'Ngora',
        'Ntoroko', 'Ntungamo', 'Nwoya',
        'Otuke', 'Oyam',
        'Pader', 'Pallisa',
        'Rakai', 'Rubirizi', 'Rukungiri',
        'Sembabule', 'Serere', 'Sheema', 'Sironko', 'Soroti',
        'Tororo',
        'Wakiso',
        'Yumbe',
        'Zombo',
    ];

    protected static $postcode = ['#', '##', '###', '####', '#####'];

    protected static $region = ['Central', 'East', 'North', 'West'];

    /**
     * @example 'Fort Portal'
     */
    public static function cityName()
    {
        return static::randomElement(static::$cityName);
    }

    /**
     * @example 'Kampala'
     */
    public static function district()
    {
        return static::randomElement(static::$district);
    }

    /**
     * @example 'East'
     */
    public static function region()
    {
        return static::randomElement(static::$region);
    }
}

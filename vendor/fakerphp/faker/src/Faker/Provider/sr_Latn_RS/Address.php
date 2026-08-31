<?php

namespace Faker\Provider\sr_Latn_RS;

class Address extends \Faker\Provider\Address
{
    protected static $postcode = ['#####'];

    protected static $streetPrefix = [
        'Bulevar',
    ];

    protected static $street = [
        'Kralja Milana', 'Cara Dušana', 'Nikole Tesle', 'Mihajla Pupina', 'Nikole Pašića',
    ];

    protected static $streetNameFormats = [
        '{{street}}',
        '{{streetPrefix}} {{street}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    /**
     * @see http://sr.wikipedia.org/sr-el/%D0%93%D1%80%D0%B0%D0%B4_%D1%83_%D0%A1%D1%80%D0%B1%D0%B8%D1%98%D0%B8
     */
    protected static $cityNames = [
        'Beograd', 'Valjevo', 'Vranje', 'Zaječar', 'Zrenjanin', 'Jagodina', 'Kragujevac', 'Kraljevo', 'Kruševac', 'Leskovac', 'Loznica', 'Niš', 'Novi Pazar', 'Novi Sad', 'Pančevo', 'Požarevac', 'Priština', 'Smederevo', 'Sombor', 'Sremska Mitrovica', 'Subotica', 'Užice', 'Čačak', 'Šabac',
    ];

    /**
     * @see https://github.com/umpirsky/country-list/blob/master/country/cldr/sr_Latn/country.php
     */
    protected static $country = [
        'Ostrvo Asension', 'Andora', 'Ujedinjeni Arapski Emirati', 'Avganistan', 'Antigva i Barbuda', 'Angvila', 'Albanija', 'Armenija', 'Holandski Antili', 'Angola', 'Antarktika', 'Argentina', 'Američka Samoa', 'Austrija', 'Australija', 'Aruba', 'Alandska ostrva', 'Azerbejdžan', 'Bosna i Hercegovina', 'Barbados', 'Bangladeš', 'Belgija', 'Burkina Faso', 'Bugarska', 'Bahrein', 'Burundi', 'Benin', 'Sv. Bartolomej', 'Bermuda', 'Brunej', 'Bolivija', 'Brazil', 'Bahami', 'Butan', 'Buve Ostrva', 'Bocvana', 'Belorusija', 'Belise', 'Kanada', 'Kokos (Keling) Ostrva', 'Demokratska Republika Kongo', 'Centralno Afrička Republika', 'Kongo', 'Švajcarska', 'Obala Slonovače', 'Kukova Ostrva', 'Čile', 'Kamerun', 'Kina', 'Kolumbija', 'Ostrvo Kliperton', 'Kostarika', 'Srbija i Crna Gora', 'Kuba', 'Kape Verde', 'Božićna Ostrva', 'Kipar', 'Češka', 'Nemačka', 'Dijego Garsija', 'Džibuti', 'Danska', 'Dominika', 'Dominikanska Republika', 'Alžir', 'Seuta i Melilja', 'Ekvador', 'Estonija', 'Egipat', 'Zapadna Sahara', 'Eritreja', 'Španija', 'Etiopija', 'Evropska unija', 'Finska', 'Fidži', 'Folklandska Ostrva', 'Mikronezija', 'Farska Ostrva', 'Francuska', 'Gabon', 'Velika Britanija', 'Grenada', 'Gruzija', 'Francuska Gvajana', 'Gurnsi', 'Gana', 'Gibraltar', 'Grenland', 'Gambija', 'Gvineja', 'Gvadelupe', 'Ekvatorijalna Gvineja', 'Grčka', 'Južna Džordžija i Južna Sendvič Ostrva', 'Gvatemala', 'Guam', 'Gvineja-Bisao', 'Gvajana', 'Hong Kong (S. A. R. Kina)', 'Herd i Mekdonald Ostrva', 'Honduras', 'Hrvatska', 'Haiti', 'Mađarska', 'Kanarska ostrva', 'Indonezija', 'Irska', 'Izrael', 'Ostrvo Man', 'Indija', 'Britansko Indijska Okeanska Teritorija', 'Irak', 'Iran', 'Island', 'Italija', 'Džersi', 'Jamajka', 'Jordan', 'Japan', 'Kenija', 'Kirgizstan', 'Kambodža', 'Kiribati', 'Komorska Ostrva', 'Sent Kits i Nevis', 'Severna Koreja', 'Južna Koreja', 'Kuvajt', 'Kajmanska Ostrva', 'Kazahstan', 'Laos', 'Liban', 'Sent Lucija', 'Lihtenštajn', 'Šri Lanka', 'Liberija', 'Lesoto', 'Litvanija', 'Luksemburg', 'Letonija', 'Libija', 'Maroko', 'Monako', 'Moldavija', 'Crna Gora', 'Sv. Martin', 'Madagaskar', 'Maršalska Ostrva', 'Makedonija', 'Mali', 'Mijanmar', 'Mongolija', 'Makao (S. A. R. Kina)', 'Severna Marijanska Ostrva', 'Martinik', 'Mauritanija', 'Monserat', 'Malta', 'Mauricius', 'Maldivi', 'Malavi', 'Meksiko', 'Malezija', 'Mozambik', 'Namibija', 'Nova Kaledonija', 'Niger', 'Norfolk Ostrvo', 'Nigerija', 'Nikaragva', 'Holandija', 'Norveška', 'Nepal', 'Nauru', 'Niue', 'Novi Zeland', 'Oman', 'Panama', 'Peru', 'Francuska Polinezija', 'Papua Nova Gvineja', 'Filipini', 'Pakistan', 'Poljska', 'Sen Pjer i Mikelon', 'Pitcairn', 'Porto Riko', 'Palestinska Teritorija', 'Portugal', 'Palau', 'Paragvaj', 'Katar', 'Ostala okeanija', 'Rejunion', 'Rumunija', 'Srbija', 'Rusija', 'Ruanda', 'Saudijska Arabija', 'Solomonska Ostrva', 'Sejšeli', 'Sudan', 'Švedska', 'Singapur', 'Sveta Jelena', 'Slovenija', 'Svalbard i Janmajen Ostrva', 'Slovačka', 'Sijera Leone', 'San Marino', 'Senegal', 'Somalija', 'Surinam', 'Sao Tome i Principe', 'Salvador', 'Sirija', 'Svazilend', 'Tristan da Kunja', 'Turks i Kajkos Ostrva', 'Čad', 'Francuske Južne Teritorije', 'Togo', 'Tajland', 'Tadžikistan', 'Tokelau', 'Istočni Timor', 'Turkmenistan', 'Tunis', 'Tonga', 'Turska', 'Trinidad i Tobago', 'Tuvalu', 'Tajvan', 'Tanzanija', 'Ukrajina', 'Uganda', 'Manja Udaljena Ostrva SAD', 'Sjedinjene Američke Države', 'Urugvaj', 'Uzbekistan', 'Vatikan', 'Sent Vinsent i Grenadini', 'Venecuela', 'Britanska Devičanska Ostrva', 'S.A.D. Devičanska Ostrva', 'Vijetnam', 'Vanuatu', 'Valis i Futuna Ostrva', 'Samoa', 'Jemen', 'Majote', 'Južnoafrička Republika', 'Zambija', 'Zimbabve',
    ];

    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    public static function street()
    {
        return static::randomElement(static::$street);
    }

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }
}

<?php

namespace Faker\Provider\tr_TR;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%##', '%#', '%'];

    protected static $streetSuffix = [
        'Sokak', 'Caddesi', 'Kavşağı', 'Durağı', 'İş Hanı', 'Mevkii',
    ];

    protected static $postcode = ['#####'];

    /**
     * @var array Cities of Turkey, for future updates please use @see https://tr.wikipedia.org/wiki/T%C3%BCrkiye'nin_illeri
     */
    protected static $cityNames = [
        'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın',
        'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa',
        'Çanakkale', 'Çankırı', 'Çorum',
        'Denizli', 'Diyarbakır', 'Düzce',
        'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir',
        'Gaziantep', 'Giresun', 'Gümüşhane',
        'Hakkari', 'Hatay',
        'Iğdır', 'Isparta', 'İstanbul', 'İzmir',
        'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis',
        'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya',
        'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş',
        'Nevşehir', 'Niğde',
        'Ordu', 'Osmaniye',
        'Rize',
        'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas',
        'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli',
        'Uşak',
        'Van',
        'Yalova', 'Yozgat',
        'Zonguldak',
    ];

    /**
     * @var array Countries in Turkish
     *
     * @see https://tr.wikipedia.org/wiki/%C3%9Clkeler_listesi
     */
    protected static $country = [
        'Almanya', 'Amerika Birleşik Devletleri', 'Arjantin', 'Arnavutluk', 'Avustralya', 'Avusturya', 'Azerbaycan',
        'Bahreyn', 'Belçika', 'Beyaz Rusya', 'Birleşik Arap Emirlikleri', 'Bosna-hersek', 'Brezilya', 'Bulgaristan',
        'Çek Cumhuriyeti', 'Cezayir', 'Çin Halk Cumhuriyeti',
        'Danimarka', 'Dominik Cumhuriyeti',
        'Endonezya', 'Ermenistan', 'Estonya',
        'Fas', 'Filipinler', 'Filistin', 'Finlandiya', 'Fransa',
        'Güney Afrika Cumhuriyeti', 'Güney Kore', 'Gürcistan',
        'Hindistan', 'Hırvatistan', 'Hollanda',
        'İngiltere', 'Irak', 'İran', 'İrlanda', 'İskoçya', 'İspanya', 'İsrail', 'İsveç', 'İsviçre', 'İtalya',
        'Jamaika', 'Japonya',
        'Kamboçya', 'Kanada', 'Karadağ', 'Kazakistan', 'Kıbrıs', 'Kırgızistan', 'Kosta Rika', 'Küba', 'Kuzey Kore',
        'Letonya', 'Libya', 'Litvanya', 'Lübnan', 'Lüksemburg',
        'Macaristan', 'Makedonya', 'Maldivler', 'Malta', 'Maurıtıus', 'Mısır',
        'Nepal',
        'Özbekistan',
        'Pakistan', 'Polonya', 'Portekiz', 'Romanya',
        'Rusya',
        'Sırbistan', 'Slovakya', 'Slovenya',
        'Sri Lanka', 'Sudan', 'Suriye', 'Suudi Arabistan',
        'Tacikistan', 'Tayland', 'Tayvan', 'Tunus', 'Türkiye',
        'Ukrayna', 'Umman', 'Ürdün',
        'Venezuela', 'Vietnam',
        'Yemen', 'Yeni Zelanda', 'Yeşil Burun', 'Yunanistan',
        'Zambiya', 'Zimbabve',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    protected static $streetNameFormats = [
        '{{lastName}} {{streetSuffix}}',
        '{{firstName}} {{streetSuffix}}',
        '{{firstName}} {{streetSuffix}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }
}

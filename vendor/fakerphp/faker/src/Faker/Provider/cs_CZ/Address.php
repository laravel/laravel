<?php

namespace Faker\Provider\cs_CZ;

class Address extends \Faker\Provider\Address
{
    protected static $streetAddressFormats = [
        '{{streetName}}',
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}',
    ];

    protected static $addressFormats = [
        "{{streetAddress}}\n{{region}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
        "{{streetAddress}}\n{{postcode}} {{city}}\nČeská republika",
    ];

    protected static $buildingNumber = ['%', '%%', '%/%%', '%%/%%', '%/%%%', '%%/%%%'];

    protected static $postcode = ['#####', '### ##'];

    /**
     * Source: https://cs.wikipedia.org/wiki/Seznam_m%C4%9Bst_v_%C4%8Cesku_podle_po%C4%8Dtu_obyvatel
     */
    protected static $city = [
        'Brno', 'Břeclav', 'Cheb', 'Chomutov', 'Chrudim', 'Černošice', 'Česká Lípa', 'České Budějovice',
        'Český Těšín', 'Děčín', 'Frýdek-Místek', 'Havlíčkův Brod', 'Havířov', 'Hodonín', 'Hradec Králové',
        'Jablonec nad Nisou', 'Jihlava', 'Karlovy Vary', 'Karviná', 'Kladno', 'Kolín', 'Krnov', 'Kroměříž',
        'Liberec', 'Litoměřice', 'Litvínov', 'Mladá Boleslav', 'Most', 'Nový Jičín', 'Olomouc', 'Opava', 'Orlová',
        'Ostrava', 'Pardubice', 'Plzeň', 'Praha', 'Prostějov', 'Písek', 'Přerov', 'Příbram', 'Sokolov', 'Šumperk',
        'Teplice', 'Trutnov', 'Tábor', 'Třebíč', 'Třinec', 'Uherské Hradiště', 'Ústí nad Labem',
        'Valašské Meziříčí', 'Vsetín', 'Zlín', 'Znojmo',
    ];

    /**
     * Source: https://cs.wikipedia.org/wiki/Seznam_st%C3%A1t%C5%AF_sv%C4%9Bta
     */
    protected static $country = [
        'Afghánistán', 'Albánie', 'Alžírsko', 'Andorra', 'Angola', 'Antigua a Barbuda', 'Argentina',
        'Arménie', 'Austrálie', 'Ázerbájdžán', 'Bahamy', 'Bahrajn', 'Bangladéš', 'Barbados', 'Belgie',
        'Belize', 'Benin', 'Bělorusko', 'Bhútán', 'Bolívie', 'Bosna a Hercegovina', 'Botswana', 'Brazílie',
        'Brunej', 'Bulharsko', 'Burkina Faso', 'Burundi', 'Cookovy ostrovy', 'Čad', 'Černá Hora', 'Česká republika',
        'Čína', 'Dánsko', 'Demokratická republika Kongo', 'Dominika', 'Dominikánská republika', 'Džibutsko',
        'Egypt', 'Ekvádor', 'Eritrea', 'Estonsko', 'Etiopie', 'Fidži', 'Filipíny', 'Finsko', 'Francie', 'Gabon',
        'Gambie', 'Ghana', 'Grenada', 'Gruzie', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras',
        'Chile', 'Chorvatsko', 'Indie', 'Indonésie', 'Irák', 'Írán', 'Irsko', 'Island', 'Itálie', 'Izrael', 'Jamajka',
        'Japonsko', 'Jemen', 'Jihoafrická republika', 'Jižní Korea', 'Jižní Súdán', 'Jordánsko', 'Kambodža', 'Kamerun',
        'Kanada', 'Kapverdy', 'Katar', 'Kazachstán', 'Keňa', 'Kiribati', 'Kolumbie', 'Komory', 'Republika Kongo',
        'Kostarika', 'Kuba', 'Kuvajt', 'Kypr', 'Kyrgyzstán', 'Laos', 'Lesotho', 'Libanon', 'Libérie', 'Libye',
        'Lichtenštejnsko', 'Litva', 'Lotyšsko', 'Lucembursko', 'Madagaskar', 'Maďarsko', 'Makedonie', 'Malajsie',
        'Malawi', 'Maledivy', 'Mali', 'Malta', 'Maroko', 'Marshallovy ostrovy', 'Mauritánie', 'Mauricius', 'Mexiko',
        'Federativní státy Mikronésie', 'Moldavsko', 'Monako', 'Mongolsko', 'Mosambik', 'Myanmar', 'Namibie', 'Nauru',
        'Nepál', 'Německo', 'Niger', 'Nigérie', 'Nikaragua', 'Niue', 'Nizozemsko', 'Norsko', 'Nový Zéland', 'Omán',
        'Pákistán', 'Palau', 'Panama', 'Papua-Nová Guinea', 'Paraguay', 'Peru', 'Pobřeží slonoviny', 'Polsko',
        'Portugalsko', 'Rakousko', 'Rovníková Guinea', 'Rumunsko', 'Rusko', 'Rwanda', 'Řecko', 'Salvador', 'Samoa',
        'San Marino', 'Saúdská Arábie', 'Senegal', 'Severní Korea', 'Seychely', 'Sierra Leone', 'Singapur',
        'Slovensko', 'Slovinsko', 'Somálsko', 'Spojené arabské emiráty', 'Spojené království', 'Spojené státy americké',
        'Srbsko', 'Středoafrická republika', 'Surinam', 'Súdán', 'Svatá Lucie', 'Svatý Kryštof a Nevis',
        'Svatý Tomáš a Princův ostrov', 'Svatý Vincenc a Grenadiny', 'Svazijsko', 'Sýrie', 'Šalamounovy ostrovy',
        'Španělsko', 'Šrí Lanka', 'Švédsko', 'Švýcarsko', 'Tádžikistán', 'Tanzanie', 'Thajsko', 'Togo', 'Tonga',
        'Trinidad a Tobago', 'Tunisko', 'Turecko', 'Turkmenistán', 'Tuvalu', 'Uganda', 'Ukrajina', 'Uruguay',
        'Uzbekistán', 'Vanuatu', 'Vatikán', 'Venezuela', 'Vietnam', 'Východní Timor', 'Zambie', 'Zimbabwe',
    ];

    /**
     * Source: https://cs.wikipedia.org/wiki/Kraje_v_%C4%8Cesku#Ekonomika
     */
    private static $regions = [
        'Hlavní město Praha', 'Jihomoravský kraj', 'Jihočeský kraj', 'Karlovarský kraj', 'Královéhradecký kraj',
        'Liberecký kraj', 'Moravskoslezský kraj', 'Olomoucký kraj', 'Pardubický kraj', 'Plzeňský kraj',
        'Středočeský kraj', 'Vysočina', 'Zlínský kraj', 'Ústecký kraj',
    ];

    /**
     * Source: http://aplikace.mvcr.cz/adresy/
     */
    protected static $street = [
        'Alžírská', 'Angelovova', 'Antonínská', 'Arménská', 'Čelkovická', 'Červenkova', 'Československého exilu',
        'Chlumínská', 'Chládkova', 'Diskařská', 'Do Kopečka', 'Do Vozovny', 'Do Vršku', 'Doubravická', 'Doudova',
        'Drahotínská', 'Dělnická', 'Generála Šišky', 'Gončarenkova', 'Gutova', 'Havlínova', 'Havraní', 'Helmova',
        'Hečkova', 'Holubinková', 'Holínská', 'Horní Hrdlořezská', 'Horní Stromky', 'Hostivařské nám.', 'Houbařská',
        'Hořanská', 'Hrachovská', 'Hrad III. nádvoří', 'Hrdlořezská', 'Jenská', 'Jerevanská', 'Ježovická', 'K Březince',
        'K Dobré Vodě', 'K Hořavce', 'K Hrušovu', 'K Háji', 'K Návsi', 'K Padesátníku', 'K Pyramidce', 'K Samotě',
        'K Vinici', 'K Vystrkovu', 'Karlovarská', 'Karlínské nám.', 'Kaňkova', 'Ke Kyjovu', 'Ke Stadionu', 'Kejnická',
        'Klatovská', 'Kohoutových', 'Kopanská', 'Kralupská', 'Kukelská', 'Kukučínova', 'Kunešova', 'Kvestorská',
        'Křišťanova', 'Lanžhotská', 'Leštínská', 'Lindavská', 'Litevská', 'Lojovická', 'Lukešova', 'Maltézské náměstí',
        'Melodická', 'Mečíková', 'Milady Horákové', 'Mšenská', 'N. A. Někrasova', 'Na Dědince', 'Na Habrové',
        'Na Jezerce', 'Na Jílech', 'Na Petynce', 'Na Rozcestí', 'Na Sedlišti', 'Na Vrchu', 'Na Výšině', 'Na Úbočí',
        'Na Štamberku', 'Nad Hliníkem', 'Nad Hřištěm', 'Nad Klikovkou', 'Nad libeňským nádražím', 'Nad Nuslemi',
        'Nad Slávií', 'Nad Trnkovem', 'Nad Šauerovými sady', 'Netřebská', 'Nivnická', 'Nádražní', 'nám. Pod Lípou',
        'nám. Před bateriemi', 'nám. Svatopluka Čecha', 'Odlehlá', 'Okrasná', 'Omská', 'Otavova', 'Oválová',
        'Palackého nám.', 'Pavlišovská', 'Paškova', 'Petřínské sady', 'Pilovská', 'Pod Bruskou', 'Pod novou školou',
        'Pod soutratím', 'Pod Svahem', 'Pod Útesy', 'Pohledná', 'Pošepného nám.', 'Prokopových', 'Pávovské náměstí',
        'Pětipeského', 'Příbramská', 'Radbuzská', 'Radnické schody', 'Raichlova', 'Roentgenova', 'Rozkošného',
        'Rozrazilová', 'Ruzyňská', 'Římovská', 'Říční', 'Satalická', 'Schoellerova', 'Smrková', 'Souvratní', 'Sovova',
        'Sportovní', 'Stadionová', 'Statková', 'Stavební', 'Široká', 'Školní', 'Tatranská', 'Tomsova', 'Toruňská',
        'Točenská', 'Trnkovo náměstí', 'Truhlářova', 'Tvrdonická', 'Týmlova', 'U Beránky', 'U Chmelnice',
        'U Chodovského hřbitova', 'U Drážky', 'U Fořta', 'U Kamýku', 'U Klubovny', 'U Lesa', 'U Pekáren',
        'U Prašné brány', 'U Prádelny', 'U Silnice', 'U Sladovny', 'U Slovanky', 'U Soutoku', 'U Trojice', 'U Vinice',
        'U vinných sklepů', 'U Vodárny', 'U Vorlíků', 'U zeleného ptáka', 'U Čekárny', 'U Županských', 'Ukrajinská',
        'Újezdská', 'V Jámě', 'V Předním Hloubětíně', 'V Rohu', 'V Uličce', 'Valčíkova', 'Ve Lhotce', 'Ve Vrších',
        'Velenická', 'Violková', 'Vlašská', 'Voděradská', 'Vyderská', 'Vysokoškolská', 'Výpadová', 'Vřesovická',
        'Za Pekárnou', 'Zámecká',
    ];

    /**
     * Randomly returns a czech city.
     *
     * @example 'Krnov'
     *
     * @return string
     */
    public function city()
    {
        return static::randomElement(static::$city);
    }

    /**
     * Randomly returns a czech region.
     *
     * @example 'Liberecký kraj'
     *
     * @return string
     */
    public static function region()
    {
        return static::randomElement(static::$regions);
    }

    /**
     * Real street names as random data can hardly be
     * generated due to inflection.
     *
     * @example 'U Vodárny'
     *
     * @return string
     */
    public function streetName()
    {
        return static::randomElement(static::$street);
    }
}

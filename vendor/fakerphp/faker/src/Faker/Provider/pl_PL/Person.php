<?php

namespace Faker\Provider\pl_PL;

/**
 * Most popular first and last names published by Ministry of the Interior:
 *
 * @see https://msw.gov.pl/pl/sprawy-obywatelskie/ewidencja-ludnosci-dowo/statystyki-imion-i-nazw
 */
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
        '{{title}} {{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{title}} {{title}} {{firstNameMale}} {{lastNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{title}} {{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{title}} {{title}} {{firstNameFemale}} {{lastNameFemale}}',
    ];

    protected static $firstNameMale = [
        'Adam', 'Adrian', 'Alan', 'Albert', 'Aleks', 'Aleksander', 'Alex', 'Andrzej', 'Antoni', 'Arkadiusz', 'Artur',
        'Bartek', 'Błażej', 'Borys', 'Bruno', 'Cezary', 'Cyprian', 'Damian', 'Daniel', 'Dariusz', 'Dawid', 'Dominik',
        'Emil', 'Ernest', 'Eryk', 'Fabian', 'Filip', 'Franciszek', 'Fryderyk', 'Gabriel', 'Grzegorz', 'Gustaw', 'Hubert',
        'Ignacy', 'Igor', 'Iwo', 'Jacek', 'Jakub', 'Jan', 'Jeremi', 'Jerzy', 'Jędrzej', 'Józef', 'Julian', 'Juliusz',
        'Kacper', 'Kajetan', 'Kamil', 'Karol', 'Kazimierz', 'Konrad', 'Konstanty', 'Kornel', 'Krystian', 'Krzysztof', 'Ksawery',
        'Leon', 'Leonard', 'Łukasz', 'Maciej', 'Maks', 'Maksymilian', 'Marcel', 'Marcin', 'Marek', 'Mariusz', 'Mateusz', 'Maurycy',
        'Michał', 'Mieszko', 'Mikołaj', 'Miłosz', 'Natan', 'Nataniel', 'Nikodem', 'Norbert', 'Olaf', 'Olgierd', 'Oliwier', 'Oskar',
        'Patryk', 'Paweł', 'Piotr', 'Przemysław', 'Radosław', 'Rafał', 'Robert', 'Ryszard', 'Sebastian', 'Stanisław', 'Stefan', 'Szymon',
        'Tadeusz', 'Tomasz', 'Tymon', 'Tymoteusz', 'Wiktor', 'Witold', 'Wojciech',
    ];

    protected static $firstNameFemale = [
        'Ada', 'Adrianna', 'Agata', 'Agnieszka', 'Aleksandra', 'Alicja', 'Amelia', 'Anastazja', 'Angelika', 'Aniela', 'Anita',
        'Anna', 'Anna', 'Antonina', 'Apolonia', 'Aurelia', 'Barbara', 'Bianka', 'Blanka', 'Dagmara', 'Daria', 'Dominika', 'Dorota',
        'Eliza', 'Elżbieta', 'Emilia', 'Ewa', 'Ewelina', 'Gabriela', 'Hanna', 'Helena', 'Ida', 'Iga', 'Inga', 'Izabela',
        'Jagoda', 'Janina', 'Joanna', 'Julia', 'Julianna', 'Julita', 'Justyna', 'Kaja', 'Kalina', 'Kamila', 'Karina', 'Karolina',
        'Katarzyna', 'Kinga', 'Klara', 'Klaudia', 'Kornelia', 'Krystyna', 'Laura', 'Lena', 'Lidia', 'Liliana', 'Liwia', 'Łucja',
        'Magdalena', 'Maja', 'Malwina', 'Małgorzata', 'Marcelina', 'Maria', 'Marianna', 'Marika', 'Marta', 'Martyna', 'Matylda',
        'Melania', 'Michalina', 'Milena', 'Monika', 'Nadia', 'Natalia', 'Natasza', 'Nela', 'Nicole', 'Nikola', 'Nina',
        'Olga', 'Oliwia', 'Patrycja', 'Paulina', 'Pola', 'Roksana', 'Rozalia', 'Róża', 'Sandra', 'Sara', 'Sonia', 'Sylwia',
        'Tola', 'Urszula', 'Weronika', 'Wiktoria', 'Zofia', 'Zuzanna',
    ];

    protected static $lastNameMale = [
        'Adamczyk', 'Adamski', 'Andrzejewski', 'Baran', 'Baranowski', 'Bąk', 'Błaszczyk', 'Borkowski', 'Borowski', 'Brzeziński',
        'Chmielewski', 'Cieślak', 'Czarnecki', 'Czerwiński', 'Dąbrowski', 'Duda', 'Dudek', 'Gajewski', 'Głowacki', 'Górski', 'Grabowski',
        'Jabłoński', 'Jakubowski', 'Jankowski', 'Jasiński', 'Jaworski', 'Kaczmarczyk', 'Kaczmarek', 'Kalinowski', 'Kamiński', 'Kaźmierczak',
        'Kołodziej', 'Konieczny', 'Kowalczyk', 'Kowalski', 'Kozłowski', 'Krajewski', 'Krawczyk', 'Król', 'Krupa', 'Kubiak', 'Kucharski', 'Kwiatkowski',
        'Laskowski', 'Lewandowski', 'Lis', 'Maciejewski', 'Majewski', 'Makowski', 'Malinowski', 'Marciniak', 'Mazur', 'Mazurek', 'Michalak',
        'Michalski', 'Mróz', 'Nowak', 'Nowakowski', 'Nowicki', 'Olszewski', 'Ostrowski', 'Pawlak', 'Pawłowski', 'Pietrzak', 'Piotrowski', 'Przybylski',
        'Rutkowski', 'Sadowski', 'Sawicki', 'Sikora', 'Sikorski', 'Sobczak', 'Sokołowski', 'Stępień', 'Szczepański', 'Szewczyk', 'Szulc', 'Szymański', 'Szymczak',
        'Tomaszewski', 'Urbański', 'Walczak', 'Wasilewski', 'Wieczorek', 'Wilk', 'Wiśniewski', 'Witkowski', 'Włodarczyk', 'Wojciechowski',
        'Woźniak', 'Wójcik', 'Wróbel', 'Wróblewski', 'Wysocki', 'Zając', 'Zakrzewski', 'Zalewski', 'Zawadzki', 'Zieliński', 'Ziółkowski',
    ];

    protected static $lastNameFemale = [
        'Adamczyk', 'Adamska', 'Andrzejewska', 'Baran', 'Baranowska', 'Bąk', 'Błaszczyk', 'Borkowska', 'Borowska', 'Brzezińska',
        'Chmielewska', 'Cieślak', 'Czarnecka', 'Czerwińska', 'Dąbrowska', 'Duda', 'Dudek', 'Gajewska', 'Głowacka', 'Górecka', 'Górska', 'Grabowska',
        'Jabłońska', 'Jakubowska', 'Jankowska', 'Jasińska', 'Jaworska', 'Kaczmarczyk', 'Kaczmarek', 'Kalinowska', 'Kamińska', 'Kaźmierczak',
        'Kołodziej', 'Kowalczyk', 'Kowalska', 'Kozłowska', 'Krajewska', 'Krawczyk', 'Król', 'Krupa', 'Kubiak', 'Kucharska', 'Kwiatkowska',
        'Laskowska', 'Lewandowska', 'Lis', 'Maciejewska', 'Majewska', 'Makowska', 'Malinowska', 'Marciniak', 'Mazur', 'Mazurek', 'Michalak',
        'Michalska', 'Mróz', 'Nowak', 'Nowakowska', 'Nowicka', 'Olszewska', 'Ostrowska', 'Pawlak', 'Pawłowska', 'Pietrzak', 'Piotrowska', 'Przybylska',
        'Rutkowska', 'Sadowska', 'Sawicka', 'Sikora', 'Sikorska', 'Sobczak', 'Sokołowska', 'Stępień', 'Szczepańska', 'Szewczyk', 'Szulc', 'Szymańska', 'Szymczak',
        'Tomaszewska', 'Urbańska', 'Walczak', 'Wasilewska', 'Wieczorek', 'Wilk', 'Wiśniewska', 'Witkowska', 'Włodarczyk', 'Wojciechowska',
        'Woźniak', 'Wójcik', 'Wróbel', 'Wróblewska', 'Wysocka', 'Zając', 'Zakrzewska', 'Zalewska', 'Zawadzka', 'Zielińska', 'Ziółkowska',
    ];

    /**
     * Unisex academic degree
     *
     * @var string[]
     */
    protected static $title = ['mgr', 'inż.', 'dr', 'doc.'];

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @example 'Adamczyk'
     */
    public function lastName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::lastNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::lastNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$lastNameFormat));
    }

    public static function lastNameMale()
    {
        return static::randomElement(static::$lastNameMale);
    }

    public static function lastNameFemale()
    {
        return static::randomElement(static::$lastNameFemale);
    }

    public function title($gender = null)
    {
        return static::randomElement(static::$title);
    }

    /**
     * replaced by specific unisex Polish title
     */
    public static function titleMale()
    {
        return static::randomElement(static::$title);
    }

    /**
     * replaced by specific unisex Polish title
     */
    public static function titleFemale()
    {
        return static::randomElement(static::$title);
    }

    /**
     * PESEL - Universal Electronic System for Registration of the Population
     *
     * @see http://en.wikipedia.org/wiki/PESEL
     *
     * @param DateTime $birthdate
     * @param string   $sex       M for male or F for female
     *
     * @return string 11 digit number, like 44051401358
     */
    public static function pesel($birthdate = null, $sex = null)
    {
        if ($birthdate === null) {
            $birthdate = \Faker\Provider\DateTime::dateTimeThisCentury();
        }

        $weights = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
        $length = count($weights);

        $fullYear = (int) $birthdate->format('Y');
        $year = (int) $birthdate->format('y');
        $month = $birthdate->format('m') + (((int) ($fullYear / 100) - 14) % 5) * 20;
        $day = $birthdate->format('d');

        $result = [(int) ($year / 10), $year % 10, (int) ($month / 10), $month % 10, (int) ($day / 10), $day % 10];

        for ($i = 6; $i < $length; ++$i) {
            $result[$i] = static::randomDigit();
        }

        $result[$length - 1] |= 1;

        if ($sex == 'F') {
            $result[$length - 1] -= 1;
        }

        $checksum = 0;

        for ($i = 0; $i < $length; ++$i) {
            $checksum += $weights[$i] * $result[$i];
        }
        $checksum = (10 - ($checksum % 10)) % 10;
        $result[] = $checksum;

        return implode('', $result);
    }

    /**
     * National Identity Card number
     *
     * @see http://en.wikipedia.org/wiki/Polish_National_Identity_Card
     *
     * @return string 3 letters and 6 digits, like ABA300000
     */
    public static function personalIdentityNumber()
    {
        $range = str_split('ABCDEFGHIJKLMNPRSTUVWXYZ');
        $low = ['A', static::randomElement($range), static::randomElement($range)];
        $high = [static::randomDigit(), static::randomDigit(), static::randomDigit(), static::randomDigit(), static::randomDigit()];
        $weights = [7, 3, 1, 7, 3, 1, 7, 3];
        $checksum = 0;

        for ($i = 0, $size = count($low); $i < $size; ++$i) {
            $checksum += $weights[$i] * (ord($low[$i]) - 55);
        }

        for ($i = 0, $size = count($high); $i < $size; ++$i) {
            $checksum += $weights[$i + 3] * $high[$i];
        }
        $checksum %= 10;

        return implode('', $low) . $checksum . implode('', $high);
    }

    /**
     * Taxpayer Identification Number (NIP in Polish)
     *
     * @see http://en.wikipedia.org/wiki/PESEL#Other_identifiers
     * @see http://pl.wikipedia.org/wiki/NIP
     *
     * @return string 10 digit number
     */
    public static function taxpayerIdentificationNumber()
    {
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $result = [];

        do {
            $result = [
                static::randomDigitNotNull(), static::randomDigitNotNull(), static::randomDigitNotNull(),
                static::randomDigit(), static::randomDigit(), static::randomDigit(),
                static::randomDigit(), static::randomDigit(), static::randomDigit(),
            ];
            $checksum = 0;

            for ($i = 0, $size = count($result); $i < $size; ++$i) {
                $checksum += $weights[$i] * $result[$i];
            }
            $checksum %= 11;
        } while ($checksum == 10);
        $result[] = $checksum;

        return implode('', $result);
    }
}

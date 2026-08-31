<?php

namespace Faker\Provider\el_GR;

class Company extends \Faker\Provider\Company
{
    protected static $companySuffix = [
        'Ο.Ε',
        'Ε.Ε',
        'Α.Ε',
        'Ε.Π.Ε',
    ];

    protected static $companyFormats = [
        '{{lastName}} {{firstName}} {{companySuffix}}',
        '{{lastName}}-{{firstName}}',
    ];

    protected static $grafm = ['#########'];

    protected static $doy = [
        'Α\' Αθήνας',
        'Β\' Αθήνας',
        'Γ\' Αθήνας',
        'ΣΤ\' Αθήνας',
        'Γαλάτσιου',
        'Α\' Πειραιά',
        'Β\' Πειραιά',
        'Γ\' Πειραιά',
        'Α\' Θεσσαλονίκης',
        'Β\' Θεσσαλονίκης',
        'Γλυφάδας',
        'Ωροπού',
        'Καλιθέας',
        'Αγίου Δημητρίου',
        'Νέας Σμύρνης',
        'Αμαρουσίου',
        'Θήρας',
        'Αμοργού',
        'Πατρών',
        'ΔΟΥ ΠΛΟΙΩΝ',
        'ΦΑΕΕ ΑΘΗΝΩΝ',
    ];

    protected static $object = [
        'Προγραμματιστής',
        'Δικηγόρος',
        'Γιατρός',
        'Γραφίστας',
        'Αρχαιολόγος',
        'Εκπαιδευτικός',
        'Μεταφραστής',
        'Μηχανολόγος-μηχανικός',
        'Αρχιτέκτονας',
        'Δημοσιογράφος',
        'Υπηρεσίες Διαδικτύου',
        'Ραδιοφωνικές παραγωγές',
        'Καραγκιοζοπαίχτης',
        'Κουλουράς',
        'Κομπάρσος',
        'Καλλιτεχνικός πράκτορας',
        'Εισαγωγαί-εξαγωγαί',
        'Ωρολογοποιός',
        'Καθεκλοποιός',
    ];

    /**
     * @example 'Αθήνας'
     */
    public static function doy()
    {
        return static::randomElement(static::$doy);
    }

    /**
     * Return The profession of a company
     *
     * @example 'Δημοσιογράφος'
     */
    public static function object()
    {
        return static::randomElement(static::$object);
    }
}

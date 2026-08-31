<?php

namespace Faker\Provider\de_DE;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companySuffix}}',
        '{{lastName}}',
        '{{lastName}}',
    ];

    /**
     * @see http://www.personalseite.de/information/titel.htm
     */
    protected static $jobTitleFormat = [
        'Abteilungsdirektor', 'Arbeitsdirektor', 'Aufsichtsrat', 'Beirat', 'Bereichsleiter', 'Betriebsleiter', 'Finanzvorstand', 'Geschäftsführender Gesellschafter', 'Geschäftsführer', 'Gesellschafter',
        'Handlungsbevollmächtigter', 'Kaufmännischer Vorstand', 'Leiter Rechtsabteilung', 'Mitglied des Aufsichtsrats', 'Personalleiter', 'Prokurist', 'Stellvertretender Vorsitzender des Vorstandes',
        'Vorsitzender der Geschäftsführung', 'Vorsitzender des Aufsichtsrats', 'Vorsitzender des Vorstandes', 'Vorstand Personal', 'Vorstand Technik', 'Vorstand Vertrieb', 'Vorstandsmitglied', 'Werksleiter',
    ];

    protected static $companySuffix = ['e.G.', 'e.V.', 'GbR', 'GbR', 'OHG mbH', 'GmbH & Co. OHG', 'AG & Co. OHG', 'GmbH', 'GmbH', 'GmbH', 'GmbH', 'AG', 'AG', 'AG', 'AG', 'KG', 'KG', 'KG', 'GmbH & Co. KG', 'GmbH & Co. KG', 'AG & Co. KG', 'Stiftung & Co. KG', 'KGaA', 'GmbH & Co. KGaA', 'AG & Co. KGaA', 'Stiftung & Co. KGaA'];
}

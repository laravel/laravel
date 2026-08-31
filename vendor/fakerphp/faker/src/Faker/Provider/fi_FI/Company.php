<?php

namespace Faker\Provider\fi_FI;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}}-{{lastName}}',
        '{{lastName}}, {{lastName}} and {{lastName}}',
    ];

    protected static $catchPhraseWords = [
        [
            'Ainoa', 'Adaptiivinen', 'Automatisoitu', 'Avoin', 'Avoimen lähdekoodin', 'Digitalisoitu', 'Dynaaminen', 'Erikoistunut', 'Ennakoiva', 'Ergonominen', 'Hallittu', 'Hajautettu', 'Horisontaalinen', 'Integroitu', 'Innovatiivinen', 'Jaettava', 'Keskittynyt', 'Käyttäjäystävällinen', 'Laajenettu', 'Laajennettava', 'Lievittävä', 'Maailmanlaajuinen', 'Muokattava', 'Monitoroitu', 'Monikerroksinen', 'Ohjelmoitava', 'Optimoitu', 'Organisoitu', 'Orgaaninen', 'Rinnastettava', 'Räätälöitävä', 'Saumaton', 'Tasapainotettava', 'Täysin yhteensopiva', 'Yhteensopiva', 'Yksinomainen', 'Vaiheittainen', 'Virtuaalinen', 'Visionäärinen', 'Valinnainen', 'Verkostoitunut',
        ],
        [
            '24/7', '3. sukupolven', '4. sukupolven', '5. sukupolven', '6. sukupolven', 'alueellinen', 'analysoiva', 'assymmetrinen', 'asynkroninen', 'asiakaslähtöinen', 'dynaaminen', 'globaali', 'heuristinen', 'hybridi', 'inkrementaalinen', 'interaktiivinen', 'kahtiajakautuva', 'kaksisuuntainen', 'konkreettinen', 'korkean tason', 'logistinen', 'johdonmukainen', 'maksimaalinen', 'moniajettava', 'multimedia', 'optimaalinen', 'optimoiva', 'omistautunut', 'paikallinen', 'reaaliaikainen', 'seuraavan sukupolven', 'skaalautuva', 'systeeminen',
        ],
        [
            'algoritmi', 'yhteisö', 'analysaattori', 'sovellus', 'lähestymistapa', 'arkkitehtuuri', 'arkisto', 'ryhmä', 'sovitin', 'benchmark', 'kapasiteetti', 'tietokanta', 'piiri', 'emulaatio', 'konsepti', 'firmware', 'rakenne', 'funktio', 'rauta', 'help-desk', 'hierarkia', 'hubi', 'implementaatio', 'intranet', 'malli', 'ohjelmisto', 'ratkaisu', 'strategia', 'menestys', 'projekti', 'tuote', 'tuki', 'www-sivusto', 'synergia', 'protokolla', 'koodaus', 'ennuste', 'joustavuus', 'extranet', 'salaus', 'kompleksisuus', 'ydin', 'asenne', 'liitto', 'internetratkaisu', 'asennus', 'monitorointi', 'infrastruktuuri',
        ],
    ];

    protected static $bsWords = [
        [
            'implementoi', 'käyttää', 'integroi', 'optimoi', 'kehittää', 'muuntaa', 'omaksuu', 'mahdollistaa', 'orkestoi', 'uudelleenkeksii', 'kerää', 'kokoaa', 'kasvattaa', 'parantaa', 'helpottaa', 'toimittaa', 'antaa', 'täyttää', 'vapauttaa', 'ajaa', 'jatkaa', 'mullistaa', 'iteroi', 'generoi', 'hyödyntää', 'brandaa', 'muokkaa', 'kasvattaa', 'kytkee', 'innovoi', 'viljelee', 'visualisoi',
        ],
        [
            'maailmanluokan', '24/7', 'maailmanlaajuisia', 'interaktiivisia', 'seksikkäitä', 'rikkaita', 'visionäärisiä', 'parhaita', 'viraaleja', 'dynaamisia', 'strategisia', 'vallankumouksellisia', '24/365', 'e-bisnes', 'virtuaalisia', 'intuitiivisia', 'ennakoivia', 'kitkattomia', 'jaettuja', 'reaaliaikaisia', 'huippuluokan', 'tehokkaita', 'suorituskykyisiä', 'pystyviä', 'laajennettavissa olevia', 'venyviä', 'yhteisvaikutteisia', 'seuraavan sukupolven', 'plug-and-play', 'yhteistyökykyisiä', 'kustomoituja', 'langattomia', 'selkeitä', 'avoimen lähdekoodin',
        ],
        [
            'kanavia', 'silmämunia', 'ratkaisuja', 'e-palveluja', 'portaaleja', 'teknologioita', 'käyttäjiä', 'verkkoja', 'palveluita', 'ohjelmia', 'web-ohjelmistoja', 'kokemuksia', 'systeemejä', 'rajapintoja', 'yhteisöjä', 'markkinarakoja', 'skeemoja', 'arkkitehtuureja', 'malleja', 'sisältöä', 'mainoksia',
        ],
    ];

    protected static $companySuffix = ['OY', 'AB', 'OY AB', 'RY', 'OYj', 'Tmi.', 'Inc.', 'Ltd'];

    /**
     * @example 'Ainoa 3.sukupolven ohjelmisto'
     */
    public function catchPhrase()
    {
        $result = [];

        foreach (static::$catchPhraseWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return implode(' ', $result);
    }

    /**
     * @example 'hyödyntää maailmanlaajuisia yhteisöjä'
     */
    public function bs()
    {
        $result = [];

        foreach (static::$bsWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return implode(' ', $result);
    }
}

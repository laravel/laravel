<?php

namespace Faker\Provider\cs_CZ;

class Company extends \Faker\Provider\Company
{
    /**
     * @var array Czech company name formats.
     */
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{lastName}} {{companySuffix}}',
        '{{lastName}}-{{lastName}} {{companySuffix}}',
        '{{lastName}} a {{lastName}} {{companySuffix}}',
    ];

    /**
     * @var array Czech catch phrase formats.
     */
    protected static $catchPhraseFormats = [
        '{{catchPhraseVerb}} {{catchPhraseNoun}} {{catchPhraseAttribute}}',
        '{{catchPhraseVerb}} {{catchPhraseNoun}} a {{catchPhraseNoun}} {{catchPhraseAttribute}}',
        '{{catchPhraseVerb}} {{catchPhraseNoun}} {{catchPhraseAttribute}} a {{catchPhraseAttribute}}',
        'Ne{{catchPhraseVerb}} {{catchPhraseNoun}} {{catchPhraseAttribute}}',
    ];

    /**
     * @var array Czech nouns (used by the catch phrase format).
     */
    protected static $noun = [
        'bezpečnost', 'pohodlí', 'seo', 'rychlost', 'testování', 'údržbu', 'odebírání', 'výstavbu',
        'návrh', 'prodej', 'nákup', 'zprostředkování', 'odvoz', 'přepravu', 'pronájem',
    ];

    /**
     * @var array Czech verbs (used by the catch phrase format).
     */
    protected static $verb = [
        'zajišťujeme', 'nabízíme', 'děláme', 'provozujeme', 'realizujeme', 'předstihujeme', 'mobilizujeme',
    ];

    /**
     * @var array End of sentences (used by the catch phrase format).
     */
    protected static $attribute = [
        'pro vás', 'pro vaší službu', 'a jsme jednička na trhu', 'pro lepší svět', 'zdarma', 'se zárukou',
        's inovací', 'turbíny', 'mrakodrapů', 'lampiónků a svíček', 'bourací techniky', 'nákupních košíků',
        'vašeho webu', 'pro vaše zákazníky', 'za nízkou cenu', 'jako jediní na trhu', 'webu', 'internetu',
        'vaší rodiny', 'vašich známých', 'vašich stránek', 'čehokoliv na světě', 'za hubičku',
    ];

    /**
     * @var array Company suffixes.
     */
    protected static $companySuffix = ['s.r.o.', 's.r.o.', 's.r.o.', 's.r.o.', 'a.s.', 'o.p.s.', 'o.s.'];

    /**
     * Returns a random catch phrase noun.
     *
     * @return string
     */
    public function catchPhraseNoun()
    {
        return static::randomElement(static::$noun);
    }

    /**
     * Returns a random catch phrase attribute.
     *
     * @return string
     */
    public function catchPhraseAttribute()
    {
        return static::randomElement(static::$attribute);
    }

    /**
     * Returns a random catch phrase verb.
     *
     * @return string
     */
    public function catchPhraseVerb()
    {
        return static::randomElement(static::$verb);
    }

    /**
     * @return string
     */
    public function catchPhrase()
    {
        $format = static::randomElement(static::$catchPhraseFormats);

        return ucfirst($this->generator->parse($format));
    }

    /**
     * Generates valid czech IČO
     *
     * @see http://phpfashion.com/jak-overit-platne-ic-a-rodne-cislo
     *
     * @return string
     */
    public function ico()
    {
        $ico = static::numerify('#######');
        $split = str_split($ico);
        $prod = 0;

        foreach ([8, 7, 6, 5, 4, 3, 2] as $i => $p) {
            $prod += $p * $split[$i];
        }
        $mod = $prod % 11;

        if ($mod === 0 || $mod === 10) {
            return "{$ico}1";
        }

        if ($mod === 1) {
            return "{$ico}0";
        }

        return $ico . (11 - $mod);
    }
}

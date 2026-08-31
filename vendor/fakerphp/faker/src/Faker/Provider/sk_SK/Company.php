<?php

namespace Faker\Provider\sk_SK;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}}-{{lastName}}',
        '{{lastName}}, {{lastName}} and {{lastName}}',
    ];

    protected static $catchPhraseWords = [
        [
            'Adaptívne', 'Automatické', 'Vyvážené', 'Obchodný-sústredený', 'centralizované', 'Cloned', 'kompatibilné', 'nastaviteľný', 'Cross-group', 'Cross-platform', 'zákazníka zamerané', 'Prispôsobiteľné', 'Decentralizované', 'De-inžinierstva', 'prenesenú', 'Digitálne', 'distribuované', 'Rôzne', 'Down veľkosti', 'Rozšírený', 'Celopodnikové ', ' Ergonomická', 'Expanded', 'Rozšírené', 'Plne konfigurovateľné', 'Funkcia na báze', 'Základný', 'Perspektívne', 'inovatívne', 'integrované', 'Intuitívne', 'Inverzný', 'Povinne monitorovaná', 'Multi-prešitia', 'multi-laterálne', 'Viacvrstvová', 'Viacvrstvová', 'Sieťová', 'Objektovo založená', 'Open-architektúru', 'Open-source', 'Operatívny', 'Optimalizované', 'Voliteľný', 'Organické', 'Organizovaná', 'Trvalé', 'Polarizované', 'Preventívne', 'Aktívne', 'Na zisk zameraná', 'Hlboká', 'programovateľný', 'progresívne', 'verejného kľúča', 'kvalita zamerané', 'reaktívne', 'Rekonštruované', 'Znížená', 'Právo veľké', 'robustné', 'Bezpečné', 'Jednoduchšie', 'Prepínanie', 'Synchronizované', 'synergický', 'spojil', 'tím-orientovaná', 'Univerzálne', 'Užívateľsky prívetivý', 'Univerzálne', 'Virtuálne', 'Vizionárske',
        ],
        [
            'kontrola', 'analýzu', 'asymetrický', 'asynchrónne', 'prístup orientovaný', 'pozadí', 'na šírku pásma, monitorovať', 'obojsmerná', 'rozštiepené', 'jasne myslenie', 'klient-driven', 'klient-server', 'koherentné', 'súdržnosti', 'kompozitné', 'kontextový', 'kontextovo založená', 'obsah na báze', 'oddaný', 'dopytom', 'učebný', 'riadiace', 'diskrétne', 'dynamická', 'posilnenie', 'zahŕňajúci', 'manažér', 'explicitné', 'vyžaruje', 'odolné proti chybám', 'popredia', 'čerstvé myslenia', 'Globálne', 'heuristický', 'homogénna', 'ľudských zdrojov', 'hybridný', 'pôsobivých', 'čiastkové', 'nehmotnej', 'interaktívny', 'stredné', 'miestna', 'logistické', 'maximálny', 'metodickú', 'kritické', 'mobil', 'modulárny', 'motivuje', 'multimediálny', 'multi-štát', 'multi-tasking', 'národný', 'založená na skutočných potrebách', 'neutrálny', 'objektovo orientovaný', 'optimálnu', 'optimalizácia', 'radikálne', 'v reálnom čase', 'vzájomná', 'regionálne', 'citlivý', 'škálovateľné', 'stredné', 'zameriavalo na hľadanie riešenia', 'stabilný', 'statické', 'systematické', 'systémové', 'systém-hodný', 'hmotná', 'terciárny', 'prechodné', 'jednotný', 'pridaná hodnota', 'dobre modulovaný',
        ],
        [
            'schopnosť', 'prístup', 'algoritmus', 'aliancie', 'analyzátor', 'aplikácie', 'prístup', 'architektúra', 'archív', 'pole', 'postoj', 'referenčné', 'schopnosť', 'kapacita', 'výzva', 'okruh', 'spolupráca', 'zložitosť', 'koncept', 'konglomerát', 'pohotovostný', 'jadro', 'databáza', 'sklad dát', 'definície', 'emulácia', 'kódovanie', 'šifrovanie', 'extranet', 'firmware', 'flexibilita', 'počasia', 'rám', 'rámec', 'funkcie', 'funkcie', 'hardware', 'help-desk', 'hierarchie', 'húb', 'vykonávanie', 'infraštruktúra', 'iniciatíva', 'inštalácia', 'rozhranie', 'encyklopédia', 'pákový efekt', 'metodika', 'migrácia', 'model', 'moderátor', 'kontrola', 'moratórium', 'neurónové-siete', 'paradigma', 'paralelné', 'politika', 'portál', 'produkt', 'produktivita', 'projekt', 'projekcie', 'protokol', 'služba', 'softvér', 'riešenie', 'normalizácie', 'stratégia', 'štruktúra', 'úspech', 'nadstavba', 'pomoc', 'synergia', 'priepustnosť', 'časovom horizonte', 'sadu nástrojov', 'využitie', 'web', 'pracovná sila',
        ],
    ];

    protected static $bsWords = [
        [
            'implement', 'utilize', 'integrate', 'streamline', 'optimize', 'evolve', 'transform', 'embrace', 'enable', 'orchestrate', 'leverage', 'reinvent', 'aggregate', 'architect', 'enhance', 'incentivize', 'morph', 'empower', 'envisioneer', 'monetize', 'harness', 'facilitate', 'seize', 'disintermediate', 'synergize', 'strategize', 'deploy', 'brand', 'grow', 'target', 'syndicate', 'synthesize', 'deliver', 'mesh', 'incubate', 'engage', 'maximize', 'benchmark', 'expedite', 'reintermediate', 'whiteboard', 'visualize', 'repurpose', 'innovate', 'scale', 'unleash', 'drive', 'extend', 'engineer', 'revolutionize', 'generate', 'exploit', 'transition', 'e-enable', 'iterate', 'cultivate', 'matrix', 'productize', 'redefine', 'recontextualize',
        ],
        [
            'clicks-and-mortar', 'value-added', 'vertical', 'proactive', 'robust', 'revolutionary', 'scalable', 'leading-edge', 'innovative', 'intuitive', 'strategic', 'e-business', 'mission-critical', 'sticky', 'one-to-one', '24/7', 'end-to-end', 'global', 'B2B', 'B2C', 'granular', 'frictionless', 'virtual', 'viral', 'dynamic', '24/365', 'best-of-breed', 'killer', 'magnetic', 'bleeding-edge', 'web-enabled', 'interactive', 'dot-com', 'sexy', 'back-end', 'real-time', 'efficient', 'front-end', 'distributed', 'seamless', 'extensible', 'turn-key', 'world-class', 'open-source', 'cross-platform', 'cross-media', 'synergistic', 'bricks-and-clicks', 'out-of-the-box', 'enterprise', 'integrated', 'impactful', 'wireless', 'transparent', 'next-generation', 'cutting-edge', 'user-centric', 'visionary', 'customized', 'ubiquitous', 'plug-and-play', 'collaborative', 'compelling', 'holistic', 'rich',
        ],
        [
            'synergies', 'web-readiness', 'paradigms', 'markets', 'partnerships', 'infrastructures', 'platforms', 'initiatives', 'channels', 'eyeballs', 'communities', 'ROI', 'solutions', 'e-tailers', 'e-services', 'action-items', 'portals', 'niches', 'technologies', 'content', 'vortals', 'supply-chains', 'convergence', 'relationships', 'architectures', 'interfaces', 'e-markets', 'e-commerce', 'systems', 'bandwidth', 'infomediaries', 'models', 'mindshare', 'deliverables', 'users', 'schemas', 'networks', 'applications', 'metrics', 'e-business', 'functionalities', 'experiences', 'webservices', 'methodologies',
        ],
    ];

    protected static $companySuffix = ['s.r.o.', 'a.s.', 'v.o.s.'];

    /**
     * @example 'Robust full-range hub'
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
     * @example 'integrate extensible convergence'
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

<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\Italian;

use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Word;

class Inflectible
{
    /** @return iterable<Transformation> */
    public static function getSingular(): iterable
    {
        // Reverse of -sce → -scia (fasce → fascia)
        yield new Transformation(new Pattern('([aeiou])sce$'), '\\1scia');

        // Reverse of -cie → -cia (farmacia → farmacie)
        yield new Transformation(new Pattern('cie$'), 'cia');

        // Reverse of -gie → -gia (bugia → bugie)
        yield new Transformation(new Pattern('gie$'), 'gia');

        // Reverse of -ce → -cia (arance → arancia)
        yield new Transformation(new Pattern('([^aeiou])ce$'), '\1cia');

        // Reverse of -ge → -gia (valige → valigia)
        yield new Transformation(new Pattern('([^aeiou])ge$'), '\1gia');

        // Reverse of -chi → -co (bachi → baco)
        yield new Transformation(new Pattern('([bcdfghjklmnpqrstvwxyz][aeiou])chi$'), '\1co');

        // Reverse of -ghi → -go (laghi → lago)
        yield new Transformation(new Pattern('([bcdfghjklmnpqrstvwxyz][aeiou])ghi$'), '\1go');

        // Reverse of -ci → -co (medici → medico)
        yield new Transformation(new Pattern('([aeiou][bcdfghjklmnpqrstvwxyz])ci$'), '\1co');

        // Reverse of -gi → -go (psicologi → psicologo)
        yield new Transformation(new Pattern('([aeiou][bcdfghjklmnpqrstvwxyz])gi$'), '\1go');

        // Reverse of -i → -io (zii → zio, negozi → negozio)
        // This is more complex due to Italian's stress patterns, but we'll handle the basic case
        yield new Transformation(new Pattern('([^aeiou])i$'), '\1io');

        // Handle words that end with -i but should go to -co/-go (amici → amico, not amice)
        yield new Transformation(new Pattern('([^aeiou])ci$'), '\1co');
        yield new Transformation(new Pattern('([^aeiou])gi$'), '\1go');

        // Reverse of -a → -e
        yield new Transformation(new Pattern('e$'), 'a');

        // Reverse of -e → -i
        yield new Transformation(new Pattern('i$'), 'e');

        // Reverse of -o → -i
        yield new Transformation(new Pattern('i$'), 'o');
    }

    /** @return iterable<Transformation> */
    public static function getPlural(): iterable
    {
        // Words ending in -scia without stress on 'i' become -sce (e.g. fascia → fasce)
        yield new Transformation(new Pattern('([aeiou])scia$'), '\\1sce');

        // Words ending in -cia/gia with stress on 'i' keep the 'i' in plural
        yield new Transformation(new Pattern('cia$'), 'cie'); // e.g. farmacia → farmacie
        yield new Transformation(new Pattern('gia$'), 'gie'); // e.g. bugia → bugie

        // Words ending in -cia/gia without stress on 'i' lose the 'i' in plural
        yield new Transformation(new Pattern('([^aeiou])cia$'), '\\1ce'); // e.g. arancia → arance
        yield new Transformation(new Pattern('([^aeiou])gia$'), '\\1ge'); // e.g. valigia → valige

        // Words ending in -co/-go with stress on 'o' become -chi/-ghi
        yield new Transformation(new Pattern('([bcdfghjklmnpqrstvwxyz][aeiou])co$'), '\\1chi'); // e.g. baco → bachi
        yield new Transformation(new Pattern('([bcdfghjklmnpqrstvwxyz][aeiou])go$'), '\\1ghi'); // e.g. lago → laghi

        // Words ending in -co/-go with stress on the penultimate syllable become -ci/-gi
        yield new Transformation(new Pattern('([aeiou][bcdfghjklmnpqrstvwxyz])co$'), '\\1ci'); // e.g. medico → medici
        yield new Transformation(new Pattern('([aeiou][bcdfghjklmnpqrstvwxyz])go$'), '\\1gi'); // e.g. psicologo → psicologi

        // Words ending in -io with stress on 'i' keep the 'i' in plural
        yield new Transformation(new Pattern('([^aeiou])io$'), '\\1i'); // e.g. zio → zii

        // Words ending in -io with stress on 'o' lose the 'i' in plural
        yield new Transformation(new Pattern('([aeiou])io$'), '\\1i'); // e.g. negozio → negozi

        // Standard ending rules
        yield new Transformation(new Pattern('a$'), 'e');  // -a → -e
        yield new Transformation(new Pattern('e$'), 'i');  // -e → -i
        yield new Transformation(new Pattern('o$'), 'i');  // -o → -i
    }

    /** @return iterable<Substitution> */
    public static function getIrregular(): iterable
    {
        // Irregular substitutions (singular => plural)
        $irregulars = [
            'ala' => 'ali',
            'albergo' => 'alberghi',
            'amica' => 'amiche',
            'amico' => 'amici',
            'ampio' => 'ampi',
            'arancia' => 'arance',
            'arma' => 'armi',
            'asparago' => 'asparagi',
            'banca' => 'banche',
            'belga' => 'belgi',
            'braccio' => 'braccia',
            'budello' => 'budella',
            'bue' => 'buoi',
            'caccia' => 'cacce',
            'calcagno' => 'calcagna',
            'camicia' => 'camicie',
            'cane' => 'cani',
            'capitale' => 'capitali',
            'carcere' => 'carceri',
            'casa' => 'case',
            'cavaliere' => 'cavalieri',
            'centinaio' => 'centinaia',
            'cerchio' => 'cerchia',
            'cervello' => 'cervella',
            'chiave' => 'chiavi',
            'chirurgo' => 'chirurgi',
            'ciglio' => 'ciglia',
            'città' => 'città',
            'corno' => 'corna',
            'corpo' => 'corpi',
            'crisi' => 'crisi',
            'dente' => 'denti',
            'dio' => 'dei',
            'dito' => 'dita',
            'dottore' => 'dottori',
            'fiore' => 'fiori',
            'fratello' => 'fratelli',
            'fuoco' => 'fuochi',
            'gamba' => 'gambe',
            'ginocchio' => 'ginocchia',
            'gioco' => 'giochi',
            'giornale' => 'giornali',
            'giraffa' => 'giraffe',
            'labbro' => 'labbra',
            'lenzuolo' => 'lenzuola',
            'libro' => 'libri',
            'madre' => 'madri',
            'maestro' => 'maestri',
            'magico' => 'magici',
            'mago' => 'maghi',
            'maniaco' => 'maniaci',
            'manico' => 'manici',
            'mano' => 'mani',
            'medico' => 'medici',
            'membro' => 'membri',
            'metropoli' => 'metropoli',
            'migliaio' => 'migliaia',
            'miglio' => 'miglia',
            'mille' => 'mila',
            'mio' => 'miei',
            'moglie' => 'mogli',
            'mosaico' => 'mosaici',
            'muro' => 'muri',
            'nemico' => 'nemici',
            'nome' => 'nomi',
            'occhio' => 'occhi',
            'orecchio' => 'orecchi',
            'osso' => 'ossa',
            'paio' => 'paia',
            'pane' => 'pani',
            'papa' => 'papi',
            'pasta' => 'paste',
            'penna' => 'penne',
            'pesce' => 'pesci',
            'piede' => 'piedi',
            'pittore' => 'pittori',
            'poeta' => 'poeti',
            'porco' => 'porci',
            'porto' => 'porti',
            'problema' => 'problemi',
            'ragazzo' => 'ragazzi',
            're' => 're',
            'rene' => 'reni',
            'riso' => 'risa',
            'rosa' => 'rosa',
            'sale' => 'sali',
            'sarto' => 'sarti',
            'scuola' => 'scuole',
            'serie' => 'serie',
            'serramento' => 'serramenta',
            'sorella' => 'sorelle',
            'specie' => 'specie',
            'staio' => 'staia',
            'stazione' => 'stazioni',
            'strido' => 'strida',
            'strillo' => 'strilla',
            'studio' => 'studi',
            'suo' => 'suoi',
            'superficie' => 'superfici',
            'tavolo' => 'tavoli',
            'tempio' => 'templi',
            'treno' => 'treni',
            'tuo' => 'tuoi',
            'uomo' => 'uomini',
            'uovo' => 'uova',
            'urlo' => 'urla',
            'valigia' => 'valigie',
            'vestigio' => 'vestigia',
            'vino' => 'vini',
            'viola' => 'viola',
            'zio' => 'zii',
        ];

        foreach ($irregulars as $singular => $plural) {
            yield new Substitution(new Word($singular), new Word($plural));
        }
    }
}

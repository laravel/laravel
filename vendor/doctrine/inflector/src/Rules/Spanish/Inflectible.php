<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\Spanish;

use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Word;

class Inflectible
{
    /** @return Transformation[] */
    public static function getSingular(): iterable
    {
        yield new Transformation(new Pattern('/ereses$/'), 'erés');
        yield new Transformation(new Pattern('/iones$/'), 'ión');
        yield new Transformation(new Pattern('/ces$/'), 'z');
        yield new Transformation(new Pattern('/es$/'), '');
        yield new Transformation(new Pattern('/s$/'), '');
    }

    /** @return Transformation[] */
    public static function getPlural(): iterable
    {
        yield new Transformation(new Pattern('/ú([sn])$/i'), 'u\1es');
        yield new Transformation(new Pattern('/ó([sn])$/i'), 'o\1es');
        yield new Transformation(new Pattern('/í([sn])$/i'), 'i\1es');
        yield new Transformation(new Pattern('/é([sn])$/i'), 'e\1es');
        yield new Transformation(new Pattern('/á([sn])$/i'), 'a\1es');
        yield new Transformation(new Pattern('/z$/i'), 'ces');
        yield new Transformation(new Pattern('/([aeiou]s)$/i'), '\1');
        yield new Transformation(new Pattern('/([^aeéiou])$/i'), '\1es');
        yield new Transformation(new Pattern('/$/'), 's');
    }

    /** @return Substitution[] */
    public static function getIrregular(): iterable
    {
        yield new Substitution(new Word('el'), new Word('los'));
        yield new Substitution(new Word('papá'), new Word('papás'));
        yield new Substitution(new Word('mamá'), new Word('mamás'));
        yield new Substitution(new Word('sofá'), new Word('sofás'));
        yield new Substitution(new Word('mes'), new Word('meses'));
    }
}

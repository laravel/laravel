<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\French;

use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Word;

class Inflectible
{
    /** @return Transformation[] */
    public static function getSingular(): iterable
    {
        yield new Transformation(new Pattern('/(b|cor|ém|gemm|soupir|trav|vant|vitr)aux$/'), '\1ail');
        yield new Transformation(new Pattern('/ails$/'), 'ail');
        yield new Transformation(new Pattern('/(journ|chev|loc)aux$/'), '\1al');
        yield new Transformation(new Pattern('/(bijou|caillou|chou|genou|hibou|joujou|pou|au|eu|eau)x$/'), '\1');
        yield new Transformation(new Pattern('/s$/'), '');
    }

    /** @return Transformation[] */
    public static function getPlural(): iterable
    {
        yield new Transformation(new Pattern('/(s|x|z)$/'), '\1');
        yield new Transformation(new Pattern('/(b|cor|ém|gemm|soupir|trav|vant|vitr)ail$/'), '\1aux');
        yield new Transformation(new Pattern('/ail$/'), 'ails');
        yield new Transformation(new Pattern('/(chacal|carnaval|festival|récital)$/'), '\1s');
        yield new Transformation(new Pattern('/al$/'), 'aux');
        yield new Transformation(new Pattern('/(bleu|émeu|landau|pneu|sarrau)$/'), '\1s');
        yield new Transformation(new Pattern('/(bijou|caillou|chou|genou|hibou|joujou|lieu|pou|au|eu|eau)$/'), '\1x');
        yield new Transformation(new Pattern('/$/'), 's');
    }

    /** @return Substitution[] */
    public static function getIrregular(): iterable
    {
        yield new Substitution(new Word('monsieur'), new Word('messieurs'));
        yield new Substitution(new Word('madame'), new Word('mesdames'));
        yield new Substitution(new Word('mademoiselle'), new Word('mesdemoiselles'));
    }
}

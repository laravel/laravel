<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\Portuguese;

use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Word;

class Inflectible
{
    /** @return Transformation[] */
    public static function getSingular(): iterable
    {
        yield new Transformation(new Pattern('/^(g|)ases$/i'), '\1ás');
        yield new Transformation(new Pattern('/(japon|escoc|ingl|dinamarqu|fregu|portugu)eses$/i'), '\1ês');
        yield new Transformation(new Pattern('/(ae|ao|oe)s$/'), 'ao');
        yield new Transformation(new Pattern('/(ãe|ão|õe)s$/'), 'ão');
        yield new Transformation(new Pattern('/^(.*[^s]s)es$/i'), '\1');
        yield new Transformation(new Pattern('/sses$/i'), 'sse');
        yield new Transformation(new Pattern('/ns$/i'), 'm');
        yield new Transformation(new Pattern('/(r|t|f|v)is$/i'), '\1il');
        yield new Transformation(new Pattern('/uis$/i'), 'ul');
        yield new Transformation(new Pattern('/ois$/i'), 'ol');
        yield new Transformation(new Pattern('/eis$/i'), 'ei');
        yield new Transformation(new Pattern('/éis$/i'), 'el');
        yield new Transformation(new Pattern('/([^p])ais$/i'), '\1al');
        yield new Transformation(new Pattern('/(r|z)es$/i'), '\1');
        yield new Transformation(new Pattern('/^(á|gá)s$/i'), '\1s');
        yield new Transformation(new Pattern('/([^ê])s$/i'), '\1');
    }

    /** @return Transformation[] */
    public static function getPlural(): iterable
    {
        yield new Transformation(new Pattern('/^(alem|c|p)ao$/i'), '\1aes');
        yield new Transformation(new Pattern('/^(irm|m)ao$/i'), '\1aos');
        yield new Transformation(new Pattern('/ao$/i'), 'oes');
        yield new Transformation(new Pattern('/^(alem|c|p)ão$/i'), '\1ães');
        yield new Transformation(new Pattern('/^(irm|m)ão$/i'), '\1ãos');
        yield new Transformation(new Pattern('/ão$/i'), 'ões');
        yield new Transformation(new Pattern('/^(|g)ás$/i'), '\1ases');
        yield new Transformation(new Pattern('/^(japon|escoc|ingl|dinamarqu|fregu|portugu)ês$/i'), '\1eses');
        yield new Transformation(new Pattern('/m$/i'), 'ns');
        yield new Transformation(new Pattern('/([^aeou])il$/i'), '\1is');
        yield new Transformation(new Pattern('/ul$/i'), 'uis');
        yield new Transformation(new Pattern('/ol$/i'), 'ois');
        yield new Transformation(new Pattern('/el$/i'), 'eis');
        yield new Transformation(new Pattern('/al$/i'), 'ais');
        yield new Transformation(new Pattern('/(z|r)$/i'), '\1es');
        yield new Transformation(new Pattern('/(s)$/i'), '\1');
        yield new Transformation(new Pattern('/$/'), 's');
    }

    /** @return Substitution[] */
    public static function getIrregular(): iterable
    {
        yield new Substitution(new Word('abdomen'), new Word('abdomens'));
        yield new Substitution(new Word('alemão'), new Word('alemães'));
        yield new Substitution(new Word('artesã'), new Word('artesãos'));
        yield new Substitution(new Word('álcool'), new Word('álcoois'));
        yield new Substitution(new Word('árvore'), new Word('árvores'));
        yield new Substitution(new Word('bencão'), new Word('bencãos'));
        yield new Substitution(new Word('cão'), new Word('cães'));
        yield new Substitution(new Word('campus'), new Word('campi'));
        yield new Substitution(new Word('cadáver'), new Word('cadáveres'));
        yield new Substitution(new Word('capelão'), new Word('capelães'));
        yield new Substitution(new Word('capitão'), new Word('capitães'));
        yield new Substitution(new Word('chão'), new Word('chãos'));
        yield new Substitution(new Word('charlatão'), new Word('charlatães'));
        yield new Substitution(new Word('cidadão'), new Word('cidadãos'));
        yield new Substitution(new Word('consul'), new Word('consules'));
        yield new Substitution(new Word('cristão'), new Word('cristãos'));
        yield new Substitution(new Word('difícil'), new Word('difíceis'));
        yield new Substitution(new Word('email'), new Word('emails'));
        yield new Substitution(new Word('escrivão'), new Word('escrivães'));
        yield new Substitution(new Word('fóssil'), new Word('fósseis'));
        yield new Substitution(new Word('gás'), new Word('gases'));
        yield new Substitution(new Word('germens'), new Word('germen'));
        yield new Substitution(new Word('grão'), new Word('grãos'));
        yield new Substitution(new Word('hífen'), new Word('hífens'));
        yield new Substitution(new Word('irmão'), new Word('irmãos'));
        yield new Substitution(new Word('liquens'), new Word('liquen'));
        yield new Substitution(new Word('mal'), new Word('males'));
        yield new Substitution(new Word('mão'), new Word('mãos'));
        yield new Substitution(new Word('orfão'), new Word('orfãos'));
        yield new Substitution(new Word('país'), new Word('países'));
        yield new Substitution(new Word('pai'), new Word('pais'));
        yield new Substitution(new Word('pão'), new Word('pães'));
        yield new Substitution(new Word('projétil'), new Word('projéteis'));
        yield new Substitution(new Word('réptil'), new Word('répteis'));
        yield new Substitution(new Word('sacristão'), new Word('sacristães'));
        yield new Substitution(new Word('sotão'), new Word('sotãos'));
        yield new Substitution(new Word('tabelião'), new Word('tabeliães'));
    }
}

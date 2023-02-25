<?php

namespace Faker\Provider\zh_TW;

/**
 * @deprecated Use {@link \Faker\Provider\Internet} instead
 * @see \Faker\Provider\Internet
 */
class Internet extends \Faker\Provider\Internet
{
    /**
     * @deprecated Use {@link \Faker\Provider\Internet::userName()} instead
     * @see \Faker\Provider\Internet::userName()
     */
    public function userName()
    {
        return parent::userName();
    }

    /**
     * @deprecated Use {@link \Faker\Provider\Internet::domainWord()} instead
     * @see \Faker\Provider\Internet::domainWord()
     */
    public function domainWord()
    {
        return parent::domainWord();
    }
}

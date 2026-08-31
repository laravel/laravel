<?php

namespace Faker\Provider\fa_IR;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @see https://fa.wikipedia.org/wiki/%D8%B4%D9%85%D8%A7%D8%B1%D9%87%E2%80%8C%D9%87%D8%A7%DB%8C_%D8%AA%D9%84%D9%81%D9%86_%D8%AF%D8%B1_%D8%A7%DB%8C%D8%B1%D8%A7%D9%86#.D8.AA.D9.84.D9.81.D9.86.E2.80.8C.D9.87.D8.A7.DB.8C_.D9.87.D9.85.D8.B1.D8.A7.D9.87
     */
    protected static $formats = [ // land line formts seprated by province
        '011########', //Mazandaran
        '013########', //Gilan
        '017########', //Golestan
        '021########', //Tehran
        '023########', //Semnan
        '024########', //Zanjan
        '025########', //Qom
        '026########', //Alborz
        '028########', //Qazvin
        '031########', //Isfahan
        '034########', //Kerman
        '035########', //Yazd
        '038########', //Chaharmahal and Bakhtiari
        '041########', //East Azerbaijan
        '044########', //West Azerbaijan
        '045########', //Ardabil
        '051########', //Razavi Khorasan
        '054########', //Sistan and Baluchestan
        '056########', //South Khorasan
        '058########', //North Khorasan
        '061########', //Khuzestan
        '066########', //Lorestan
        '071########', //Fars
        '074########', //Kohgiluyeh and Boyer-Ahmad
        '076########', //Hormozgan
        '077########', //Bushehr
        '081########', //Hamadan
        '083########', //Kermanshah
        '084########', //Ilam
        '086########', //Markazi
        '087########', //Kurdistan
    ];

    protected static $mobileNumberPrefixes = [
        '0910#######', //mci
        '0911#######',
        '0912#######',
        '0913#######',
        '0914#######',
        '0915#######',
        '0916#######',
        '0917#######',
        '0918#######',
        '0919#######',
        '0901#######',
        '0901#######',
        '0902#######',
        '0903#######',
        '0930#######',
        '0933#######',
        '0935#######',
        '0936#######',
        '0937#######',
        '0938#######',
        '0939#######',
        '0920#######',
        '0921#######',
        '0937#######',
        '0990#######', // MCI
    ];

    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileNumberPrefixes));
    }
}

<?php

namespace Faker\Provider\es_VE;

class Address extends \Faker\Provider\es_ES\Address
{
    protected static $cityPrefix = ['San', 'Santa', 'Puerto', 'Valle', 'Villa', 'Parroquia', 'El', 'Los', 'La', 'Las'];
    protected static $citySuffix = ['del Valle', 'de Mara', 'de Altagracia', 'de Asis', 'del Tuy', 'de Mata'];
    protected static $buildingNumber = ['%##', '%#', '%'];
    protected static $streetPrefix = [
        'Calle', 'Avenida', 'Av.', 'Cl.', 'Carretera', 'Callejón', 'Vereda',
    ];
    protected static $streetSuffix = ['Norte', 'Este', ' Sur', ' Oeste'];
    protected static $postcode = ['####'];
    protected static $state = [
        'Amazonas', 'Anzoátegui', 'Apure', 'Aragua', 'Barinas', 'Bolívar', 'Carabobo', 'Cojedes', 'Delta Amacuro',
        'Distrito Capital', 'Falcón', 'Guárico', 'Lara', 'Mérida', 'Miranda', 'Monagas', 'Nueva Esparta', 'Portuguesa',
        'Sucre', 'Táchira', 'Trujillo', 'Vargas', 'Yaracuy', 'Zulia',
    ];
    protected static $cityFormats = [
        '{{cityPrefix}} {{firstName}}{{citySuffix}}',
        '{{cityPrefix}} {{firstName}}',
        '{{firstName}} {{citySuffix}}',
        '{{lastName}} {{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{streetPrefix}} {{firstName}}',
        '{{streetPrefix}} {{lastName}}',
        '{{streetPrefix}} {{firstName}} {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}}, {{buildingNumber}}, {{secondaryAddress}}',
        '{{streetName}}, {{secondaryAddress}}',
    ];
    protected static $addressFormats = [
        '{{streetAddress}}, {{city}} Edo. {{state}}',
        '{{streetAddress}}, {{city}} Edo. {{state}}, {{postcode}}',
    ];
    protected static $secondaryAddressFormats = ['Nro #', 'Piso #', 'Casa #', 'Hab. #', 'Apto #', 'Nro ##', 'Piso ##', 'Casa ##', 'Hab. ##', 'Apto ##'];

    /**
     * @example 'Avenida'
     */
    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * @example 'Villa'
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example 'Nro 3'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Aragua'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }
}

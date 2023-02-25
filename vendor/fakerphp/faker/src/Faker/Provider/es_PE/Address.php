<?php

namespace Faker\Provider\es_PE;

class Address extends \Faker\Provider\es_ES\Address
{
    protected static $cityPrefix = ['San', 'Puerto', 'Gral.', 'Don'];
    protected static $citySuffix = ['Alta', 'Baja', 'Norte', 'Este', ' Sur', ' Oeste'];
    protected static $buildingNumber = ['%####', '%###', '%##', '%#', '%'];
    protected static $streetPrefix = ['Jr.', 'Av.', 'Cl.', 'Urb.'];
    protected static $streetSuffix = [''];
    protected static $postcode = ['LIMA ##'];
    protected static $state = [
        'Lima', 'Callao', 'Arequipa', 'Cuzco', 'Piura', 'Iquitos', 'Huaraz', 'Tacna', 'Ayacucho', 'Pucallpa', 'Trujillo', 'Chimbote', 'Ica', 'Moquegua', 'Puno', 'Tarapoto', 'Cajamarca', 'Lambayeque', 'Huanuco', 'Jauja', 'Tumbes', 'Madre de Dios',
    ];
    protected static $cityFormats = [
        '{{cityPrefix}} {{firstName}} {{lastName}}',
        '{{cityPrefix}} {{firstName}}',
        '{{firstName}} {{citySuffix}}',
        '{{lastName}} {{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{streetPrefix}} {{firstName}} {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}} # {{buildingNumber}} ',
        '{{streetName}} # {{buildingNumber}} {{secondaryAddress}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{city}}, {{state}}",
    ];
    protected static $secondaryAddressFormats = ['Dpto. ###', 'Hab. ###', 'Piso #', 'Piso ##'];

    /**
     * @example ''
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example 'Jr.'
     */
    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * @example 'Dpto. 402'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Lima'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }
}

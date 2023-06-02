<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serializer Class Paths
    |--------------------------------------------------------------------------
    |
    | The full class path to the serializer classes you want to use for both
    | success- and error responses. The success serializer must implement
    | Fractal's serializer. You can override these for every response.
    |
    */

    'serializers' => [
        'success' => Flugg\Responder\Serializers\SuccessSerializer::class,
        'error' => \Flugg\Responder\Serializers\ErrorSerializer::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Decorators
    |--------------------------------------------------------------------------
    |
    | Response decorators are used to decorate both your success- and error
    | responses. A decorator can be disabled by removing it from the list
    | below. You may additionally add your own decorators to the list.
    |
    */

    'decorators' => [
        \Flugg\Responder\Http\Responses\Decorators\StatusCodeDecorator::class,
        \Flugg\Responder\Http\Responses\Decorators\SuccessFlagDecorator::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Transformer
    |--------------------------------------------------------------------------
    |
    | When transforming data without specifying a transformer we'll instead
    | use a fallback transformer specified below. The [ArrayTransformer]
    | transformer will simply convert the data to an array untouched.
    |
    */

    'fallback_transformer' => \Flugg\Responder\Transformers\ArrayTransformer::class,

    /*
    |--------------------------------------------------------------------------
    | Load Relationships With Query String Parameter
    |--------------------------------------------------------------------------
    |
    | The package can automatically load relationships from the query string
    | and will look for a query string parameter with the name configured
    | below. You can set the value to null to disable the autoloading.
    |
    */

    'load_relations_parameter' => 'with',

    /*
    |--------------------------------------------------------------------------
    | Filter Fields With Query String Parameter
    |--------------------------------------------------------------------------
    |
    | The package can automatically filter the fields of transformed data
    | from a query string parameter configured below. The technique is
    | also known as sparse fieldsets. Set it to null to disable it.
    |
    */

    'filter_fields_parameter' => 'only',

    /*
    |--------------------------------------------------------------------------
    | Recursion Limit
    |--------------------------------------------------------------------------
    |
    | When transforming data, you may be including relations recursively.
    | By setting the value below, you can limit the amount of times it
    | should include relationships recursively. Five might be good.
    |
    */

    'recursion_limit' => 5,

    /*
    |--------------------------------------------------------------------------
    | Error Message Translation Files
    |--------------------------------------------------------------------------
    |
    | You can declare error messages in a language file, which allows for
    | returning messages in different languages. The array below lists
    | the language files that will be searched in to find messages.
    |
    */

    'error_message_files' => ['errors'],

    /*
    |--------------------------------------------------------------------------
    | CamelCase Relations
    |--------------------------------------------------------------------------
    |
    | By default laravel responder will convert relations request to camel-case
    | but some people would like to use snake-case, so you can set it below
    |
    */

    'use_camel_case_relations' => true,

];
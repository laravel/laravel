<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filename & Format
    |--------------------------------------------------------------------------
    |
    | The default filename
    |
    */

    'filename'  => '_ide_helper.php',

    /*
    |--------------------------------------------------------------------------
    | Models filename
    |--------------------------------------------------------------------------
    |
    | The default filename for the models helper file
    |
    */

    'models_filename' => '_ide_helper_models.php',

    /*
    |--------------------------------------------------------------------------
    | Where to write the PhpStorm specific meta file
    |--------------------------------------------------------------------------
    |
    | PhpStorm also supports the directory `.phpstorm.meta.php/` with arbitrary
    | files in it, should you need additional files for your project; e.g.
    | `.phpstorm.meta.php/laravel_ide_Helper.php'.
    |
    */
    'meta_filename' => '.phpstorm.meta.php',

    /*
    |--------------------------------------------------------------------------
    | Fluent helpers
    |--------------------------------------------------------------------------
    |
    | Set to true to generate commonly used Fluent methods
    |
    */

    'include_fluent' => false,

    /*
    |--------------------------------------------------------------------------
    | Factory Builders
    |--------------------------------------------------------------------------
    |
    | Set to true to generate factory generators for better factory()
    | method auto-completion.
    |
    | Deprecated for Laravel 8 or latest.
    |
    */

    'include_factory_builders' => false,

    /*
    |--------------------------------------------------------------------------
    | Write Model Magic methods
    |--------------------------------------------------------------------------
    |
    | Set to false to disable write magic methods of model
    |
    */

    'write_model_magic_where' => true,

    /*
    |--------------------------------------------------------------------------
    | Write Model External Eloquent Builder methods
    |--------------------------------------------------------------------------
    |
    | Set to false to disable write external eloquent builder methods
    |
    */

    'write_model_external_builder_methods' => true,

    /*
    |--------------------------------------------------------------------------
    | Write Model relation count properties
    |--------------------------------------------------------------------------
    |
    | Set to false to disable writing of relation count properties to model DocBlocks.
    |
    */

    'write_model_relation_count_properties' => true,

    /*
    |--------------------------------------------------------------------------
    | Write Eloquent Model Mixins
    |--------------------------------------------------------------------------
    |
    | This will add the necessary DocBlock mixins to the model class
    | contained in the Laravel Framework. This helps the IDE with
    | auto-completion.
    |
    | Please be aware that this setting changes a file within the /vendor directory.
    |
    */

    'write_eloquent_model_mixins' => false,

    /*
    |--------------------------------------------------------------------------
    | Helper files to include
    |--------------------------------------------------------------------------
    |
    | Include helper files. By default not included, but can be toggled with the
    | -- helpers (-H) option. Extra helper files can be included.
    |
    */

    'include_helpers' => false,

    'helper_files' => [
        base_path() . '/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model locations to include
    |--------------------------------------------------------------------------
    |
    | Define in which directories the ide-helper:models command should look
    | for models.
    |
    | glob patterns are supported to easier reach models in sub-directories,
    | e.g. `app/Services/* /Models` (without the space)
    |
    */

    'model_locations' => [
        'src',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models to ignore
    |--------------------------------------------------------------------------
    |
    | Define which models should be ignored.
    |
    */

    'ignored_models' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Models hooks
    |--------------------------------------------------------------------------
    |
    | Define which hook classes you want to run for models to add custom information
    |
    | Hooks should implement Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface.
    |
    */

    'model_hooks' => [
        // App\Support\IdeHelper\MyModelHook::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Extra classes
    |--------------------------------------------------------------------------
    |
    | These implementations are not really extended, but called with magic functions
    |
    */

    'extra' => [
        'Eloquent' => ['Illuminate\Database\Eloquent\Builder', 'Illuminate\Database\Query\Builder'],
        'Session' => ['Illuminate\Session\Store'],
    ],

    'magic' => [],

    /*
    |--------------------------------------------------------------------------
    | Interface implementations
    |--------------------------------------------------------------------------
    |
    | These interfaces will be replaced with the implementing class. Some interfaces
    | are detected by the helpers, others can be listed below.
    |
    */

    'interfaces' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Support for custom DB types
    |--------------------------------------------------------------------------
    |
    | This setting allow you to map any custom database type (that you may have
    | created using CREATE TYPE statement or imported using database plugin
    | / extension to a Doctrine type.
    |
    | Each key in this array is a name of the Doctrine2 DBAL Platform. Currently valid names are:
    | 'postgresql', 'db2', 'drizzle', 'mysql', 'oracle', 'sqlanywhere', 'sqlite', 'mssql'
    |
    | This name is returned by getName() method of the specific Doctrine/DBAL/Platforms/AbstractPlatform descendant
    |
    | The value of the array is an array of type mappings. Key is the name of the custom type,
    | (for example, "jsonb" from Postgres 9.4) and the value is the name of the corresponding Doctrine2 type (in
    | our case it is 'json_array'. Doctrine types are listed here:
    | https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#types
    |
    | So to support jsonb in your models when working with Postgres, just add the following entry to the array below:
    |
    | "postgresql" => array(
    |       "jsonb" => "json_array",
    |  ),
    |
    */
    'custom_db_types' => [

    ],

    /*
     |--------------------------------------------------------------------------
     | Support for camel cased models
     |--------------------------------------------------------------------------
     |
     | There are some Laravel packages (such as Eloquence) that allow for accessing
     | Eloquent model properties via camel case, instead of snake case.
     |
     | Enabling this option will support these packages by saving all model
     | properties as camel case, instead of snake case.
     |
     | For example, normally you would see this:
     |
     |  * @property \Illuminate\Support\Carbon $created_at
     |  * @property \Illuminate\Support\Carbon $updated_at
     |
     | With this enabled, the properties will be this:
     |
     |  * @property \Illuminate\Support\Carbon $createdAt
     |  * @property \Illuminate\Support\Carbon $updatedAt
     |
     | Note, it is currently an all-or-nothing option.
     |
     */
    'model_camel_case_properties' => false,

    /*
    |--------------------------------------------------------------------------
    | Property Casts
    |--------------------------------------------------------------------------
    |
    | Cast the given "real type" to the given "type".
    |
    */
    'type_overrides' => [
        'integer' => 'int',
        'boolean' => 'bool',
    ],

    /*
    |--------------------------------------------------------------------------
    | Include DocBlocks from classes
    |--------------------------------------------------------------------------
    |
    | Include DocBlocks from classes to allow additional code inspection for
    | magic methods and properties.
    |
    */
    'include_class_docblocks' => false,

    /*
    |--------------------------------------------------------------------------
    | Force FQN usage
    |--------------------------------------------------------------------------
    |
    | Use the fully qualified (class) name in docBlock,
    | event if class exists in a given file
    | or there is an import (use className) of a given class
    |
    */
    'force_fqn' => false,

    /*
    |--------------------------------------------------------------------------
    | Use generics syntax
    |--------------------------------------------------------------------------
    |
    | Use generics syntax within DocBlocks,
    | e.g. `Collection<User>` instead of `Collection|User[]`.
    |
    */
    'use_generics_annotations' => true,

    /*
    |--------------------------------------------------------------------------
    | Additional relation types
    |--------------------------------------------------------------------------
    |
    | Sometimes it's needed to create custom relation types. The key of the array
    | is the Relationship Method name. The value of the array is the canonical class
    | name of the Relationship, e.g. `'relationName' => RelationShipClass::class`.
    |
    */
    'additional_relation_types' => [],

    /*
    |--------------------------------------------------------------------------
    | Additional relation return types
    |--------------------------------------------------------------------------
    |
    | When using custom relation types its possible for the class name to not contain
    | the proper return type of the relation. The key of the array is the relationship
    | method name. The value of the array is the return type of the relation.
    | e.g. `'relationName' => 'many'`.
    |
    */
    'additional_relation_return_types' => [],

    /*
    |--------------------------------------------------------------------------
    | Run artisan commands after migrations to generate model helpers
    |--------------------------------------------------------------------------
    |
    | The specified commands should run after migrations are finished running.
    |
    */
    'post_migrate' => [
        'ide-helper:models --write',
    ],

];

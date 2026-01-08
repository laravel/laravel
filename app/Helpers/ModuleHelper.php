<?php

if (!function_exists('getModuleSchema')) {
    function getModuleSchema()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callingClass = $backtrace[1]['class'] ?? null;
        $callingFile = $backtrace[1]['file'] ?? '';

        // 1. Primero intentar obtener la conexión de la propiedad $connection de la clase
        if ($callingClass && class_exists($callingClass)) {
            try {
                $reflector = new ReflectionClass($callingClass);
                if ($reflector->hasProperty('connection')) {
                    $property = $reflector->getProperty('connection');
                    $property->setAccessible(true);
                    $connectionName = $property->getValue(new $callingClass);

                    if ($connectionName) {
                        // Verificar si la conexión ya existe
                        if (!array_key_exists($connectionName, config('database.connections'))) {
                            // Si no existe, cargarla desde la configuración del módulo
                            loadModuleConnection($connectionName);
                        }

                        return \Illuminate\Support\Facades\Schema::connection($connectionName);
                    }
                }
            } catch (\Exception $e) {
                // Continuar con el método alternativo
            }
        }

        // 2. Método alternativo por nombre de módulo (backward compatibility)
        if (preg_match('#modules/([^/]+)/#i', $callingFile, $matches)) {
            $moduleName = $matches[1];// strtolower($matches[1]);

            // Verificar si la conexión ya existe
            if (!array_key_exists($moduleName, config('database.connections'))) {
                // Si no existe, cargarla desde la configuración del módulo
                loadModuleConnection($moduleName);
            }

            return \Illuminate\Support\Facades\Schema::connection($moduleName);
        }

        // 3. Conexión por defecto
        return \Illuminate\Support\Facades\Schema::connection(config('database.default'));
    }
}

if (!function_exists('loadModuleConnection')) {
    function loadModuleConnection($connectionName)
    {
        // Buscar el módulo por nombre de conexión
        $modulesPath = base_path('modules');
        $moduleDirs = glob($modulesPath . '/*', GLOB_ONLYDIR);

        foreach ($moduleDirs as $modulePath) {
            $moduleDirName = basename($modulePath);

            // Si el nombre del directorio coincide con la conexión (en cualquier case)
            if ($moduleDirName === $connectionName) {
                $configPath = "{$modulePath}/config/database.php";

                if (file_exists($configPath)) {
                    $config = require $configPath;

                    if (isset($config['connection'])) {
                        // Registrar la conexión dinámicamente
                        config(["database.connections.{$connectionName}" => $config['connection']]);
                        return true;
                    }
                }
            }
        }

        throw new \Exception("No se pudo cargar la conexión '{$connectionName}'. Verifica que el módulo exista y tenga config/database.php");
    }
}
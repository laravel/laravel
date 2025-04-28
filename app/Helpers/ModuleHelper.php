<?php

if (!function_exists('getModuleSchema')) {
    /**
     * Obtiene una instancia de Schema para una conexión específica basada en la ruta del archivo.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    function getModuleSchema()
    {
        // Obtener la ruta completa del archivo de migración llamante
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callingFile = $backtrace[1]['file'] ?? '';

        // Extraer el nombre del módulo desde la ruta del archivo
        // Suponiendo que los módulos están en `modules/ModuleName/`
        if (preg_match('#modules/([^/]+)/#i', $callingFile, $matches)) {
            //$moduleName = strtolower($matches[1]);
            $moduleName = $matches[1];
            return \Illuminate\Support\Facades\Schema::connection($moduleName);
        }

        // Retornar Schema predeterminado si no se encuentra el módulo
        return \Illuminate\Support\Facades\Schema::connection(null);
    }
}

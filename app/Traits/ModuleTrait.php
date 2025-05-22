<?php

namespace App\Traits;

trait ModuleTrait
{
    /**
     * Obtiene el nombre de la conexión de base de datos basado en el módulo.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        // Extrae el nombre del módulo desde el namespace de la clase
        $namespaceParts = explode('\\', static::class);
        $moduleName = $namespaceParts[1] ?? null;

        // Retorna el nombre del módulo como la conexión, en minúsculas
        return $moduleName ? $moduleName : null;
    }
}

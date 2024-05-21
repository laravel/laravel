#!/bin/bash

# Verificar si Homebrew está instalado
if ! command -v brew &> /dev/null; then
    echo "❌ Homebrew no está instalado. Por favor, instálalo desde https://brew.sh/"
    exit 1
fi

# Obtener la versión de PHP desde composer.json
PHP_VERSION=$(cat composer.json | grep '"php":' | awk -F'"' '{print $4}' | sed 's/\^//')

if [ -z "$PHP_VERSION" ]; then
    echo "❌ No se encontró la versión de PHP en composer.json"
    exit 1
fi

echo "🔍 Versión de PHP requerida: $PHP_VERSION"

# Formatear la versión a la sintaxis de Homebrew
BREW_PHP_VERSION="php@$PHP_VERSION"

# Comprobar si la versión ya está instalada
if brew list --formula | grep -q "^$BREW_PHP_VERSION\$"; then
    echo "✅ La versión $PHP_VERSION ya está instalada."
else
    echo "⚠️ La versión $PHP_VERSION no está instalada. Instalándola ahora..."
    brew install $BREW_PHP_VERSION
fi

# Obtener la versión actual de PHP
CURRENT_PHP_VERSION=$(php -v | head -n 1 | awk '{print $2}' | cut -d'.' -f1,2)
BREW_CURRENT_PHP="php@$CURRENT_PHP_VERSION"

# Si la versión activa no coincide con la requerida, cambiarla
if [ "$BREW_CURRENT_PHP" != "$BREW_PHP_VERSION" ]; then
    echo "🔄 Cambiando de PHP $CURRENT_PHP_VERSION a PHP $PHP_VERSION..."
    brew unlink $BREW_CURRENT_PHP
    brew link --force --overwrite $BREW_PHP_VERSION
else
    echo "✅ PHP ya está en la versión correcta ($PHP_VERSION)."
fi

# Mostrar la versión actual después del cambio
php -v

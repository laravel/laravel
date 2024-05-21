#!/bin/bash

# Check if Homebrew is installed
if ! command -v brew &> /dev/null; then
    echo "❌ Homebrew is not installed. Please install it from https://brew.sh/"
    exit 1
fi

# Check if jq is installed
if ! command -v jq &> /dev/null; then
    echo "❌ The script requires 'jq'. Install it with 'brew install jq'."
    exit 1
fi

# Get PHP version from composer.json
PHP_VERSION=$(cat composer.json | grep '"php":' | awk -F'"' '{print $4}' | sed 's/\^//')

if [ -z "$PHP_VERSION" ]; then
    echo "❌ PHP version not found in composer.json"
    exit 1
fi

echo "🔍 Required PHP version: $PHP_VERSION"

# Get current stable PHP version from Homebrew
LATEST_PHP_VERSION_FULL=$(brew info php --json | jq -r '.[0].versions.stable')
LATEST_PHP_VERSION=$(echo "$LATEST_PHP_VERSION_FULL" | cut -d'.' -f1,2)

# Use the correct formula depending on whether it's the latest version or not
if [[ "$PHP_VERSION" == "$LATEST_PHP_VERSION" ]]; then
    BREW_PHP_VERSION="php"
else
    BREW_PHP_VERSION="php@$PHP_VERSION"
fi

# Check if the version is already installed
if brew list --formula | grep -q "^$BREW_PHP_VERSION\$"; then
    echo "✅ Version $PHP_VERSION is already installed."
else
    echo "⚠️ Version $PHP_VERSION is not installed. Installing now..."
    brew install $BREW_PHP_VERSION
fi

# Get the current active PHP version in the system
CURRENT_PHP_VERSION=$(php -v | head -n 1 | awk '{print $2}' | cut -d'.' -f1,2)

if [[ "$CURRENT_PHP_VERSION" == "$LATEST_PHP_VERSION" ]]; then
    BREW_CURRENT_PHP="php"
else
    BREW_CURRENT_PHP="php@$CURRENT_PHP_VERSION"
fi

# If active version doesn't match, change it
if [ "$BREW_CURRENT_PHP" != "$BREW_PHP_VERSION" ]; then
    echo "🔄 Switching from PHP $CURRENT_PHP_VERSION to PHP $PHP_VERSION..."
    brew unlink $BREW_CURRENT_PHP
    brew link --force --overwrite $BREW_PHP_VERSION
else
    echo "✅ PHP is already at the correct version ($PHP_VERSION)."
fi

# Show current version after the change
php -v

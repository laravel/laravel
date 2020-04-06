#!/bin/bash

# Helpers
# -------

exitWithMessageOnError () {
  if [ ! $? -eq 0 ]; then
    echo "An error has occurred during deployment."
    echo $1
    exit 1
  fi
}

# Prerequisites
# -------------

# Verify node.js installed
hash node 2>/dev/null
exitWithMessageOnError "Missing node.js executable, please install node.js, if already installed make sure it can be reached from current environment."

# Setup
# -----

PHP=`which php`

SCRIPT_DIR="${BASH_SOURCE[0]%\\*}"
SCRIPT_DIR="${SCRIPT_DIR%/*}"
ARTIFACTS=$SCRIPT_DIR/../artifacts
KUDU_SYNC_CMD=${KUDU_SYNC_CMD//\"}

if [[ ! -n "$DEPLOYMENT_SOURCE" ]]; then
  DEPLOYMENT_SOURCE=$SCRIPT_DIR
fi

if [[ ! -n "$NEXT_MANIFEST_PATH" ]]; then
  NEXT_MANIFEST_PATH=$ARTIFACTS/manifest

  if [[ ! -n "$PREVIOUS_MANIFEST_PATH" ]]; then
    PREVIOUS_MANIFEST_PATH=$NEXT_MANIFEST_PATH
  fi
fi

if [[ ! -n "$DEPLOYMENT_TARGET" ]]; then
  DEPLOYMENT_TARGET=$ARTIFACTS/wwwroot
else
  KUDU_SERVICE=true
fi

if [[ ! -n "$KUDU_SYNC_CMD" ]]; then
  # Install kudu sync
  echo "Installing Kudu Sync"
  npm install kudusync -g --silent
  exitWithMessageOnError "npm failed"

  if [[ ! -n "$KUDU_SERVICE" ]]; then
    # In case we are running locally this is the correct location of kuduSync
    KUDU_SYNC_CMD=kuduSync
  else
    # In case we are running on kudu service this is the correct location of kuduSync
    KUDU_SYNC_CMD=$APPDATA/npm/node_modules/kuduSync/bin/kuduSync
  fi
fi

# Node Helpers
# ------------

selectNodeVersion () {
  if [[ -n "$KUDU_SELECT_NODE_VERSION_CMD" ]]; then
    SELECT_NODE_VERSION="$KUDU_SELECT_NODE_VERSION_CMD \"$DEPLOYMENT_SOURCE\" \"$DEPLOYMENT_TARGET\" \"$DEPLOYMENT_TEMP\""
    eval $SELECT_NODE_VERSION
    exitWithMessageOnError "select node version failed"

    if [[ -e "$DEPLOYMENT_TEMP/__nodeVersion.tmp" ]]; then
      NODE_EXE=`cat "$DEPLOYMENT_TEMP/__nodeVersion.tmp"`
      exitWithMessageOnError "getting node version failed"
    fi

    if [[ -e "$DEPLOYMENT_TEMP/.tmp" ]]; then
      NPM_JS_PATH=`cat "$DEPLOYMENT_TEMP/__npmVersion.tmp"`
      exitWithMessageOnError "getting npm version failed"
    fi

    if [[ ! -n "$NODE_EXE" ]]; then
      NODE_EXE=node
    fi

    NPM_CMD="\"$NODE_EXE\" \"$NPM_JS_PATH\""
  else
    NPM_CMD=npm
    NODE_EXE=node
  fi
}

##################################################################################################################################
# Deployment
# ----------

# 0. Variables
echo "-----------------Variables---------------------------------"
$PHP -v
echo "BASH_SOURCE = $BASH_SOURCE"
echo "SCRIPT_DIR = $SCRIPT_DIR"
echo "ARTIFACTS = $ARTIFACTS"
echo "KUDU_SERVICE = $KUDU_SERVICE"
echo "KUDU_SYNC_CMD = $KUDU_SYNC_CMD"
echo "SELECT_NODE_VERSION = $SELECT_NODE_VERSION"
echo "NODE_EXE = $NODE_EXE"
echo "NPM_CMD = $NPM_CMD"
echo "DEPLOYMENT_SOURCE = $DEPLOYMENT_SOURCE"
echo "DEPLOYMENT_TARGET = $DEPLOYMENT_TARGET"
echo "NEXT_MANIFEST_PATH = $NEXT_MANIFEST_PATH"
echo "PREVIOUS_MANIFEST_PATH = $PREVIOUS_MANIFEST_PATH"
echo "POST_DEPLOYMENT_ACTION_DIR = $POST_DEPLOYMENT_ACTION_DIR"
echo "POST_DEPLOYMENT_ACTION = $POST_DEPLOYMENT_ACTION"
echo "-----------------Variables END ---------------------------------"
echo ""
echo ""

# 1. KuduSync
if [[ "$IN_PLACE_DEPLOYMENT" -ne "1" ]]; then
  "$KUDU_SYNC_CMD" -v 50 -f "$DEPLOYMENT_SOURCE" -t "$DEPLOYMENT_TARGET" -n "$NEXT_MANIFEST_PATH" -p "$PREVIOUS_MANIFEST_PATH" -i ".git;.hg;.deployment;deploy.sh"
  exitWithMessageOnError "Kudu Sync failed"
fi

# 2. Composer
if [ ! -e "$DEPLOYMENT_TARGET/composer.phar" ]; then
  echo "Downloading composer.phar"
  $PHP -r 'copy("https://getcomposer.org/installer", "composer-setup.php");'
  exitWithMessageOnError "Downloading composer.phar failed"
fi

if [ -e "$DEPLOYMENT_TARGET/composer.json" ]; then
  echo "Running composer install"
  cd "$DEPLOYMENT_TARGET"
  $PHP composer.phar install --no-dev
  exitWithMessageOnError "Installing composer packages failed"
  cd - > /dev/null
fi

if [ -e "$DEPLOYMENT_TARGET/composer.phar" ]; then
  echo "Deleting composer.phar"
  $PHP -r 'unlink("composer-setup.php");'
  exitWithMessageOnError "Deleting composer.phar failed"
fi

# 3. Install NPM packages
if [ -e "$DEPLOYMENT_TARGET/package.json" ]; then
  echo "Running npm install"
  cd "$DEPLOYMENT_TARGET"
  eval npm install --production
  exitWithMessageOnError "npm failed"
  cd - > /dev/null
fi

##################################################################################################################################

# Post deployment stub
if [[ -n "$POST_DEPLOYMENT_ACTION" ]]; then
  POST_DEPLOYMENT_ACTION=${POST_DEPLOYMENT_ACTION//\"}
  cd "${POST_DEPLOYMENT_ACTION_DIR%\\*}"
  "$POST_DEPLOYMENT_ACTION"
  exitWithMessageOnError "Post deployment action failed"
fi

echo "Finished successfully."

#!/bin/bash

set -e $DRUPAL_TI_DEBUG

# Ensure the right Drupal version is installed.
# Note: This function is re-entrant.
drupal_ti_ensure_drupal

# Add custom modules to drupal build.
cd "$DRUPAL_TI_DRUPAL_DIR"

# Download custom branches of address and composer_manager.
(
    cd "$DRUPAL_TI_MODULES_PATH"
    git clone --branch 8.x-1.x http://git.drupal.org/project/composer_manager.git
)

# Ensure the module is linked into the codebase.
cd "$DRUPAL_TI_DRUPAL_DIR"
MODULE_DIR=$(cd "$TRAVIS_BUILD_DIR"; pwd)

mkdir -p "$DRUPAL_TI_DRUPAL_DIR/$DRUPAL_TI_MODULES_PATH"

# Point module into the drupal installation.
cd "$DRUPAL_TI_DRUPAL_DIR"
ln -sf "$MODULE_DIR" "$DRUPAL_TI_MODULES_PATH/$DRUPAL_TI_MODULE_NAME"

# Initialize composer_manager.
php modules/composer_manager/scripts/init.php
composer drupal-rebuild
composer update -n --verbose
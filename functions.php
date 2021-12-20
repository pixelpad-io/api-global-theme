<?php

define("CHILD_THEME_URL", get_stylesheet_directory_uri());
define("CHILD_THEME_DIR", get_stylesheet_directory());
define("PARENT_THEME_DIR", dirname(__FILE__));

/**
 * load necessary front-end files
 */
add_action("init", function () {
    require_once(CHILD_THEME_DIR . "/class-front-page.php");
});

/**
 * load necessary admin files
 */
add_action("admin_init", function () {
    require_once(PARENT_THEME_DIR . "/class-updater.php");
    $updater = new ThemeUpdater();
});

<?php

define("GLOBAL_THEME_DIR", dirname(__FILE__));

/**
 * load necessary front-end files
 */
add_action("init", function () {
    require_once(GLOBAL_THEME_DIR . "/class-front-page.php");
});

/**
 * load necessary admin files
 */
add_action("admin_init", function () {
    require_once(GLOBAL_THEME_DIR . "/class-updater.php");
    $updater = new ThemeUpdater();
});

<?php

define("CHILD_THEME_URL", get_stylesheet_directory_uri());
define("CHILD_THEME_DIR", get_stylesheet_directory());


add_action("init", function () {
    /**
     * load necessary files
     */
    require_once(CHILD_THEME_DIR . "/class-front-page.php");

}, 1);


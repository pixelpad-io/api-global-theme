<?php

namespace THEME;

FourOhFour::render();

/** This file is the HTTP 404 error template. It is rendered when the
 * query doesn't match anything (or when a 404 is generated through any
 * other means). */
class FourOhFour {

    public static function render() {
        ?>
        <h1><?php _e('404 Not Found', 'pixelpad-theme'); ?></h1>
        <p><?php _e('Page cannot be found!', 'pixelpad-theme'); ?></p>

        <?php
    }

}

<?php

namespace THEME;

$fof = new FourOhFour();
$fof->render();

/** This file is the HTTP 404 error template. It is rendered when the
 * query doesn't match anything (or when a 404 is generated through any
 * other means). */
class FourOhFour extends \THEME\Page {

    public function render_content() {
        ?>
        <h1><?php _e('404 Not Found', 'pixelpad-theme'); ?></h1>
        <p><?php _e('Page cannot be found!', 'pixelpad-theme'); ?></p>

        <?php
    }

}

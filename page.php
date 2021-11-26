<?php

/**
 * Default for displaying all pages. typically used by Woocommerce
 *
 */

namespace THEME;

$default = new DefaultPage();
$default->render();

class DefaultPage extends Page {

    public function render_content() {
        while (have_posts()) {
            the_post();
            the_content();
        }
    }
}

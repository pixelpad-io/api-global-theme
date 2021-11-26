<?php

/**
 * the child should inherit from this frontpage
 */

class FrontPageParent {
    public function __construct() {
    }

    public function render() {
        echo "API FRONT PAGE";
    }
}

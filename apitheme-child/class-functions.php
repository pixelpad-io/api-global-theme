<?php

class GamejamFunctions {
    public function __construct() {
        /* ignores case sensitivity for posts, specifically for the gamejam */
        add_action('parse_request', array(__CLASS__, 'parseUppercaseGameJam'));
    }


    /**
     * allow users to use pixelpad.io/gamejam or Gamejam or GAMEJAM and link to same place
     * @param type $wp
     * 
     */
    public static function parseUppercaseGameJam($wp) {
        if (isset($wp->query_vars["category_name"])) {
            $query = $wp->query_vars["category_name"];
            if (strtolower($query) == "gamejam") {
                if ((bool) preg_match('/[A-Z]/', $query)) {
                    wp_redirect(site_url() . '/' . strtolower($query), 301);
                    exit;
                }
            }
        }
    }

    /**
     * set object variables to default key and value. if key and value given by user, use that instead
     */
    public static function parse_default_props($object, $default_array, $array_given) {
        foreach ($default_array as $key => $val) {
            if (array_key_exists($key, $array_given)) {
                $object->{$key} = $array_given[$key];
            } else {
                $object->{$key} = $val;
            }
        }
    }

    

}

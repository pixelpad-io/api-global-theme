<?php

class ThemeUpdater {

    public $theme_slug;
    public $version;

    public function __construct() {
        $this->theme_slug = get_template();
        $this->theme_data = wp_get_theme( $this->theme_slug );
        $this->version = $this->theme_data['Version'];
        $this->jsonURL = "https://raw.githubusercontent.com/pixelpad-io/api-global-theme/master/info.json";

        add_filter("themes_api", array($this, "info"), 20, 3);
        add_filter("site_transient_update_themes", array($this, "update"));

    }

    public function info($res, $action, $args) {
        error_log($action);
        return;

        if ("theme_information" !== $action) {
            return false;
        }
        if ($this->theme_slug !== $args->slug) {
            return false;
        }

        $remote = $this->request();
        if (!$remote) {
            return false;
        }

        $res = new stdClass();

        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->requires_php = $remote->requires_php;
        $res->last_updated = $remote->last_updated;

        $res->sections = array(
            "description" => $remote->sections->description,
            "installation" => $remote->sections->installation,
            "changelog" => $remote->sections->changelog
        );
        return $res;
    }

    public function update($transient) {

        if (empty($transient->checked)) {
            return $transient;
        }

        $remote = $this->request();
        if ($remote && version_compare($this->version, $remote->version, "<")) {
            $res = array(
                'theme'        => $this->theme_slug,
                'new_version'  => $remote->version,
                'url'          => '',
                'package'      => $remote->download_url,
                'requires'     => '',
                'requires_php' => '',
            );
            $transient->response[$this->theme_slug] = $res;
        }
        return $transient;
    }

    public function request() {
        $remote = wp_remote_get(
            $this->jsonURL,
            array(
                "timeout" => 10,
                "headers" => array(
                    "Accept" => "application/json"
                )
            )
        );

        if (
            is_wp_error($remote)
            || 200 !== wp_remote_retrieve_response_code($remote)
            || empty(wp_remote_retrieve_body($remote))
        ) {
            return false;
        }

        $remote = json_decode(wp_remote_retrieve_body($remote));
        return $remote;
    }
}
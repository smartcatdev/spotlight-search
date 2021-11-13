<?php
/**
* Plugin name: WP Spotlight
* Author: TastyShawarma
* Description: Find anything, quickly! A spotlight search for WordPress admins.
*
*/

namespace spotlight;

if (!defined('ABSPATH')) {
    die;
}
require 'constants.php';

function get_plugin_path($path = '') {
    return plugin_dir_path(__FILE__) . $path;
}

function get_includes_path($path = '') {
    return get_plugin_path('includes/') . $path;
}

function get_plugin_url($url) {
    return plugin_dir_url(__FILE__) . $url;
}

add_action('init', 'spotlight\include_files');
function include_files() {
    if(is_admin()) {
        require get_includes_path('functions-general.php');
    }
    
}

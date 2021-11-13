<?php

namespace spotlight;

function load_styles_scripts() {
    wp_enqueue_script( 'spotlight-main-script', get_plugin_url('assets/script.js'), ['jquery'], Constants::VERSION );
    wp_enqueue_style('spotlight-main-style', get_plugin_url('assets/style.css'), [], Constants::VERSION);
}
add_action('admin_enqueue_scripts', 'spotlight\load_styles_scripts');

function display_spotlight() {    
    include get_plugin_path('includes/template-spotlight.php');
}
add_action('admin_footer', 'spotlight\display_spotlight');

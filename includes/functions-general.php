<?php

namespace spotlight;


function load_styles_scripts() {
    wp_enqueue_script('spotlight-main-script', get_plugin_url('assets/script.js'), ['jquery'], Constants::VERSION );
    wp_localize_script('spotlight-main-script', 'spotlightSettings', [
        'apiUrl' => esc_url_raw(get_rest_url() . 'spotlight/v1/'),
        'nonce' => wp_create_nonce('spotlight-nonce')
    ]);
    wp_enqueue_style('spotlight-main-style', get_plugin_url('assets/style.css'), [], Constants::VERSION);
}
add_action('wp_enqueue_scripts', 'spotlight\load_styles_scripts');

/**
 * Load spotlight template
 * 
 */
function display_spotlight() {    
    include get_plugin_path('includes/template-spotlight.php');
}
add_action('wp_footer', 'spotlight\display_spotlight');

/**
 * Create search endpoint
 * 
 */
function create_rest_endpoint() {
    register_rest_route('spotlight/v1', '/search', [
        'methods' => 'GET',
        'callback' => 'spotlight\do_search'
    ]);
}
add_action('rest_api_init', 'spotlight\create_rest_endpoint');

/**
 * Do the actual search
 * 
 */
function do_search($request) {
    
    global $wpdb;

    $term = $request->get_param('term');
    $post_types = "'post', 'page', 'job'";
    $post_statuses = "'publish', 'draft'";

    $search = '%' . $term . '%';
    $query = "  SELECT DISTINCT ID, post_date, post_title, post_type, type FROM(
                (SELECT DISTINCT ID, post_date, post_title, post_type, 'title' as type
                FROM {$wpdb->prefix}posts 
                WHERE (post_title LIKE %s OR post_name LIKE %s) 
                AND post_type in ({$post_types}) 
                AND post_status in ({$post_statuses}) LIMIT 25)
                UNION 
                (SELECT DISTINCT ID, post_date, post_title, post_type, 'content' as type 
                FROM {$wpdb->prefix}posts 
                WHERE post_content like %s 
                AND post_type in ({$post_types}) 
                AND post_status in ({$post_statuses}) LIMIT 10))
                ORDER BY field(post_type, 'page', 'post', 'job'), post_date DESC;";
                
    $result = $wpdb->get_results(
        $wpdb->prepare(
            $query,
            $search,
            $search,
            $search
        )
    );

    return new \WP_Rest_Response(parse_data($result), 200);
}

/**
 * Mutate the data, add missing info and prepare to return as a response
 * 
 */
function parse_data($posts) {
    foreach ($posts as $post) {
        $post->url = get_permalink($post->ID);
        $post->edit_url = get_admin_url() . 'post.php?post=' . $post->ID . '&action=edit';
    }
    return $posts;
}
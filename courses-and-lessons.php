<?php

/**
 * Plugin Name: Courses and Lessons
 * Description: Creates a custom post type for Lessons and a custom taxonomy for Courses
 * Version: 1.0
 * Author: Carlos Capote <carlos.capote@hawara.es>
 * Text Domain: courses-and-lessons
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once 'src/loader.php';

$plugin = new Plugin(
    plugin_dir_path(__FILE__)
);

$plugin->addActions();
$plugin->addFilters();

/**
 * Display custom column content
 */
add_action('manage_lesson_posts_custom_column',  function ($column, $post_id) {
    switch ($column) {
        case 'courses':
            $courses = get_the_terms($post_id, 'courses');

            if (empty($courses))
                return;

            $course = reset($courses);

            echo '<a href="' . admin_url('edit.php?post_type=lesson&courses=' . $course->slug) . '">' .
                $course->name .
            '</a>';

            break;
        case 'lesson_order':
            echo esc_html(get_post_meta($post_id, 'lesson_order', true));

            break;
    }
}, 10, 2);

/**
 * Make the lesson order column sortable
 */
add_filter('manage_edit-lesson_sortable_columns', function($columns) {
    $columns['lesson_order'] = 'lesson_order';

    return $columns;
});

/**
 * Handle sorting lessons by order
 */
add_action('pre_get_posts', function($query) {
    if (! is_admin() || ! $query->is_main_query()) return;

    if ($query->get('orderby') === 'lesson_order') {
        $query->set('meta_key', 'lesson_order');
        $query->set('orderby', 'meta_value_num');
    }
});

/**
 * Add a filter by courses to the manage lessons page
 */
add_action('restrict_manage_posts', function ($post_type){
    if (! is_admin())
        return;

	if('lesson' !== $post_type)
		return;

	$taxonomies_slugs = ['courses'];

	foreach($taxonomies_slugs as $slug) {
		$taxonomy = get_taxonomy($slug);
		$selected = isset($_REQUEST[$slug]) ? $_REQUEST[$slug] : '';

		wp_dropdown_categories([
			'show_option_all' => $taxonomy->labels->all_items,
			'taxonomy' => $slug,
			'name' => $slug,
			'orderby' => 'name',
			'value_field' => 'slug',
			'selected' => $selected,
			'hierarchical' => false,
		]);
	}
});

/**
 * Activation hook to flush rewrite rules
 */
register_activation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

/**
 * Deactivation hook to flush rewrite rules
 */
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});
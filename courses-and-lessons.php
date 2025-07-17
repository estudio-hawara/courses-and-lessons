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

$plugin = new Plugin(plugin_dir_path(__FILE__));
$templates = new Templates($plugin);

/**
 * Register the Lesson custom post type
 */
(new Lesson)->init();

/**
 * Register the Courses taxonomy
 */
(new Course)->init();

/**
 * Load plugin textdomain for translations
 */
(new TextDomain($plugin))->init();

/**
 * Show a selector to choose the course of each lesson
 */
add_action('add_meta_boxes', function() {
    add_meta_box('course_dropdown_meta_box', __('Course', 'courses-and-lessons'),
        function ($post) {
            $current_terms = wp_get_post_terms($post->ID, 'courses', ['fields' => 'ids']);
            $current_term_id = $current_terms ? $current_terms[0] : 0;

            $terms = get_terms([
                'taxonomy' => 'courses',
                'hide_empty' => false,
            ]);

            wp_nonce_field('save_course_dropdown', 'course_dropdown_nonce');

            echo '<select name="course_dropdown" id="course_dropdown">';
            echo '<option value="">' . __('- Select Course -', 'courses-and-lessons') . '</option>';
            foreach ($terms as $term) {
                $selected = selected($term->term_id, $current_term_id, false);
                echo "<option value='{$term->term_id}' $selected>{$term->name}</option>";
            }
            echo '</select>';
        },
        'lesson',
        'side',
        'default'
    );
});

/**
 * Save the selected course
 */
add_action('save_post_lesson', function ($post_id) {
    if (! isset($_POST['course_dropdown_nonce']))
        return;

    if (! wp_verify_nonce($_POST['course_dropdown_nonce'], 'save_course_dropdown'))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (wp_is_post_revision($post_id))
        return;

    $terms = isset($_POST['course_dropdown']) ? [intval($_POST['course_dropdown'])] : [];

    wp_set_post_terms($post_id, $terms, 'courses');
});

/**
 * Save the lesson order
 */
add_action('save_post_lesson', function ($post_id) {
    if (! isset($_POST['lesson_order_nonce'])) 
        return;

    if (! wp_verify_nonce($_POST['lesson_order_nonce'], 'save_lesson_order'))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (wp_is_post_revision($post_id))
        return;

    if (isset($_POST['lesson_order']))
        update_post_meta($post_id, 'lesson_order', intval($_POST['lesson_order']));
});

/**
 * Add custom columns to lesson admin list
 */
add_filter('manage_lesson_posts_columns', function ($columns) {
    return [
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'courses' => __('Courses', 'courses-and-lessons'),
        'lesson_order' => __('Order', 'courses-and-lessons'),
        'date' => $columns['date'],
    ];
});

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

// TODO: Populate the lesson order
// https://www.voxfor.com/implementing-custom-quick-edit-for-custom-post-type-fields-in-wordpress/

/**
 * Make the lesson order editable
 */
add_action('quick_edit_custom_box', function($column_name, $post_type) use ($templates) {
    if ($column_name !== 'lesson_order' || $post_type !== 'lesson')
        return;

    wp_nonce_field('save_lesson_order', 'lesson_order_nonce');

    echo $templates->render('quick-edit-lesson-order', [
        'title' => __('Lesson Order', 'courses-and-lessons'),
    ]);
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
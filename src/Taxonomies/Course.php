<?php

class Course implements HasActions
{
    public function addActions(): void
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Register the courses taxonomy
     */
    public function register()
    {
        $labels = [
            'name' => __('Courses', 'courses-and-lessons'),
            'singular_name' => __('Course', 'courses-and-lessons'),
            'search_items' => __('Search Courses', 'courses-and-lessons'),
            'popular_items' => __('Popular Courses', 'courses-and-lessons'),
            'all_items' => __('All Courses', 'courses-and-lessons'),
            'edit_item' => __('Edit Course', 'courses-and-lessons'),
            'update_item' => __('Update Course', 'courses-and-lessons'),
            'add_new_item' => __('Add New Course', 'courses-and-lessons'),
            'new_item_name' => __('New Course Name', 'courses-and-lessons'),
            'separate_items_with_commas' => __('Separate courses with commas', 'courses-and-lessons'),
            'add_or_remove_items' => __('Add or remove courses', 'courses-and-lessons'),
            'choose_from_most_used' => __('Choose from the most used courses', 'courses-and-lessons'),
            'menu_name' => __('Courses', 'courses-and-lessons'),
        ];
        
        $args = [
            'labels' => $labels,
            'hierarchical' => false,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'show_in_quick_edit' => false,
            'query_var' => true,
            'rewrite' => ['slug' => 'courses'],
            'show_in_rest' => false,
            'show_tagcloud' => false,
            'meta_box_cb' => false,
        ];

        register_taxonomy('courses', ['lesson'], $args);
    }
}
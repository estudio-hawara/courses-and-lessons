<?php

namespace CoursesAndLessons\Taxonomies;

use CoursesAndLessons\Interfaces\HasActions;

class Module implements HasActions
{
    public function addActions(): void
    {
        add_action('init', [$this, 'register']);
    }

    /**
     * Register the modules taxonomy
     */
    public function register()
    {
        $labels = [
            'name' => __('Modules', 'courses-and-lessons'),
            'singular_name' => __('Module', 'courses-and-lessons'),
            'search_items' => __('Search Modules', 'courses-and-lessons'),
            'popular_items' => __('Popular Modules', 'courses-and-lessons'),
            'all_items' => __('All Modules', 'courses-and-lessons'),
            'edit_item' => __('Edit Module', 'courses-and-lessons'),
            'update_item' => __('Update Module', 'courses-and-lessons'),
            'add_new_item' => __('Add New Module', 'courses-and-lessons'),
            'new_item_name' => __('New Module Name', 'courses-and-lessons'),
            'separate_items_with_commas' => __('Separate modules with commas', 'courses-and-lessons'),
            'add_or_remove_items' => __('Add or remove modules', 'courses-and-lessons'),
            'choose_from_most_used' => __('Choose from the most used modules', 'courses-and-lessons'),
            'menu_name' => __('Modules', 'courses-and-lessons'),
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
            'show_in_rest' => false,
            'show_tagcloud' => false,
            'meta_box_cb' => false,
        ];

        register_taxonomy('module', ['lesson'], $args);
    }
}
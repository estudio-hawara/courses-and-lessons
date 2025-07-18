<?php

class Lesson implements HasActions, HasFilters
{
    public function addActions(): void
    {
        add_action('init', [$this, 'register']);
    }

    public function addFilters(): void
    {
        add_filter('manage_lesson_posts_columns', [$this, 'setManageColumns']);
    }

    /**
     * Register the lesson custom post type
     */
    public function register(): void
    {
        $labels = [
            'name' => __('Lessons', 'courses-and-lessons'),
            'singular_name' => __('Lesson', 'courses-and-lessons'),
            'menu_name' => __('Lessons', 'courses-and-lessons'),
            'add_new' => __('Add New', 'courses-and-lessons'),
            'add_new_item' => __('Add New Lesson', 'courses-and-lessons'),
            'edit_item' => __('Edit Lesson', 'courses-and-lessons'),
            'new_item' => __('New Lesson', 'courses-and-lessons'),
            'view_item' => __('View Lesson', 'courses-and-lessons'),
            'search_items' => __('Search Lessons', 'courses-and-lessons'),
            'not_found' => __('No lessons found', 'courses-and-lessons'),
            'not_found_in_trash' => __('No lessons found in trash', 'courses-and-lessons'),
            'all_items' => __('All Lessons', 'courses-and-lessons'),
            'archives' => __('Lesson Archives', 'courses-and-lessons'),
            'attributes' => __('Lesson Attributes', 'courses-and-lessons'),
            'insert_into_item' => __('Insert into lesson', 'courses-and-lessons'),
            'uploaded_to_this_item' => __('Uploaded to this lesson', 'courses-and-lessons'),
        ];
        
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'lessons'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
            'show_in_rest' => true,
        ];
        
        register_post_type('lesson', $args);
    }

    /**
     * Add custom columns to lesson admin list
     */
    public function setManageColumns($columns) {
        return [
            'cb' => $columns['cb'],
            'title' => $columns['title'],
            'module' => __('Module', 'courses-and-lessons'),
            'lesson_order' => __('Order', 'courses-and-lessons'),
            'date' => $columns['date'],
        ];
    }
}
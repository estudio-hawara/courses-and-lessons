<?php

class LessonCourse implements HasActions
{
    public function __construct(
        protected readonly Plugin $plugin
    ) {
        //
    }

    public function addActions(): void
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post_lesson', [$this, 'savePost']);
        add_action('manage_lesson_posts_custom_column', [$this, 'displayColumnValue'], accepted_args: 2);
        add_action('restrict_manage_posts', [$this, 'filterByCourse']);
    }

    /**
     * Show a selector to choose the course of each lesson
     */
    public function addMetaBox()
    {
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
    }

    /**
     * Save the lesson order on submission
     */
    public function savePost($post_id) {
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
    }

    /**
     * Show the lesson course value in the lesson management table
     */
    public function displayColumnValue($column, $post_id) {
        if ($column !== 'courses')
            return;

        $courses = get_the_terms($post_id, 'courses');

        if (empty($courses))
            return;

        $course = reset($courses);

        echo '<a href="' . admin_url('edit.php?post_type=lesson&courses=' . $course->slug) . '">' .
            $course->name .
        '</a>';
    }

    /**
     * Filter lessons by course
     */
    public function filterByCourse($post_type)
    {
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
    }
}
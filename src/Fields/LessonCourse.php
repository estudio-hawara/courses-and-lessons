<?php

class LessonCourse
{
    public function __construct(
        protected readonly Plugin $plugin
    ) {
        //
    }

    public function addActions()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('save_post_lesson', [$this, 'savePost']);
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
}
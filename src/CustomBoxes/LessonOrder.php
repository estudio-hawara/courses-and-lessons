<?php

class LessonOrder
{
    protected Templates $templates;

    public function __construct(
        protected readonly Plugin $plugin
    ) {
        $this->templates = $plugin->getTemplates();
    }

    public function addActions()
    {
        add_action('quick_edit_custom_box', [$this, 'addCustomBox'], 10, 2);
        add_action('save_post_lesson', [$this, 'savePost']);
    }

    public function addCustomBox($column_name, $post_type) {
        if ($column_name !== 'lesson_order' || $post_type !== 'lesson')
            return;

        wp_nonce_field('save_lesson_order', 'lesson_order_nonce');

        echo $this->templates->render('quick-edit-lesson-order', [
            'title' => __('Lesson Order', 'courses-and-lessons'),
        ]);
    }

    public function savePost($post_id)
    {
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
    }
}
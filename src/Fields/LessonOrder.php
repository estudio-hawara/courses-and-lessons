<?php

class LessonOrder
{
    protected Templates $templates;

    public function __construct(
        protected readonly Plugin $plugin
    ) {
        $this->templates = $plugin->getTemplates();
    }

    // TODO: Populate the lesson order
    // https://www.voxfor.com/implementing-custom-quick-edit-for-custom-post-type-fields-in-wordpress/

    public function addActions()
    {
        add_action('quick_edit_custom_box', [$this, 'addCustomBox'], 10, 2);
        add_action('save_post_lesson', [$this, 'savePost']);
        add_action('manage_lesson_posts_custom_column',  [$this, 'displayColumnValue'], 10, 2);
        add_filter('manage_edit-lesson_sortable_columns', [$this, 'makeSortable']);
        add_action('pre_get_posts', [$this, 'handleSort']);
    }

    /**
     * Make the lesson order editable in the quick box
     */
    public function addCustomBox($column_name, $post_type)
    {
        if ($column_name !== 'lesson_order' || $post_type !== 'lesson')
            return;

        wp_nonce_field('save_lesson_order', 'lesson_order_nonce');

        echo $this->templates->render('quick-edit-lesson-order', [
            'title' => __('Lesson Order', 'courses-and-lessons'),
        ]);
    }

    /**
     * Save the lesson order on submission
     */
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

    /**
     * Show the lesson order value in the lesson management table
     */
    public function displayColumnValue($column, $post_id)
    {
        if ($column !== 'lesson_order')
            return;

        echo esc_html(get_post_meta($post_id, 'lesson_order', true));
    }

    /**
     * Make the lesson order column sortable
     */
    public function makeSortable($columns)
    {
        $columns['lesson_order'] = 'lesson_order';

        return $columns;
    }

    /**
     * Handle sorting by lesson order
     */
    public function handleSort($query)
    {
        if (! is_admin() || ! $query->is_main_query())
            return;

        if ($query->get('orderby') === 'lesson_order') {
            $query->set('meta_key', 'lesson_order');
            $query->set('orderby', 'meta_value_num');
        }
    }
}
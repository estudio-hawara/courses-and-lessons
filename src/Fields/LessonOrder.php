<?php

class LessonOrder implements HasActions, HasFilters
{
    protected Templates $templates;

    public function __construct(
        protected readonly Plugin $plugin
    ) {
        $this->templates = $plugin->getTemplates();
    }

    public function addActions(): void
    {
        add_action('quick_edit_custom_box', [$this, 'addCustomBox'], accepted_args: 2);
        add_action('save_post_lesson', [$this, 'savePost']);
        add_action('manage_lesson_posts_custom_column',  [$this, 'displayColumnValue'], accepted_args: 2);
        add_action('pre_get_posts', [$this, 'handleSort']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScript']);
    }

    public function addFilters(): void
    {
        add_filter('manage_edit-lesson_sortable_columns', [$this, 'makeSortable']);
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
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (wp_is_post_revision($post_id))
            return;

        if (isset($_POST['lesson_order']) && $_POST['lesson_order']) {
            if (! isset($_POST['lesson_order_nonce'])) 
                return;

            if (! wp_verify_nonce($_POST['lesson_order_nonce'], 'save_lesson_order'))
                return;

            $order = intval($_POST['lesson_order']);
        } else {
            $terms = wp_get_post_terms($post_id, 'courses', ['fields' => 'ids']);

            if (! is_array($terms) || empty($terms))
                return;

            $course_id = reset($terms);

            if (! $course_id)
                return;

            $order = $this->getNextOrder($course_id, $post_id);
        }

        update_post_meta($post_id, 'lesson_order', $order);
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

    /**
     * Enqueue script on the admin lessons listing page
     */
    public function enqueueScript($hook)
    {
        if ($hook !== 'edit.php')
            return;

        if (! isset($_GET['post_type']) || $_GET['post_type'] !== 'lesson')
            return;

        wp_enqueue_script(
            'lesson-order-quick-edit',
            path_join($this->plugin->getUrl(), 'js/lesson-order-quick-edit.js'),
            ['jquery', 'inline-edit-post'],
            false,
            true
        );
    }

    /**
     * Get the next lesson order for a given course
     */
    private function getNextOrder(int $courseId, int $postId): int
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT MAX(pm.meta_value)
            FROM wp_postmeta pm
            INNER JOIN wp_term_relationships tr ON pm.post_id = tr.object_id
            INNER JOIN wp_posts p ON pm.post_id = p.id
            WHERE pm.meta_key = 'lesson_order'
            AND tr.term_taxonomy_id = %d
            AND pm.post_id != %d
            AND p.post_status = 'publish'
            GROUP BY tr.term_taxonomy_id",
            $courseId,
            $postId
        );

        $max_order = $wpdb->get_var($query);
        $order = !is_null($max_order) ? intval($max_order) + 1 : 1;

        return $order;
    }
}
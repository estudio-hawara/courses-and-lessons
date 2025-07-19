<?php

class LessonModule implements HasActions
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
        add_action('restrict_manage_posts', [$this, 'filterByModule']);
    }

    /**
     * Show a selector to choose the module of each lesson
     */
    public function addMetaBox()
    {
        add_meta_box('module_dropdown_meta_box', __('Module', 'courses-and-lessons'),
            function ($post) {
                $current_terms = wp_get_post_terms($post->ID, 'modules', ['fields' => 'ids']);

                $current_term_id = $current_terms ? $current_terms[0] : 0;

                $terms = get_terms([
                    'taxonomy' => 'modules',
                    'hide_empty' => false,
                ]);

                wp_nonce_field('save_module_dropdown', 'module_dropdown_nonce');

                echo '<select name="module_dropdown" id="module_dropdown">';
                echo '<option value="">' . __('- Select Module -', 'courses-and-lessons') . '</option>';
                foreach ($terms as $term) {
                    $selected = selected($term?->term_id, $current_term_id, false);
                    echo "<option value='{$term?->term_id}' $selected>{$term?->name}</option>";
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
        if (! isset($_POST['module_dropdown_nonce']))
            return;

        if (! wp_verify_nonce($_POST['module_dropdown_nonce'], 'save_module_dropdown'))
            return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (wp_is_post_revision($post_id))
            return;

        $terms = isset($_POST['module_dropdown']) ? [intval($_POST['module_dropdown'])] : [];

        wp_set_post_terms($post_id, $terms, 'modules');
    }

    /**
     * Show the lesson module value in the lesson management table
     */
    public function displayColumnValue($column, $post_id) {
        if ($column !== 'module')
            return;

        $modules = get_the_terms($post_id, 'modules');

        if (! is_array($modules))
            return;

        $module = reset($modules);

        echo '<a href="' . admin_url('edit.php?post_type=lesson&modules=' . $module->slug) . '">' .
            $module->name .
        '</a>';
    }

    /**
     * Filter lessons by module
     */
    public function filterByModule($post_type)
    {
        if (! is_admin())
            return;

        if('lesson' !== $post_type)
            return;

        $taxonomies_slugs = ['modules'];

        foreach($taxonomies_slugs as $slug) {
            $taxonomy = get_taxonomy($slug);
            $selected = isset($_REQUEST[$slug]) ? $_REQUEST[$slug] : '';

            wp_dropdown_categories([
                'show_option_all' => $taxonomy?->labels->all_items ?? __('Show all', 'courses-and-lessons'),
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
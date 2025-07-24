<?php

namespace CoursesAndLessons\Fields;

use CoursesAndLessons\Interfaces\HasActions;
use CoursesAndLessons\Plugin;
use CoursesAndLessons\Templates;

class ModuleOrder implements HasActions
{
    protected Templates $templates;

    public function __construct(
        protected readonly Plugin $plugin
    ) {
        $this->templates = $plugin->getTemplates();
    }

    public function addActions(): void
    {
        add_action('module_edit_form_fields', [$this, 'editMetaField']);
        add_action('module_add_form_fields', [$this, 'addMetaField']);
        add_action('edited_module', [$this, 'saveMetaField']);
        add_action('create_module', [$this, 'saveMetaField']);
    }

    /**
     * Add the order field meta box to edit module page
     */
    public function editMetaField($term)
    {
        $currentOrder = $term?->term_id ? get_term_meta($term->term_id, 'module_order', true) : null;

        wp_nonce_field('save_module_order', 'module_order_nonce');

        echo $this->templates->render('module-order/edit-form-field', [
            'currentOrder' => $currentOrder,
        ]);
    }

    /**
     * Add the order field meta box to new module page
     */
    public function addMetaField($taxonomy)
    {
        wp_nonce_field('save_module_order', 'module_order_nonce');

        echo $this->templates->render('module-order/add-form-field');
    }

    /**
     * Save the course relationship meta field
     */
    public function saveMetaField($term_id)
    {
        if (isset($_POST['module_order']) && $_POST['module_order']) {
            if (! isset($_POST['module_order_nonce'])) 
                return;

            if (! wp_verify_nonce($_POST['module_order_nonce'], 'save_module_order'))
                return;

            $order = intval($_POST['module_order']);
        } else {
            $course_id = get_term_meta($term_id, 'module_course', true);

            if (! $course_id)
                return;

            $order = $this->getNextOrder($course_id, $term_id);
        }

        update_term_meta($term_id, 'module_order', $order);
    }

    /**
     * Get the next module order for a given course
     */
    private function getNextOrder(int $courseId, int $termId): int
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT MAX(tmo.meta_value)
            FROM wp_termmeta tmc
            INNER JOIN wp_termmeta tmo ON tmo.term_id = tmc.term_id AND tmo.meta_key = 'module_order'
            WHERE
                tmc.meta_key = 'module_course' AND
                tmc.meta_value = %d AND
                tmc.term_id != %d",
            $courseId,
            $termId
        );

        $maxOrder = $wpdb->get_var($query);
        $order = !is_null($maxOrder) ? intval($maxOrder) + 1 : 1;

        return $order;
    }
}
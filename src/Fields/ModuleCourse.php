<?php

class ModuleCourse implements HasActions
{
    protected Templates $templates;

    public function __construct(
        protected readonly Plugin $plugin
    ) {
        $this->templates = $plugin->getTemplates();
    }

    public function addActions(): void
    {
        add_action('modules_edit_form_fields', [$this, 'editMetaField'], accepted_args: 2);
        add_action('modules_add_form_fields', [$this, 'addMetaField']);
        add_action('edited_modules', [$this, 'saveMetaField']);
        add_action('create_modules', [$this, 'saveMetaField']);
    }

    /**
     * Add course selection meta box to edit module page
     */
    public function editMetaField($term, $taxonomy)
    {
        $currentCourse = get_term_meta($term->term_id, 'module_course', true);

        $courses = get_terms([
            'taxonomy' => 'courses',
            'hide_empty' => false,
        ]);

        wp_nonce_field('save_module_course', 'module_course_nonce');

        echo $this->templates->render('module-course/edit-form-field', [
            'courses' => $courses,
            'currentCourse' => $currentCourse,
        ]);
    }

    /**
     * Add course selection meta box to new module page
     */
    public function addMetaField($taxonomy)
    {
        $courses = get_terms([
            'taxonomy' => 'courses',
            'hide_empty' => false,
        ]);

        wp_nonce_field('save_module_course', 'module_course_nonce');

        echo $this->templates->render('module-course/add-form-field', [
            'courses' => $courses,
        ]);
    }

    /**
     * Save the course relationship meta field
     */
    public function saveMetaField($term_id)
    {
        if (! isset($_POST['module_course']))
            return;

        if (! isset($_POST['module_course_nonce'])) 
            return;

        if (! wp_verify_nonce($_POST['module_course_nonce'], 'save_module_course'))
            return;

        $moduleCourse = intval($_POST['module_course']);

        update_term_meta($term_id, 'module_course', $moduleCourse);
    }
}
<?php

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
        add_action('modules_edit_form_fields', [$this, 'editMetaField'], accepted_args: 2);
        add_action('modules_add_form_fields', [$this, 'addMetaField']);
        add_action('edited_modules', [$this, 'saveMetaField'], accepted_args: 2);
        add_action('create_modules', [$this, 'saveMetaField'], accepted_args: 2);
    }

    /**
     * Add the order field meta box to edit module page
     */
    public function editMetaField($term, $taxonomy)
    {
        $currentOrder = get_term_meta($term->term_id, 'module_order', true);

        wp_nonce_field('save_module_order', 'module_order_nonce');

        echo $this->templates->render('edit-module-order-form-field', [
            'currentOrder' => $currentOrder,
        ]);
    }

    /**
     * Add the order field meta box to new module page
     */
    public function addMetaField($taxonomy)
    {
        wp_nonce_field('save_module_order', 'module_order_nonce');

        echo $this->templates->render('add-module-order-form-field');
    }

    /**
     * Save the course relationship meta field
     */
    public function saveMetaField($term_id, $tt_id)
    {
        if (! isset($_POST['module_order']))
            return;

        if (! isset($_POST['module_order_nonce'])) 
            return;

        if (! wp_verify_nonce($_POST['module_order_nonce'], 'save_module_order'))
            return;

        $moduleOrder = intval($_POST['module_order']);
        update_term_meta($term_id, 'module_order', $moduleOrder);
    }
}
<?php

class TextDomain
{
    public function __construct(
        protected readonly Plugin $plugin
    ) {
        //
    }

    public function addActions()
    {
        add_action('init', [$this, 'load']);
    }

    public function getPath(): string
    {
        return path_join($this->plugin->getPath(), "languages/");
    }

    /**
     * Load plugin textdomain for translations
     */
    public function load()
    {
        load_plugin_textdomain(
            'courses-and-lessons',
            false,
            $this->getPath(),
        );
    }
}
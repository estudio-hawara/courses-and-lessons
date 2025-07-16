<?php

class Templates
{
    public function __construct(
        protected readonly Plugin $plugin
    ) {
        //
    }

    public function render(string $template, array $data = []): string
    {
        extract($data);
        ob_start();

        include path_join($this->plugin->getPath(), "views/$template.php");

        return ob_get_clean();
    }
}
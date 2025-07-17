<?php

class Templates implements HasPath
{
    public function __construct(
        protected readonly Plugin $plugin
    ) {
        //
    }

    public function getPath(): string
    {
        return path_join($this->plugin->getPath(), "views/");
    }

    public function render(string $template, array $data = []): string
    {
        extract($data);
        ob_start();

        include path_join($this->getPath(), "$template.php");

        return ob_get_clean();
    }
}
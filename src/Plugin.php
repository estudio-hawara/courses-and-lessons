<?php

class Plugin
{
    protected Templates $templates;

    public function __construct(
        protected readonly string $path,
    ) {
        $this->templates = new Templates($this);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTemplates(): Templates
    {
        return $this->templates;
    }
}
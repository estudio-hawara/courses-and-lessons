<?php

class Plugin
{
    public function __construct(
        protected readonly string $path
    ) {
        //
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
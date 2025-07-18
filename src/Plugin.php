<?php

require_once 'Interfaces/HasActions.php';
require_once 'Interfaces/HasFilters.php';
require_once 'Interfaces/HasHooks.php';
require_once 'Interfaces/HasPath.php';

require_once 'Templates.php';
require_once 'TextDomain.php';

require_once 'PostTypes/Lesson.php';
require_once 'Taxonomies/Course.php';
require_once 'Taxonomies/Module.php';

require_once 'Fields/LessonModule.php';
require_once 'Fields/LessonOrder.php';

class Plugin implements HasActions, HasFilters, HasHooks, HasPath
{
    protected readonly string $file;
    protected readonly string $path;
    protected readonly string $url;
    protected Templates $templates;
    protected Lesson $lesson;
    protected Course $course;
    protected Module $module;
    protected LessonModule $lessonModule;
    protected LessonOrder $lessonOrder;
    protected TextDomain $textDomain;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->path = plugin_dir_path($file);
        $this->url = plugin_dir_url($file);

        $this->templates = new Templates($this);
        $this->lesson = new Lesson;
        $this->course = new Course;
        $this->module = new Module;
        $this->lessonOrder = new LessonOrder($this);
        $this->lessonModule = new LessonModule($this);
        $this->textDomain = new TextDomain($this);
    }

    public function addActions(): void
    {
        $this->lesson->addActions();
        $this->course->addActions();
        $this->module->addActions();
        $this->lessonOrder->addActions();
        $this->lessonModule->addActions();
        $this->textDomain->addActions();
    }

    public function addFilters(): void
    {
        $this->lesson->addFilters();
        $this->lessonOrder->addFilters();
    }

    public function registerHooks(): void
    {
        register_activation_hook($this->getFile(), fn()  => flush_rewrite_rules());
        register_deactivation_hook($this->getFile(), fn() => flush_rewrite_rules());
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTemplates(): Templates
    {
        return $this->templates;
    }
}